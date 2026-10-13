<?php

namespace App\Services\Leads;

use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LeadCaptureService
{
    public function __construct(
        private readonly LeadScoringService $scoring,
        private readonly NewLeadEmailNotifier $emailNotifier
    ) {
    }

    public function capture(
        array $input,
        ?string $ip = null,
        ?string $landingPage = null,
        ?string $referrer = null
    ): array {
        $projectData = $this->projectData($input['project_data'] ?? []);

        $name = trim((string) (
            $input['name']
            ?? $input['full_name']
            ?? ''
        ));

        $phone = trim((string) ($input['phone'] ?? ''));
        $phoneNormalized = $this->normalizePhone($phone);

        if ($phoneNormalized === null) {
            throw ValidationException::withMessages([
                'phone' => 'Geçerli bir Türkiye telefon numarası giriniz.',
            ]);
        }

        $source = $this->source($input);

        $projectType = $this->firstValue([
            $input['project_type'] ?? null,
            $projectData['project_type'] ?? null,
            $projectData['projectType'] ?? null,
        ]);

        $city = $this->firstValue([
            $input['city'] ?? null,
            $projectData['city'] ?? null,
        ]);

        $district = $this->firstValue([
            $input['district'] ?? null,
            $projectData['district'] ?? null,
            $input['location'] ?? null,
        ]);

        $timeline = $this->firstValue([
            $input['timeline'] ?? null,
            $projectData['timeline'] ?? null,
        ]);

        $area = (float) (
            $input['area']
            ?? $projectData['area']
            ?? $input['calculated_area']
            ?? 0
        );

        $budgetConfirmed = filter_var(
            $input['budget_confirmed'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        /*
         * Formu gönderen kişi proje değerlendirmesi veya keşif istediği için
         * bu, açık bir ticari talep olarak değerlendirilir.
         */
        $explicitRequest = filter_var(
            $input['explicit_request'] ?? true,
            FILTER_VALIDATE_BOOLEAN
        );
        $scoreResult = $this->scoring->score([
            'budget_confirmed' => $budgetConfirmed,
            'timeline' => $timeline,
            'phone' => $phoneNormalized,
            'city' => $city,
            'project_type' => $projectType,
            'area' => $area,
            'explicit_request' => $explicitRequest,
            'has_file' => filter_var(
                $input['has_file'] ?? false,
                FILTER_VALIDATE_BOOLEAN
            ),
            'is_decision_maker' => filter_var(
                $input['is_decision_maker'] ?? false,
                FILTER_VALIDATE_BOOLEAN
            ),
        ]);

        $contactConsent = filter_var(
            $input['contact_consent'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        $marketingConsent = filter_var(
            $input['marketing_consent'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        $notes = $this->buildNotes(
            input: $input,
            projectData: $projectData,
            quality: $scoreResult['quality'],
            city: $city,
            district: $district,
            timeline: $timeline,
            area: $area,
            budgetConfirmed: $budgetConfirmed
        );

        $attributes = [
            'public_id' => (string) Str::ulid(),
            'name' => $name,
            'phone' => $phone,
            'phone_normalized' => $phoneNormalized,
            'email' => $this->nullableString($input['email'] ?? null),
            'source' => $source,
            'project_type' => $projectType,
            'notes' => $notes,
            'project_data' => $projectData,
            'estimated_cost' => $this->nullableNumber(
                $input['estimated_cost'] ?? null
            ),
            'estimated_min' => $this->nullableNumber(
                $input['estimated_min'] ?? null
            ),
            'estimated_max' => $this->nullableNumber(
                $input['estimated_max'] ?? null
            ),
            'budget_confirmed' => $budgetConfirmed,
            'lead_score' => $scoreResult['score'],
            'lead_quality' => $scoreResult['quality'],
            'sales_ready' => $scoreResult['sales_ready'],
            'status' => 'yeni',
            'city' => $city,
            'district' => $district,
            'area' => $area > 0 ? $area : null,
            'timeline' => $timeline,
            'utm_source' => $this->nullableString($input['utm_source'] ?? null),
            'utm_medium' => $this->nullableString($input['utm_medium'] ?? null),
            'utm_campaign' => $this->nullableString($input['utm_campaign'] ?? null),
            'utm_content' => $this->nullableString($input['utm_content'] ?? null),
            'utm_term' => $this->nullableString($input['utm_term'] ?? null),
            'landing_page' => $this->nullableString(
                $input['landing_page'] ?? $landingPage
            ),
            'referrer' => $this->nullableString(
                $input['referrer'] ?? $referrer
            ),
            'price_version' => $this->nullableString(
                $input['price_version'] ?? 'worksitem-2026'
            ),
            'price_snapshot' => $this->priceSnapshot(
                input: $input,
                projectData: $projectData
            ),
            'contact_consent' => $contactConsent,
            'marketing_consent' => $marketingConsent,
            'consent_version' => (
                $contactConsent || $marketingConsent
            ) ? ($input['consent_version'] ?? '2026-09-v1') : null,
            'consent_at' => (
                $contactConsent || $marketingConsent
            ) ? now() : null,
            'ip_hash' => $this->hashIp($ip),
            'last_activity_at' => now(),
        ];

        $result = DB::transaction(function () use (
            $attributes,
            $phoneNormalized,
            $source,
            $scoreResult
        ): array {
            /*
             * Aynı telefon ve aynı kaynak 10 dakika içinde tekrar gönderilirse
             * ikinci CRM kartını açmak yerine mevcut kartı günceller.
             */
            $existing = Lead::query()
                ->where('phone_normalized', $phoneNormalized)
                ->where('source', $source)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($existing) {
                $existing->update([
                    'lead_score' => max(
                        (int) $existing->lead_score,
                        (int) $scoreResult['score']
                    ),
                    'sales_ready' => (
                        (bool) $existing->sales_ready
                        || (bool) $scoreResult['sales_ready']
                    ),
                    'lead_quality' => $this->strongerQuality(
                        (string) $existing->lead_quality,
                        (string) $scoreResult['quality']
                    ),
                    'last_activity_at' => now(),
                ]);

                return [
                    'lead' => $existing->fresh(),
                    'duplicate' => true,
                ];
            }

            return [
                'lead' => Lead::create($attributes),
                'duplicate' => false,
            ];
        });

        /*
         * E-posta DB transaction tamamlandıktan sonra gönderilir.
         * SMTP hatası müşterinin lead kaydını bozmaz.
         * Duplicate lead yeniden e-posta üretmez.
         */
        if (! $result['duplicate']) {
            try {
                $this->emailNotifier->notify(
                    $result['lead']
                );
            } catch (\Throwable $exception) {
                Log::error(
                    'New lead email notification failed',
                    [
                        'lead_id' =>
                            $result['lead']->id ?? null,
                        'error' =>
                            $exception->getMessage(),
                    ]
                );
            }
        }

        // Müşteriye "talebiniz alındı" SMS'i (bilgilendirme; hata lead kaydını bozmaz)
        if (! $result['duplicate']) {
            try {
                app(\App\Services\Sms\CustomerSms::class)->leadReceived($result['lead']);
            } catch (\Throwable $exception) {
                Log::warning('Lead SMS failed', ['lead_id' => $result['lead']->id ?? null, 'error' => $exception->getMessage()]);
            }
        }

        return $result;
    }

    private function priceSnapshot(
        array $input,
        array $projectData
    ): array {
        $trusted = $projectData['trusted_calculation'] ?? null;

        if (
            is_array($trusted)
            && ($trusted['calculation_type'] ?? null)
                === 'building_official_reference'
        ) {
            return [
                'trust_level' => 'server_verified',
                'calculation_id' => $trusted['calculation_id'] ?? null,
                'calculation_type' => $trusted['calculation_type'] ?? null,
                'building_type_key' => $trusted['building_type_key'] ?? null,
                'building_type_name' => $trusted['building_type_name'] ?? null,
                'category_key' => $trusted['category_key'] ?? null,
                'category_name' => $trusted['category_name'] ?? null,
                'class_code' => $trusted['class_code'] ?? null,
                'class_name' => $trusted['class_name'] ?? null,
                'area' => $this->nullableNumber(
                    $trusted['area'] ?? null
                ),
                'unit_price' => $this->nullableNumber(
                    $trusted['unit_price'] ?? null
                ),
                'total' => $this->nullableNumber(
                    $trusted['total'] ?? null
                ),
                'period' => $trusted['period'] ?? null,
                'source_name' => $trusted['source_name'] ?? null,
                'source_date' => $trusted['source_date'] ?? null,
                'vat_included' => (bool) (
                    $trusted['vat_included'] ?? false
                ),
                'price_version' => $trusted['price_version'] ?? null,
                'calculated_at' => $trusted['calculated_at'] ?? null,
                'captured_at' => now()->toIso8601String(),
            ];
        }

        /*
         * Tadilat ön bütçesi LeadController tarafından server snapshot
         * üzerinden doğrulanıp trusted_calculation olarak aktarılır.
         */
        if (
            is_array($trusted)
            && ($trusted['calculation_type'] ?? null)
                === 'renovation_market_estimate'
        ) {
            return [
                'trust_level' => 'server_verified',

                'calculation_id' =>
                    $trusted['calculation_id'] ?? null,

                'calculation_type' =>
                    $trusted['calculation_type'] ?? null,

                'scope' =>
                    $trusted['scope'] ?? null,

                'scope_label' =>
                    $trusted['scope_label'] ?? null,

                'quality' =>
                    $trusted['quality'] ?? null,

                'quality_label' =>
                    $trusted['quality_label'] ?? null,

                'area' => $this->nullableNumber(
                    $trusted['area'] ?? null
                ),

                'base_unit_price' => $this->nullableNumber(
                    $trusted['base_unit_price'] ?? null
                ),

                'unit_price' => $this->nullableNumber(
                    $trusted['unit_price'] ?? null
                ),

                'total' => $this->nullableNumber(
                    $trusted['total'] ?? null
                ),

                'estimate_low' => $this->nullableNumber(
                    $trusted['estimate_low'] ?? null
                ),

                'estimate_high' => $this->nullableNumber(
                    $trusted['estimate_high'] ?? null
                ),

                'vat_included' => (bool) (
                    $trusted['vat_included'] ?? false
                ),

                'price_version' =>
                    $trusted['price_version'] ?? null,

                'calculated_at' =>
                    $trusted['calculated_at'] ?? null,

                'captured_at' =>
                    now()->toIso8601String(),
            ];
        }
        /*
         * Seramik V4 Zero-Trust.
         * LeadController snapshot'ı doğruladıktan sonra bu alan
         * yalnız server tarafından oluşturulan trusted_calculation'dan gelir.
         */
        if (
            is_array($trusted)
            && ($trusted['calculation_type'] ?? null)
                === 'tile_market_estimate'
        ) {
            return [
                'trust_level' =>
                    'server_verified',

                'calculation_id' =>
                    $trusted['calculation_id']
                    ?? null,

                'calculation_type' =>
                    $trusted['calculation_type']
                    ?? null,

                'material' =>
                    $trusted['material']
                    ?? null,

                'mode' =>
                    $trusted['mode']
                    ?? null,

                'tile_size' =>
                    $trusted['tile_size']
                    ?? null,

                'area' =>
                    $this->nullableNumber(
                        $trusted['area']
                        ?? null
                    ),

                'waste_rate' =>
                    $this->nullableNumber(
                        $trusted['waste_rate']
                        ?? null
                    ),

                'grout_mm' =>
                    $this->nullableNumber(
                        $trusted['grout_mm']
                        ?? null
                    ),

                'theoretical_tiles' =>
                    $this->nullableNumber(
                        $trusted[
                            'theoretical_tiles'
                        ] ?? null
                    ),

                'layout_tiles' =>
                    $this->nullableNumber(
                        $trusted[
                            'layout_tiles'
                        ] ?? null
                    ),

                'base_tiles' =>
                    $this->nullableNumber(
                        $trusted[
                            'base_tiles'
                        ] ?? null
                    ),

                'recommended_tiles' =>
                    $this->nullableNumber(
                        $trusted[
                            'recommended_tiles'
                        ] ?? null
                    ),

                'gross_area' =>
                    $this->nullableNumber(
                        $trusted[
                            'gross_area'
                        ] ?? null
                    ),

                'market_key' =>
                    $trusted['market_key']
                    ?? null,

                'benchmark_status' =>
                    $trusted[
                        'benchmark_status'
                    ] ?? null,

                'benchmark_confidence' =>
                    $this->nullableNumber(
                        $trusted[
                            'benchmark_confidence'
                        ] ?? null
                    ),

                'unit' =>
                    $trusted['unit']
                    ?? null,

                'currency' =>
                    $trusted['currency']
                    ?? null,

                'vat_status' =>
                    $trusted['vat_status']
                    ?? null,

                'unit_price' =>
                    $this->nullableNumber(
                        $trusted['unit_price']
                        ?? null
                    ),

                'total' =>
                    $this->nullableNumber(
                        $trusted['total']
                        ?? null
                    ),

                'estimate_low' =>
                    $this->nullableNumber(
                        $trusted[
                            'estimate_low'
                        ] ?? null
                    ),

                'estimate_high' =>
                    $this->nullableNumber(
                        $trusted[
                            'estimate_high'
                        ] ?? null
                    ),

                'price_sources' =>
                    $trusted[
                        'price_sources'
                    ] ?? [],

                'price_checked_at' =>
                    $trusted[
                        'price_checked_at'
                    ] ?? null,

                'price_version' =>
                    $trusted[
                        'price_version'
                    ] ?? null,

                'calculated_at' =>
                    $trusted[
                        'calculated_at'
                    ] ?? null,

                'captured_at' =>
                    now()->toIso8601String(),
            ];
        }

        /*
         * Legacy hesaplar P0.5D/P0.5E tamamlanana kadar korunur,
         * ancak güven seviyesi açıkça işaretlenir.
         */
        return [
            'trust_level' => 'legacy_browser_input',
            'estimated_cost' => $this->nullableNumber(
                $input['estimated_cost'] ?? null
            ),
            'estimated_min' => $this->nullableNumber(
                $input['estimated_min'] ?? null
            ),
            'estimated_max' => $this->nullableNumber(
                $input['estimated_max'] ?? null
            ),
            'captured_at' => now()->toIso8601String(),
        ];
    }
    public function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '0090')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = '90' . substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            $digits = '90' . $digits;
        }

        if (!preg_match('/^90[2-5]\d{9}$/', $digits)) {
            return null;
        }

        return $digits;
    }

    private function projectData(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || trim((string) $value) === '') {
            return [];
        }

        $decoded = json_decode((string) $value, true);

        if (!is_array($decoded)) {
            throw ValidationException::withMessages([
                'project_data' => 'Proje bilgileri okunamadı.',
            ]);
        }

        return $decoded;
    }

    private function source(array $input): string
    {
        $source = $this->nullableString($input['source'] ?? null);

        if ($source !== null) {
            return Str::limit($source, 100, '');
        }

        $sourceTool = $this->nullableString(
            $input['source_tool']
            ?? $input['calculated_service']
            ?? null
        );

        if ($sourceTool !== null) {
            return Str::limit(
                'worksitem_tools_' . Str::slug($sourceTool),
                100,
                ''
            );
        }

        return 'worksitem_direct';
    }

    private function buildNotes(
        array $input,
        array $projectData,
        string $quality,
        ?string $city,
        ?string $district,
        ?string $timeline,
        float $area,
        bool $budgetConfirmed
    ): ?string {
        $lines = [];

        $customerNote = $this->nullableString($input['notes'] ?? null);

        if ($customerNote !== null) {
            $lines[] = 'MÜŞTERİ NOTU: ' . $customerNote;
        }

        $tool = $this->nullableString(
            $input['calculated_service']
            ?? $input['source_tool']
            ?? null
        );

        if ($tool !== null) {
            $lines[] = 'ARAÇ/KAYNAK: ' . $tool;
        }

        $lines[] = 'LEAD KALİTESİ: ' . mb_strtoupper($quality, 'UTF-8');

        if ($city !== null || $district !== null) {
            $lines[] = 'KONUM: ' . trim(
                ($city ?? '-') . ' / ' . ($district ?? '-')
            );
        }

        if ($area > 0) {
            $lines[] = 'ALAN: ' . $area . ' m²';
        }

        if ($timeline !== null) {
            $lines[] = 'BAŞLANGIÇ: ' . $timeline;
        }

        $lines[] = 'BÜTÇE ONAYI: ' . (
            $budgetConfirmed ? 'Evet' : 'Hayır'
        );

        if (!empty($projectData['scope'])) {
            $lines[] = 'KAPSAM: ' . $projectData['scope'];
        }

        if (!empty($projectData['quality'])) {
            $lines[] = 'KALİTE: ' . $projectData['quality'];
        }

        return implode(PHP_EOL, array_unique($lines));
    }

    private function strongerQuality(string $current, string $incoming): string
    {
        $rank = [
            'unqualified' => 0,
            'research' => 1,
            'nurture' => 2,
            'warm' => 3,
            'hot' => 4,
        ];

        return ($rank[$incoming] ?? 0) > ($rank[$current] ?? 0)
            ? $incoming
            : $current;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function nullableNumber(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function firstValue(array $values): ?string
    {
        foreach ($values as $value) {
            $value = $this->nullableString($value);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private function hashIp(?string $ip): ?string
    {
        if ($ip === null || $ip === '') {
            return null;
        }

        return hash_hmac(
            'sha256',
            $ip,
            (string) config('app.key')
        );
    }
}

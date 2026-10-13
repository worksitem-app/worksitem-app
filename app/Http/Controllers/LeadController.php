<?php

namespace App\Http\Controllers;

use App\Services\Leads\LeadCaptureService;
use App\Services\Leads\SalesLeadNotifier;
use App\Services\Pricing\CalculationSnapshotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function store(
        Request $request,
        LeadCaptureService $captureService,
        SalesLeadNotifier $salesLeadNotifier,
        CalculationSnapshotService $snapshotService
    ): RedirectResponse|JsonResponse {
        $request->merge([
            'name' => $request->input(
                'name',
                $request->input('full_name')
            ),
        ]);

        $key = 'lead-form:' . sha1((string) $request->ip());

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Çok fazla talep gönderdiniz.',
                    'retry_after' => $seconds,
                ], 429);
            }

            return back()
                ->withErrors([
                    'message' => 'Çok fazla talep gönderdiniz. Lütfen daha sonra tekrar deneyin.',
                ])
                ->withInput();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'min:10', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'source' => ['nullable', 'string', 'max:100'],
            'source_tool' => ['nullable', 'string', 'max:150'],
            'calculated_service' => ['nullable', 'string', 'max:150'],
            'calculated_area' => ['nullable', 'string', 'max:100'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:150'],
            'timeline' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'project_data' => ['nullable'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'estimated_min' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'estimated_max' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'budget_confirmed' => ['nullable', 'boolean'],
            'explicit_request' => ['nullable', 'boolean'],
            'has_file' => ['nullable', 'boolean'],
            'is_decision_maker' => ['nullable', 'boolean'],
            'contact_consent' => ['required', 'accepted'],
            'marketing_consent' => ['nullable', 'boolean'],
            'consent_version' => ['nullable', 'string', 'max:50'],
            'utm_source' => ['nullable', 'string', 'max:150'],
            'utm_medium' => ['nullable', 'string', 'max:150'],
            'utm_campaign' => ['nullable', 'string', 'max:150'],
            'utm_content' => ['nullable', 'string', 'max:150'],
            'utm_term' => ['nullable', 'string', 'max:150'],
            'landing_page' => ['nullable', 'string', 'max:2000'],
            'referrer' => ['nullable', 'string', 'max:2000'],
            'price_version' => ['nullable', 'string', 'max:100'],
            'calculation_id' => ['nullable', 'uuid'],
        ], [
            'name.required' => 'Lütfen adınızı ve soyadınızı yazın.',
            'phone.required' => 'Lütfen telefon numaranızı yazın.',
            'phone.min' => 'Telefon numaranızı 05XX XXX XX XX biçiminde, 11 haneli yazın.',
            'phone.max' => 'Telefon numaranızı 05XX XXX XX XX biçiminde, 11 haneli yazın.',
            'contact_consent.required' => 'Devam etmek için KVKK onay kutusunu işaretleyin.',
            'contact_consent.accepted' => 'Devam etmek için KVKK onay kutusunu işaretleyin.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi yazın.',
        ]);

        foreach (config('worksitem_lead_guard.fields', []) as $field) {
            if (\App\Support\LeadTextGuard::isOffensive($request->input($field))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $field => 'Lütfen formda uygun bir dil kullanın.',
                ]);
            }
        }

        // Sahte / anlamsız ad ve konum (lkjnhbv, fkfkdfgf, asdasd, test ...) e-postaya ve admin'e düşmesin
        if (! \App\Support\LeadTextGuard::isValidFullName($request->input('name'))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'name' => 'Lütfen gerçek adınızı ve soyadınızı yazın. Örnek: Ayşe Yılmaz',
            ]);
        }
        foreach (['location', 'city', 'district'] as $field) {
            if (filled($request->input($field)) && \App\Support\LeadTextGuard::looksFake($request->input($field))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $field => 'Lütfen geçerli bir şehir ve ilçe yazın. Örnek: İzmir, Karşıyaka',
                ]);
            }
        }

        /* SERAMIK_ZERO_TRUST_GUARD */
        if (
            ($validated['source_tool'] ?? null)
                === 'seramik-maliyet-hesaplayici'
            && empty($validated['calculation_id'])
            && (
                (float) ($validated['estimated_cost'] ?? 0) > 0
                || (float) ($validated['estimated_min'] ?? 0) > 0
                || (float) ($validated['estimated_max'] ?? 0) > 0
            )
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'calculation_id' =>
                    'Seramik fiyatı doğrulanamadı. Lütfen metrajı yeniden hesaplayın.',
            ]);
        }

        /*
         * Zero-Trust fiyat doğrulaması:
         *
         * calculation_id mevcutsa tarayıcıdan gelen fiyat, alan ve
         * price_version güvenilir kabul edilmez. Aynı session'a bağlı
         * server-side snapshot tek doğruluk kaynağıdır.
         *
         * calculation_id olmayan legacy akış P0.5D/P0.5E tamamlanana
         * kadar geriye uyumluluk amacıyla korunur.
         */
        if (!empty($validated['calculation_id'])) {
            $snapshot = $snapshotService->get(
                $request,
                (string) $validated['calculation_id']
            );

            /*
             * Tadilat hesapları için ayrı zero-trust sözleşmesi.
             * Browser fiyatı, alanı, kapsamı ve kaliteyi doğruluk
             * kaynağı olarak kullanılmaz.
             */
            if (
                is_array($snapshot)
                && ($snapshot['calculation_type'] ?? null)
                    === 'renovation_market_estimate'
                && isset($snapshot['estimate'])
                && is_array($snapshot['estimate'])
            ) {
                $estimate = $snapshot['estimate'];

                $pricing = $estimate['pricing'] ?? null;
                $marketEstimate = $estimate['market_estimate'] ?? null;

                if (
                    !is_array($pricing)
                    || !is_array($marketEstimate)
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'calculation_id' =>
                            'Tadilat hesaplama kaydı eksik veya geçersiz. Lütfen maliyeti yeniden hesaplayın.',
                    ]);
                }

                $trustedScope = trim(
                    (string) ($estimate['scope'] ?? '')
                );

                $trustedScopeLabel = trim(
                    (string) ($estimate['scope_label'] ?? '')
                );

                $trustedQuality = trim(
                    (string) ($estimate['quality'] ?? '')
                );

                $trustedQualityLabel = trim(
                    (string) ($estimate['quality_label'] ?? '')
                );

                $trustedArea = (float) (
                    $estimate['area'] ?? 0
                );

                $trustedBaseUnitPrice = (float) (
                    $pricing['base_unit_price'] ?? 0
                );

                $trustedAdjustedUnitPrice = (float) (
                    $pricing['adjusted_unit_price'] ?? 0
                );

                $trustedMarketTotal = (float) (
                    $marketEstimate['total'] ?? 0
                );

                $trustedMarketMin = (float) (
                    $marketEstimate['estimate_low'] ?? 0
                );

                $trustedMarketMax = (float) (
                    $marketEstimate['estimate_high'] ?? 0
                );

                $trustedPriceVersion = trim(
                    (string) ($estimate['price_version'] ?? '')
                );

                $trustedVatIncluded = (bool) (
                    $estimate['vat_included'] ?? false
                );

                if (
                    !in_array(
                        $trustedScope,
                        ['partial', 'medium', 'complete'],
                        true
                    )
                    || $trustedScopeLabel === ''
                    || $trustedQuality !== 'standard'
                    || $trustedQualityLabel === ''
                    || $trustedArea <= 0
                    || $trustedBaseUnitPrice <= 0
                    || $trustedAdjustedUnitPrice <= 0
                    || $trustedMarketTotal <= 0
                    || $trustedMarketMin <= 0
                    || $trustedMarketMax <= 0
                    || $trustedMarketMin > $trustedMarketTotal
                    || $trustedMarketMax < $trustedMarketTotal
                    || $trustedPriceVersion === ''
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'calculation_id' =>
                            'Tadilat hesaplama kaydı geçersiz. Lütfen maliyeti yeniden hesaplayın.',
                    ]);
                }

                /*
                 * V1 standard-only olduğundan ayarlanmış fiyat ile
                 * onaylı baz fiyat aynı olmalıdır.
                 */
                if (
                    abs(
                        $trustedAdjustedUnitPrice
                        - $trustedBaseUnitPrice
                    ) > 0.01
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'calculation_id' =>
                            'Tadilat fiyat modeli doğrulanamadı. Lütfen maliyeti yeniden hesaplayın.',
                    ]);
                }

                /*
                 * Browser-controlled değerleri server snapshot ile ez.
                 */
                $validated['area'] = $trustedArea;
                $validated['calculated_area'] =
                    (string) $trustedArea;

                $validated['estimated_cost'] =
                    $trustedMarketTotal;

                $validated['estimated_min'] =
                    $trustedMarketMin;

                $validated['estimated_max'] =
                    $trustedMarketMax;

                $validated['price_version'] =
                    $trustedPriceVersion;

                $projectData =
                    $validated['project_data'] ?? [];

                if (is_string($projectData)) {
                    $decodedProjectData =
                        json_decode($projectData, true);

                    $projectData =
                        is_array($decodedProjectData)
                            ? $decodedProjectData
                            : [];
                }

                if (!is_array($projectData)) {
                    $projectData = [];
                }

                /*
                 * Browser tarafından değiştirilebilen aynı alanları
                 * project_data içinde de server değerleriyle ez.
                 */
                $projectData['area'] = $trustedArea;
                $projectData['scope'] = $trustedScope;
                $projectData['quality'] = $trustedQuality;
                $projectData['calculation_mode'] =
                    'renovation_market_estimate';

                $projectData['trusted_calculation'] = [
                    'calculation_id' =>
                        (string) $validated['calculation_id'],

                    'calculation_type' =>
                        'renovation_market_estimate',

                    'scope' => $trustedScope,
                    'scope_label' => $trustedScopeLabel,

                    'quality' => $trustedQuality,
                    'quality_label' => $trustedQualityLabel,

                    'area' => $trustedArea,

                    'base_unit_price' =>
                        $trustedBaseUnitPrice,

                    'unit_price' =>
                        $trustedAdjustedUnitPrice,

                    'total' =>
                        $trustedMarketTotal,

                    'estimate_low' =>
                        $trustedMarketMin,

                    'estimate_high' =>
                        $trustedMarketMax,

                    'vat_included' =>
                        $trustedVatIncluded,

                    'price_version' =>
                        $trustedPriceVersion,

                    'calculated_at' =>
                        $snapshot['created_at'] ?? null,
                ];

                $validated['project_data'] =
                    $projectData;

            } elseif (
                is_array($snapshot)
                && ($snapshot['calculation_type'] ?? null)
                    === 'tile_market_estimate'
                && isset($snapshot['estimate'])
                && is_array($snapshot['estimate'])
            ) {
                /*
                 * Seramik V4 Zero-Trust
                 *
                 * Browser tarafından gönderilen alan, adet ve fiyatlar
                 * doğruluk kaynağı değildir.
                 * Tek doğruluk kaynağı session-bound server snapshot'tır.
                 */
                $estimate = $snapshot['estimate'];

                $requiredTileKeys = [
                    'material',
                    'mode',
                    'tile_size',
                    'area',
                    'waste_rate',
                    'grout_mm',
                    'theoretical_tiles',
                    'layout_tiles',
                    'base_tiles',
                    'recommended_tiles',
                    'gross_area',
                    'market_key',
                    'benchmark_status',
                    'benchmark_confidence',
                    'unit',
                    'currency',
                    'vat_status',
                    'unit_price',
                    'estimated_cost',
                    'estimated_min',
                    'estimated_max',
                    'price_version',
                ];

                foreach (
                    $requiredTileKeys
                    as $requiredKey
                ) {
                    if (
                        !array_key_exists(
                            $requiredKey,
                            $estimate
                        )
                    ) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'calculation_id' =>
                                'Seramik hesaplama kaydı eksik veya geçersiz. Lütfen metrajı yeniden hesaplayın.',
                        ]);
                    }
                }

                $trustedMaterial = trim(
                    (string) $estimate['material']
                );

                $trustedMode = trim(
                    (string) $estimate['mode']
                );

                $trustedTileSize = trim(
                    (string) $estimate['tile_size']
                );

                $trustedArea =
                    (float) $estimate['area'];

                $trustedWasteRate =
                    (float) $estimate['waste_rate'];

                $trustedGroutMm =
                    (float) $estimate['grout_mm'];

                $trustedTheoreticalTiles =
                    (int) $estimate['theoretical_tiles'];

                $trustedLayoutTiles =
                    (int) $estimate['layout_tiles'];

                $trustedBaseTiles =
                    (int) $estimate['base_tiles'];

                $trustedRecommendedTiles =
                    (int) $estimate['recommended_tiles'];

                $trustedGrossArea =
                    (float) $estimate['gross_area'];

                $trustedMarketKey = trim(
                    (string) $estimate['market_key']
                );

                $trustedBenchmarkStatus = trim(
                    (string) $estimate['benchmark_status']
                );

                $trustedBenchmarkConfidence =
                    (float) $estimate[
                        'benchmark_confidence'
                    ];

                $trustedUnit = trim(
                    (string) $estimate['unit']
                );

                $trustedCurrency = strtoupper(
                    trim(
                        (string) $estimate['currency']
                    )
                );

                $trustedVatStatus = trim(
                    (string) $estimate['vat_status']
                );

                $trustedUnitPrice =
                    (float) $estimate['unit_price'];

                $trustedCost =
                    (float) $estimate['estimated_cost'];

                $trustedMin =
                    (float) $estimate['estimated_min'];

                $trustedMax =
                    (float) $estimate['estimated_max'];

                $trustedPriceVersion = trim(
                    (string) $estimate['price_version']
                );

                if (
                    !in_array(
                        $trustedMaterial,
                        [
                            'ceramic',
                            'porcelain_matt',
                            'porcelain_glazed',
                            'porcelain_polished',
                        ],
                        true
                    )
                    || !in_array(
                        $trustedMode,
                        ['quick', 'detailed'],
                        true
                    )
                    || $trustedTileSize === ''
                    || $trustedArea <= 0
                    || $trustedWasteRate < 0
                    || $trustedWasteRate > 30
                    || $trustedGroutMm < 0
                    || $trustedGroutMm > 50
                    || $trustedTheoreticalTiles <= 0
                    || $trustedLayoutTiles <= 0
                    || $trustedBaseTiles <= 0
                    || $trustedRecommendedTiles <= 0
                    || $trustedGrossArea <= 0
                    || $trustedMarketKey === ''
                    || $trustedBenchmarkStatus !== 'ready'
                    || $trustedBenchmarkConfidence <= 0
                    || !in_array(
                        $trustedUnit,
                        ['TL/m²', 'TL/m2'],
                        true
                    )
                    || $trustedCurrency !== 'TRY'
                    || $trustedVatStatus !== 'included'
                    || $trustedUnitPrice <= 0
                    || $trustedCost <= 0
                    || $trustedMin <= 0
                    || $trustedMax <= 0
                    || $trustedMin > $trustedCost
                    || $trustedMax < $trustedCost
                    || $trustedPriceVersion === ''
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'calculation_id' =>
                            'Seramik fiyat doğrulaması başarısız. Lütfen metrajı yeniden hesaplayın.',
                    ]);
                }

                /*
                 * Browser-controlled lead değerlerini
                 * server snapshot ile ez.
                 */
                $validated['area'] =
                    $trustedArea;

                $validated['calculated_area'] =
                    (string) $trustedArea;

                $validated['estimated_cost'] =
                    $trustedCost;

                $validated['estimated_min'] =
                    $trustedMin;

                $validated['estimated_max'] =
                    $trustedMax;

                $validated['price_version'] =
                    $trustedPriceVersion;

                /*
                 * Browser project_data yalnız yardımcı bilgi olabilir.
                 * Fiyat/metraj sözleşmesindeki kritik alanlar server
                 * snapshot değerleriyle yeniden yazılır.
                 */
                $projectData =
                    $validated['project_data'] ?? [];

                if (is_string($projectData)) {
                    $decodedProjectData =
                        json_decode(
                            $projectData,
                            true
                        );

                    $projectData =
                        is_array($decodedProjectData)
                            ? $decodedProjectData
                            : [];
                }

                if (!is_array($projectData)) {
                    $projectData = [];
                }

                $projectData['area'] =
                    $trustedArea;

                $projectData['tile_size'] =
                    $trustedTileSize;

                $projectData['waste_rate'] =
                    $trustedWasteRate;

                $projectData['theoretical_tiles'] =
                    $trustedTheoreticalTiles;

                $projectData['layout_tiles'] =
                    $trustedLayoutTiles;

                $projectData['base_tiles'] =
                    $trustedBaseTiles;

                $projectData['recommended_tiles'] =
                    $trustedRecommendedTiles;

                $projectData['gross_area'] =
                    $trustedGrossArea;

                $projectData['tile_material_budget'] =
                    $trustedCost;

                $projectData['estimated_cost'] =
                    $trustedCost;

                $projectData['estimated_min'] =
                    $trustedMin;

                $projectData['estimated_max'] =
                    $trustedMax;

                $projectData['budget_scope'] =
                    'tile_only';

                $projectData['calculation_mode'] =
                    'tile_market_estimate';

                $projectData['result_version'] =
                    'v4-a3b';

                $projectData['trusted_calculation'] = [
                    'calculation_id' =>
                        (string)
                        $validated['calculation_id'],

                    'calculation_type' =>
                        'tile_market_estimate',

                    'material' =>
                        $trustedMaterial,

                    'mode' =>
                        $trustedMode,

                    'tile_size' =>
                        $trustedTileSize,

                    'area' =>
                        $trustedArea,

                    'waste_rate' =>
                        $trustedWasteRate,

                    'grout_mm' =>
                        $trustedGroutMm,

                    'theoretical_tiles' =>
                        $trustedTheoreticalTiles,

                    'layout_tiles' =>
                        $trustedLayoutTiles,

                    'base_tiles' =>
                        $trustedBaseTiles,

                    'recommended_tiles' =>
                        $trustedRecommendedTiles,

                    'gross_area' =>
                        $trustedGrossArea,

                    'market_key' =>
                        $trustedMarketKey,

                    'benchmark_status' =>
                        $trustedBenchmarkStatus,

                    'benchmark_confidence' =>
                        $trustedBenchmarkConfidence,

                    'unit' =>
                        $trustedUnit,

                    'currency' =>
                        $trustedCurrency,

                    'vat_status' =>
                        $trustedVatStatus,

                    'unit_price' =>
                        $trustedUnitPrice,

                    'total' =>
                        $trustedCost,

                    'estimate_low' =>
                        $trustedMin,

                    'estimate_high' =>
                        $trustedMax,

                    'price_sources' =>
                        $estimate[
                            'price_sources'
                        ] ?? [],

                    'price_checked_at' =>
                        $estimate[
                            'price_checked_at'
                        ] ?? null,

                    'price_version' =>
                        $trustedPriceVersion,

                    'calculated_at' =>
                        $snapshot[
                            'created_at'
                        ] ?? null,
                ];

                $validated['project_data'] =
                    $projectData;

            } else {
            if (
                !is_array($snapshot)
                || ($snapshot['calculation_type'] ?? null)
                    !== 'building_official_reference'
                || !isset($snapshot['estimate'])
                || !is_array($snapshot['estimate'])
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'calculation_id' => 'Hesaplama doğrulanamadı veya süresi doldu. Lütfen maliyeti yeniden hesaplayın.',
                ]);
            }

            $estimate = $snapshot['estimate'];

            $requiredEstimateKeys = [
                'building_type_key',
                'building_type_name',
                'category_key',
                'category_name',
                'class_code',
                'class_name',
                'area',
                'unit_price',
                'total',
                'period',
                'source_name',
                'source_date',
                'vat_included',
                'price_version',
            ];

            foreach ($requiredEstimateKeys as $requiredKey) {
                if (!array_key_exists($requiredKey, $estimate)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'calculation_id' => 'Hesaplama kaydı eksik veya geçersiz. Lütfen maliyeti yeniden hesaplayın.',
                    ]);
                }
            }

            $trustedArea = (float) $estimate['area'];
            $trustedUnitPrice = (float) $estimate['unit_price'];
            $trustedOfficialTotal = (float) $estimate['total'];

            $marketEstimate = $estimate['market_estimate'] ?? null;

            if (!is_array($marketEstimate)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'calculation_id' => 'Piyasa ön bütçesi doğrulanamadı. Lütfen maliyeti yeniden hesaplayın.',
                ]);
            }

            $trustedMarketTotal = (float) (
                $marketEstimate['total'] ?? 0
            );

            $trustedMarketMin = (float) (
                $marketEstimate['estimate_low'] ?? 0
            );

            $trustedMarketMax = (float) (
                $marketEstimate['estimate_high'] ?? 0
            );

            $trustedMarketVersion = trim(
                (string) ($marketEstimate['market_version'] ?? '')
            );

            if (
                $trustedArea <= 0
                || $trustedUnitPrice <= 0
                || $trustedOfficialTotal <= 0
                || $trustedMarketTotal <= 0
                || $trustedMarketMin <= 0
                || $trustedMarketMax <= 0
                || $trustedMarketMin > $trustedMarketTotal
                || $trustedMarketMax < $trustedMarketTotal
                || $trustedMarketVersion === ''
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'calculation_id' => 'Hesaplama kaydı geçersiz. Lütfen maliyeti yeniden hesaplayın.',
                ]);
            }

            /*
             * Browser-controlled parasal değerleri server snapshot ile ez.
             * CRM ve müşteri-facing fiyat Worksitem piyasa ön bütçesidir.
             * Resmî referans aşağıdaki trusted_calculation audit kaydında
             * ayrıca korunur.
             */
            $validated['calculated_area'] = (string) $trustedArea;
            $validated['estimated_cost'] = $trustedMarketTotal;
            $validated['estimated_min'] = $trustedMarketMin;
            $validated['estimated_max'] = $trustedMarketMax;
            $validated['price_version'] = $trustedMarketVersion;

            /*
             * project_data kullanıcı tarafından değiştirilebilir.
             * Önce mevcut veriyi güvenli şekilde diziye çevir, ardından
             * trusted_calculation alanını server snapshot'tan oluştur.
             */
            $projectData = $validated['project_data'] ?? [];

            if (is_string($projectData)) {
                $decodedProjectData = json_decode($projectData, true);
                $projectData = is_array($decodedProjectData)
                    ? $decodedProjectData
                    : [];
            }

            if (!is_array($projectData)) {
                $projectData = [];
            }

            /*
             * LeadCaptureService alanı project_data.area üzerinden de
             * okuyabildiği için browser-controlled area burada mutlaka
             * server snapshot ile ezilir.
             */
            $projectData['area'] = $trustedArea;

            $projectData['calculation_mode'] =
                'official_building_reference';

            $projectData['trusted_calculation'] = [
                'calculation_id' => (string) $validated['calculation_id'],
                'calculation_type' => (string) $snapshot['calculation_type'],
                'building_type_key' => (string) $estimate['building_type_key'],
                'building_type_name' => (string) $estimate['building_type_name'],
                'category_key' => (string) $estimate['category_key'],
                'category_name' => (string) $estimate['category_name'],
                'class_code' => (string) $estimate['class_code'],
                'class_name' => (string) $estimate['class_name'],
                'area' => $trustedArea,
                'unit_price' => $trustedUnitPrice,
                'total' => $trustedMarketTotal,
                'estimate_low' => $trustedMarketMin,
                'estimate_high' => $trustedMarketMax,
                'market_version' => $trustedMarketVersion,
                'official_reference' => [
                    'unit_price' => $trustedUnitPrice,
                    'total' => $trustedOfficialTotal,
                    'price_version' => (string) $estimate['price_version'],
                ],
                'period' => (string) $estimate['period'],
                'source_name' => (string) $estimate['source_name'],
                'source_date' => $estimate['source_date'],
                'vat_included' => (bool) $estimate['vat_included'],
                'price_version' => (string) $estimate['price_version'],
                'calculated_at' => $snapshot['created_at'] ?? null,
            ];

            $validated['project_data'] = $projectData;
            }
        }

        /*
         * lead_quality doğrulanmış veriye bilerek alınmaz.
         * Tarayıcıdan gelen kalite yerine sunucu puanlaması kullanılır.
         */
        $result = $captureService->capture(
            input: $validated,
            ip: $request->ip(),
            landingPage: $request->fullUrl(),
            referrer: $request->headers->get('referer')
        );

        RateLimiter::hit($key, 600);

        $lead = $result['lead'];

        if (!$result['duplicate'] && $lead->sales_ready) {
            try {
                $salesLeadNotifier->notify($lead);
            } catch (\Throwable $exception) {
                /*
                 * Bildirim hatası müşterinin form gönderimini engellemez.
                 */
                Log::error('Sales lead notification failed', [
                    'lead_id' => $lead->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        Log::info('Worksitem lead captured', [
            'lead_id' => $lead->id,
            'public_id' => $lead->public_id,
            'quality' => $lead->lead_quality,
            'score' => $lead->lead_score,
            'sales_ready' => $lead->sales_ready,
            'duplicate' => $result['duplicate'],
            'source' => $lead->source,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Talebiniz alınmıştır.',
                'public_id' => $lead->public_id,
                'quality' => $lead->lead_quality,
                'sales_ready' => $lead->sales_ready,
                'duplicate' => $result['duplicate'],
            ], $result['duplicate'] ? 200 : 201);
        }

        // Teşekkür sayfasındaki keşif teklifi bu talebe bağlanır (yalnızca bu oturum)
        $request->session()->put('kesif_lead', $lead->public_id);

        return redirect()
            ->route('lead.thanks')
            ->with('status', $lead->lead_quality)
            ->with('lead_public_id', $lead->public_id)
            ->with('google_ads_conversion', ! $result['duplicate']);
    }

    public function thanks(Request $request): View
    {
        return view('thanks', [
            'status' => session('status', 'qualified'),
        ]);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\MaterialPriceObservation;
use App\Services\Pricing\MaterialBenchmarkAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class ResearchPaintWebPrices extends Command
{
    protected $signature =
        'prices:research-paint-web
        {--dry-run : Araştır fakat DB fiyatlarını değiştirme}';

    protected $description =
        'Google Search grounding ile günlük boya ve boya sarf malzemesi piyasa araştırması';

    private array $catalog = [
        'paint_wall_standard_15l' => [
            'name' => 'İç Cephe Boyası 15 L',
            'category' => 'boya',
            'unit' => 'TL/15L',
            'min' => 500,
            'max' => 15000,
        ],

        'paint_ceiling_10l' => [
            'name' => 'Tavan Boyası 10 L',
            'category' => 'boya',
            'unit' => 'TL/10L',
            'min' => 100,
            'max' => 6000,
        ],

        'paint_primer_15l' => [
            'name' => 'İç Cephe Astarı 15 L',
            'category' => 'boya',
            'unit' => 'TL/15L',
            'min' => 200,
            'max' => 10000,
        ],

        'paint_cover_10m2' => [
            'name' => 'Koruma Örtüsü 10 m²',
            'category' => 'boya_sarf',
            'unit' => 'TL/10m2',
            'min' => 10,
            'max' => 1000,
        ],

        'paint_masking_tape_25m' => [
            'name' => 'Maskeleme Bandı 25 m',
            'category' => 'boya_sarf',
            'unit' => 'TL/rulo',
            'min' => 10,
            'max' => 1000,
        ],

        'paint_roller_25cm' => [
            'name' => 'İç Cephe Boya Rulosu 25 cm',
            'category' => 'boya_sarf',
            'unit' => 'TL/adet',
            'min' => 20,
            'max' => 2500,
        ],

        'paint_brush' => [
            'name' => 'Kestirme Fırça',
            'category' => 'boya_sarf',
            'unit' => 'TL/adet',
            'min' => 20,
            'max' => 2000,
        ],

        'paint_tray' => [
            'name' => 'Boya Tavası',
            'category' => 'boya_sarf',
            'unit' => 'TL/adet',
            'min' => 20,
            'max' => 2000,
        ],

        'paint_sandpaper_sheet' => [
            'name' => 'Zımpara Kağıdı',
            'category' => 'boya_sarf',
            'unit' => 'TL/adet',
            'min' => 5,
            'max' => 500,
        ],

        'paint_wall_putty_4_5kg' => [
            'name' => 'Duvar Macunu 4,5 kg',
            'category' => 'boya_sarf',
            'unit' => 'TL/4.5kg',
            'min' => 100,
            'max' => 5000,
        ],
    ];


    public function handle(
        MaterialBenchmarkAnalyzer $analyzer
    ): int {

        $apiKey =
            (string) config(
                'services.gemini.api_key'
            );

        if ($apiKey === '') {
            $this->error('GEMINI_API_KEY_YOK');
            return self::FAILURE;
        }

        $threshold =
            $this->reviewThreshold();

        $this->info(
            'REVIEW_THRESHOLD='.
            $threshold.
            '%'
        );

        try {

            $research =
                $this->research(
                    $apiKey
                );

        } catch (Throwable $e) {

            $this->error(
                'WEB_RESEARCH_FAIL='.
                $e->getMessage()
            );

            return self::FAILURE;
        }


        $observations = 0;
        $alerts = [];
        $report = [];


        foreach (
            $research['materials'] ?? []
            as $material
        ) {

            $key =
                (string)
                ($material['material_key'] ?? '');

            if (
                !isset(
                    $this->catalog[$key]
                )
            ) {
                continue;
            }

            $spec =
                $this->catalog[$key];

            foreach (
                $material['sources'] ?? []
                as $source
            ) {

                $price =
                    (float)
                    ($source['price'] ?? 0);

                if (
                    $price < $spec['min']
                    ||
                    $price > $spec['max']
                ) {

                    $alerts[] =
                        'SANITY_FAIL | '.
                        $key.
                        ' | '.
                        $price;

                    continue;
                }


                $sourceName =
                    trim(
                        (string)
                        ($source[
                            'source_name'
                        ] ?? '')
                    );

                $sourceUrl =
                    trim(
                        (string)
                        ($source[
                            'source_url'
                        ] ?? '')
                    );


                if (
                    $sourceName === ''
                    ||
                    !filter_var(
                        $sourceUrl,
                        FILTER_VALIDATE_URL
                    )
                ) {

                    $alerts[] =
                        'SOURCE_FAIL | '.
                        $key;

                    continue;
                }


                $report[] =
                    $key.
                    ' | '.
                    $sourceName.
                    ' | '.
                    number_format(
                        $price,
                        2,
                        ',',
                        '.'
                    ).
                    ' '.
                    $spec['unit'];


                $this->line(
                    end($report)
                );


                if ($this->option('dry-run')) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | GÖZLEM KAYDI
                |--------------------------------------------------------------------------
                */

                $hash =
                    hash(
                        'sha256',
                        implode('|', [
                            $key,
                            mb_strtolower(
                                $sourceName
                            ),
                            now(
                                'Europe/Istanbul'
                            )->toDateString(),
                            number_format(
                                $price,
                                4,
                                '.',
                                ''
                            ),
                            $sourceUrl,
                        ])
                    );


                MaterialPriceObservation::query()
                    ->updateOrCreate(
                        [
                            'observation_hash'
                                => $hash,
                        ],
                        [
                            'material_key'
                                => $key,

                            'product_name'
                                => $source[
                                    'product_name'
                                ]
                                ?? $spec['name'],

                            'brand'
                                => $source[
                                    'brand'
                                ]
                                ?? null,

                            'spec'
                                => $source[
                                    'spec'
                                ]
                                ?? null,

                            'price'
                                => $price,

                            'unit'
                                => $spec['unit'],

                            'region'
                                => 'Türkiye',

                            'currency'
                                => 'TRY',

                            'market_level'
                                => 'retail',

                            'source_name'
                                => $sourceName,

                            'source_url'
                                => $sourceUrl,

                            /*
                            | Kaynak sayfasında özel
                            | yayın tarihi yoksa uydurmuyoruz.
                            */
                            'source_date'
                                => null,

                            'observed_at'
                                => now(),

                            /*
                            | Tüketiciye açık yapı-market
                            | perakende satış fiyatı.
                            */
                            'vat_status'
                                => 'included',

                            'seller'
                                => $sourceName,

                            'confidence_score'
                                => min(
                                    95,
                                    max(
                                        75,
                                        (float)
                                        ($source[
                                            'confidence'
                                        ] ?? 80)
                                    )
                                ),

                            'verification_status'
                                => 'needs_review',

                            'metadata' => [
                                'research_method'
                                    => 'gemini_google_search',

                                'researched_at'
                                    => now(
                                        'Europe/Istanbul'
                                    )->toIso8601String(),

                                'package_match'
                                    => $source[
                                        'package_match'
                                    ]
                                    ?? null,

                                'price_condition'
                                    => $source[
                                        'price_condition'
                                    ]
                                    ?? null,
                            ],
                        ]
                    );

                $observations++;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DRY RUN BURADA BİTER
        |--------------------------------------------------------------------------
        */

        if ($this->option('dry-run')) {

            $this->newLine();

            $this->warn(
                'DRY_RUN=YES | DB_DEGISTIRILMEDI'
            );

            $this->info(
                'FOUND_ROWS='.
                count($report)
            );

            return self::SUCCESS;
        }


        /*
        |--------------------------------------------------------------------------
        | BENCHMARK → MERKEZİ FİYAT MOTORU
        |--------------------------------------------------------------------------
        */

        foreach (
            array_keys($this->catalog)
            as $key
        ) {

            $analysis =
                $analyzer->analyze(
                    $key,
                    7
                );


            if (
                ($analysis['status'] ?? null)
                !== 'ready'
            ) {

                $alerts[] =
                    'BENCHMARK_BEKLIYOR | '.
                    $key.
                    ' | '.
                    (
                        $analysis['status']
                        ?? 'unknown'
                    );

                continue;
            }


            $benchmark =
                $analysis['benchmark'];

            $newPrice =
                (float)
                $benchmark[
                    'benchmark_price'
                ];


            $current =
                DB::table('unit_prices')
                    ->where(
                        'key',
                        $key
                    )
                    ->first();


            /*
            |--------------------------------------------------------------------------
            | %10 MÜHENDİS KAPISI
            |--------------------------------------------------------------------------
            */

            if (
                $current
                &&
                (float)
                $current->price > 0
            ) {

                $oldPrice =
                    (float)
                    $current->price;

                $change =
                    (
                        (
                            $newPrice -
                            $oldPrice
                        )
                        /
                        $oldPrice
                    )
                    * 100;


                if (
                    abs($change)
                    > $threshold
                ) {

                    $alerts[] =
                        'KAINTA MUHENDIS KONTROLU | '.
                        $key.
                        ' | mevcut='.
                        number_format(
                            $oldPrice,
                            2,
                            ',',
                            '.'
                        ).
                        ' | yeni='.
                        number_format(
                            $newPrice,
                            2,
                            ',',
                            '.'
                        ).
                        ' | fark=%'.
                        number_format(
                            $change,
                            2,
                            ',',
                            '.'
                        );

                    /*
                    | Büyük sapmada mevcut
                    | fiyat korunur.
                    */
                    continue;
                }
            }


            $spec =
                $this->catalog[$key];


            $data = [
                'category'
                    => $spec['category'],

                'name'
                    => $spec['name'],

                'price'
                    => $newPrice,

                'unit'
                    => $spec['unit'],

                'updated_at'
                    => now(),
            ];


            if (
                Schema::hasColumn(
                    'unit_prices',
                    'price_pool'
                )
            ) {
                $data[
                    'price_pool'
                ] = 'market';
            }


            if (
                Schema::hasColumn(
                    'unit_prices',
                    'source_name'
                )
            ) {

                $data[
                    'source_name'
                ] =
                    'Google Search benchmark: '.
                    implode(
                        ' + ',
                        $benchmark[
                            'sources'
                        ] ?? []
                    );
            }


            if (
                Schema::hasColumn(
                    'unit_prices',
                    'source_date'
                )
            ) {
                $data[
                    'source_date'
                ] =
                    now(
                        'Europe/Istanbul'
                    )->toDateString();
            }


            if (
                Schema::hasColumn(
                    'unit_prices',
                    'vat_included'
                )
            ) {
                $data[
                    'vat_included'
                ] = true;
            }


            if (
                Schema::hasColumn(
                    'unit_prices',
                    'review_status'
                )
            ) {
                $data[
                    'review_status'
                ] = 'approved';
            }


            if (
                Schema::hasColumn(
                    'unit_prices',
                    'is_active'
                )
            ) {
                $data[
                    'is_active'
                ] = true;
            }


            if (
                Schema::hasColumn(
                    'unit_prices',
                    'last_checked_at'
                )
            ) {
                $data[
                    'last_checked_at'
                ] = now();
            }


            if (
                Schema::hasColumn(
                    'unit_prices',
                    'notes'
                )
            ) {

                $data[
                    'notes'
                ] =
                    'Günlük web araştırması; '.
                    'en az 2 uygun perakende '.
                    'kaynağı benchmarkı. '.
                    '>%'.
                    $threshold.
                    ' sapma mühendis onayına düşer.';
            }


            if (!$current) {
                $data[
                    'created_at'
                ] = now();
            }


            DB::table('unit_prices')
                ->updateOrInsert(
                    [
                        'key'
                            => $key,
                    ],
                    $data
                );


            $report[] =
                'MERKEZI_FIYAT_GUNCELLENDI | '.
                $key.
                ' | '.
                number_format(
                    $newPrice,
                    2,
                    ',',
                    '.'
                ).
                ' '.
                $spec['unit'];
        }


        Cache::forget(
            'global_system_prices'
        );


        /*
        |--------------------------------------------------------------------------
        | MÜHENDİS / CEO RAPORU
        |--------------------------------------------------------------------------
        */

        $this->sendReport(
            $report,
            $alerts
        );


        $this->newLine();

        $this->info(
            'OBSERVATIONS='.
            $observations
        );

        $this->info(
            'ALERTS='.
            count($alerts)
        );

        return self::SUCCESS;
    }


    private function research(
        string $apiKey
    ): array {

        $model =
            config('services.gemini.price_research_model', 'gemini-3.6-flash');


        $prompt = <<<'PROMPT'
Türkiye'deki boya ve yapı marketi fiyatlarını BUGÜN için araştır.

Google Search kullan.

Öncelik:
1. Bauhaus Türkiye
2. Koçtaş
3. büyük ve güvenilir yapı marketleri
4. üretici veya güvenilir perakendeci

Blog yazısından, eski fiyat rehberinden veya forumdan fiyat alma.

Her ürün için mümkünse EN AZ 2 farklı satıcı/kaynak bul.

Aynı material_key altında karşılaştırılan ürünlerin ambalajı ve ürün sınıfı benzer olmalı.

Araştırılacak kalemler:

paint_wall_standard_15l
- standart/silikonlu iç cephe boya
- 15 litre
- TL/15L

paint_ceiling_10l
- beyaz tavan boyası
- 10 litre
- TL/10L

paint_primer_15l
- iç cephe/geçiş astarı
- 15 litre
- TL/15L

paint_cover_10m2
- boya koruma örtüsü
- yaklaşık 10 m²
- TL/10m2

paint_masking_tape_25m
- maskeleme bandı
- yaklaşık 25 metre
- TL/rulo

paint_roller_25cm
- iç cephe boya rulosu
- 25 cm
- TL/adet

paint_brush
- kestirme boya fırçası
- TL/adet

paint_tray
- boya tavası
- TL/adet

paint_sandpaper_sheet
- boya öncesi zımpara kağıdı
- TL/adet

paint_wall_putty_4_5kg
- iç cephe duvar macunu
- yaklaşık 4-5 kg
- TL/4.5kg

Kurallar:
- fiyatı uydurma.
- ürün bulunamazsa sources boş dön.
- liste fiyatı, indirim veya sepette fiyat varsa price_condition alanında açıkla.
- farklı hacmi matematikle 15 L veya 10 L'ye çevirmeye çalışma.
- source_url gerçek arama sonucundaki ürün/kategori URL'si olsun.
- mümkün olduğunca bugünkü erişilebilir perakende fiyatını kullan.
- fiyat TRY olsun.
PROMPT;


        $schema = [
            'type' => 'object',

            'properties' => [
                'materials' => [
                    'type' => 'array',

                    'items' => [
                        'type' => 'object',

                        'properties' => [
                            'material_key' => [
                                'type' => 'string',
                            ],

                            'sources' => [
                                'type' => 'array',

                                'items' => [
                                    'type' => 'object',

                                    'properties' => [
                                        'source_name' => [
                                            'type' => 'string',
                                        ],

                                        'source_url' => [
                                            'type' => 'string',
                                        ],

                                        'product_name' => [
                                            'type' => 'string',
                                        ],

                                        'brand' => [
                                            'type' => [
                                                'string',
                                                'null',
                                            ],
                                        ],

                                        'spec' => [
                                            'type' => [
                                                'string',
                                                'null',
                                            ],
                                        ],

                                        'price' => [
                                            'type' => 'number',
                                        ],

                                        'package_match' => [
                                            'type' => 'string',
                                        ],

                                        'price_condition' => [
                                            'type' => [
                                                'string',
                                                'null',
                                            ],
                                        ],

                                        'confidence' => [
                                            'type' => 'number',
                                        ],
                                    ],

                                    'required' => [
                                        'source_name',
                                        'source_url',
                                        'product_name',
                                        'price',
                                        'package_match',
                                        'confidence',
                                    ],
                                ],
                            ],
                        ],

                        'required' => [
                            'material_key',
                            'sources',
                        ],
                    ],
                ],
            ],

            'required' => [
                'materials',
            ],
        ];


        $response =
            Http::timeout(180)
                ->retry(2, 1500)
                ->withHeaders([
                    'x-goog-api-key'
                        => $apiKey,

                    'Content-Type'
                        => 'application/json',
                ])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/interactions',
                    [
                        'model'
                            => $model,

                        'input'
                            => $prompt,

                        'tools' => [
                            [
                                'type'
                                    => 'google_search',
                            ],
                        ],

                        'response_format' => [
                            [
                                'type'
                                    => 'text',

                                'mime_type'
                                    => 'application/json',

                                'schema'
                                    => $schema,
                            ],
                        ],
                    ]
                );


        if (!$response->successful()) {

            throw new RuntimeException(
                'Gemini HTTP '.
                $response->status().
                ' | '.
                mb_substr(
                    $response->body(),
                    0,
                    800
                )
            );
        }


        $payload =
            $response->json();


        $searchUsed = false;
        $texts = [];


        foreach (
            $payload['steps'] ?? []
            as $step
        ) {

            if (
                ($step['type'] ?? null)
                === 'google_search_call'
            ) {
                $searchUsed = true;
            }


            if (
                ($step['type'] ?? null)
                !== 'model_output'
            ) {
                continue;
            }


            foreach (
                $step['content'] ?? []
                as $content
            ) {

                if (
                    ($content['type'] ?? null)
                    === 'text'
                    &&
                    isset(
                        $content['text']
                    )
                ) {
                    $texts[] =
                        $content['text'];
                }
            }
        }


        if (!$searchUsed) {

            throw new RuntimeException(
                'Google Search grounding çalışmadı.'
            );
        }


        $text =
            trim(
                implode(
                    '',
                    $texts
                )
            );


        if ($text === '') {

            throw new RuntimeException(
                'Gemini boş araştırma sonucu döndürdü.'
            );
        }


        $result =
            json_decode(
                $text,
                true
            );


        if (
            !is_array($result)
            ||
            !isset(
                $result['materials']
            )
        ) {

            throw new RuntimeException(
                'Gemini geçerli fiyat JSON çıktısı üretmedi.'
            );
        }


        return $result;
    }


    private function reviewThreshold(): float
    {
        $row =
            DB::table(
                'pricing_settings'
            )
            ->where(
                'key',
                'paint_price_review_threshold_pct'
            )
            ->first();


        if (
            $row
            &&
            is_numeric(
                $row->value
            )
        ) {
            return max(
                1,
                (float) $row->value
            );
        }


        return 10.0;
    }


    private function sendReport(
        array $report,
        array $alerts
    ): void {

        $email =
            config(
                'worksitem_prices.report_email',
                'info@worksitem.com'
            );


        $body =
            "WORKSITEM — GÜNLÜK BOYA PİYASA ARAŞTIRMASI\n".
            now('Europe/Istanbul')
                ->format('d.m.Y H:i').
            "\n\n".
            "===== FIYATLAR =====\n".
            (
                $report
                    ? implode(
                        "\n",
                        $report
                    )
                    : 'FIYAT_YOK'
            ).
            "\n\n".
            "===== MUHENDIS KONTROLU =====\n".
            (
                $alerts
                    ? implode(
                        "\n",
                        $alerts
                    )
                    : 'UYARI_YOK'
            );


        try {

            Mail::raw(
                $body,
                function ($message)
                use (
                    $email,
                    $alerts
                ) {

                    $message
                        ->to($email)
                        ->subject(
                            $alerts
                                ? '⚠️ WORKSITEM Boya Fiyat Kontrolü'
                                : 'WORKSITEM Günlük Boya Piyasa Fiyatları'
                        );
                }
            );

        } catch (Throwable $e) {

            $this->warn(
                'MAIL_FAIL='.
                $e->getMessage()
            );
        }
    }
}

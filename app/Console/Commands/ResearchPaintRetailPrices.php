<?php

namespace App\Console\Commands;

use App\Models\MaterialPriceObservation;
use App\Services\Pricing\MaterialBenchmarkAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ResearchPaintRetailPrices extends Command
{
    protected $signature =
        'prices:research-paint
        {--dry-run : DB ve fiyat motorunu değiştirme}';

    protected $description =
        'Boya ve yardımcı malzemelerin günlük yapı-market fiyatlarını araştırır';

    public function handle(
        MaterialBenchmarkAnalyzer $analyzer
    ): int {

        $dryRun = (bool) $this->option('dry-run');

        $threshold = 10.0;

        if (Schema::hasTable('pricing_settings')) {
            $row = DB::table('pricing_settings')
                ->where('key', 'paint_price_review_threshold_pct')
                ->first();

            if ($row && is_numeric($row->value)) {
                $threshold = (float) $row->value;
            }
        }

        $items = $this->sources();

        $report = [];
        $alerts = [];
        $keys = [];

        foreach ($items as $item) {

            $keys[$item['material_key']] = true;

            try {

                $price = $this->fetchPrice($item);

                $this->line(
                    $item['material_key'].
                    ' | '.$item['source_name'].
                    ' | '.$price.' '.$item['unit']
                );

                $report[] =
                    $item['material_key'].
                    ' | '.$item['source_name'].
                    ' | '.$price.' '.$item['unit'];

                /*
                |--------------------------------------------------------------------------
                | CURRENT PRICE GUARD
                |--------------------------------------------------------------------------
                */

                $current = DB::table('unit_prices')
                    ->where('key', $item['material_key'])
                    ->where('price_pool', 'market')
                    ->where('is_active', 1)
                    ->first();

                if (
                    $current &&
                    (float) $current->price > 0
                ) {

                    $change =
                        (($price - (float) $current->price)
                        / (float) $current->price)
                        * 100;

                    if (abs($change) > $threshold) {

                        $alerts[] =
                            'MÜHENDİS KONTROLÜ | '.
                            $item['material_key'].
                            ' | '.$item['source_name'].
                            ' | mevcut='.
                            number_format((float) $current->price, 2, ',', '.').
                            ' | yeni='.
                            number_format($price, 2, ',', '.').
                            ' | fark=%'.
                            number_format($change, 2, ',', '.');
                    }
                }

                if ($dryRun) {
                    continue;
                }

                $hash = hash(
                    'sha256',
                    implode('|', [
                        $item['material_key'],
                        $item['source_name'],
                        now('Europe/Istanbul')->toDateString(),
                        number_format($price, 4, '.', ''),
                        $item['url'],
                    ])
                );

                MaterialPriceObservation::query()
                    ->updateOrCreate(
                        [
                            'observation_hash' => $hash,
                        ],
                        [
                            'material_key' =>
                                $item['material_key'],

                            'product_name' =>
                                $item['product_name'],

                            'brand' =>
                                $item['brand'],

                            'spec' =>
                                $item['spec'],

                            'price' =>
                                $price,

                            'unit' =>
                                $item['unit'],

                            'region' =>
                                'Türkiye',

                            'currency' =>
                                'TRY',

                            'market_level' =>
                                $item['market_level'],

                            'source_name' =>
                                $item['source_name'],

                            'source_url' =>
                                $item['url'],

                            'source_date' =>
                                now('Europe/Istanbul')
                                    ->toDateString(),

                            'observed_at' =>
                                now(),

                            'vat_status' =>
                                'included',

                            'seller' =>
                                $item['seller'],

                            'confidence_score' =>
                                $item['confidence'],

                            'verification_status' =>
                                'needs_review',

                            'metadata' => [
                                'daily_research' => true,
                                'parser' => 'json_ld',
                            ],
                        ]
                    );

            } catch (Throwable $e) {

                $msg =
                    'KAYNAK HATASI | '.
                    $item['material_key'].
                    ' | '.$item['source_name'].
                    ' | '.$e->getMessage();

                $alerts[] = $msg;

                $this->error($msg);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | BENCHMARK + SAFE PROMOTION
        |--------------------------------------------------------------------------
        */

        if (! $dryRun) {

            foreach (array_keys($keys) as $key) {

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
                        'BENCHMARK BEKLİYOR | '.
                        $key.
                        ' | durum='.
                        ($analysis['status'] ?? 'unknown');

                    continue;
                }

                $benchmark =
                    $analysis['benchmark'];

                $newPrice =
                    (float)
                    $benchmark['benchmark_price'];

                $current =
                    DB::table('unit_prices')
                        ->where('key', $key)
                        ->first();

                /*
                |----------------------------------------------------------------------
                | Büyük değişimde otomatik güncelleme YOK.
                |----------------------------------------------------------------------
                */

                if (
                    $current &&
                    (float) $current->price > 0
                ) {

                    $change =
                        (($newPrice -
                            (float) $current->price)
                        / (float) $current->price)
                        * 100;

                    if (
                        abs($change)
                        > $threshold
                    ) {

                        $alerts[] =
                            'PROMOTION DURDU | '.
                            $key.
                            ' | benchmark='.
                            number_format($newPrice, 2, ',', '.').
                            ' | mevcut='.
                            number_format((float) $current->price, 2, ',', '.').
                            ' | fark=%'.
                            number_format($change, 2, ',', '.');

                        continue;
                    }
                }

                $data = [
                    'category' =>
                        str_contains($key, 'paint_')
                            ? 'boya'
                            : 'boya_sarf',

                    'name' =>
                        $key,

                    'price' =>
                        $newPrice,

                    'unit' =>
                        $benchmark['unit'],

                    'price_pool' =>
                        'market',

                    'source_name' =>
                        implode(
                            ' + ',
                            $benchmark['sources'] ?? []
                        ),

                    'source_date' =>
                        now('Europe/Istanbul')
                            ->toDateString(),

                    'vat_included' =>
                        true,

                    'review_status' =>
                        'approved',

                    'is_active' =>
                        true,

                    'notes' =>
                        'Günlük boya piyasa benchmarkı; '.
                        'otomatik güvenlik eşiği %'.
                        $threshold.'.',

                    'last_checked_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ];

                if (! $current) {
                    $data['created_at'] = now();
                }

                DB::table('unit_prices')
                    ->updateOrInsert(
                        ['key' => $key],
                        $data
                    );

                $report[] =
                    'MERKEZİ FİYAT GÜNCELLENDİ | '.
                    $key.
                    ' | '.$newPrice.
                    ' '.$benchmark['unit'];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DAILY ENGINEER REPORT
        |--------------------------------------------------------------------------
        */

        if (! $dryRun) {

            $body =
                "WORKSITEM — GÜNLÜK BOYA FİYAT RAPORU\n".
                now('Europe/Istanbul')
                    ->format('d.m.Y H:i').
                "\n\n".
                implode("\n", $report).
                "\n\n===== KONTROL / UYARI =====\n".
                (
                    count($alerts)
                        ? implode("\n", $alerts)
                        : 'ANORMAL FİYAT YOK'
                );

            try {

                Mail::raw(
                    $body,
                    function ($message) use ($alerts) {

                        $message
                            ->to('info@worksitem.com')
                            ->subject(
                                count($alerts)
                                    ? '⚠️ WORKSITEM Boya Fiyat Kontrolü'
                                    : 'WORKSITEM Günlük Boya Fiyatları'
                            );
                    }
                );

            } catch (Throwable $e) {

                $this->error(
                    'MAIL_FAIL='.
                    $e->getMessage()
                );
            }
        }


        $this->newLine();

        $this->info(
            'PRICE_ROWS='.count($report)
        );

        $this->info(
            'ALERTS='.count($alerts)
        );

        $this->info(
            'REVIEW_THRESHOLD='.$threshold.'%'
        );

        if ($dryRun) {
            $this->warn(
                'DRY_RUN=YES | DB_DEGISTIRILMEDI'
            );
        }

        return self::SUCCESS;
    }


    private function fetchPrice(
        array $item
    ): float {

        $response = Http::timeout(25)
            ->retry(2, 1000)
            ->withHeaders([
                'User-Agent' =>
                    'Mozilla/5.0 WORKSITEM-PriceResearch/1.0',

                'Accept-Language' =>
                    'tr-TR,tr;q=0.9',
            ])
            ->get($item['url']);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'HTTP '.$response->status()
            );
        }

        $prices =
            $this->jsonLdPrices(
                $response->body()
            );

        $prices = array_values(
            array_filter(
                $prices,
                fn ($price) =>
                    $price >= $item['min']
                    &&
                    $price <= $item['max']
            )
        );

        if (empty($prices)) {
            throw new \RuntimeException(
                'Güvenilir ürün fiyatı parse edilemedi'
            );
        }

        /*
        | Satış/indirim fiyatı varsa düşük olan
        | geçerli perakende fiyat kabul edilir.
        */
        return round(
            min($prices),
            2
        );
    }


    private function jsonLdPrices(
        string $html
    ): array {

        libxml_use_internal_errors(true);

        $dom =
            new \DOMDocument();

        $dom->loadHTML(
            '<?xml encoding="utf-8" ?>'.
            $html
        );

        libxml_clear_errors();

        $xpath =
            new \DOMXPath($dom);

        $prices = [];

        foreach (
            $xpath->query(
                '//script[@type="application/ld+json"]'
            ) as $script
        ) {

            $json =
                json_decode(
                    trim($script->textContent),
                    true
                );

            if (
                !is_array($json)
            ) {
                continue;
            }

            $this->collectPrices(
                $json,
                $prices
            );
        }

        return array_values(
            array_unique($prices)
        );
    }


    private function collectPrices(
        array $node,
        array &$prices
    ): void {

        foreach (
            ['price', 'lowPrice']
            as $field
        ) {

            if (
                isset($node[$field])
                &&
                is_numeric(
                    str_replace(
                        ',',
                        '.',
                        (string)
                        $node[$field]
                    )
                )
            ) {

                $prices[] =
                    (float)
                    str_replace(
                        ',',
                        '.',
                        (string)
                        $node[$field]
                    );
            }
        }

        foreach (
            $node as $value
        ) {

            if (is_array($value)) {

                $this->collectPrices(
                    $value,
                    $prices
                );
            }
        }
    }


    private function sources(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | DUVAR BOYASI 15 L
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_wall_standard_15l',
                'product_name'=>'İç Cephe Boyası 15 L',
                'brand'=>'Polisan',
                'spec'=>'15 L iç cephe',
                'unit'=>'TL/15L',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>90,
                'min'=>300,
                'max'=>20000,
                'url'=>'https://www.bauhaus.com.tr/polisan-15l-ambians-extra-ipek-mat-yeni-dingin-ci7637-ic-cephe-boyasi-61593279',
            ],

            [
                'material_key'=>'paint_wall_standard_15l',
                'product_name'=>'İç Cephe Boyası 15 L',
                'brand'=>'Dyo',
                'spec'=>'15 L iç cephe',
                'unit'=>'TL/15L',
                'market_level'=>'retail',
                'source_name'=>'Koçtaş',
                'seller'=>'Koçtaş',
                'confidence'=>90,
                'min'=>300,
                'max'=>20000,
                'url'=>'https://www.koctas.com.tr/dyoplast-silikonlu-mat-beyaz-ic-cephe-boyasi-15-litre/p/1000157677',
            ],


            /*
            |--------------------------------------------------------------------------
            | TAVAN 10 L
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_ceiling_10l',
                'product_name'=>'Tavan Boyası 10 L',
                'brand'=>'Polisan',
                'spec'=>'10 L beyaz tavan boyası',
                'unit'=>'TL/10L',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>90,
                'min'=>200,
                'max'=>10000,
                'url'=>'https://www.bauhaus.com.tr/polisan-10-lt-elegans-extra-tavan-boyasi-15007081',
            ],

            [
                'material_key'=>'paint_ceiling_10l',
                'product_name'=>'Tavan Boyası 10 L',
                'brand'=>'Polisan',
                'spec'=>'10 L beyaz tavan boyası',
                'unit'=>'TL/10L',
                'market_level'=>'retail',
                'source_name'=>'Koçtaş',
                'seller'=>'Koçtaş',
                'confidence'=>90,
                'min'=>200,
                'max'=>10000,
                'url'=>'https://www.koctas.com.tr/polisan-ultra-tavan-boyasi-10-litre/p/1000161573',
            ],


            /*
            |--------------------------------------------------------------------------
            | ASTAR 15 L
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_primer_15l',
                'product_name'=>'Geçiş Astarı 15 L',
                'brand'=>'Dyo',
                'spec'=>'15 L iç cephe geçiş astarı',
                'unit'=>'TL/15L',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>90,
                'min'=>200,
                'max'=>10000,
                'url'=>'https://www.bauhaus.com.tr/dyo-15-lt-dyo-gecis-astari-15030302',
            ],

            [
                'material_key'=>'paint_primer_15l',
                'product_name'=>'Geçiş Astarı 15 L',
                'brand'=>'Dyo',
                'spec'=>'15 L iç cephe geçiş astarı',
                'unit'=>'TL/15L',
                'market_level'=>'retail',
                'source_name'=>'Koçtaş',
                'seller'=>'Koçtaş',
                'confidence'=>90,
                'min'=>200,
                'max'=>10000,
                'url'=>'https://www.koctas.com.tr/dyo-gecis-astari/p/1000089101',
            ],


            /*
            |--------------------------------------------------------------------------
            | ÖRTÜ 10 m²
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_cover_10m2',
                'product_name'=>'Koruma Örtüsü 10 m²',
                'brand'=>'Permolit',
                'spec'=>'4 x 2,5 m = 10 m²',
                'unit'=>'TL/10m2',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>90,
                'min'=>10,
                'max'=>1000,
                'url'=>'https://www.bauhaus.com.tr/permolit-koruyucu-ortu-4-x-25-10-m-61204580',
            ],


            /*
            |--------------------------------------------------------------------------
            | MASKELEME BANDI 48 mm x 25 m
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_masking_tape_25m',
                'product_name'=>'Maskeleme Bandı 48 mm x 25 m',
                'brand'=>'Vodabond',
                'spec'=>'48 mm x 25 m',
                'unit'=>'TL/rulo',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>90,
                'min'=>10,
                'max'=>1000,
                'url'=>'https://www.bauhaus.com.tr/vodabond-maskeleme-bandi-48mm-x-25mt-61560945',
            ],

            [
                'material_key'=>'paint_masking_tape_25m',
                'product_name'=>'Maskeleme Bandı 48 mm x 25 m',
                'brand'=>'Vodaseal',
                'spec'=>'48 mm x 25 m',
                'unit'=>'TL/rulo',
                'market_level'=>'retail',
                'source_name'=>'Koçtaş',
                'seller'=>'Koçtaş',
                'confidence'=>90,
                'min'=>10,
                'max'=>1000,
                'url'=>'https://www.koctas.com.tr/vodaseal-maskeleme-bandi-48mmx25m/p/1000541693',
            ],


            /*
            |--------------------------------------------------------------------------
            | RULO 25 cm
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_roller_25cm',
                'product_name'=>'İç Cephe Boya Rulosu 25 cm',
                'brand'=>'Rulon',
                'spec'=>'25 cm iç cephe',
                'unit'=>'TL/adet',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>90,
                'min'=>20,
                'max'=>2000,
                'url'=>'https://www.bauhaus.com.tr/rulon-kisa-ipli-boya-rulosu-25-cm-61257609',
            ],

            [
                'material_key'=>'paint_roller_25cm',
                'product_name'=>'İç Cephe Boya Rulosu 25 cm',
                'brand'=>'Rulon',
                'spec'=>'25 cm iç cephe',
                'unit'=>'TL/adet',
                'market_level'=>'retail',
                'source_name'=>'Koçtaş',
                'seller'=>'Koçtaş',
                'confidence'=>90,
                'min'=>20,
                'max'=>2000,
                'url'=>'https://www.koctas.com.tr/rulon-ic-cephe-boya-rulosu-25-cm/p/1000778264',
            ],


            /*
            |--------------------------------------------------------------------------
            | FIRÇA
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_brush',
                'product_name'=>'Kestirme Fırça',
                'brand'=>'Kaptan',
                'spec'=>'No:5',
                'unit'=>'TL/adet',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>85,
                'min'=>20,
                'max'=>2000,
                'url'=>'https://www.bauhaus.com.tr/kaptan-no-5-kestirme-firca-61495807',
            ],


            /*
            |--------------------------------------------------------------------------
            | BOYA TAVASI
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_tray',
                'product_name'=>'Profesyonel Boya Tavası',
                'brand'=>'Dizayn',
                'spec'=>'30 x 43 cm',
                'unit'=>'TL/adet',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>85,
                'min'=>20,
                'max'=>2000,
                'url'=>'https://www.bauhaus.com.tr/dizayn-profesyonel-boya-tavasi-30x43x75-cm-61237504',
            ],


            /*
            |--------------------------------------------------------------------------
            | ZIMPARA
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_sandpaper_sheet',
                'product_name'=>'Zımpara Kağıdı 320',
                'brand'=>'Bauhaus',
                'spec'=>'230 x 280 mm; Kum 320',
                'unit'=>'TL/adet',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>85,
                'min'=>5,
                'max'=>1000,
                'url'=>'https://www.bauhaus.com.tr/bauhaus-zimpara-kagidi-kum-320-230x280-mm-21376173',
            ],

            [
                'material_key'=>'paint_sandpaper_sheet',
                'product_name'=>'Zımpara Kağıdı 320',
                'brand'=>'Atlas',
                'spec'=>'230 x 280 mm; Kum 320',
                'unit'=>'TL/adet',
                'market_level'=>'retail',
                'source_name'=>'Koçtaş',
                'seller'=>'Koçtaş',
                'confidence'=>85,
                'min'=>5,
                'max'=>1000,
                'url'=>'https://www.koctas.com.tr/atlas-su-zimparasi-320-kum-230x280-mm/p/1000017453',
            ],


            /*
            |--------------------------------------------------------------------------
            | MACUN
            |--------------------------------------------------------------------------
            */

            [
                'material_key'=>'paint_wall_putty_4_5kg',
                'product_name'=>'İç Cephe Duvar Macunu 4,5 kg',
                'brand'=>'Dyo',
                'spec'=>'4,5 kg / lt sınıfı iç cephe macunu',
                'unit'=>'TL/4.5kg',
                'market_level'=>'retail',
                'source_name'=>'Bauhaus',
                'seller'=>'Bauhaus',
                'confidence'=>85,
                'min'=>100,
                'max'=>5000,
                'url'=>'https://www.bauhaus.com.tr/dyo-45-lt-dyorit-ince-plastik-macun-15080005',
            ],
        ];
    }
}

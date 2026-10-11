<?php

namespace App\Console\Commands;

use App\Models\MaterialPriceObservation;
use App\Services\Pricing\MaterialBenchmarkAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class ResearchTileWebPrices extends Command
{
    protected $signature =
        'prices:research-tile-web
        {--dry-run : Araştır fakat DB ve merkezi fiyatları değiştirme}';

    protected $description =
        'Google Search grounding ile seramik, fayans ve porselen piyasa fiyatlarını araştırır';

    private array $catalog = [
                'tile_ceramic_floor_60x60_m2' => [
            'name' => '60x60 Seramik Yer Karosu',
            'category' => 'seramik',
            'unit' => 'TL/m²',
            'min' => 150,
            'max' => 10000,
        ],
'tile_ceramic_wall_30x60_m2' => [
            'name' => '30x60 Seramik Duvar Karosu',
            'category' => 'seramik',
            'unit' => 'TL/m²',
            'min' => 150,
            'max' => 10000,
        ],
        'tile_ceramic_floor_45x45_m2' => [
            'name' => '45x45 Seramik Yer Karosu',
            'category' => 'seramik',
            'unit' => 'TL/m²',
            'min' => 150,
            'max' => 10000,
        ],
        'tile_porcelain_glazed_30x60_m2' => [
            'name' => '30x60 Sırlı Porselen Karo',
            'category' => 'porselen',
            'unit' => 'TL/m²',
            'min' => 200,
            'max' => 15000,
        ],
        'tile_porcelain_glazed_60x60_m2' => [
            'name' => '60x60 Sırlı Porselen Karo',
            'category' => 'porselen',
            'unit' => 'TL/m²',
            'min' => 200,
            'max' => 15000,
        ],
        'tile_porcelain_glazed_60x120_m2' => [
            'name' => '60x120 Sırlı Porselen Karo',
            'category' => 'porselen',
            'unit' => 'TL/m²',
            'min' => 250,
            'max' => 20000,
        ],
        'tile_porcelain_matt_60x60_m2' => [
            'name' => '60x60 Mat Porselen Karo',
            'category' => 'porselen',
            'unit' => 'TL/m²',
            'min' => 200,
            'max' => 15000,
        ],
        'tile_porcelain_matt_60x120_m2' => [
            'name' => '60x120 Mat Porselen Karo',
            'category' => 'porselen',
            'unit' => 'TL/m²',
            'min' => 250,
            'max' => 20000,
        ],
        'tile_porcelain_polished_60x60_m2' => [
            'name' => '60x60 Parlak Porselen Karo',
            'category' => 'porselen',
            'unit' => 'TL/m²',
            'min' => 200,
            'max' => 18000,
        ],
        'tile_porcelain_polished_60x120_m2' => [
            'name' => '60x120 Parlak Porselen Karo',
            'category' => 'porselen',
            'unit' => 'TL/m²',
            'min' => 250,
            'max' => 25000,
        ],
    ];

    public function handle(
        MaterialBenchmarkAnalyzer $analyzer
    ): int {
        $dryRun = (bool) $this->option('dry-run');
        $threshold = $this->reviewThreshold();

        $this->info('REVIEW_THRESHOLD='.$threshold.'%');

        $research = [
            'materials' => [],
            'alerts' => [],
        ];

        /*
         * 1. Doğrudan doğrulanabilen mağaza/üretici sayfaları
         */
        try {
            $direct = $this->researchDirect();

            $research = $this->mergeResearchResults(
                $research,
                $direct,
                'direct_verified'
            );

            $this->info(
                'DIRECT_RESEARCH='.
                count($direct['materials'] ?? []).
                ' MATERIAL'
            );

        } catch (Throwable $e) {
            $research['alerts'][] =
                'DIRECT_RESEARCH_FAIL | '.$e->getMessage();

            $this->warn(
                'DIRECT_RESEARCH_FAIL='.$e->getMessage()
            );
        }

        /*
         * Yerevdekor dinamik ürün araştırması
         */
        try {
            $yerevdekor=$this->researchYerevdekor();

            $research=$this->mergeResearchResults(
                $research,
                $yerevdekor,
                'yerevdekor_category_median'
            );

            $this->info(
                'YEREVDEKOR_RESEARCH='.
                count($yerevdekor['materials'] ?? []).
                ' MATERIAL'
            );

        } catch (Throwable $e) {
            $research['alerts'][]=
                'YEREVDEKOR_FAIL | '.$e->getMessage();

            $this->warn(
                'YEREVDEKOR_FAIL='.$e->getMessage()
            );
        }

        /*
         * 2. Gemini + Google Search geniş piyasa araştırması
         */
        $apiKey = (string) config('services.gemini.api_key');

        if ($apiKey !== '') {
            try {
                $google = $this->research($apiKey);

                $research = $this->mergeResearchResults(
                    $research,
                    $google,
                    'gemini_google_search'
                );

                $this->info(
                    'GOOGLE_RESEARCH='.
                    count($google['materials'] ?? []).
                    ' MATERIAL'
                );

            } catch (Throwable $e) {
                /*
                 * Google araştırması çökerse direct veri çalışmaya devam eder.
                 * Fiyat motoru tamamen durmaz.
                 */
                $research['alerts'][] =
                    'GOOGLE_RESEARCH_FAIL | '.$e->getMessage();

                $this->warn(
                    'GOOGLE_RESEARCH_FAIL='.$e->getMessage()
                );
            }
        } else {
            $research['alerts'][] =
                'GOOGLE_RESEARCH_SKIP | GEMINI_API_KEY_YOK';
        }

        if (empty($research['materials'])) {
            $this->error(
                'WEB_RESEARCH_FAIL=Hiçbir geçerli fiyat kaynağı bulunamadı.'
            );

            if (!$dryRun) {
                $this->sendReport(
                    [],
                    $research['alerts'] ?? []
                );
            }

            return self::FAILURE;
        }

        $report = [];
        $alerts = $research['alerts'] ?? [];
        $observations = 0;

        foreach ($research['materials'] ?? [] as $material) {
            $key = (string) ($material['material_key'] ?? '');

            if (!isset($this->catalog[$key])) {
                continue;
            }

            $spec = $this->catalog[$key];

            foreach ($material['sources'] ?? [] as $source) {
                $price = (float) ($source['price_per_m2'] ?? 0);

                if ($price < $spec['min'] || $price > $spec['max']) {
                    $alerts[] =
                        'SANITY_FAIL | '.$key.' | fiyat='.$price;
                    continue;
                }

                $sourceName = trim(
                    (string) ($source['source_name'] ?? '')
                );

                $sourceUrl = trim(
                    (string) ($source['source_url'] ?? '')
                );

                if (
                    $sourceName === '' ||
                    !filter_var($sourceUrl, FILTER_VALIDATE_URL)
                ) {
                    $alerts[] = 'SOURCE_FAIL | '.$key;
                    continue;
                }

                $report[] =
                    $key.
                    ' | '.$sourceName.
                    ' | '.number_format($price, 2, ',', '.').
                    ' TL/m²';

                $this->line(end($report));

                if ($dryRun) {
                    continue;
                }

                $hash = hash(
                    'sha256',
                    implode('|', [
                        $key,
                        mb_strtolower($sourceName),
                        now('Europe/Istanbul')->toDateString(),
                        number_format($price, 4, '.', ''),
                        $sourceUrl,
                    ])
                );

                MaterialPriceObservation::query()
                    ->updateOrCreate(
                        [
                            'observation_hash' => $hash,
                        ],
                        [
                            'material_key' => $key,
                            'product_name' =>
                                $source['product_name']
                                ?? $spec['name'],
                            'brand' =>
                                $source['brand']
                                ?? null,
                            'spec' =>
                                $source['spec']
                                ?? $spec['name'],
                            'price' => $price,
                            'unit' => $spec['unit'],
                            'region' => 'Türkiye',
                            'currency' => 'TRY',
                            'market_level' => 'retail',
                            'source_name' => $sourceName,
                            'source_url' => $sourceUrl,
                            'source_date' => null,
                            'observed_at' => now(),
                            'vat_status' =>
                                ($source['vat_included'] ?? null) === true
                                    ? 'included'
                                    : 'unknown',
                            'seller' => $sourceName,
                            'confidence_score' =>
                                min(
                                    95,
                                    max(
                                        70,
                                        (float) ($source['confidence'] ?? 80)
                                    )
                                ),
                            'verification_status' => 'needs_review',
                            'metadata' => [
                                'research_method' =>
                                    $source['research_method']
                                    ?? 'unknown',
                                'researched_at' =>
                                    now('Europe/Istanbul')
                                        ->toIso8601String(),
                                'package_text' =>
                                    $source['package_text'] ?? null,
                                'normalization_note' =>
                                    $source['normalization_note'] ?? null,
                            ],
                        ]
                    );

                $observations++;
            }
        }

        if ($dryRun) {
            $this->warn('DRY_RUN=YES | DB_DEGISTIRILMEDI');
            $this->info('FOUND_ROWS='.count($report));
            return self::SUCCESS;
        }

        $researchedKeys = collect($research['materials'] ?? [])
            ->pluck('material_key')
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach ($researchedKeys as $key) {
            $analysis = $analyzer->analyze($key, 7);

            if (($analysis['status'] ?? null) !== 'ready') {
                $alerts[] =
                    'BENCHMARK_BEKLIYOR | '.
                    $key.
                    ' | durum='.
                    ($analysis['status'] ?? 'unknown');

                continue;
            }

            $benchmark = $analysis['benchmark'];
            $newPrice = (float) $benchmark['benchmark_price'];

            $current = DB::table('unit_prices')
                ->where('key', $key)
                ->first();

            if ($current && (float) $current->price > 0) {
                $oldPrice = (float) $current->price;

                $change =
                    (($newPrice - $oldPrice) / $oldPrice) * 100;

                if (abs($change) > $threshold) {
                    $alerts[] =
                        'KAINTA MUHENDIS KONTROLU | '.
                        $key.
                        ' | mevcut='.
                        number_format($oldPrice, 2, ',', '.').
                        ' | yeni='.
                        number_format($newPrice, 2, ',', '.').
                        ' | fark=%'.
                        number_format($change, 2, ',', '.');

                    continue;
                }
            }

            $data = [
                'category' => $this->catalog[$key]['category'],
                'name' => $this->catalog[$key]['name'],
                'price' => $newPrice,
                'unit' => $this->catalog[$key]['unit'],
                'price_pool' => 'market',
                'source_name' =>
                    'Google Search benchmark: '.
                    implode(
                        ' + ',
                        $benchmark['sources'] ?? []
                    ),
                'source_date' =>
                    now('Europe/Istanbul')->toDateString(),
                'vat_included' => true,
                'review_status' => 'approved',
                'is_active' => true,
                'notes' =>
                    'Günlük seramik/porselen web araştırması. '.
                    '>%'.$threshold.
                    ' fiyat sapması mühendis onayına düşer.',
                'updated_at' => now(),
            ];

            if (!$current) {
                $data['created_at'] = now();
            }

            DB::table('unit_prices')
                ->updateOrInsert(
                    ['key' => $key],
                    $data
                );

            $report[] =
                'MERKEZI_FIYAT_GUNCELLENDI | '.
                $key.
                ' | '.
                number_format($newPrice, 2, ',', '.').
                ' TL/m²';
        }

        Cache::forget('global_system_prices');

        $this->sendReport($report, $alerts);

        $this->newLine();
        $this->info('OBSERVATIONS='.$observations);
        $this->info('ALERTS='.count($alerts));

        return self::SUCCESS;
    }

    private function researchDirect(): array
    {
        $sources = [
                        [
                'material_key'=>'tile_ceramic_floor_60x60_m2',
                'source_name'=>'Altuğ İç Mimarlık',
                'product_name'=>'Çanakkale Seramik Side 60x60',
                'brand'=>'Çanakkale Seramik',
                'spec'=>'60x60 DSE yer karosu',
                'mode'=>'altug',
                'confidence'=>92,
                'url'=>'https://altugicmimarlik.com/urunler/katalog-310100906232-12952',
            ],
            [
                'material_key'=>'tile_ceramic_floor_60x60_m2',
                'source_name'=>'YapıMarka',
                'product_name'=>'Kalebodur Salvia Mat Gri 60x60',
                'brand'=>'Kalebodur',
                'spec'=>'60x60 mat yer karosu',
                'mode'=>'yapimarka',
                'confidence'=>94,
                'url'=>'https://www.yapimarka.com/kalebodur-gs-d8067-salvia-mat-gri--60x60',
            ],
[
                'material_key'=>'tile_ceramic_wall_30x60_m2',
                'source_name'=>'Evdema',
                'product_name'=>'VitrA 30x60 Nebula Gri Parlak',
                'brand'=>'VitrA',
                'spec'=>'30x60 duvar karosu',
                'mode'=>'evdema',
                'confidence'=>90,
                'url'=>'https://online.evdema.com/vitra-30x60-karo-nebula-gri-parlak-k94853900001vte0-108m2',
            ],
            [
                'material_key'=>'tile_ceramic_wall_30x60_m2',
                'source_name'=>'VitrA',
                'product_name'=>'VitrA 30x60 Düz Renkler',
                'brand'=>'VitrA',
                'spec'=>'30x60 duvar karosu',
                'mode'=>'vitra',
                'confidence'=>95,
                'url'=>'https://www.vitra.com.tr/karo-seramik-urunleri/30x60-duz-renkler-fon-beyaz-p-k823053b0001vtet',
            ],
            [
                'material_key'=>'tile_porcelain_matt_60x60_m2',
                'source_name'=>'Evdema',
                'product_name'=>'VitrA 60x60 Urbancrete Vizon Mat',
                'brand'=>'VitrA',
                'spec'=>'60x60 mat porselen',
                'mode'=>'evdema',
                'confidence'=>90,
                'url'=>'https://online.evdema.com/60x60-urbancrete-fon-vizon-mat-porselen-karo-144-m2',
            ],
            [
                'material_key'=>'tile_porcelain_matt_60x60_m2',
                'source_name'=>'VitrA',
                'product_name'=>'VitrA 60x60 Urbancrete Vizon Mat',
                'brand'=>'VitrA',
                'spec'=>'60x60 mat porselen',
                'mode'=>'vitra',
                'confidence'=>95,
                'url'=>'https://www.vitra.com.tr/karo-seramik-urunleri/60x60-urbancrete-fon-vizon-r10a-p-k947187r0001vte0',
            ],
            [
                'material_key'=>'tile_porcelain_matt_60x120_m2',
                'source_name'=>'Evdema',
                'product_name'=>'VitrA 60x120 Cementside-V2 Grej',
                'brand'=>'VitrA',
                'spec'=>'60x120 mat porselen',
                'mode'=>'evdema',
                'confidence'=>90,
                'url'=>'https://online.evdema.com/vitra-60x120-cementside-v2-grej-r10b-7r-k948058r0001vtsp-144m2',
            ],
            [
                'material_key'=>'tile_porcelain_matt_60x120_m2',
                'source_name'=>'VitrA',
                'product_name'=>'VitrA 60x120 Cementside',
                'brand'=>'VitrA',
                'spec'=>'60x120 mat porselen',
                'mode'=>'vitra',
                'confidence'=>95,
                'url'=>'https://www.vitra.com.tr/karo-seramik-urunleri/60x120-cementside-fon-gri-r10b-p-k952796r0001vtsp',
            ],
        ];

        $materials=[];
        $alerts=[];

        foreach($sources as $item){
            try{
                $data=$this->fetchDirectPrice($item);

                $materials[$item['material_key']]['material_key']
                    =$item['material_key'];

                $materials[$item['material_key']]['sources'][]=[
                    'source_name'=>$item['source_name'],
                    'source_url'=>$item['url'],
                    'product_name'=>$item['product_name'],
                    'brand'=>$item['brand'],
                    'spec'=>$item['spec'],
                    'price_per_m2'=>$data['price'],
                    'package_text'=>$data['package_text'],
                    'normalization_note'=>$data['note'],
                    'vat_included'=>true,
                    'confidence'=>$item['confidence'],
                    'research_method'=>'direct_verified',
                ];
            }catch(\Throwable $e){
                $alerts[]=
                    'KAYNAK HATASI | '.
                    $item['material_key'].' | '.
                    $item['source_name'].' | '.
                    $e->getMessage();
            }
        }

        if(empty($materials)){
            throw new RuntimeException(
                'Güvenilir seramik fiyatı alınamadı. | '.
                implode(' || ',$alerts)
            );
        }

        return [
            'materials'=>array_values($materials),
            'alerts'=>$alerts,
        ];
    }
    private function mergeResearchResults(
        array $base,
        array $incoming,
        string $method
    ): array {
        $materials = [];

        foreach ($base['materials'] ?? [] as $material) {
            $key = (string) ($material['material_key'] ?? '');

            if ($key === '') {
                continue;
            }

            $materials[$key] = [
                'material_key' => $key,
                'sources' => $material['sources'] ?? [],
            ];
        }

        foreach ($incoming['materials'] ?? [] as $material) {
            $key = (string) ($material['material_key'] ?? '');

            if ($key === '' || !isset($this->catalog[$key])) {
                continue;
            }

            if (!isset($materials[$key])) {
                $materials[$key] = [
                    'material_key' => $key,
                    'sources' => [],
                ];
            }

            foreach ($material['sources'] ?? [] as $source) {
                $url = trim(
                    (string) ($source['source_url'] ?? '')
                );

                $price = (float) (
                    $source['price_per_m2'] ?? 0
                );

                if (
                    $url === '' ||
                    !filter_var($url, FILTER_VALIDATE_URL) ||
                    $price <= 0
                ) {
                    continue;
                }

                $source['research_method'] =
                    $source['research_method']
                    ?? $method;

                /*
                 * Aynı URL yalnızca bir kez.
                 */
                $duplicate = collect(
                    $materials[$key]['sources']
                )->contains(
                    fn ($existing) =>
                        trim(
                            (string) (
                                $existing['source_url'] ?? ''
                            )
                        ) === $url
                );

                if ($duplicate) {
                    continue;
                }

                $materials[$key]['sources'][] = $source;
            }
        }

        return [
            'materials' => array_values($materials),

            'alerts' => array_values(
                array_unique(
                    array_merge(
                        $base['alerts'] ?? [],
                        $incoming['alerts'] ?? []
                    )
                )
            ),
        ];
    }

    private function researchYerevdekor(): array
    {
        $url='https://www.yerevdekor.com/Seramik';

        $response=Http::timeout(35)
            ->retry(2,1000)
            ->withHeaders([
                'User-Agent'=>
                    'Mozilla/5.0 WORKSITEM-PriceResearch/1.0',
                'Accept-Language'=>'tr-TR,tr;q=0.9',
            ])
            ->get($url);

        if(!$response->successful()){
            throw new RuntimeException(
                'Yerevdekor HTTP '.$response->status()
            );
        }

        $html=$response->body();

        $plain=preg_replace(
            '/\s+/u',
            ' ',
            html_entity_decode(
                strip_tags($html),
                ENT_QUOTES|ENT_HTML5,
                'UTF-8'
            )
        );

        preg_match_all(
            '~<a\b[^>]*href=["\x27]([^"\x27]+)["\x27][^>]*>(.*?)</a>~isu',
            $html,
            $links,
            PREG_SET_ORDER
        );

        $groups=[];
        $seen=[];

        foreach($links as $link){

            $href=trim((string)($link[1] ?? ''));

            $title=trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    html_entity_decode(
                        strip_tags($link[2] ?? ''),
                        ENT_QUOTES|ENT_HTML5,
                        'UTF-8'
                    )
                )
            );

            /*
             * Yerevdekor gerçek ürün URL'leri:
             * ..._44369.html
             */
            if(
                $title==='' ||
                mb_strlen($title)>250 ||
                !preg_match(
                    '/_([0-9]+)\.html(?:\?.*)?$/u',
                    $href,
                    $id
                )
            ){
                continue;
            }

            $productId=$id[1];

            if(isset($seen[$productId])){
                continue;
            }

            /*
             * Benchmark dışı ürünler
             */
            if(preg_match(
                '/defolu|endüstriyel|dekor|mozaik|bordür|süpürgelik|basamak/iu',
                $title
            )){
                continue;
            }

            if(!preg_match(
                '/(?<![0-9])([0-9]+(?:[.,][0-9]+)?)\s*[xX]\s*([0-9]+(?:[.,][0-9]+)?)(?![0-9])/u',
                $title,
                $sizeMatch
            )){
                continue;
            }

            $w=str_replace(',','.',$sizeMatch[1]);
            $h=str_replace(',','.',$sizeMatch[2]);

            $size=$w.'x'.$h;

            $key=$this->classifyYerevdekorProduct(
                $title,
                $size
            );

            if(
                !$key ||
                !isset($this->catalog[$key])
            ){
                continue;
            }

            /*
             * Ürünün hemen önünde Tükendi varsa kullanma.
             */
            $titlePos=mb_strpos($plain,$title);

            if($titlePos!==false){
                $before=mb_substr(
                    $plain,
                    max(0,$titlePos-80),
                    80
                );

                if(preg_match('/Tükendi/iu',$before)){
                    continue;
                }
            }

            /*
             * Önce indirimli fiyat.
             */
            $price=null;

            if(preg_match(
                '/originaldiscountprice'.
                preg_quote($productId,'/').
                '\s*=\s*([0-9.]+)/u',
                $html,
                $m
            )){
                $price=(float)$m[1];

            }elseif(preg_match(
                '/originallocalprice'.
                preg_quote($productId,'/').
                '\s*=\s*([0-9.]+)/u',
                $html,
                $m
            )){
                $price=(float)$m[1];
            }

            if(!$price || $price<=0){
                continue;
            }

            $spec=$this->catalog[$key];

            if(
                $price<(float)$spec['min'] ||
                $price>(float)$spec['max']
            ){
                continue;
            }

            /*
             * Marka dataLayer'dan.
             */
            $brand=null;

            if(preg_match(
                '/"item_id":"'.
                preg_quote($productId,'/').
                '".{0,1000}?"item_brand":"([^"]+)"/su',
                $html,
                $bm
            )){
                $brand=json_decode(
                    '"'.$bm[1].'"'
                ) ?: $bm[1];
            }

            $groups[$key][]=[
                'id'=>$productId,
                'title'=>$title,
                'brand'=>$brand,
                'price'=>$price,
            ];

            $seen[$productId]=true;
        }


        $materials=[];

        foreach($groups as $key=>$products){

            $prices=collect($products)
                ->pluck('price')
                ->map(fn($v)=>(float)$v)
                ->sort()
                ->values();

            $count=$prices->count();

            if(!$count){
                continue;
            }

            if($count%2){
                $median=(float)$prices[intdiv($count,2)];
            }else{
                $median=(
                    (float)$prices[$count/2-1] +
                    (float)$prices[$count/2]
                )/2;
            }

            $brands=collect($products)
                ->pluck('brand')
                ->filter()
                ->unique()
                ->values();

            $materials[]=[
                'material_key'=>$key,
                'sources'=>[
                    [
                        'source_name'=>'Yerevdekor',
                        'source_url'=>$url,
                        'product_name'=>
                            $this->catalog[$key]['name'].
                            ' Yerevdekor piyasa medyanı',
                        'brand'=>'Çoklu marka',
                        'spec'=>
                            $count.' ürün · '.
                            $brands->count().' marka',
                        'price_per_m2'=>
                            round($median,2),
                        'package_text'=>null,
                        'normalization_note'=>
                            $count.
                            ' uygun Yerevdekor ürününün '.
                            'güncel TL/m² fiyat medyanı.',
                        'vat_included'=>true,
                        'confidence'=>
                            $count>=4 ? 92 : 84,
                        'research_method'=>
                            'yerevdekor_category_median',
                    ],
                ],
            ];
        }

        if(empty($materials)){
            throw new RuntimeException(
                'Uygun Yerevdekor ürünü bulunamadı'
            );
        }

        return [
            'materials'=>$materials,
            'alerts'=>[],
        ];
    }


    private function classifyYerevdekorProduct(
        string $title,
        string $size
    ): ?string {

        $text=mb_strtolower($title,'UTF-8');

        $porcelain=
            str_contains($text,'porselen') ||
            str_contains($text,'granit');

        if($porcelain){

            $polished=
                str_contains($text,'parlak') ||
                str_contains($text,'lapp') ||
                str_contains($text,'polished');

            $matt=
                preg_match(
                    '/(?:^|\s)mat(?:\s|$)/u',
                    $text
                )===1 ||
                str_contains($text,'matt');

            $glazed=
                str_contains($text,'sırlı') ||
                str_contains($text,'glazed');

            if($glazed){
                return match($size){
                    '30x60'=>'tile_porcelain_glazed_30x60_m2',
                    '60x60'=>'tile_porcelain_glazed_60x60_m2',
                    '60x120'=>'tile_porcelain_glazed_60x120_m2',
                    default=>null,
                };
            }

            if($polished){
                return match($size){
                    '60x60'=>'tile_porcelain_polished_60x60_m2',
                    '60x120'=>'tile_porcelain_polished_60x120_m2',
                    default=>null,
                };
            }

            if($matt){
                return match($size){
                    '60x60'=>'tile_porcelain_matt_60x60_m2',
                    '60x120'=>'tile_porcelain_matt_60x120_m2',
                    default=>null,
                };
            }

            return null;
        }

        /*
         * Seramik/fayans
         */
        if(
            $size==='30x60' &&
            (
                str_contains($text,'duvar') ||
                str_contains($text,'fayans')
            )
        ){
            return 'tile_ceramic_wall_30x60_m2';
        }

        if(
            $size==='45x45' &&
            str_contains($text,'yer')
        ){
            return 'tile_ceramic_floor_45x45_m2';
        }

        if(
            $size==='60x60' &&
            str_contains($text,'yer')
        ){
            return 'tile_ceramic_floor_60x60_m2';
        }

        return null;
    }

    private function fetchDirectPrice(array $item): array
    {
        $response=Http::timeout(25)
            ->retry(2,1000)
            ->withHeaders([
                'User-Agent'=>'Mozilla/5.0 WORKSITEM-PriceResearch/1.0',
                'Accept-Language'=>'tr-TR,tr;q=0.9',
            ])
            ->get($item['url']);

        if(!$response->successful()){
            throw new RuntimeException(
                'HTTP '.$response->status()
            );
        }

        $text=html_entity_decode(
            strip_tags($response->body()),
            ENT_QUOTES|ENT_HTML5,
            'UTF-8'
        );

        $text=preg_replace('/\s+/u',' ',$text);

        if($item['mode']==='altug'){

            if(!preg_match(
                '/Birim\s*₺\s*([0-9][0-9.,]*)\s*\/\s*m(?:²|2)/iu',
                $text,
                $m
            )){
                throw new RuntimeException(
                    'Altuğ TL/m² fiyatı bulunamadı'
                );
            }

            $net=$this->trNumber($m[1]);

            $vatRate=0;

            if(preg_match(
                '/\+\s*%([0-9]+)\s*KDV/iu',
                $text,
                $v
            )){
                $vatRate=(float)$v[1];
            }

            $gross=$net*(1+($vatRate/100));

            $boxArea=null;

            if(preg_match(
                '/1\s*kutu\s*=\s*([0-9.,]+)\s*m(?:²|2)/iu',
                $text,
                $b
            )){
                $boxArea=$this->trNumber($b[1]);
            }

            return [
                'price'=>round($gross,2),
                'package_text'=>$boxArea
                    ? number_format($boxArea,2,',','.').' m²/kutu'
                    : null,
                'note'=>'Kaynak net fiyatına %'.
                    number_format($vatRate,0).
                    ' KDV eklenerek TL/m² normalize edildi.',
            ];
        }


        if($item['mode']==='yapimarka'){

            if(!preg_match(
                '/₺\s*([0-9][0-9.,]*)\s*\/\s*m(?:²|2)/iu',
                $text,
                $m
            )){
                throw new RuntimeException(
                    'YapıMarka TL/m² fiyatı bulunamadı'
                );
            }

            $m2=$this->trNumber($m[1]);

            if(!preg_match(
                '/1\s*kutu\s*=\s*([0-9.,]+)\s*m(?:²|2).*?kutu\s*fiyatı\s*₺\s*([0-9.,]+)/iu',
                $text,
                $k
            )){
                throw new RuntimeException(
                    'YapıMarka kutu doğrulama verisi bulunamadı'
                );
            }

            $boxArea=$this->trNumber($k[1]);
            $boxPrice=$this->trNumber($k[2]);

            $cross=$boxPrice/$boxArea;

            if(abs($cross-$m2)>max(1,$m2*0.01)){
                throw new RuntimeException(
                    'YapıMarka m²/kutu çapraz kontrolü başarısız'
                );
            }

            $boxKg=null;

            if(preg_match(
                '/Kutu\s*Ağırlık:\s*([0-9.,]+)\s*kg/iu',
                $text,
                $w
            )){
                $boxKg=$this->trNumber($w[1]);
            }

            return [
                'price'=>round($m2,2),
                'package_text'=>
                    number_format($boxArea,2,',','.').' m²/kutu'.
                    ($boxKg
                        ? ' · '.number_format($boxKg,1,',','.').' kg/kutu'
                        : ''),
                'note'=>'KDV dahil TL/m² fiyatı kutu toplamı ile çapraz doğrulandı.',
            ];
        }

        if($item['mode']==='evdema'){
            if(!preg_match(
                '/₺\s*([0-9][0-9.,]*)\s*\/\s*M2/iu',
                $text,$m
            )){
                throw new RuntimeException(
                    'Evdema TL/m² fiyatı bulunamadı'
                );
            }

            return [
                'price'=>round($this->trNumber($m[1]),2),
                'package_text'=>null,
                'note'=>'Kaynak sayfasındaki doğrudan TL/m² fiyatı.',
            ];
        }

        if(!preg_match(
            '/m(?:²|2)\s*Fiyatı\s*=\s*([0-9.,]+)\s*TL/iu',
            $text,$m
        )){
            throw new RuntimeException(
                'VitrA m² fiyatı bulunamadı'
            );
        }

        if(!preg_match(
            '/1\s*Kutu\s*=\s*([0-9.,]+)\s*m(?:²|2)\s*=\s*([0-9.,]+)\s*TL/iu',
            $text,$k
        )){
            throw new RuntimeException(
                'VitrA kutu doğrulama verisi bulunamadı'
            );
        }

        $m2=$this->trNumber($m[1]);
        $boxArea=$this->trNumber($k[1]);
        $boxPrice=$this->trNumber($k[2]);

        if($m2<=0 || $boxArea<=0 || $boxPrice<=0){
            throw new RuntimeException(
                'VitrA fiyat verisi geçersiz'
            );
        }

        $cross=$boxPrice/$boxArea;

        if(abs($cross-$m2)>max(1,$m2*0.005)){
            throw new RuntimeException(
                'VitrA m²/kutu çapraz kontrolü başarısız'
            );
        }

        return [
            'price'=>round($m2,2),
            'package_text'=>
                number_format($boxArea,2,',','.').' m²/kutu',
            'note'=>
                'm² fiyatı kutu toplamı ile çapraz doğrulandı.',
        ];
    }
    private function explicitM2Price(string $html): float
    {
        $text = html_entity_decode(
            strip_tags($html),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $text = preg_replace('/\s+/u', ' ', $text);

        if (!preg_match(
            '/([0-9][0-9\.\,]*)\s*TL\s*m(?:²|2)\s*fiyatıdır/iu',
            $text,
            $match
        )) {
            throw new RuntimeException(
                'Açık TL/m² fiyatı bulunamadı'
            );
        }

        $price = $this->trNumber($match[1]);

        if ($price <= 0) {
            throw new RuntimeException(
                'Geçersiz TL/m² fiyatı'
            );
        }

        return round($price, 2);
    }

    private function packageArea(string $html): ?float
    {
        $text = html_entity_decode(
            strip_tags($html),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $text = preg_replace('/\s+/u', ' ', $text);

        if (!preg_match(
            '/kutu.{0,80}?([0-9]+(?:[\.,][0-9]+)?)\s*m(?:²|2)/iu',
            $text,
            $match
        )) {
            return null;
        }

        $area = $this->trNumber($match[1]);

        return $area > 0 ? $area : null;
    }

    private function jsonLdPrices(string $html): array
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?>'.$html
        );

        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $prices = [];

        foreach (
            $xpath->query(
                '//script[@type="application/ld+json"]'
            ) as $script
        ) {
            $json = json_decode(
                trim($script->textContent),
                true
            );

            if (is_array($json)) {
                $this->collectDirectPrices(
                    $json,
                    $prices
                );
            }
        }

        return array_values(
            array_filter(
                array_unique($prices),
                fn ($price) => $price > 0
            )
        );
    }

    private function collectDirectPrices(
        array $node,
        array &$prices
    ): void {
        foreach (['price', 'lowPrice'] as $field) {
            if (
                isset($node[$field]) &&
                is_numeric($node[$field])
            ) {
                $prices[] = (float) $node[$field];
            }
        }

        foreach ($node as $value) {
            if (is_array($value)) {
                $this->collectDirectPrices(
                    $value,
                    $prices
                );
            }
        }
    }

    private function trNumber(string $value): float
    {
        $value = trim($value);
        $value = preg_replace('/[^\d.,]/u', '', $value);

        if (
            str_contains($value, '.') &&
            str_contains($value, ',')
        ) {
            // Türkçe: 1.380,96
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);

        } elseif (str_contains($value, ',')) {
            // Türkçe ondalık: 562,75 / 1,44
            $value = str_replace(',', '.', $value);

        } elseif (
            preg_match('/^\d{1,3}(?:\.\d{3})+$/', $value)
        ) {
            // Türkçe binlik: 1.096 / 1.352 / 1.503
            $value = str_replace('.', '', $value);
        }

        return (float) $value;
    }

    private function research(string $apiKey): array
    {
        $model = config('services.gemini.price_research_model', 'gemini-3.6-flash');

        $catalogText = collect($this->catalog)
            ->map(
                fn ($spec, $key) =>
                    '- '.$key.': '.$spec['name']
            )
            ->implode("\n");

        $prompt = <<<PROMPT
Türkiye'de bugün satışta olan seramik, fayans ve porselen karo ürünlerinin
GERÇEK perakende piyasa fiyatlarını Google Search kullanarak araştır.

ARAŞTIRILACAK WORKSITEM ÜRÜNLERİ:

{$catalogText}

AMAÇ

Her material_key için tek bir marka veya tek bir mağazaya bağlı kalmadan
Türkiye piyasasını temsil edecek güvenilir TL/m² fiyat havuzu oluştur.

ARAMA STRATEJİSİ

Her ürün anahtarı için tek Google sorgusuna bağlı kalma.

Ürün adına göre aşağıdaki arama niyetlerinin uygun kombinasyonlarını kullan:

- "[ebat] seramik fiyat"
- "[ebat] seramik fiyat m2"
- "[ebat] fayans fiyat"
- "[ebat] karo fiyat"
- "[ebat] yer karosu fiyat"
- "[ebat] duvar karosu fiyat"
- "[ebat] porselen karo fiyat"
- "[ebat] granit seramik fiyat"
- "[ebat] mat porselen fiyat"
- "[ebat] parlak porselen fiyat"
- "[ebat] sırlı porselen fiyat"
- "[ebat] kutu fiyatı"
- "[ebat] TL/m2"

Malzeme türüne göre uygun eş anlamlıları kullan:

seramik / fayans / karo
porselen / granit seramik / porselen karo
mat / matt
parlak / polished / glossy
sırlı / glazed

MARKA VE MODEL ÇEŞİTLİLİĞİ

Tek markaya bağlı kalma.

Mümkün olduğunda şu üreticiler dahil farklı marka ve modelleri araştır:

VitrA
Kale
Çanakkale Seramik
Bien
Seramiksan
NG Kütahya Seramik
Yurtbay
Ege Seramik
Qua Granite
Decovita

Bu liste sınırlayıcı değildir.

Google'da bulunan başka gerçek Türk üretici ve satıcılarını da araştır.

HER MATERIAL_KEY İÇİN HEDEF

- mümkünse 4-8 bağımsız satış kaynağı
- mümkünse en az 4 farklı domain
- mümkünse en az 4 farklı marka
- aynı domain sonucu baskın hale gelmesin
- aynı satıcının aynı ürünü tekrar etmesin

Bir domain içinde çok sayıda ürün bulursan,
aynı material_key için mümkünse en fazla 1 temsilci ürün döndür.

KAYNAK ÖNCELİĞİ

1. Üreticinin resmi ürün/satış sayfası
2. Yetkili veya kurumsal seramik mağazası
3. Yapı market
4. Güvenilir e-ticaret mağazası
5. Pazaryeri yalnızca ek piyasa sinyali

ÜRÜN DOĞRULAMA

Bir ürünü kabul etmeden önce:

- gerçek ürün sayfasını mümkünse aç
- marka belli olmalı
- model/seri mümkünse belli olmalı
- ebat material_key ile uyumlu olmalı
- ürün tipi material_key ile uyumlu olmalı
- fiyat gerçek satış fiyatı olmalı
- fiyat birimi anlaşılmalı
- kutu fiyatıysa kutu m² bilgisi doğrulanmalı
- Türkiye'de satışa yönelik olmalı

EBAT KURALI

Tam ebatı tercih et.

Örneğin material_key 60x60 ise:

59.8x59.8
61x61
59.5x59.5

gibi yakın ebatları 60x60 olarak kabul etme.

Aynı şekilde 60x120 yerine yakın ama farklı ebat kullanma.

MALZEME SINIFI KURALI

Seramik/fayans sonucunu porselen olarak sınıflandırma.

Mat porselen ile parlak porseleni karıştırma.

Sırlı porselen, mat porselen ve parlak/polished porseleni mümkün olduğunca
ayrı tut.

FIYAT NORMALIZASYONU

Çıktı fiyatı mutlaka TL/m² olsun.

Fiyat zaten TL/m² ise doğrudan kullan.

Fiyat TL/kutu ise:

price_per_m2 = kutu_fiyati / kutu_m2

Kutu m² bilgisi açıkça doğrulanamıyorsa kullanma.

Fiyat TL/adet ise:

tek_karo_m2 =
(en_cm / 100) × (boy_cm / 100)

price_per_m2 =
adet_fiyati / tek_karo_m2

Birim veya paket bilgisi belirsizse fiyatı tahmin etme.

INDIRIM

Normal fiyat ve indirimli güncel satış fiyatı birlikte görünüyorsa
güncel satın alınabilir fiyatı kullan.

normalization_note alanında indirimli olduğunu belirt.

Kupon sonrası belirsiz fiyat,
üyelik fiyatı,
"başlayan fiyat",
telefonla fiyat,
teklif al

gibi değerleri normal perakende benchmarkına alma.

ELE

Şunları kullanma:

- fiyatı olmayan ürün
- yalnızca teklif isteyen sayfa
- ikinci el
- sosyal medya
- Pinterest
- forum
- sadece görsel
- sadece katalog ve satış fiyatı olmayan PDF
- fiyat birimi anlaşılamayan sonuç
- farklı ebat
- farklı ürün tipi
- kutu m² doğrulanamayan kutu fiyatı
- gerçek URL'si doğrulanamayan sonuç
- erişilemeyen veya güvenilirliği düşük kaynak

KDV

KDV dahil olduğu açıkça belirtiliyorsa:

vat_included = true

Açıkça bilinmiyorsa:

vat_included = null

Kesin olmayan KDV bilgisini uydurma.

GÜVEN PUANI

90-100:
resmi üretici veya güçlü kurumsal mağaza,
ürün ve fiyat açık,
m² fiyatı doğrudan veya paket hesabı tam doğrulanmış.

80-89:
güvenilir mağaza,
ürün/fiyat/paket bilgisi açık.

70-79:
kullanılabilir ancak ikincil doğrulama gerektiren kaynak.

70 altındaki kaynakları döndürme.

KRİTİK

Ürün, fiyat, marka, URL veya paket bilgisi uydurma.

Google snippet fiyatını tek başına yeterli kabul etme.
Mümkünse gerçek ürün sayfasını açıp doğrula.

Sonuç azsa sahte kaynak üretme.
Bulabildiğin doğrulanmış kaynakları döndür.

Her material_key için kaynaklar mümkün olduğunca
farklı domain ve farklı markalardan gelsin.

SADECE tanımlanan JSON şemasına uygun çıktı döndür.
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
                                            'type' => ['string', 'null'],
                                        ],
                                        'spec' => [
                                            'type' => ['string', 'null'],
                                        ],
                                        'price_per_m2' => [
                                            'type' => 'number',
                                        ],
                                        'package_text' => [
                                            'type' => ['string', 'null'],
                                        ],
                                        'normalization_note' => [
                                            'type' => ['string', 'null'],
                                        ],
                                        'vat_included' => [
                                            'type' => ['boolean', 'null'],
                                        ],
                                        'confidence' => [
                                            'type' => 'number',
                                        ],
                                    ],
                                    'required' => [
                                        'source_name',
                                        'source_url',
                                        'product_name',
                                        'price_per_m2',
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

        $response = Http::timeout(180)
            ->retry(2, 1500)
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post(
                'https://generativelanguage.googleapis.com/v1beta/interactions',
                [
                    'model' => $model,
                    'input' => $prompt,
                    'tools' => [
                        [
                            'type' => 'google_search',
                        ],
                    ],
                    'response_format' => [
                        [
                            'type' => 'text',
                            'mime_type' => 'application/json',
                            'schema' => $schema,
                        ],
                    ],
                ]
            );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Gemini HTTP '.
                $response->status().
                ' | '.
                mb_substr($response->body(), 0, 800)
            );
        }

        $payload = $response->json();

        $searchUsed = false;
        $texts = [];

        foreach ($payload['steps'] ?? [] as $step) {
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

            foreach ($step['content'] ?? [] as $content) {
                if (
                    ($content['type'] ?? null) === 'text'
                    && isset($content['text'])
                ) {
                    $texts[] = $content['text'];
                }
            }
        }

        if (!$searchUsed) {
            throw new RuntimeException(
                'Google Search grounding çalışmadı.'
            );
        }

        $text = trim(implode('', $texts));

        if ($text === '') {
            throw new RuntimeException(
                'Gemini boş araştırma sonucu döndürdü.'
            );
        }

        $result = json_decode($text, true);

        if (
            !is_array($result)
            || !isset($result['materials'])
        ) {
            throw new RuntimeException(
                'Gemini geçerli seramik fiyat JSON çıktısı üretmedi.'
            );
        }

        return $result;
    }

    private function reviewThreshold(): float
    {
        $row = DB::table('pricing_settings')
            ->where(
                'key',
                'tile_price_review_threshold_pct'
            )
            ->first();

        if ($row && is_numeric($row->value)) {
            return max(1, (float) $row->value);
        }

        return 10.0;
    }

    private function sendReport(
        array $report,
        array $alerts
    ): void {
        $email = config(
            'worksitem_prices.report_email',
            'info@worksitem.com'
        );

        $body =
            "WORKSITEM — SERAMİK / PORSELEN PİYASA ARAŞTIRMASI\n".
            now('Europe/Istanbul')->format('d.m.Y H:i').
            "\n\n===== FIYATLAR =====\n".
            (
                $report
                    ? implode("\n", $report)
                    : 'FIYAT_YOK'
            ).
            "\n\n===== KAINTA MUHENDIS KONTROLU =====\n".
            (
                $alerts
                    ? implode("\n", $alerts)
                    : 'UYARI_YOK'
            );

        try {
            Mail::raw(
                $body,
                function ($message) use (
                    $email,
                    $alerts
                ) {
                    $message
                        ->to($email)
                        ->subject(
                            $alerts
                                ? '⚠️ WORKSITEM Seramik Fiyat Kontrolü'
                                : 'WORKSITEM Günlük Seramik Piyasa Fiyatları'
                        );
                }
            );
        } catch (Throwable $e) {
            $this->warn(
                'MAIL_FAIL='.$e->getMessage()
            );
        }
    }
}

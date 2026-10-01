<?php

namespace App\Services\Pricing;

use App\Models\MaterialPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LiveMarketTickerService
{
    public const MARKET_MULTIPLIER = 1.60;

    public function data(): array
    {
        $materials = $this->materials();
        $works = $this->tenderWorks();

        return [
            'generated_at' => now()->toIso8601String(),

            'market_multiplier' =>
                self::MARKET_MULTIPLIER,

            'materials' => $materials,

            /*
             * Yeni doğru isim.
             */
            'works' => $works,

            /*
             * Eski API uyumluluğu bozulmasın.
             */
            'labor' => $works,

            'items' => array_merge(
                $materials,
                $works
            ),
        ];
    }

    private function materials(): array
    {
        return MaterialPrice::query()
            ->where('is_active', true)
            ->whereNotNull('current_price')
            ->orderByDesc('source_date')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                return [
                    'type' => 'material',
                    'key' => $row->key,
                    'label' => $row->name,
                    'description' => $row->name,

                    'price' => round(
                        (float) $row->current_price,
                        2
                    ),

                    'base_price' => null,
                    'unit' => $row->unit,

                    'source' =>
                        $row->source_name,

                    'source_date' =>
                        optional(
                            $row->source_date
                        )->format('Y-m-d'),

                    'poz_no' => null,
                ];
            })
            ->values()
            ->all();
    }

    private function tenderWorks(): array
    {
        if (!Schema::hasTable('tender_pozs')) {
            return [];
        }

        $columns = Schema::getColumnListing('tender_pozs');

        if (!in_array('description', $columns, true)) {
            return [];
        }

        $priceColumn = collect([
            'unit_price',
            'price',
            'official_unit_price',
            'rayic_price',
        ])->first(
            fn ($column) =>
                in_array($column, $columns, true)
        );

        if (!$priceColumn) {
            return [];
        }

        $latestYear = in_array('year', $columns, true)
            ? DB::table('tender_pozs')->max('year')
            : null;

        /*
         * Ana sayfada kullanıcıların anlayacağı
         * temel inşaat/tadilat iş grupları.
         *
         * Gruplar her requestte karıştırılır.
         * Her gruptan da rastgele bir poz alınır.
         */
        $groups = [

            [
                'label' => 'Duvar Örme',
                'include' => [
                    'gazbeton duvar',
                    'tuğla duvar',
                    'duvar örülmesi',
                    'duvar yapılması',
                    'taş duvar yapılması',
                ],
                'exclude' => [
                    'sökülmesi',
                    'yıkılması',
                    'onarılması',
                    'deney',
                ],
            ],

            [
                'label' => 'Betonarme Kalıp',
                'include' => [
                    'kalıp yapılması',
                    'betonarme kalıp',
                    'ahşap seri kalıp',
                ],
                'exclude' => [
                    'iskele',
                    'deney',
                    'köprü',
                ],
            ],

            [
                'label' => 'Sıva',
                'include' => [
                    'sıva yapılması',
                    'alçı sıva',
                    'çimento harçlı sıva',
                ],
                'exclude' => [
                    'sökülmesi',
                    'deney',
                    'malzeme',
                ],
            ],

            [
                'label' => 'Şap',
                'include' => [
                    'şap yapılması',
                    'çimento şap',
                    'şap betonu',
                ],
                'exclude' => [
                    'sökülmesi',
                    'deney',
                ],
            ],

            [
                'label' => 'Seramik Kaplama',
                'include' => [
                    'seramik kaplama yapılması',
                    'seramik karo',
                    'fayans kaplama',
                    'porselen karo',
                ],
                'exclude' => [
                    'ustası',
                    'sökülmesi',
                    'deney',
                ],
            ],

            [
                'label' => 'Alçıpan Bölme',
                'include' => [
                    'alçıpan bölme',
                    'alçı levha ile bölme',
                    'alçı levha bölme',
                ],
                'exclude' => [
                    'boya',
                    'sökülmesi',
                    'asma tavan',
                ],
            ],

            [
                'label' => 'Asma Tavan',
                'include' => [
                    'asma tavan yapılması',
                    'alçıpan asma tavan',
                    'alçı levha asma tavan',
                ],
                'exclude' => [
                    'sökülmesi',
                ],
            ],

            [
                'label' => 'İç Cephe Boya',
                'include' => [
                    'iç cephe boya yapılması',
                    'plastik boya yapılması',
                    'silikonlu boya yapılması',
                ],
                'exclude' => [
                    'ustası',
                    'astar malzemesi',
                    'sökülmesi',
                ],
            ],

            [
                'label' => 'Dış Cephe Boya',
                'include' => [
                    'dış cephe boya',
                    'akrilik esaslı dış cephe',
                    'silikon esaslı dış cephe',
                ],
                'exclude' => [
                    'malzemesi',
                    'deney',
                ],
            ],

            [
                'label' => 'Isı Yalıtımı',
                'include' => [
                    'ısı yalıtımı yapılması',
                    'mantolama yapılması',
                    'xps levha ile',
                    'eps levha ile',
                ],
                'exclude' => [
                    'boru',
                    'kanal',
                    'tesisat',
                    'deney',
                ],
            ],

            [
                'label' => 'Çatı',
                'include' => [
                    'çatı örtüsü yapılması',
                    'çatı kaplaması yapılması',
                    'sandviç panel çatı',
                    'kiremit çatı',
                ],
                'exclude' => [
                    'sökülmesi',
                    'deney',
                ],
            ],

            [
                'label' => 'Kazı',
                'include' => [
                    'makine ile kazı',
                    'kazı yapılması',
                    'temel kazısı',
                ],
                'exclude' => [
                    'deney',
                    'sondaj',
                    'arkeolojik',
                ],
            ],

            [
                'label' => 'Dolgu',
                'include' => [
                    'dolgu yapılması',
                    'kum çakıl dolgu',
                    'stabilize dolgu',
                ],
                'exclude' => [
                    'baraj',
                    'sedde',
                    'deney',
                ],
            ],

            [
                'label' => 'Parke Döşeme',
                'include' => [
                    'laminat parke',
                    'ahşap parke yapılması',
                    'parke döşenmesi',
                ],
                'exclude' => [
                    'sökülmesi',
                    'taşı',
                ],
            ],

            [
                'label' => 'Mermer Kaplama',
                'include' => [
                    'mermer kaplama yapılması',
                    'mermer döşeme',
                    'mermer plak',
                ],
                'exclude' => [
                    'malzeme bedeli',
                    'deney',
                ],
            ],

            [
                'label' => 'Kapı',
                'include' => [
                    'ahşap kapı yapılması',
                    'çelik kapı',
                    'kapı kasası ve kanadı',
                ],
                'exclude' => [
                    'aksesuar',
                    'kilit',
                    'sökülmesi',
                ],
            ],

            [
                'label' => 'Pencere / Doğrama',
                'include' => [
                    'pvc doğrama yapılması',
                    'alüminyum doğrama yapılması',
                    'pencere doğraması',
                ],
                'exclude' => [
                    'profil',
                    'malzeme',
                    'sökülmesi',
                ],
            ],

            [
                'label' => 'Kaldırım',
                'include' => [
                    'kilitli parke taşı döşenmesi',
                    'beton parke taşı döşenmesi',
                    'kaldırım yapılması',
                ],
                'exclude' => [
                    'sökülmesi',
                    'malzeme',
                ],
            ],
        ];

        /*
         * Her requestte kategori sırası değişir.
         * 10 başarılı kategori bulunana kadar devam eder.
         */
        $groups = collect($groups)
            ->shuffle()
            ->values();

        $selected = collect();
        $usedPoz = [];

        foreach ($groups as $group) {

            if ($selected->count() >= 10) {
                break;
            }

            $query = DB::table('tender_pozs')
                ->whereNotNull($priceColumn)
                ->where($priceColumn, '>', 0);

            if ($latestYear !== null) {
                $query->where('year', $latestYear);
            }

            if (in_array('unit', $columns, true)) {
                $query
                    ->whereNotNull('unit')
                    ->where('unit', '<>', '');
            }

            $query->where(
                function ($q) use ($group) {
                    foreach ($group['include'] as $keyword) {
                        $q->orWhere(
                            'description',
                            'like',
                            '%'.$keyword.'%'
                        );
                    }
                }
            );

            foreach ($group['exclude'] as $keyword) {
                $query->where(
                    'description',
                    'not like',
                    '%'.$keyword.'%'
                );
            }

            /*
             * Aşırı teknik/uzun pozlar vitrinde olmasın.
             */
            $query->whereRaw(
                'CHAR_LENGTH(description) BETWEEN 18 AND 180'
            );

            /*
             * Aynı poz tekrar seçilmesin.
             */
            if (
                !empty($usedPoz)
                && in_array('poz_no', $columns, true)
            ) {
                $query->whereNotIn(
                    'poz_no',
                    $usedPoz
                );
            }

            $row = $query
                ->inRandomOrder()
                ->first();

            if (!$row) {
                continue;
            }

            $pozNo = property_exists($row, 'poz_no')
                ? (string) $row->poz_no
                : '-';

            $usedPoz[] = $pozNo;

            $basePrice =
                (float) $row->{$priceColumn};

            $unit = property_exists($row, 'unit')
                ? trim((string) $row->unit)
                : '';

            $institution =
                property_exists($row, 'institution')
                    ? (string) $row->institution
                    : 'Tender Poz';

            $selected->push([
                'type' => 'tender',

                /*
                 * Ticker temiz görünür.
                 */
                'label' =>
                    $group['label'],

                /*
                 * API'de hangi gerçek pozdan
                 * geldiği tamamen korunur.
                 */
                'description' =>
                    trim(
                        (string) $row->description
                    ),

                'key' =>
                    'tender_'.$pozNo,

                'poz_no' =>
                    $pozNo,

                'base_price' =>
                    round(
                        $basePrice,
                        2
                    ),

                'price' =>
                    round(
                        $basePrice
                        * self::MARKET_MULTIPLIER,
                        2
                    ),

                'unit' =>
                    $unit,

                'source' =>
                    $institution,

                'source_date' =>
                    null,

                'price_column' =>
                    $priceColumn,
            ]);
        }

        return $selected
            ->values()
            ->all();
    }
    public function diagnostics(): array
    {
        return [
            'multiplier' =>
                self::MARKET_MULTIPLIER,

            'tender_total' =>
                Schema::hasTable(
                    'tender_pozs'
                )
                    ? DB::table(
                        'tender_pozs'
                    )->count()
                    : 0,
        ];
    }
}

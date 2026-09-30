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

        $columns =
            Schema::getColumnListing(
                'tender_pozs'
            );

        if (!in_array(
            'description',
            $columns,
            true
        )) {
            return [];
        }

        /*
         * Kullanıcının istediği:
         * Resmi poz fiyatını oku,
         * sonra ×1.60 uygula.
         */
        $priceColumn = collect([
            'unit_price',
            'price',
            'official_unit_price',
            'rayic_price',
        ])->first(
            fn ($column) =>
                in_array(
                    $column,
                    $columns,
                    true
                )
        );

        if (!$priceColumn) {
            return [];
        }

        $latestYear = null;

        if (in_array(
            'year',
            $columns,
            true
        )) {
            $latestYear =
                DB::table('tender_pozs')
                    ->max('year');
        }

        /*
         * Canlı piyasada kullanıcıya anlamlı,
         * farklı inşaat işlerinden karışık sepet.
         */
        $groups = [

            [
                'label' => 'Duvar Örme',
                'include' => [
                    'duvar örülmesi',
                    'duvar örme',
                    'tuğla ile duvar',
                    'gazbeton ile duvar',
                    'duvar yapılması',
                ],
                'exclude' => [
                    'yıkılması',
                    'sökülmesi',
                    'boyanması',
                    'kaplanması',
                ],
            ],

            [
                'label' => 'Betonarme Kalıp',
                'include' => [
                    'betonarme kalıbı',
                    'betonarme kalıp',
                    'kalıp yapılması',
                    'kalıp yapılması ve sökülmesi',
                ],
                'exclude' => [
                    'iskele',
                    'doğrama',
                ],
            ],

            [
                'label' => 'Sıva',
                'include' => [
                    'sıva yapılması',
                    'çimento harçlı sıva',
                    'alçı sıva',
                ],
                'exclude' => [
                    'sökülmesi',
                    'kazınması',
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
                ],
            ],

            [
                'label' => 'Seramik Kaplama',
                'include' => [
                    'seramik kaplama yapılması',
                    'seramik kaplama',
                    'seramik karo',
                ],
                'exclude' => [
                    'sökülmesi',
                ],
            ],

            [
                'label' => 'Alçıpan Bölme',
                'include' => [
                    'alçıpan bölme',
                    'alçı levha ile bölme',
                    'alçı levha bölme',
                    'alçıpan',
                ],
                'exclude' => [
                    'sökülmesi',
                    'asma tavan',
                ],
            ],

            [
                'label' => 'Boya',
                'include' => [
                    'plastik boya yapılması',
                    'silikonlu boya yapılması',
                    'boya yapılması',
                    'boyacı ustası',
                ],
                'exclude' => [
                    'sökülmesi',
                ],
            ],

            [
                'label' => 'Kazı',
                'include' => [
                    'makine ile kazı',
                    'kazı yapılması',
                    'kazılması',
                ],
                'exclude' => [
                    'elle kazı',
                ],
            ],
        ];

        $selected = collect();
        $usedPoz = [];

        foreach ($groups as $group) {

            $query =
                DB::table('tender_pozs')
                    ->whereNotNull(
                        $priceColumn
                    )
                    ->where(
                        $priceColumn,
                        '>',
                        0
                    );

            if ($latestYear !== null) {
                $query->where(
                    'year',
                    $latestYear
                );
            }

            $query->where(
                function ($q) use ($group) {

                    foreach (
                        $group['include']
                        as $keyword
                    ) {
                        $q->orWhere(
                            'description',
                            'like',
                            '%'.$keyword.'%'
                        );
                    }
                }
            );

            foreach (
                $group['exclude']
                as $keyword
            ) {
                $query->where(
                    'description',
                    'not like',
                    '%'.$keyword.'%'
                );
            }

            /*
             * Genel ve okunabilir pozları
             * öne almaya çalışıyoruz.
             */
            $query->orderByRaw(
                'CHAR_LENGTH(description) ASC'
            );

            if (in_array(
                'poz_no',
                $columns,
                true
            )) {
                $query->orderBy(
                    'poz_no'
                );
            }

            $candidates =
                $query
                    ->limit(30)
                    ->get();

            $row = $candidates->first(
                function ($candidate) use (
                    &$usedPoz
                ) {
                    $poz =
                        property_exists(
                            $candidate,
                            'poz_no'
                        )
                            ? (string)
                                $candidate->poz_no
                            : md5(
                                (string)
                                $candidate->description
                            );

                    return !in_array(
                        $poz,
                        $usedPoz,
                        true
                    );
                }
            );

            if (!$row) {
                continue;
            }

            $pozNo =
                property_exists(
                    $row,
                    'poz_no'
                )
                    ? (string) $row->poz_no
                    : '-';

            $usedPoz[] = $pozNo;

            $basePrice =
                (float)
                $row->{$priceColumn};

            $unit =
                property_exists(
                    $row,
                    'unit'
                )
                    ? (string) $row->unit
                    : '';

            $institution =
                property_exists(
                    $row,
                    'institution'
                )
                    ? (string)
                        $row->institution
                    : 'Tender Poz';

            $selected->push([
                'type' => 'tender',

                'key' =>
                    'tender_'.$pozNo,

                /*
                 * Tickerda kısa,
                 * herkesin anlayacağı isim.
                 */
                'label' =>
                    $group['label'],

                /*
                 * Tam açıklama API'de korunuyor.
                 */
                'description' =>
                    (string)
                    $row->description,

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

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

        $query = DB::table('tender_pozs')
            ->whereNotNull($priceColumn)
            ->where($priceColumn, '>', 0);

        /*
         * Son yılın pozlarını kullan.
         */
        if (in_array('year', $columns, true)) {

            $latestYear = DB::table('tender_pozs')
                ->max('year');

            if ($latestYear) {
                $query->where('year', $latestYear);
            }
        }

        /*
         * Kullanıcıya birimi belli olmayan
         * anlamsız satırları göstermiyoruz.
         */
        if (in_array('unit', $columns, true)) {
            $query
                ->whereNotNull('unit')
                ->where('unit', '<>', '');
        }

        /*
         * Ana sayfada yalnız herkesin anlayabileceği
         * gerçek inşaat / tadilat uygulamaları.
         *
         * Havuz hala 43 bin pozdan geliyor;
         * sadece vitrinde anlamsız/niche pozları eliyoruz.
         */
        $allowed = [
            'duvar',
            'kalıp',
            'beton',
            'sıva',
            'şap',
            'seramik',
            'fayans',
            'granit',
            'mermer',
            'parke',
            'boya',
            'badana',
            'alçı',
            'alçıpan',
            'asma tavan',
            'yalıtım',
            'mantolama',
            'çatı',
            'kaplama',
            'doğrama',
            'kapı',
            'pencere',
            'cephe',
            'kazı',
            'dolgu',
            'hafriyat',
            'döşeme',
            'kaldırım',
            'bordür',
            'taş duvar',
            'betonarme',
            'demir',
            'donatı',
            'iskele',
            'boru döşenmesi',
            'kanal döşenmesi',
        ];

        $query->where(function ($q) use ($allowed) {
            foreach ($allowed as $keyword) {
                $q->orWhere(
                    'description',
                    'like',
                    '%'.$keyword.'%'
                );
            }
        });

        /*
         * Ana sayfada anlamsız veya aşırı teknik
         * kalemleri göstermiyoruz.
         */
        $excluded = [
            'deney',
            'test ',
            'laboratuvar',
            'medikal',
            'hoparlör',
            'kamera',
            'busbar',
            'santral',
            'asansör',
            'jeneratör',
            'trafo',
            'vana kutusu',
            'fotovoltaik',
            'kablo',
            'elektronik',
            'cihaz',
            'ekipman',
            'baraj',
            'köprü',
            'havaalanı',
            'liman',
            'demiryolu',
        ];

        foreach ($excluded as $keyword) {
            $query->where(
                'description',
                'not like',
                '%'.$keyword.'%'
            );
        }

        /*
         * Açıklaması sadece ölçü/kod gibi görünen
         * satırlar da vitrinden çıkıyor.
         */
        $query->whereRaw(
            'CHAR_LENGTH(description) BETWEEN 18 AND 150'
        );

        /*
         * HER REQUESTTE FARKLI:
         * filtrelenmiş gerçek inşaat pozlarından
         * rastgele 10 adet.
         */
        $rows = $query
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return $rows
            ->map(function ($row) use ($priceColumn) {

                $basePrice =
                    (float) $row->{$priceColumn};

                $description =
                    trim(
                        (string) $row->description
                    );

                /*
                 * Kayan yazıda fazla uzun açıklamayı
                 * okunabilir şekilde kısalt.
                 */
                $label =
                    mb_strlen(
                        $description,
                        'UTF-8'
                    ) > 72
                        ? mb_substr(
                            $description,
                            0,
                            69,
                            'UTF-8'
                        ).'…'
                        : $description;

                $pozNo =
                    property_exists(
                        $row,
                        'poz_no'
                    )
                        ? (string) $row->poz_no
                        : '-';

                $unit =
                    property_exists(
                        $row,
                        'unit'
                    )
                        ? trim(
                            (string) $row->unit
                        )
                        : '';

                $institution =
                    property_exists(
                        $row,
                        'institution'
                    )
                        ? (string)
                            $row->institution
                        : 'Tender Poz';

                return [
                    'type' =>
                        'tender',

                    'key' =>
                        'tender_'.$pozNo,

                    'label' =>
                        $label,

                    'description' =>
                        $description,

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
                ];
            })
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

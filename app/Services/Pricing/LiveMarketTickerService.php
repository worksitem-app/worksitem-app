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
        /*
         * RESMI POZLAR CANLI PIYASA FIYATI DEGILDIR.
         *
         * Bu nedenle ticker'da:
         * - tender_pozs
         * - POZ x 1.60
         * - rastgele POZ secimi
         * kullanilmaz.
         *
         * Yalnizca dogrulanmis piyasa gozlemleri
         * material market engine uzerinden gosterilir.
         */
        return [];
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

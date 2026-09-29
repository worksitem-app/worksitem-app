<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ToolPriceService
{
    public function __construct(
        private readonly PriceService $prices
    ) {
    }


    /**
     * Araç için tanımlanmış tüm merkezi fiyatları döndürür.
     *
     * Desteklenen kaynaklar:
     *
     * market_key
     *   -> unit_prices / PriceService::byKey()
     *
     * official_poz
     *   -> tender_pozs / PriceService::byTenderPoz()
     */
    public function forTool(
        string $toolSlug
    ): Collection {

        if (
            !Schema::hasTable(
                'tool_price_bindings'
            )
        ) {
            return collect();
        }


        $bindings =
            DB::table(
                'tool_price_bindings'
            )
                ->where(
                    'tool_slug',
                    $toolSlug
                )
                ->orderBy('role')
                ->get();


        return $bindings
            ->mapWithKeys(
                function ($binding) {

                    $sourceType =
                        (string)
                        (
                            $binding->source_type
                            ?? 'market_key'
                        );


                    $record = null;


                    if (
                        $sourceType
                        === 'market_key'
                    ) {

                        $record =
                            $this->prices
                                ->byKey(
                                    (string)
                                    $binding->price_key
                                );
                    }


                    if (
                        $sourceType
                        === 'official_poz'
                    ) {

                        $record =
                            $this->prices
                                ->byTenderPoz(
                                    (string)
                                    $binding->poz_no,

                                    (string)
                                    $binding->institution,

                                    !empty(
                                        $binding->period
                                    )
                                        ? (string)
                                          $binding->period
                                        : null
                                );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Fail closed
                    |--------------------------------------------------------------------------
                    |
                    | Merkezi/onaylı fiyat bulunmuyorsa uydurma
                    | fiyat üretmeyiz.
                    |
                    */

                    if (!is_array($record)) {
                        return [];
                    }


                    $multiplier =
                        (float)
                        (
                            $binding->multiplier
                            ?? 1
                        );


                    if ($multiplier <= 0) {
                        return [];
                    }


                    $basePrice =
                        (float)
                        (
                            $record['price']
                            ?? 0
                        );


                    if ($basePrice <= 0) {
                        return [];
                    }


                    $resolvedPrice =
                        round(
                            $basePrice
                            *
                            $multiplier,
                            2
                        );


                    $result =
                        (object) [

                            'role' =>
                                $binding->role,

                            'source_type' =>
                                $sourceType,

                            'source_ref' =>
                                $binding->source_ref,

                            'price_key' =>
                                $binding->price_key,

                            'institution' =>
                                $binding->institution,

                            'poz_no' =>
                                $binding->poz_no,

                            'period' =>
                                $record['period']
                                ??
                                $binding->period,

                            'multiplier' =>
                                $multiplier,

                            'is_required' =>
                                (bool)
                                $binding->is_required,

                            'binding_notes' =>
                                $binding->notes,

                            'name' =>
                                $record['name']
                                ?? null,

                            'base_price' =>
                                round(
                                    $basePrice,
                                    2
                                ),

                            'price' =>
                                $resolvedPrice,

                            'unit' =>
                                $record['unit']
                                ?? null,

                            'source' =>
                                $record['source']
                                ?? null,

                            'source_name' =>
                                $record['source_name']
                                ?? null,

                            'source_date' =>
                                $record['source_date']
                                ?? null,

                            'vat_included' =>
                                $record['vat_included']
                                ?? null,

                            'review_status' =>
                                $record['review_status']
                                ?? null,

                            'profit_status' =>
                                $record['profit_status']
                                ?? null,
                        ];


                    return [
                        (string)
                        $binding->role
                            => $result,
                    ];
                }
            );
    }
}

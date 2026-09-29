<?php

namespace App\Services\Pricing;

use App\Services\PriceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class BuildingEstimateService
{
    public function __construct(
        private readonly PriceService $priceService
    ) {
    }

    public function estimate(
        string $buildingTypeKey,
        float $area
    ): array {

        $buildingTypeKey =
            trim($buildingTypeKey);

        if ($buildingTypeKey === '') {
            throw new RuntimeException(
                'Yapı türü seçilmedi.'
            );
        }

        if (
            $area <= 0 ||
            $area > 10000000
        ) {
            throw new RuntimeException(
                'Geçerli bir toplam inşaat alanı giriniz.'
            );
        }

        if (
            !Schema::hasTable('building_types') ||
            !Schema::hasTable('building_classes')
        ) {
            throw new RuntimeException(
                'Yapı sınıfı verisi bulunamadı.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 1. Yapı türünün resmî yapı sınıfını belirle
        |--------------------------------------------------------------------------
        |
        | Burada fiyat okunmaz.
        | building_types yalnız sınıflandırma kaynağıdır.
        |
        */

        $building = DB::table('building_types as bt')
            ->join(
                'building_classes as bc',
                'bc.id',
                '=',
                'bt.building_class_id'
            )
            ->where(
                'bt.key',
                $buildingTypeKey
            )
            ->where(
                'bt.is_active',
                true
            )
            ->where(
                'bc.is_active',
                true
            )
            ->select([
                'bt.key as building_type_key',
                'bt.name as building_type_name',
                'bt.category_key',
                'bt.category_name',

                'bc.id as building_class_id',
                'bc.code as class_code',
                'bc.name as class_name',
            ])
            ->first();

        if (!$building) {
            throw new RuntimeException(
                'Seçilen yapı türü için aktif yapı sınıfı bulunamadı.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 2. Fiyatı TEK merkezden çöz
        |--------------------------------------------------------------------------
        |
        | PriceService sırası:
        |
        | unit_prices / market / approved / active
        |                  ↓
        | building_class_costs resmî fallback
        |
        */

        $price = $this->priceService
            ->buildingClass(
                (string) $building->class_code,
                '2026'
            );

        if (
            !is_array($price) ||
            !isset($price['price']) ||
            (float) $price['price'] <= 0
        ) {
            throw new RuntimeException(
                'Seçilen yapı sınıfı için doğrulanmış birim fiyat bulunamadı.'
            );
        }

        $unitPrice =
            (float) $price['price'];

        $total = round(
            $area * $unitPrice,
            2
        );

        if ($total <= 0) {
            throw new RuntimeException(
                'Yapı maliyeti hesaplanamadı.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Tekrarlanabilir fiyat versiyonu
        |--------------------------------------------------------------------------
        */

        $versionSource = implode(
            '|',
            [
                'central-building-v1',

                (string) (
                    $price['period']
                    ?? '2026'
                ),

                (string)
                    $building->building_type_key,

                (string)
                    $building->class_code,

                number_format(
                    $unitPrice,
                    2,
                    '.',
                    ''
                ),

                (string) (
                    $price['resolution']
                    ?? ''
                ),

                (string) (
                    $price['source_name']
                    ?? ''
                ),

                (string) (
                    $price['source_date']
                    ?? ''
                ),

                (string) (
                    (bool) (
                        $price['vat_included']
                        ?? false
                    )
                ),
            ]
        );

        $priceVersion =
            'central-building-v1-'.
            substr(
                hash(
                    'sha256',
                    $versionSource
                ),
                0,
                16
            );


        /*
        |--------------------------------------------------------------------------
        | 4. Sonuç
        |--------------------------------------------------------------------------
        */

        return [
            'calculation_mode' =>
                'central_building_estimate',

            'building_type_key' =>
                $building->building_type_key,

            'building_type_name' =>
                $building->building_type_name,

            'category_key' =>
                $building->category_key,

            'category_name' =>
                $building->category_name,

            'building_class_id' =>
                $building->building_class_id,

            'class_code' =>
                $building->class_code,

            'class_name' =>
                $building->class_name,

            'area' =>
                round(
                    $area,
                    2
                ),

            'unit_price' =>
                round(
                    $unitPrice,
                    2
                ),

            'total' =>
                $total,

            'pricing' => [
                'key' =>
                    $price['key']
                    ?? null,

                'resolution' =>
                    $price['resolution']
                    ?? null,

                'source' =>
                    $price['source']
                    ?? null,

                'price_pool' =>
                    $price['price_pool']
                    ?? null,

                'review_status' =>
                    $price['review_status']
                    ?? null,
            ],

            'period' =>
                $price['period']
                ?? '2026',

            'source_name' =>
                $price['source_name']
                ?? null,

            'source_date' =>
                $price['source_date']
                ?? null,

            'vat_included' =>
                (bool) (
                    $price['vat_included']
                    ?? false
                ),

            'price_version' =>
                $priceVersion,
        ];
    }
}

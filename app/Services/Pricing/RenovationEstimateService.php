<?php

namespace App\Services\Pricing;

use App\Services\PriceService;
use RuntimeException;

class RenovationEstimateService
{
    private const MODEL_VERSION =
        'renovation-v2-central-settings';


    /*
    |--------------------------------------------------------------------------
    | Scope -> Merkezi fiyat anahtarı
    |--------------------------------------------------------------------------
    |
    | "light" eski UI alias'ıdır.
    | Server snapshot'ta canonical olarak "partial" tutulur.
    |
    */

    private const SCOPE_KEYS = [

        'partial' =>
            'renovation_partial_m2_v1',

        'medium' =>
            'renovation_medium_m2_v1',

        'complete' =>
            'renovation_complete_m2_v1',

        'turnkey' =>
            'renovation_complete_m2_v1',
    ];


    private const SCOPE_LABELS = [

        'partial' =>
            'Kısmi / Hafif Yenileme',

        'medium' =>
            'Orta Kapsamlı Tadilat',

        'complete' =>
            'Komple Tadilat',

        'turnkey' =>
            'Anahtar Teslim Tadilat',
    ];


    private const QUALITY_LABELS = [
        'standard' => 'Standart',
    ];


    public function __construct(
        private readonly PriceService $priceService,
        private readonly PricingSettingsService $settings
    ) {
    }


    public function estimate(
        string $scope,
        string $quality,
        float $area
    ): array {

        $scope =
            trim(
                strtolower(
                    $scope
                )
            );

        $quality =
            trim(
                strtolower(
                    $quality
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Legacy UI alias
        |--------------------------------------------------------------------------
        */

        if ($scope === 'light') {
            $scope = 'partial';
        }


        if (
            !array_key_exists(
                $scope,
                self::SCOPE_KEYS
            )
        ) {
            throw new RuntimeException(
                'Geçerli bir tadilat kapsamı seçiniz.'
            );
        }


        if (
            !array_key_exists(
                $quality,
                self::QUALITY_LABELS
            )
        ) {
            throw new RuntimeException(
                'Geçerli bir kalite seviyesi seçiniz.'
            );
        }


        if (
            $area <= 0 ||
            $area > 1000000
        ) {
            throw new RuntimeException(
                'Geçerli bir tadilat alanı giriniz.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Merkezi taban fiyat
        |--------------------------------------------------------------------------
        */

        $priceKey =
            self::SCOPE_KEYS[$scope];


        $price =
            $this->priceService
                ->byKey(
                    $priceKey
                );


        if (!is_array($price)) {
            throw new RuntimeException(
                'Seçilen tadilat kapsamı için onaylı birim fiyat bulunamadı.'
            );
        }


        $baseUnitPrice =
            (float) (
                $price['price']
                ?? 0
            );


        if ($baseUnitPrice <= 0) {
            throw new RuntimeException(
                'Tadilat birim fiyatı geçersiz.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Merkezi admin parametreleri
        |--------------------------------------------------------------------------
        */

        $qualityFactor =
            $this->settings
                ->requireDecimal(
                    'renovation.quality.'.
                    $quality
                );


        $rangeRate =
            $this->settings
                ->requireDecimal(
                    'renovation.range_rate'
                );


        $scopeFactor = 1.00;


        if ($scope === 'turnkey') {

            $scopeFactor =
                $this->settings
                    ->requireDecimal(
                        'renovation.scope.turnkey_factor'
                    );
        }


        if (
            $qualityFactor <= 0 ||
            $scopeFactor <= 0
        ) {
            throw new RuntimeException(
                'Tadilat fiyatlandırma katsayısı sıfırdan büyük olmalıdır.'
            );
        }


        if (
            $rangeRate < 0 ||
            $rangeRate >= 1
        ) {
            throw new RuntimeException(
                'Tadilat tahmin aralığı 0 ile 1 arasında olmalıdır.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Hesap
        |--------------------------------------------------------------------------
        */

        $adjustedUnitPrice = round(
            $baseUnitPrice
            *
            $qualityFactor
            *
            $scopeFactor,
            2
        );


        $total = round(
            $area
            *
            $adjustedUnitPrice,
            2
        );


        if ($total <= 0) {
            throw new RuntimeException(
                'Tadilat ön bütçesi hesaplanamadı.'
            );
        }


        $estimateLow = round(
            $total
            *
            (1 - $rangeRate),
            2
        );


        $estimateHigh = round(
            $total
            *
            (1 + $rangeRate),
            2
        );


        /*
        |--------------------------------------------------------------------------
        | Versiyon
        |--------------------------------------------------------------------------
        */

        $versionSource =
            implode(
                '|',
                [
                    self::MODEL_VERSION,

                    $priceKey,

                    number_format(
                        $baseUnitPrice,
                        2,
                        '.',
                        ''
                    ),

                    $scope,

                    $quality,

                    number_format(
                        $qualityFactor,
                        4,
                        '.',
                        ''
                    ),

                    number_format(
                        $scopeFactor,
                        4,
                        '.',
                        ''
                    ),

                    number_format(
                        $rangeRate,
                        4,
                        '.',
                        ''
                    ),
                ]
            );


        $priceVersion =
            self::MODEL_VERSION.
            '-'.
            substr(
                hash(
                    'sha256',
                    $versionSource
                ),
                0,
                16
            );


        return [

            'calculation_mode' =>
                'renovation_market_estimate',

            'scope' =>
                $scope,

            'scope_label' =>
                self::SCOPE_LABELS[$scope],

            'quality' =>
                $quality,

            'quality_label' =>
                self::QUALITY_LABELS[$quality],

            'area' =>
                round(
                    $area,
                    2
                ),

            'pricing' => [

                'price_key' =>
                    $priceKey,

                'base_unit_price' =>
                    round(
                        $baseUnitPrice,
                        2
                    ),

                'adjusted_unit_price' =>
                    $adjustedUnitPrice,
            ],

            'market_estimate' => [

                'total' =>
                    $total,

                'estimate_low' =>
                    $estimateLow,

                'estimate_high' =>
                    $estimateHigh,
            ],

            /*
            |--------------------------------------------------------------------------
            | Snapshot için; controller müşteriye açmıyor.
            |--------------------------------------------------------------------------
            */

            'internal' => [

                'quality_factor' =>
                    $qualityFactor,

                'scope_factor' =>
                    $scopeFactor,

                'range_rate' =>
                    $rangeRate,

                /*
                 * Şehir / durum / alan ölçeği CP-03D'de
                 * request context olarak server-side uygulanacak.
                 */
                'location_factor' =>
                    1.00,

                'condition_factor' =>
                    1.00,

                'scale_factor' =>
                    1.00,
            ],

            'vat_included' =>
                false,

            'model_version' =>
                self::MODEL_VERSION,

            'price_version' =>
                $priceVersion,
        ];
    }
}

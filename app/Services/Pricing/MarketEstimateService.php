<?php

namespace App\Services\Pricing;

use RuntimeException;

class MarketEstimateService
{
    private const MODEL_VERSION =
        'market-v2-central-settings';

    private const BREAKDOWN_MODEL_VERSION =
        'budget_breakdown_v2-central-settings';

    private const BREAKDOWN_LABELS = [
        'rough_works' =>
            'Kaba yapı / hazırlık',

        'finishing_works' =>
            'İnce işler',

        'mechanical' =>
            'Mekanik',

        'electrical' =>
            'Elektrik',
    ];


    public function __construct(
        private readonly PricingSettingsService $settings
    ) {
    }


    public function estimate(
        array $officialEstimate
    ): array {

        $officialTotal =
            (float) (
                $officialEstimate['total']
                ?? 0
            );

        if ($officialTotal <= 0) {
            throw new RuntimeException(
                'Geçerli resmî referans maliyeti bulunamadı.'
            );
        }


        $officialPriceVersion =
            trim(
                (string) (
                    $officialEstimate['price_version']
                    ?? ''
                )
            );

        if ($officialPriceVersion === '') {
            throw new RuntimeException(
                'Resmî fiyat sürümü bulunamadı.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Merkezi admin parametreleri
        |--------------------------------------------------------------------------
        */

        $marketFactor =
            $this->settings
                ->requireDecimal(
                    'building.market_factor'
                );

        $locationFactor =
            $this->settings
                ->requireDecimal(
                    'building.location_factor'
                );

        $projectFactor =
            $this->settings
                ->requireDecimal(
                    'building.project_factor'
                );

        $rangeRate =
            $this->settings
                ->requireDecimal(
                    'building.range_rate'
                );


        if (
            $marketFactor <= 0 ||
            $locationFactor <= 0 ||
            $projectFactor <= 0
        ) {
            throw new RuntimeException(
                'Yapı fiyatlandırma katsayıları sıfırdan büyük olmalıdır.'
            );
        }

        if (
            $rangeRate < 0 ||
            $rangeRate >= 1
        ) {
            throw new RuntimeException(
                'Yapı tahmin aralığı 0 ile 1 arasında olmalıdır.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Merkezi bütçe dağılım oranları
        |--------------------------------------------------------------------------
        */

        $breakdownRatios = [
            'rough_works' =>
                $this->settings
                    ->requireDecimal(
                        'building.breakdown.rough'
                    ),

            'finishing_works' =>
                $this->settings
                    ->requireDecimal(
                        'building.breakdown.finishing'
                    ),

            'mechanical' =>
                $this->settings
                    ->requireDecimal(
                        'building.breakdown.mechanical'
                    ),

            'electrical' =>
                $this->settings
                    ->requireDecimal(
                        'building.breakdown.electrical'
                    ),
        ];


        foreach (
            $breakdownRatios
            as $key => $ratio
        ) {
            if (
                $ratio < 0 ||
                $ratio > 1
            ) {
                throw new RuntimeException(
                    'Geçersiz bütçe dağılım oranı: '.
                    $key
                );
            }
        }


        $ratioSum =
            array_sum(
                $breakdownRatios
            );

        if (
            abs(
                $ratioSum - 1.0
            ) > 0.0001
        ) {
            throw new RuntimeException(
                'Yapı bütçe dağılım oranlarının toplamı 1.00 olmalıdır.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Hesap
        |--------------------------------------------------------------------------
        */

        $marketTotal = round(
            $officialTotal
            * $marketFactor
            * $locationFactor
            * $projectFactor,
            2
        );


        if ($marketTotal <= 0) {
            throw new RuntimeException(
                'Piyasa ön bütçesi hesaplanamadı.'
            );
        }


        $estimateLow = round(
            $marketTotal
            * (1 - $rangeRate),
            2
        );

        $estimateHigh = round(
            $marketTotal
            * (1 + $rangeRate),
            2
        );


        $budgetBreakdown =
            $this->buildBudgetBreakdown(
                $marketTotal,
                $breakdownRatios
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

                    $officialPriceVersion,

                    number_format(
                        $marketFactor,
                        4,
                        '.',
                        ''
                    ),

                    number_format(
                        $locationFactor,
                        4,
                        '.',
                        ''
                    ),

                    number_format(
                        $projectFactor,
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

                    json_encode(
                        $breakdownRatios,
                        JSON_UNESCAPED_UNICODE |
                        JSON_UNESCAPED_SLASHES
                    ),

                    self::BREAKDOWN_MODEL_VERSION,
                ]
            );


        $marketVersion =
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
                'worksitem_market_estimate',

            'official_reference' => [
                'total' =>
                    round(
                        $officialTotal,
                        2
                    ),

                'price_version' =>
                    $officialPriceVersion,
            ],

            'market' => [
                'total' =>
                    $marketTotal,

                'estimate_low' =>
                    $estimateLow,

                'estimate_high' =>
                    $estimateHigh,
            ],

            'budget_breakdown' =>
                $budgetBreakdown,

            /*
            |--------------------------------------------------------------------------
            | API controller bunu müşteriye açmıyor.
            |--------------------------------------------------------------------------
            */

            'internal' => [
                'market_factor' =>
                    $marketFactor,

                'location_factor' =>
                    $locationFactor,

                'project_factor' =>
                    $projectFactor,

                'range_rate' =>
                    $rangeRate,

                'breakdown_ratios' =>
                    $breakdownRatios,
            ],

            'model_version' =>
                self::MODEL_VERSION,

            'market_version' =>
                $marketVersion,
        ];
    }


    private function buildBudgetBreakdown(
        float $marketTotal,
        array $ratios
    ): array {

        if ($marketTotal <= 0) {
            throw new RuntimeException(
                'Bütçe dağılımı için geçerli toplam bulunamadı.'
            );
        }


        $items = [];
        $allocated = 0.0;

        $keys =
            array_keys(
                $ratios
            );

        $lastKey =
            end($keys);


        foreach (
            $ratios
            as $key => $ratio
        ) {

            if (
                !array_key_exists(
                    $key,
                    self::BREAKDOWN_LABELS
                )
            ) {
                throw new RuntimeException(
                    'Bilinmeyen bütçe dağılım kalemi: '.
                    $key
                );
            }


            if ($key === $lastKey) {

                $amount = round(
                    $marketTotal
                    -
                    $allocated,
                    2
                );

            } else {

                $amount = round(
                    $marketTotal
                    *
                    $ratio,
                    2
                );

                $allocated = round(
                    $allocated
                    +
                    $amount,
                    2
                );
            }


            $items[] = [
                'key' =>
                    $key,

                'label' =>
                    self::BREAKDOWN_LABELS[$key],

                'ratio' =>
                    $ratio,

                'percentage' =>
                    round(
                        $ratio * 100,
                        2
                    ),

                'amount' =>
                    $amount,
            ];
        }


        $sum = round(
            array_sum(
                array_column(
                    $items,
                    'amount'
                )
            ),
            2
        );


        if (
            abs(
                $sum
                -
                $marketTotal
            ) > 0.001
        ) {
            throw new RuntimeException(
                'Bütçe dağılımı toplamı doğrulanamadı.'
            );
        }


        return [
            'model' =>
                self::BREAKDOWN_MODEL_VERSION,

            'items' =>
                $items,

            'total' =>
                $sum,
        ];
    }
}

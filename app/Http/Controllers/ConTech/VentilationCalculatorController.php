<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use App\Services\PriceService;

class VentilationCalculatorController extends Controller
{
    public function __construct(
        private readonly PriceService $prices
    ) {}

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | HAVALANDIRMA V2
        |--------------------------------------------------------------------------
        |
        | Controller içinde birim fiyat tutulmaz.
        | Yalnızca doğrulanmış poz eşleşmeleri tutulur.
        | Fiyat PriceService -> rayic_prices üzerinden gelir.
        |
        */

        $catalog = [

            'rectangular' => [

                [
                    'label' => 'Dikdörtgen Galvaniz Kanal · ≤ 600 mm · 0,60 mm',
                    'poz' => '25.470.1101',
                    'max_mm' => 600,
                ],

                [
                    'label' => 'Dikdörtgen Galvaniz Kanal · ≤ 1249 mm · 0,80 mm',
                    'poz' => '25.470.1102',
                    'max_mm' => 1249,
                ],

                [
                    'label' => 'Dikdörtgen Galvaniz Kanal · ≤ 2490 mm · 1,00 mm',
                    'poz' => '25.470.1103',
                    'max_mm' => 2490,
                ],

                [
                    'label' => 'Dikdörtgen Galvaniz Kanal · > 2490 mm · 1,20 mm',
                    'poz' => '25.470.1104',
                    'max_mm' => 999999,
                ],

            ],


            'spiral' => [

                [
                    'label' => 'Spiral Galvaniz Kanal · Ø160 mm · 0,50 mm',
                    'poz' => '25.470.1201',
                    'max_mm' => 160,
                ],

                [
                    'label' => 'Spiral Galvaniz Kanal · Ø315 mm · 0,60 mm',
                    'poz' => '25.470.1202',
                    'max_mm' => 315,
                ],

                [
                    'label' => 'Spiral Galvaniz Kanal · Ø800 mm · 0,80 mm',
                    'poz' => '25.470.1203',
                    'max_mm' => 800,
                ],

                [
                    'label' => 'Spiral Galvaniz Kanal · Ø1000 mm · 1,00 mm',
                    'poz' => '25.470.1204',
                    'max_mm' => 1000,
                ],

                [
                    'label' => 'Spiral Galvaniz Kanal · Ø1500 mm · 1,20 mm',
                    'poz' => '25.470.1205',
                    'max_mm' => 1500,
                ],

            ],


            'fire' => [

                [
                    'key' => 'ei60',
                    'label' => 'Duman Atım Kanalı · EI 60',
                    'poz' => '25.470.1580',
                ],

                [
                    'key' => 'ei120',
                    'label' => 'Duman Atım Kanalı · EI 120',
                    'poz' => '25.470.1590',
                ],

            ],


            'insulation' => [

                [
                    'key' => 'glass25',
                    'label' => '2,5 cm Camyünü · 50 kg/m³',
                    'poz' => '25.480.1301',
                ],

                [
                    'key' => 'glass50',
                    'label' => '5 cm Camyünü · 50 kg/m³',
                    'poz' => '25.480.1302',
                ],

                [
                    'key' => 'rock25',
                    'label' => '2,5 cm Taşyünü · 70 kg/m³',
                    'poz' => '25.480.1303',
                ],

                [
                    'key' => 'rock50',
                    'label' => '5 cm Taşyünü · 70 kg/m³',
                    'poz' => '25.480.1304',
                ],

                [
                    'key' => 'rubber13',
                    'label' => '13 mm Kauçuk Köpüğü',
                    'poz' => '25.480.1502',
                ],

                [
                    'key' => 'rubber19',
                    'label' => '19 mm Kauçuk Köpüğü',
                    'poz' => '25.480.1503',
                ],

            ],


            'grilles' => [

                [
                    'key' => 'supply_small',
                    'label' => 'Dağıtıcı Menfez · 100–500 cm²',
                    'poz' => '25.475.1101',
                ],

                [
                    'key' => 'supply_medium',
                    'label' => 'Dağıtıcı Menfez · 1001–1600 cm²',
                    'poz' => '25.475.1103',
                ],

                [
                    'key' => 'supply_large',
                    'label' => 'Dağıtıcı Menfez · 3601–4500 cm²',
                    'poz' => '25.475.1106',
                ],

                [
                    'key' => 'return_small',
                    'label' => 'Toplayıcı Menfez · 100–500 cm²',
                    'poz' => '25.475.1201',
                ],

                [
                    'key' => 'return_medium',
                    'label' => 'Toplayıcı Menfez · 1001–1600 cm²',
                    'poz' => '25.475.1203',
                ],

                [
                    'key' => 'return_large',
                    'label' => 'Toplayıcı Menfez · 3601–4500 cm²',
                    'poz' => '25.475.1206',
                ],

            ],


            'slots' => [

                [
                    'key' => 'slot1',
                    'label' => '1 Slotlu Lineer Difüzör',
                    'poz' => '25.475.7101',
                ],

                [
                    'key' => 'slot2',
                    'label' => '2 Slotlu Lineer Difüzör',
                    'poz' => '25.475.7102',
                ],

                [
                    'key' => 'slot3',
                    'label' => '3 Slotlu Lineer Difüzör',
                    'poz' => '25.475.7103',
                ],

                [
                    'key' => 'slot4',
                    'label' => '4 Slotlu Lineer Difüzör',
                    'poz' => '25.475.7104',
                ],

            ],


            'fans' => [

                [
                    'key' => 'fan100',
                    'label' => 'Kanal Tipi Aspiratör · 100 m³/h',
                    'poz' => '25.450.5101',
                ],

                [
                    'key' => 'fan400',
                    'label' => 'Kanal Tipi Aspiratör · 400 m³/h',
                    'poz' => '25.450.5104',
                ],

                [
                    'key' => 'fan1000',
                    'label' => 'Kanal Tipi Aspiratör · 1000 m³/h',
                    'poz' => '25.450.5107',
                ],

                [
                    'key' => 'fan2000',
                    'label' => 'Kanal Tipi Aspiratör · 2000 m³/h',
                    'poz' => '25.450.5111',
                ],

                [
                    'key' => 'fan2500',
                    'label' => 'Kanal Tipi Aspiratör · 2500 m³/h',
                    'poz' => '25.450.5112',
                ],

            ],

        ];


        // v119: fiyat dönemi sabit '2026-08' değil, hesap motorlarıyla aynı YFK dönemi
        // (config/worksitem_estimators.php → yfk_period; dönem değişince kalemler "Fiyat yok"a düşmesin)
        $period = (string) config('worksitem_estimators.yfk_period', '2026-08');

        foreach ($catalog as &$items) {

            foreach ($items as &$item) {

                $record = $this->prices->byPoz(
                    $item['poz'],
                    $period,
                    'YFK'
                );

                $found = is_array($record)
                    && isset($record['price'])
                    && is_numeric($record['price']);

                $item['price_found'] = $found;

                $item['unit_price'] = $found
                    ? (float) $record['price']
                    : null;

                $item['unit'] = $record['unit'] ?? null;

                $item['price_period'] = $found
                    ? $period
                    : null;
            }

            unset($item);
        }

        unset($items);


        return view('tools.havalandirma-calculator', [
            'catalog' => $catalog,
        ]);
    }
}

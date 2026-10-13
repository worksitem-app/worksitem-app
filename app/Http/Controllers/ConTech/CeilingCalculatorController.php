<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use App\Services\PriceService;

class CeilingCalculatorController extends Controller
{
    public function __construct(
        private readonly PriceService $prices
    ) {}

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | ASMA TAVAN SİSTEM KATALOĞU
        |--------------------------------------------------------------------------
        |
        | Burada fiyat YOKTUR.
        | Fiyat yalnızca PriceService üzerinden rayic_prices tablosundan gelir.
        |
        */

        $systems = static::catalog();

        foreach ($systems as &$system) {

            $record = null;

            if (!empty($system['poz'])) {
                $record = $this->prices->byPoz(
                    $system['poz'],
                    '2026-08',
                    'YFK'
                );
            }

            $found = is_array($record)
                && isset($record['price'])
                && is_numeric($record['price']);

            $system['price_found'] = $found;

            $system['unit_price'] = $found
                ? (float) $record['price']
                : null;

            $system['unit'] = $record['unit'] ?? 'm²';

            $system['price_source'] = $found
                ? ($record['source'] ?? 'rayic_prices')
                : null;

            $system['price_period'] = $found
                ? '2026-08'
                : null;
        }

        unset($system);

        return view('tools.asma-tavan-calculator', [
            'systems' => $systems,
        ]);
    }

    /** Sistem / katman kataloğu (fiyat içermez; hesap motoru da kullanır) */
    public static function catalog(): array
    {
        return [

            'alcipan' => [
                'label' => 'Alçıpan Asma Tavan',
                'poz' => null,
                'panel_area' => null,
                'source_page' => null,
                'note' => 'Metraj hesaplanır. Doğrulanmış asma tavan birim fiyatı henüz sisteme bağlanmadı.',
            ],

            'alu60_sarkmali' => [
                'label' => '60x60 Alüminyum Sarkmalı Sistem',
                'poz' => '15.535.1013',
                'panel_area' => 0.36,
                'source_page' => 56,
                'note' => 'YFK sarkmalı sistem referansı.',
            ],

            'galv60_sarkmali' => [
                'label' => '60x60 Galvaniz Sac Sarkmalı Sistem',
                'poz' => '15.535.1022',
                'panel_area' => 0.36,
                'source_page' => 57,
                'note' => 'YFK sarkmalı galvaniz sistem referansı.',
            ],

            'alu60_gizli' => [
                'label' => '60x60 Alüminyum Gizli Taşıyıcılı',
                'poz' => '15.535.1025',
                'panel_area' => 0.36,
                'source_page' => 57,
                'note' => 'YFK gizli taşıyıcılı sistem referansı.',
            ],

            'alu30_sarkmali' => [
                'label' => '30x30 Alüminyum Sarkmalı Sistem',
                'poz' => '15.535.1016',
                'panel_area' => 0.09,
                'source_page' => 56,
                'note' => 'YFK 30x30 sarkmalı sistem referansı.',
            ],

            'alu30_gizli' => [
                'label' => '30x30 Alüminyum Gizli Taşıyıcılı',
                'poz' => '15.535.1028',
                'panel_area' => 0.09,
                'source_page' => 57,
                'note' => 'YFK 30x30 gizli taşıyıcılı sistem referansı.',
            ],

            'galv60_gizli' => [
                'label' => '60x60 Galvaniz Sac Gizli Taşıyıcılı',
                'poz' => '15.535.1033',
                'panel_area' => 0.36,
                'source_page' => 57,
                'note' => 'YFK gizli taşıyıcılı galvaniz sistem referansı.',
            ],

            'lamel85' => [
                'label' => '85 mm Alüminyum Lamel Tavan',
                'poz' => '15.535.1036',
                'panel_area' => null,
                'source_page' => 57,
                'note' => '85 mm lamel, 15 mm derz çıtalı sistem.',
            ],

            'lamel100' => [
                'label' => '100 mm Alüminyum Lamel Tavan',
                'poz' => '15.535.1038',
                'panel_area' => null,
                'source_page' => 57,
                'note' => '100 mm lamel, 20 mm derz çıtalı sistem.',
            ],

            'pvc60' => [
                'label' => '60x60 PVC Asma Tavan',
                'poz' => '15.535.1051',
                'panel_area' => 0.36,
                'source_page' => 57,
                'note' => 'YFK sert PVC asma tavan karosu referansı.',
            ],

            'baffle' => [
                'label' => 'Baffle Tavan',
                'poz' => null,
                'panel_area' => null,
                'source_page' => null,
                'note' => 'Metraj hesaplanır. Doğrulanmış fiyat bulunmadığı için fiyat üretilmez.',
            ],

            'petek' => [
                'label' => 'Petek / Open Cell Tavan',
                'poz' => null,
                'panel_area' => null,
                'source_page' => null,
                'note' => 'Metraj hesaplanır. Doğrulanmış fiyat bulunmadığı için fiyat üretilmez.',
            ],
        ];
    }
}

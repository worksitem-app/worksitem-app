<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use App\Services\PriceService;

class EpoxyFloorCalculatorController extends Controller
{
    public function __construct(
        private readonly PriceService $prices
    ) {}

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | EPOKSI ZEMIN KATMANLARI
        |--------------------------------------------------------------------------
        |
        | Controller içinde hiçbir birim fiyat tutulmaz.
        | Fiyatlar yalnız PriceService -> rayic_prices üzerinden okunur.
        |
        */

        $layers = [

            'leveling' => [
                'label' => 'Zemin Tesviyesi · Self Leveling 2 mm',
                'poz' => '15.190.1007',
                'source_page' => 10,
                'required' => false,
                'note' => 'Çimento esaslı kendiliğinden yerleşen harç ile ortalama 2 mm zemin tesviyesi.',
            ],

            'epoxy' => [
                'label' => 'Self-Leveling Epoksi Zemin · 2,5 mm',
                'poz' => '15.190.1014',
                'source_page' => 10,
                'required' => true,
                'note' => '2,5 mm kalınlıkta self-leveling epoksi esaslı zemin kaplaması.',
            ],

            'uv_topcoat' => [
                'label' => 'UV Dayanımlı Poliüretan Son Kat',
                'poz' => '15.190.1017',
                'source_page' => 10,
                'required' => false,
                'note' => 'Epoksi kaplama üzerine UV dayanımlı, renkli, elastik, mat poliüretan son kat.',
            ],
        ];

        foreach ($layers as &$layer) {

            $record = $this->prices->byPoz(
                $layer['poz'],
                '2026-08',
                'YFK'
            );

            $found = is_array($record)
                && isset($record['price'])
                && is_numeric($record['price']);

            $layer['price_found'] = $found;

            $layer['unit_price'] = $found
                ? (float) $record['price']
                : null;

            $layer['unit'] = $record['unit'] ?? 'm²';

            $layer['price_source'] = $found
                ? ($record['source'] ?? 'rayic_prices')
                : null;

            $layer['price_period'] = $found
                ? '2026-08'
                : null;
        }

        unset($layer);

        return view('tools.epoksi-zemin-calculator', [
            'layers' => $layers,
        ]);
    }
}

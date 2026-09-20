<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use App\Services\PriceService;

class RaisedFloorCalculatorController extends Controller
{
    public function __construct(
        private readonly PriceService $prices
    ) {}

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | YUKSELTILMIS DOSEME ALT YALITIMLARI
        |--------------------------------------------------------------------------
        |
        | Ana panel + ayak + stringer sisteminin doğrulanmış fiyatı henüz yok.
        | Bu nedenle ana sistem için fiyat üretilmez.
        |
        | Yalıtım fiyatları yalnız PriceService üzerinden gelir.
        |
        */

        $insulations = [

            'none' => [
                'label' => 'Yalıtım Yok',
                'poz' => null,
                'source_page' => null,
                'note' => 'Alt yalıtım maliyeti eklenmez.',
            ],

            'rock25_high' => [
                'label' => '2,5 cm Taşyünü · λ 0,030–0,035',
                'poz' => '15.341.3061',
                'source_page' => 29,
                'note' => 'Yüke maruz kalmayan yatay uygulamalar ve yükseltilmiş döşeme uygulamaları için.',
            ],

            'rock25_standard' => [
                'label' => '2,5 cm Taşyünü · λ 0,035–0,040',
                'poz' => '15.341.3062',
                'source_page' => 29,
                'note' => 'Yüke maruz kalmayan yatay uygulamalar ve yükseltilmiş döşeme uygulamaları için.',
            ],

            'rock60_a080' => [
                'label' => '6 cm Taşyünü · Akustik ≥ 0,80',
                'poz' => '15.341.3081',
                'source_page' => 29,
                'note' => 'Yükseltilmiş döşemelerde ısı ve ses yalıtımı.',
            ],

            'rock60_a090' => [
                'label' => '6 cm Taşyünü · Akustik ≥ 0,90',
                'poz' => '15.341.3082',
                'source_page' => 29,
                'note' => 'Yükseltilmiş döşemelerde ısı ve ses yalıtımı.',
            ],

            'rock60_a100' => [
                'label' => '6 cm Taşyünü · Akustik ≥ 1,00',
                'poz' => '15.341.3083',
                'source_page' => 29,
                'note' => 'Yükseltilmiş döşemelerde ısı ve ses yalıtımı.',
            ],

            'glass_blanket_a080' => [
                'label' => '6 cm Camyünü Şilte · Akustik ≥ 0,80',
                'poz' => '15.341.4021',
                'source_page' => 29,
                'note' => 'Yükseltilmiş döşemelerde ısı ve ses yalıtımı.',
            ],

            'glass_blanket_a090' => [
                'label' => '6 cm Camyünü Şilte · Akustik ≥ 0,90',
                'poz' => '15.341.4022',
                'source_page' => 30,
                'note' => 'Yükseltilmiş döşemelerde ısı ve ses yalıtımı.',
            ],

            'glass_blanket_a100' => [
                'label' => '6 cm Camyünü Şilte · Akustik ≥ 1,00',
                'poz' => '15.341.4023',
                'source_page' => 30,
                'note' => 'Yükseltilmiş döşemelerde ısı ve ses yalıtımı.',
            ],

            'glass_board_a080' => [
                'label' => '6 cm Camyünü Levha · Akustik ≥ 0,80',
                'poz' => '15.341.4041',
                'source_page' => 30,
                'note' => 'Yükseltilmiş döşemelerde ısı ve ses yalıtımı.',
            ],
        ];


        foreach ($insulations as &$insulation) {

            $record = null;

            if (!empty($insulation['poz'])) {

                $record = $this->prices->byPoz(
                    $insulation['poz'],
                    '2026-08',
                    'YFK'
                );
            }

            $found = is_array($record)
                && isset($record['price'])
                && is_numeric($record['price']);

            $insulation['price_found'] = $found;

            $insulation['unit_price'] = $found
                ? (float) $record['price']
                : null;

            $insulation['unit'] = $record['unit'] ?? 'm²';

            $insulation['price_source'] = $found
                ? ($record['source'] ?? 'rayic_prices')
                : null;

            $insulation['price_period'] = $found
                ? '2026-08'
                : null;
        }

        unset($insulation);


        return view('tools.yukseltilmis-doseme-calculator', [
            'insulations' => $insulations,
        ]);
    }
}

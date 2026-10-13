<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use App\Services\PriceService;

class SandwichPanelCalculatorController extends Controller
{
    public function __construct(
        private readonly PriceService $prices
    ) {}

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | SANDVIC PANEL SISTEM KATALOĞU
        |--------------------------------------------------------------------------
        |
        | Çatı sistemlerindeki fiyat PriceService -> rayic_prices'tan gelir.
        |
        | Mevcut doğrulanmış YFK poz açıklamaları çatı örtüsü uygulamasını
        | tarif ettiği için cephe sistemlerine çatı fiyatı atanmaz.
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

        return view('tools.sandvic-panel-calculator', [
            'systems' => $systems,
        ]);
    }

    /** Sistem / katman kataloğu (fiyat içermez; hesap motoru da kullanır) */
    public static function catalog(): array
    {
        return [

            'roof_pu50' => [
                'label' => 'Çatı · 50 mm Poliüretan · Çift Yüz Galvaniz Sac',
                'application' => 'roof',
                'poz' => '15.320.1001',
                'source_page' => 24,
                'note' => 'Mevcut ahşap, betonarme veya çelik aşıklar üzerine 50 mm poliüretan yalıtımlı çatı paneli.',
            ],

            'roof_pu50_pvc' => [
                'label' => 'Çatı · 50 mm Poliüretan · PVC Membranlı',
                'application' => 'roof',
                'poz' => '15.320.1002',
                'source_page' => 24,
                'note' => '50 mm poliüretan yalıtımlı, üst yüz PVC membranlı çatı paneli.',
            ],

            'roof_pu50_tpo' => [
                'label' => 'Çatı · 50 mm Poliüretan · TPO Membranlı',
                'application' => 'roof',
                'poz' => '15.320.1003',
                'source_page' => 24,
                'note' => '50 mm poliüretan yalıtımlı, üst yüz TPO membranlı çatı paneli.',
            ],

            'roof_pir50' => [
                'label' => 'Çatı · 50 mm PIR · Çift Yüz Galvaniz Sac',
                'application' => 'roof',
                'poz' => '15.320.1004',
                'source_page' => 24,
                'note' => '50 mm poliizosiyanurat (PIR) yalıtımlı çatı paneli.',
            ],

            'roof_eps60' => [
                'label' => 'Çatı · 60 mm Polistren / EPS · Galvaniz Sac',
                'application' => 'roof',
                'poz' => '15.320.1008',
                'source_page' => 24,
                'note' => '60 mm polistren yalıtımlı çatı paneli.',
            ],

            'roof_rock60' => [
                'label' => 'Çatı · 60 mm Taşyünü · Çift Yüz Galvaniz Sac',
                'application' => 'roof',
                'poz' => '15.320.1010',
                'source_page' => 24,
                'note' => 'Mevcut çelik aşıklar üzerine 60 mm taşyünü yalıtımlı çatı paneli.',
            ],

            'roof_rock60_tpo' => [
                'label' => 'Çatı · 60 mm Taşyünü · TPO Membranlı',
                'application' => 'roof',
                'poz' => '15.320.1012',
                'source_page' => 24,
                'note' => '60 mm taşyünü yalıtımlı, üst yüz TPO membranlı çatı paneli.',
            ],

            'roof_hybrid_tpo' => [
                'label' => 'Çatı · 50 mm Taşyünü + 25 mm PU · TPO Membranlı',
                'application' => 'roof',
                'poz' => '15.320.1015',
                'source_page' => 24,
                'note' => 'Taşyünü + poliüretan hibrit yalıtımlı, üst yüz TPO membranlı çatı paneli.',
            ],


            /*
            |--------------------------------------------------------------------------
            | CEPHE
            |--------------------------------------------------------------------------
            |
            | Metraj yapılır.
            | Doğrulanmış cephe poz/fiyat bulunana kadar fiyat üretilmez.
            |
            */

            'facade_pu' => [
                'label' => 'Cephe · Poliüretan Sandviç Panel',
                'application' => 'facade',
                'poz' => null,
                'source_page' => null,
                'note' => 'Cephe metrajı hesaplanır. Doğrulanmış cephe birim fiyatı henüz bağlanmadı.',
            ],

            'facade_pir' => [
                'label' => 'Cephe · PIR Sandviç Panel',
                'application' => 'facade',
                'poz' => null,
                'source_page' => null,
                'note' => 'Cephe metrajı hesaplanır. Doğrulanmış cephe birim fiyatı henüz bağlanmadı.',
            ],

            'facade_eps' => [
                'label' => 'Cephe · EPS Sandviç Panel',
                'application' => 'facade',
                'poz' => null,
                'source_page' => null,
                'note' => 'Cephe metrajı hesaplanır. Doğrulanmış cephe birim fiyatı henüz bağlanmadı.',
            ],

            'facade_rock' => [
                'label' => 'Cephe · Taşyünü Sandviç Panel',
                'application' => 'facade',
                'poz' => null,
                'source_page' => null,
                'note' => 'Cephe metrajı hesaplanır. Doğrulanmış cephe birim fiyatı henüz bağlanmadı.',
            ],
        ];
    }
}

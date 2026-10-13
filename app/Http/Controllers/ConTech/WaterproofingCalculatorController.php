<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use App\Services\PriceService;

class WaterproofingCalculatorController extends Controller
{
    public function __construct(
        private readonly PriceService $prices
    ) {}

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | SU YALITIMI SISTEM KATALOĞU
        |--------------------------------------------------------------------------
        |
        | Burada fiyat tutulmaz.
        | Birim fiyat yalnız PriceService üzerinden okunur.
        |
        */

        $systems = static::catalog();

        foreach ($systems as &$system) {

            $record = $this->prices->byPoz(
                $system['poz'],
                '2026-08',
                'YFK'
            );

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

        return view('tools.su-yalitimi-calculator', [
            'systems' => $systems,
        ]);
    }

    /** Sistem / katman kataloğu (fiyat içermez; hesap motoru da kullanır) */
    public static function catalog(): array
    {
        return [

            'cement_2k' => [
                'label' => 'Çimento Esaslı İki Bileşenli · 2 Kat / 1,5 mm',
                'poz' => '15.270.1005',
                'source_page' => 21,
                'applications' => [
                    'teras',
                    'balkon',
                    'banyo',
                    'bodrum',
                    'perde',
                    'otopark',
                ],
                'note' => 'İki bileşenli polimer modifiyeli sürme esaslı su yalıtım sistemi.',
            ],

            'cement_2k_mesh' => [
                'label' => 'File Takviyeli Çimento Esaslı · 2 Kat / 1,5 mm',
                'poz' => '15.270.1006',
                'source_page' => 21,
                'applications' => [
                    'teras',
                    'balkon',
                    'banyo',
                    'bodrum',
                    'perde',
                    'otopark',
                ],
                'note' => 'File takviyeli iki bileşenli polimer modifiyeli sistem.',
            ],

            'cement_3coat_mesh' => [
                'label' => 'File Takviyeli Çimento Esaslı · 3 Kat / 2 mm',
                'poz' => '15.270.1008',
                'source_page' => 21,
                'applications' => [
                    'teras',
                    'balkon',
                    'banyo',
                    'bodrum',
                    'perde',
                    'otopark',
                ],
                'note' => 'Daha yüksek toplam kalınlığa sahip file takviyeli sistem.',
            ],

            'crystalline' => [
                'label' => 'Kristalize Su Yalıtımı · 2 Kat / 1,5 mm',
                'poz' => '15.270.1009',
                'source_page' => 21,
                'applications' => [
                    'temel',
                    'bodrum',
                    'perde',
                    'otopark',
                ],
                'note' => 'Çimento esaslı tek bileşenli kristalize yalıtım sistemi.',
            ],

            'crystalline_mesh' => [
                'label' => 'File Takviyeli Kristalize · 3 Kat / 2 mm',
                'poz' => '15.270.1012',
                'source_page' => 21,
                'applications' => [
                    'temel',
                    'bodrum',
                    'perde',
                    'otopark',
                ],
                'note' => 'File takviyeli kristalize su yalıtım sistemi.',
            ],

            'hybrid_polyurea' => [
                'label' => 'Hibrit Poliürea · 2 mm',
                'poz' => '15.270.1101',
                'source_page' => 21,
                'applications' => [
                    'teras',
                    'balkon',
                    'otopark',
                    'fabrika',
                ],
                'note' => 'Hibrit poliürea bazlı iki bileşenli sistem.',
            ],

            'pure_polyurea' => [
                'label' => '%100 Saf Poliürea · 2 mm',
                'poz' => '15.270.1111',
                'source_page' => 21,
                'applications' => [
                    'teras',
                    'otopark',
                    'fabrika',
                ],
                'note' => '%100 saf poliürea bazlı iki bileşenli sistem.',
            ],

            'geosynthetic_clay' => [
                'label' => 'Temel Geosentetik Kil Örtü',
                'poz' => '15.270.1202',
                'source_page' => 21,
                'applications' => [
                    'temel',
                    'bodrum',
                ],
                'note' => 'Bina temellerinde geosentetik kil örtü sistemi.',
            ],

            'pvc_membrane' => [
                'label' => 'PVC Geomembran · 2 mm · UV Dayanımlı',
                'poz' => '15.260.1004',
                'source_page' => 20,
                'applications' => [
                    'teras',
                    'cati',
                    'temel',
                ],
                'note' => 'PVC esaslı UV dayanımlı donatılı geomembran.',
            ],

            'hdpe_membrane' => [
                'label' => 'HDPE Geomembran · 2 mm',
                'poz' => '15.260.1006',
                'source_page' => 21,
                'applications' => [
                    'temel',
                    'bodrum',
                    'perde',
                ],
                'note' => 'HDPE esaslı geomembran sistemi.',
            ],

            'tpo_membrane' => [
                'label' => 'TPO Geomembran · 2 mm · UV Dayanımlı',
                'poz' => '15.260.1014',
                'source_page' => 21,
                'applications' => [
                    'teras',
                    'cati',
                ],
                'note' => 'TPO esaslı UV dayanımlı donatılı geomembran.',
            ],

            'roof_breathable' => [
                'label' => 'Eğimli Çatı Altı Nefes Alan Su Yalıtım Örtüsü',
                'poz' => '15.330.1003',
                'source_page' => 25,
                'applications' => [
                    'egimli_cati',
                ],
                'note' => 'Su buharı geçişine açık çatı altı su yalıtım örtüsü.',
            ],

            'roof_bituminous' => [
                'label' => 'Eğimli Çatı Altı 3 mm Bitümlü Örtü',
                'poz' => '15.330.1005',
                'source_page' => 25,
                'applications' => [
                    'egimli_cati',
                ],
                'note' => 'Polyester keçe taşıyıcılı plastomer esaslı polimer bitümlü örtü.',
            ],
        ];
    }
}

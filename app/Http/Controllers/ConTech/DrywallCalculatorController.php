<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use App\Services\PriceService;

class DrywallCalculatorController extends Controller
{
    public function __construct(
        private readonly PriceService $prices
    ) {}

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | ALCIPAN V2
        |--------------------------------------------------------------------------
        |
        | Burada fiyat YOK.
        | Controller yalnız poz eşlemesini bilir.
        | Fiyat PriceService -> rayic_prices üzerinden gelir.
        |
        */

        $systems = [

            /*
            |--------------------------------------------------------------------------
            | BÖLME DUVAR - C50
            |--------------------------------------------------------------------------
            */

            'partition_c50_standard' => [
                'application' => 'partition',
                'profile' => 'C50',
                'board' => 'standard',
                'label' => 'C50 · Standart 12,5 mm',
                'poz' => '15.530.1251',
                'source_page' => 48,
                'note' => 'C50 profil, 60 cm aks, her iki yüzde tek kat 12,5 mm standart alçı levha.',
            ],

            'partition_c50_water' => [
                'application' => 'partition',
                'profile' => 'C50',
                'board' => 'water',
                'label' => 'C50 · Suya Dayanıklı 12,5 mm',
                'poz' => '15.530.1252',
                'source_page' => 48,
                'note' => 'C50 profil, 60 cm aks, her iki yüzde tek kat su emme oranı azaltılmış alçı levha.',
            ],

            'partition_c50_fire' => [
                'application' => 'partition',
                'profile' => 'C50',
                'board' => 'fire',
                'label' => 'C50 · Yangına Dayanıklı 12,5 mm',
                'poz' => '15.530.1253',
                'source_page' => 49,
                'note' => 'C50 profil, 60 cm aks, her iki yüzde tek kat yangına dayanımı artırılmış alçı levha.',
            ],

            'partition_c50_water_fire' => [
                'application' => 'partition',
                'profile' => 'C50',
                'board' => 'water_fire',
                'label' => 'C50 · Su + Yangın Dayanımlı 12,5 mm',
                'poz' => '15.530.1254',
                'source_page' => 49,
                'note' => 'C50 profil, 60 cm aks, her iki yüzde tek kat su ve yangın dayanımı artırılmış alçı levha.',
            ],


            /*
            |--------------------------------------------------------------------------
            | BÖLME DUVAR - C75
            |--------------------------------------------------------------------------
            */

            'partition_c75_standard' => [
                'application' => 'partition',
                'profile' => 'C75',
                'board' => 'standard',
                'label' => 'C75 · Standart 12,5 mm',
                'poz' => '15.530.1259',
                'source_page' => 49,
                'note' => 'C75 profil, 60 cm aks, her iki yüzde tek kat 12,5 mm standart alçı levha.',
            ],

            'partition_c75_water' => [
                'application' => 'partition',
                'profile' => 'C75',
                'board' => 'water',
                'label' => 'C75 · Suya Dayanıklı 12,5 mm',
                'poz' => '15.530.1260',
                'source_page' => 49,
                'note' => 'C75 profil, 60 cm aks, her iki yüzde tek kat su emme oranı azaltılmış alçı levha.',
            ],

            'partition_c75_fire' => [
                'application' => 'partition',
                'profile' => 'C75',
                'board' => 'fire',
                'label' => 'C75 · Yangına Dayanıklı 12,5 mm',
                'poz' => '15.530.1261',
                'source_page' => 49,
                'note' => 'C75 profil, 60 cm aks, her iki yüzde tek kat yangına dayanımı artırılmış alçı levha.',
            ],

            'partition_c75_water_fire' => [
                'application' => 'partition',
                'profile' => 'C75',
                'board' => 'water_fire',
                'label' => 'C75 · Su + Yangın Dayanımlı 12,5 mm',
                'poz' => '15.530.1262',
                'source_page' => 49,
                'note' => 'C75 profil, 60 cm aks, her iki yüzde tek kat su ve yangın dayanımı artırılmış alçı levha.',
            ],


            /*
            |--------------------------------------------------------------------------
            | BÖLME DUVAR - C100
            |--------------------------------------------------------------------------
            */

            'partition_c100_standard' => [
                'application' => 'partition',
                'profile' => 'C100',
                'board' => 'standard',
                'label' => 'C100 · Standart 12,5 mm',
                'poz' => '15.530.1267',
                'source_page' => 49,
                'note' => 'C100 profil, 60 cm aks, her iki yüzde tek kat 12,5 mm standart alçı levha.',
            ],

            'partition_c100_water' => [
                'application' => 'partition',
                'profile' => 'C100',
                'board' => 'water',
                'label' => 'C100 · Suya Dayanıklı 12,5 mm',
                'poz' => '15.530.1268',
                'source_page' => 49,
                'note' => 'C100 profil, 60 cm aks, her iki yüzde tek kat su emme oranı azaltılmış alçı levha.',
            ],

            'partition_c100_fire' => [
                'application' => 'partition',
                'profile' => 'C100',
                'board' => 'fire',
                'label' => 'C100 · Yangına Dayanıklı 12,5 mm',
                'poz' => '15.530.1269',
                'source_page' => 49,
                'note' => 'C100 profil, 60 cm aks, her iki yüzde tek kat yangına dayanımı artırılmış alçı levha.',
            ],

            'partition_c100_water_fire' => [
                'application' => 'partition',
                'profile' => 'C100',
                'board' => 'water_fire',
                'label' => 'C100 · Su + Yangın Dayanımlı 12,5 mm',
                'poz' => '15.530.1270',
                'source_page' => 49,
                'note' => 'C100 profil, 60 cm aks, her iki yüzde tek kat su ve yangın dayanımı artırılmış alçı levha.',
            ],


            /*
            |--------------------------------------------------------------------------
            | GIYDIRME - YAPIŞTIRMA
            |--------------------------------------------------------------------------
            */

            'lining_glue_standard' => [
                'application' => 'lining_glue',
                'profile' => null,
                'board' => 'standard',
                'label' => 'Yapıştırma · Standart 12,5 mm',
                'poz' => '15.530.1701',
                'source_page' => 53,
                'note' => 'Yapıştırma yöntemiyle tek kat standart alçı levha giydirme duvar.',
            ],

            'lining_glue_water' => [
                'application' => 'lining_glue',
                'profile' => null,
                'board' => 'water',
                'label' => 'Yapıştırma · Suya Dayanıklı 12,5 mm',
                'poz' => '15.530.1702',
                'source_page' => 53,
                'note' => 'Yapıştırma yöntemiyle su emme oranı azaltılmış alçı levha giydirme duvar.',
            ],

            'lining_glue_fire' => [
                'application' => 'lining_glue',
                'profile' => null,
                'board' => 'fire',
                'label' => 'Yapıştırma · Yangına Dayanıklı 12,5 mm',
                'poz' => '15.530.1703',
                'source_page' => 53,
                'note' => 'Yapıştırma yöntemiyle yangına dayanımı artırılmış alçı levha giydirme duvar.',
            ],

            'lining_glue_water_fire' => [
                'application' => 'lining_glue',
                'profile' => null,
                'board' => 'water_fire',
                'label' => 'Yapıştırma · Su + Yangın Dayanımlı 12,5 mm',
                'poz' => '15.530.1704',
                'source_page' => 53,
                'note' => 'Yapıştırma yöntemiyle su ve yangın dayanımı artırılmış alçı levha giydirme duvar.',
            ],


            /*
            |--------------------------------------------------------------------------
            | GIYDIRME - METAL İSKELET
            |--------------------------------------------------------------------------
            */

            'lining_frame_standard' => [
                'application' => 'lining_frame',
                'profile' => 'TC60',
                'board' => 'standard',
                'label' => 'Metal İskelet · Standart 12,5 mm',
                'poz' => '15.530.1726',
                'source_page' => 53,
                'note' => 'Tek Tavan C60 profil, 60 cm aks, tek kat standart alçı levha giydirme duvar.',
            ],

            'lining_frame_water' => [
                'application' => 'lining_frame',
                'profile' => 'TC60',
                'board' => 'water',
                'label' => 'Metal İskelet · Suya Dayanıklı 12,5 mm',
                'poz' => '15.530.1727',
                'source_page' => 53,
                'note' => 'Tek Tavan C60 profil, 60 cm aks, su emme oranı azaltılmış alçı levha giydirme duvar.',
            ],

            'lining_frame_fire' => [
                'application' => 'lining_frame',
                'profile' => 'TC60',
                'board' => 'fire',
                'label' => 'Metal İskelet · Yangına Dayanıklı 12,5 mm',
                'poz' => '15.530.1728',
                'source_page' => 53,
                'note' => 'Tek Tavan C60 profil, 60 cm aks, yangına dayanımı artırılmış alçı levha giydirme duvar.',
            ],

            'lining_frame_water_fire' => [
                'application' => 'lining_frame',
                'profile' => 'TC60',
                'board' => 'water_fire',
                'label' => 'Metal İskelet · Su + Yangın Dayanımlı 12,5 mm',
                'poz' => '15.530.1729',
                'source_page' => 53,
                'note' => 'Tek Tavan C60 profil, 60 cm aks, su ve yangın dayanımı artırılmış alçı levha giydirme duvar.',
            ],
        ];


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

            $system['price_period'] = $found
                ? '2026-08'
                : null;

            $system['price_source'] = $found
                ? ($record['source'] ?? 'rayic_prices')
                : null;
        }

        unset($system);


        return view('tools.alcipan-tavan-calculator', [
            'systems' => $systems,
        ]);
    }
}

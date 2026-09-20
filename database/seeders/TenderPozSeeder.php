<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TenderPoz;

class TenderPozSeeder extends Seeder
{
    public function run()
    {
        $pozs = [
            ['year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => '15.150.1001', 'description' => 'C16/20 Hazır Beton Dökülmesi (Pompa ile)', 'unit' => 'm³', 'unit_price' => 3650.00],
            ['year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => '15.150.1003', 'description' => 'C25/30 Hazır Beton Dökülmesi (Pompa ile)', 'unit' => 'm³', 'unit_price' => 4046.71],
            ['year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => '15.150.1005', 'description' => 'C30/37 Hazır Beton Dökülmesi (Pompa ile)', 'unit' => 'm³', 'unit_price' => 4320.50],
            ['year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => '15.160.1002', 'description' => 'S420 Nervürlü İnşaat Demiri Bükülmesi ve Yerine Konması', 'unit' => 'Ton', 'unit_price' => 51322.40],
            ['year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => '15.180.1001', 'description' => 'Ahşap Seri Kalıp Yapılması', 'unit' => 'm²', 'unit_price' => 997.21],
            ['year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => '15.180.1003', 'description' => 'Plywood Düz Yüzeyli Betonarme Kalıbı Yapılması', 'unit' => 'm²', 'unit_price' => 1175.39],
            ['year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => '15.270.1001', 'description' => 'Makine ile İç Cephe Perlitli Alçı Sıva Yapılması', 'unit' => 'm²', 'unit_price' => 255.40],
            ['year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => '15.280.1012', 'description' => 'Su Bazlı Plastik Boya Yapılması (İç Cephe)', 'unit' => 'm²', 'unit_price' => 247.23],
            ['year' => '2026', 'institution' => 'Karayolları', 'poz_no' => 'KGY-01', 'description' => 'Asfalt Kaplama ve BİTÜM Yapılması', 'unit' => 'Ton', 'unit_price' => 18500.00],
            ['year' => '2026', 'institution' => 'İller Bankası', 'poz_no' => 'ILB-45', 'description' => 'Ø300 mm Betonarme Altyapı Borusu Döşenmesi', 'unit' => 'm', 'unit_price' => 1250.00],
            ['year' => '2026', 'institution' => 'Özel', 'poz_no' => 'KNT-M01', 'description' => 'Kainta Özel Lüks Mutfak Dolabı (Akrilik Kapak)', 'unit' => 'm.tül', 'unit_price' => 14500.00],
        ];

        foreach ($pozs as $poz) {
            TenderPoz::updateOrCreate(['poz_no' => $poz['poz_no']], $poz);
        }
    }
}

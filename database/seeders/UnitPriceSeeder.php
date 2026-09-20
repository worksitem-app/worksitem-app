<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitPrice;

class UnitPriceSeeder extends Seeder
{
    public function run()
    {
        $prices = [
            // Kaba İnşaat
            ['category' => 'demir', 'key' => 'demir_nervurlu_ton', 'name' => 'Nervürlü İnşaat Demiri', 'price' => 51322.40, 'unit' => 'TL/Ton'],
            ['category' => 'beton', 'key' => 'beton_c20_25_m3', 'name' => 'C20/25 Hazır Beton', 'price' => 3903.64, 'unit' => 'TL/m3'],
            ['category' => 'beton', 'key' => 'beton_c25_30_m3', 'name' => 'C25/30 Hazır Beton', 'price' => 4046.71, 'unit' => 'TL/m3'],
            ['category' => 'kalip', 'key' => 'kalip_ahsap_m2', 'name' => 'Ahşap Kalıp', 'price' => 997.21, 'unit' => 'TL/m2'],
            ['category' => 'kalip', 'key' => 'kalip_plywood_m2', 'name' => 'Plywood Kalıp', 'price' => 1175.39, 'unit' => 'TL/m2'],
            ['category' => 'hafriyat', 'key' => 'hafriyat_derin_kazi_m3', 'name' => 'Derin Kazı ve Hafriyat', 'price' => 92.06, 'unit' => 'TL/m3'],
            ['category' => 'duvar', 'key' => 'duvar_tugla_13_5_m2', 'name' => '13.5 cm Tuğla Duvar', 'price' => 908.65, 'unit' => 'TL/m2'],
            ['category' => 'duvar', 'key' => 'duvar_gazbeton_10_m2', 'name' => '10 cm Gazbeton', 'price' => 825.20, 'unit' => 'TL/m2'],
            
            // İnce İşler ve Kaplama
            ['category' => 'boya', 'key' => 'boya_ekonomik_m2', 'name' => 'Ekonomik Sınıf Boya', 'price' => 150.00, 'unit' => 'TL/m2'],
            ['category' => 'boya', 'key' => 'boya_standart_m2', 'name' => 'Standart Sınıf Boya', 'price' => 247.23, 'unit' => 'TL/m2'],
            ['category' => 'boya', 'key' => 'boya_premium_m2', 'name' => 'Premium Sınıf Boya', 'price' => 427.79, 'unit' => 'TL/m2'],
            ['category' => 'siva', 'key' => 'siva_kara_siva_m2', 'name' => 'İç/Dış Kara Sıva', 'price' => 255.40, 'unit' => 'TL/m2'],
            ['category' => 'alcipan', 'key' => 'alcipan_tek_iskelet_duvar_m2', 'name' => 'Alçıpan Bölme Duvar', 'price' => 1200.18, 'unit' => 'TL/m2'],
            ['category' => 'seramik', 'key' => 'seramik_standart_duvar_m2', 'name' => 'Standart Duvar Seramiği', 'price' => 956.65, 'unit' => 'TL/m2'],
            ['category' => 'seramik', 'key' => 'seramik_silli_porselen_m2', 'name' => 'Porselen Zemin Seramiği', 'price' => 1041.36, 'unit' => 'TL/m2'],
            ['category' => 'parke', 'key' => 'parke_laminat_ac3_m2', 'name' => 'AC3 Laminat Parke', 'price' => 696.43, 'unit' => 'TL/m2'],
            
            // Tesisat & Yalıtım & Diğer
            ['category' => 'mantolama', 'key' => 'mantolama_eps_5cm_m2', 'name' => '5cm EPS Mantolama', 'price' => 1203.59, 'unit' => 'TL/m2'],
            ['category' => 'mantolama', 'key' => 'mantolama_tasyunu_5cm_m2', 'name' => '5cm Taşyünü Mantolama', 'price' => 1431.65, 'unit' => 'TL/m2'],
            ['category' => 'tesisat', 'key' => 'tesisat_pprc_boru_m', 'name' => 'PPRC Temiz Su Borusu', 'price' => 45.50, 'unit' => 'TL/m'],
            ['category' => 'enerji', 'key' => 'enerji_ges_kw', 'name' => 'GES Kurulumu (1 kW)', 'price' => 25000.00, 'unit' => 'TL/kW'],
            ['category' => 'harc', 'key' => 'harc_cimento_50kg', 'name' => 'Çimento (50kg Torba)', 'price' => 220.00, 'unit' => 'TL/Adet'],
            
            // Proje Bazlı (Bina Maliyetleri)
            ['category' => 'bina', 'key' => 'bina_sinif_3b_m2', 'name' => '3B Sınıfı Bina Maliyeti', 'price' => 16000.00, 'unit' => 'TL/m2'],
            ['category' => 'bina', 'key' => 'bina_sinif_4a_m2', 'name' => '4A Sınıfı Bina Maliyeti', 'price' => 21050.00, 'unit' => 'TL/m2'],
        ];

        foreach ($prices as $price) {
            UnitPrice::updateOrCreate(
                ['key' => $price['key']],
                $price
            );
        }
    }
}

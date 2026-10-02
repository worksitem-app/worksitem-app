<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ─── Yapı piyasa katsayıları ───
            ['building.market_factor', 'Yapı Piyasa Çarpanı', '1.60',
             'Resmî yapı birim maliyeti üzerine uygulanan piyasa çarpanı.'],
            ['building.location_factor', 'Konum Çarpanı', '1.00',
             'Şehir/bölge bazlı konum çarpanı.'],
            ['building.project_factor', 'Proje Tipi Çarpanı', '1.00',
             'Proje türüne göre düzeltme çarpanı.'],
            ['building.range_rate', 'Yapı Tahmin Aralığı', '0.10',
             'Yapı tahmininde ± belirsizlik aralığı (0-1).'],
            ['building.breakdown.rough', 'Kaba Yapı Oranı', '0.34',
             'Bütçe dağılımı - kaba yapı / hazırlık.'],
            ['building.breakdown.finishing', 'İnce İşler Oranı', '0.27',
             'Bütçe dağılımı - ince işler.'],
            ['building.breakdown.mechanical', 'Mekanik Oranı', '0.21',
             'Bütçe dağılımı - mekanik tesisat.'],
            ['building.breakdown.electrical', 'Elektrik Oranı', '0.18',
             'Bütçe dağılımı - elektrik tesisatı.'],

            // ─── Tadilat katsayıları ───
            ['renovation.quality.standard', 'Tadilat Standart Kalite Çarpanı', '1.00',
             'Standart kalite için tadilat birim fiyat çarpanı.'],
            ['renovation.range_rate', 'Tadilat Tahmin Aralığı', '0.20',
             'Tadilat tahmininde ± belirsizlik aralığı (0-1).'],
            ['renovation.scope.turnkey_factor', 'Anahtar Teslim Tadilat Çarpanı', '1.15',
             'Turnkey (anahtar teslim) kapsam için ek çarpan.'],

            // ─── İhale ───
            ['tender_target_margin', 'İhale Hedef Marjı', '0.60',
             'İhale teklif fiyatı hedef kâr marjı (0-1).'],
        ];

        foreach ($settings as [$key, $label, $value, $description]) {
            DB::table('pricing_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'label' => $label,
                    'value' => $value,
                    'type' => 'decimal',
                    'description' => $description,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('Pricing settings seeded: ' . count($settings) . ' keys');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RenovationPriceSeeder extends Seeder
{
    public function run(): void
    {
        $source = 'Worksitem 2026 iç piyasa referansı';
        $sourceDate = '2026-09-15';
        $now = now();

        $prices = [
            // Tadilat — kapsam bazlı
            [
                'category' => 'renovation',
                'key' => 'renovation_partial_m2_v1',
                'name' => 'Kısmi / Hafif Tadilat — Birim Fiyat (V1)',
                'price' => 6500.00,
                'unit' => 'TL/m2',
                'notes' => 'Banyo, mutfak veya tek oda yenileme. Kaba yapıya dokunulmaz.',
            ],
            [
                'category' => 'renovation',
                'key' => 'renovation_medium_m2_v1',
                'name' => 'Orta Kapsamlı Tadilat — Birim Fiyat (V1)',
                'price' => 9500.00,
                'unit' => 'TL/m2',
                'notes' => 'Elektrik + su tesisatı kısmi, zemin + duvar + boya yenileme.',
            ],
            [
                'category' => 'renovation',
                'key' => 'renovation_complete_m2_v1',
                'name' => 'Komple Tadilat — Birim Fiyat (V1)',
                'price' => 13500.00,
                'unit' => 'TL/m2',
                'notes' => 'Komple tesisat yenileme, tüm yüzeyler, mutfak + banyo dahil.',
            ],
            [
                'category' => 'renovation',
                'key' => 'renovation_turnkey_m2_v1',
                'name' => 'Anahtar Teslim Tadilat — Birim Fiyat (V1)',
                'price' => 15500.00,
                'unit' => 'TL/m2',
                'notes' => 'Mobilya ve beyaz eşya dahil anahtar teslim.',
            ],

            // Homepage legacy
            [
                'category' => 'tadilat',
                'key' => 'tadilat_standart_m2',
                'name' => 'Tadilat Standart (legacy)',
                'price' => 9500.00,
                'unit' => 'TL/m2',
                'notes' => 'Anasayfa tadilat ön bütçesi için legacy anahtar.',
            ],
            [
                'category' => 'tadilat',
                'key' => 'tadilat_komple_m2',
                'name' => 'Tadilat Komple (legacy)',
                'price' => 13500.00,
                'unit' => 'TL/m2',
                'notes' => 'Komple tadilat legacy anahtarı.',
            ],

            // AI Maliyet Tahmin
            [
                'category' => 'siva',
                'key' => 'siva_alci_siva_m2',
                'name' => 'Alçı Sıva',
                'price' => 180.00,
                'unit' => 'TL/m2',
                'notes' => 'AI iç mekan tahmini için kullanılır.',
            ],
            [
                'category' => 'iscilik',
                'key' => 'iscilik_usta_yevmiye',
                'name' => 'Usta Yevmiyesi',
                'price' => 3500.00,
                'unit' => 'TL/gun',
                'notes' => 'Günlük usta ücreti (2026 ortalama).',
            ],
        ];

        foreach ($prices as $row) {
            DB::table('unit_prices')->updateOrInsert(
                ['key' => $row['key']],
                [
                    'category' => $row['category'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'unit' => $row['unit'],
                    'price_pool' => 'market',
                    'source_name' => $source,
                    'source_date' => $sourceDate,
                    'vat_included' => false,
                    'is_active' => true,
                    'review_status' => 'approved',
                    'notes' => $row['notes'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $this->command->info('Renovation prices seeded: ' . count($prices) . ' keys');
    }
}

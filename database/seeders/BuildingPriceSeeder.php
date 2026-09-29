<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BuildingPriceSeeder extends Seeder
{
    public function run(): void
    {
        $sourceName = 'ÇŞİDB — 2026 Yapı Yaklaşık Birim Maliyetleri';
        $sourceDate = '2026-02-03';
        $period = '2026';

        $prices = [
            '1A' => 2600.00,
            '1B' => 3900.00,
            '1C' => 4200.00,
            '1D' => 4800.00,

            '2A' => 8100.00,
            '2B' => 12500.00,
            '2C' => 15100.00,

            '3A' => 19800.00,
            '3B' => 21050.00,
            '3C' => 23400.00,

            '4A' => 26450.00,
            '4B' => 33900.00,
            '4C' => 40500.00,

            '5A' => 42350.00,
            '5B' => 43850.00,
            '5C' => 48750.00,
            '5D' => 53500.00,
            '5E' => 103500.00,
        ];

        DB::transaction(function () use (
            $prices,
            $sourceName,
            $sourceDate,
            $period
        ) {
            foreach ($prices as $code => $unitPrice) {
                DB::table('building_classes')->updateOrInsert(
                    ['code' => $code],
                    [
                        'name' => $code . ' Yapı Sınıfı',
                        'unit' => 'TL/m2',
                        'description' => '2026 resmî yapı yaklaşık birim maliyeti sınıfı',
                        'source_name' => $sourceName,
                        'source_date' => $sourceDate,
                        'is_active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $classId = DB::table('building_classes')
                    ->where('code', $code)
                    ->value('id');

                if (!$classId) {
                    throw new RuntimeException(
                        'Yapı sınıfı oluşturulamadı: ' . $code
                    );
                }

                DB::table('building_class_costs')->updateOrInsert(
                    [
                        'building_class_id' => $classId,
                        'period' => $period,
                    ],
                    [
                        'unit_price' => $unitPrice,
                        'unit' => 'TL/m2',
                        'source_name' => $sourceName,
                        'source_date' => $sourceDate,
                        'vat_included' => false,
                        'is_active' => true,
                        'notes' => 'Resmî yaklaşık yapı birim maliyeti referansıdır; piyasa anahtar teslim fiyatı değildir.',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $rows = [
            [
                'category' => 'seramik_sarf',
                'key' => 'tile_adhesive_standard_25kg',
                'name' => 'Kalekim 1052 Standart Seramik Yapıştırma Harcı 25 kg',
                'price' => 362.90,
                'unit' => 'TL/torba',
                'price_pool' => 'market',
                'source_name' => 'Taşçı Yapı Market',
                'source_date' => '2026-10-06',
                'vat_included' => 1,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Standart seramik yapıştırıcısı 25 kg perakende referansı. C2TE/porselen yapıştırıcısı ile karıştırılmaz.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category' => 'seramik_sarf',
                'key' => 'tile_grout_5kg',
                'name' => 'Mapei Ultracolor Plus Derz Dolgusu 5 kg',
                'price' => 285.70,
                'unit' => 'TL/paket',
                'price_pool' => 'market',
                'source_name' => 'Banyona',
                'source_date' => '2026-10-06',
                'vat_included' => 1,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Canlı ürün sayfasından doğrulanmış perakende referans fiyatı.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category' => 'seramik_sarf',
                'key' => 'tile_profile_outside_aluminum_8mm_270cm',
                'name' => '8 mm Seramik Dış Köşe Profili Alüminyum Mat 2,70 m',
                'price' => 145.86,
                'unit' => 'TL/boy',
                'price_pool' => 'market',
                'source_name' => 'Kozmos',
                'source_date' => '2026-10-06',
                'vat_included' => 1,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => '2,70 m alüminyum mat dış köşe profili; canlı ürün listesinden doğrulandı.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category' => 'seramik_sarf',
                'key' => 'tile_profile_outside_aluminum_10mm_270cm',
                'name' => '10 mm Seramik Dış Köşe Profili Alüminyum Mat 2,70 m',
                'price' => 166.06,
                'unit' => 'TL/boy',
                'price_pool' => 'market',
                'source_name' => 'Kozmos',
                'source_date' => '2026-10-06',
                'vat_included' => 1,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => '2,70 m alüminyum mat dış köşe profili; canlı ürün listesinden doğrulandı.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category' => 'seramik_sarf',
                'key' => 'tile_profile_outside_aluminum_12mm_270cm',
                'name' => '12 mm Seramik Dış Köşe Profili Alüminyum Mat 2,70 m',
                'price' => 148.10,
                'unit' => 'TL/boy',
                'price_pool' => 'market',
                'source_name' => 'Kozmos',
                'source_date' => '2026-10-06',
                'vat_included' => 1,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => '2,70 m alüminyum mat dış köşe profili; canlı ürün listesinden doğrulandı.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('unit_prices')->upsert(
            $rows,
            ['key'],
            [
                'category',
                'name',
                'price',
                'unit',
                'price_pool',
                'source_name',
                'source_date',
                'vat_included',
                'is_active',
                'review_status',
                'notes',
                'updated_at',
            ]
        );
    }

    public function down(): void
    {
        DB::table('unit_prices')
            ->whereIn('key', [
                'tile_adhesive_standard_25kg',
                'tile_grout_5kg',
                'tile_profile_outside_aluminum_8mm_270cm',
                'tile_profile_outside_aluminum_10mm_270cm',
                'tile_profile_outside_aluminum_12mm_270cm',
            ])
            ->delete();
    }
};

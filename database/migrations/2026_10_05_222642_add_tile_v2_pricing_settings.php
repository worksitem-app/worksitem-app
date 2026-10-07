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
                'key' => 'tile_labor_standard_m2',
                'label' => 'Seramik işçilik ≤500 m²',
                'value' => '450',
                'type' => 'decimal',
                'description' => 'Kainta İnşaat saha maliyet kuralı; 500 m² ve altı işçilik TL/m².',
            ],
            [
                'key' => 'tile_large_threshold',
                'label' => 'Seramik büyük metraj eşiği',
                'value' => '500',
                'type' => 'decimal',
                'description' => 'Bu alanın üzerinde eşleşen resmi uygulama pozu kullanılır.',
            ],
            [
                'key' => 'tile_large_official_multiplier',
                'label' => 'Seramik büyük metraj poz çarpanı',
                'value' => '1.60',
                'type' => 'decimal',
                'description' => '500 m² üzeri eşleşen ÇŞİDB/YFK uygulama pozuna uygulanan ticari katsayı.',
            ],
            [
                'key' => 'tile_price_review_threshold_pct',
                'label' => 'Seramik piyasa fiyatı mühendis kontrol eşiği',
                'value' => '10',
                'type' => 'decimal',
                'description' => 'İnternet benchmark fiyatı mevcut merkezi fiyattan bu orandan fazla saparsa otomatik uygulanmaz.',
            ],
            [
                'key' => 'tile_market_max_age_hours',
                'label' => 'Seramik piyasa verisi azami yaşı',
                'value' => '24',
                'type' => 'decimal',
                'description' => 'Bu süreden eski internet fiyat araştırması kullanıcıya güncel veri olarak sunulmaz.',
            ],
        ];

        foreach ($rows as $row) {
            DB::table('pricing_settings')->updateOrInsert(
                ['key' => $row['key']],
                array_merge($row, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('pricing_settings')
            ->whereIn('key', [
                'tile_labor_standard_m2',
                'tile_large_threshold',
                'tile_large_official_multiplier',
                'tile_price_review_threshold_pct',
                'tile_market_max_age_hours',
            ])
            ->delete();
    }
};

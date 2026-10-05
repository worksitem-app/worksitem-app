<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | BOYA V2 PRICING SETTINGS
        |--------------------------------------------------------------------------
        */

        $settings = [
            [
                'key' => 'paint_labor_standard_m2',
                'label' => 'Boya işçilik ≤500 m²',
                'value' => '250',
                'type' => 'decimal',
                'description' => 'Kaynak: Kainta İnşaat saha mühendisleri; 2026-10-03. Boya V2 saha referansı.',
            ],
            [
                'key' => 'paint_labor_large_threshold',
                'label' => 'Büyük metraj eşiği',
                'value' => '500',
                'type' => 'decimal',
                'description' => 'Kaynak: Kainta İnşaat saha mühendisleri; 2026-10-03. Boya V2 saha referansı.',
            ],
            [
                'key' => 'paint_labor_large_min_m2',
                'label' => 'Büyük metraj alt saha bandı',
                'value' => '150',
                'type' => 'decimal',
                'description' => 'Kaynak: Kainta İnşaat saha mühendisleri; 2026-10-03. Boya V2 saha referansı.',
            ],
            [
                'key' => 'paint_labor_large_base_m2',
                'label' => 'Büyük metraj hesap baz fiyatı',
                'value' => '175',
                'type' => 'decimal',
                'description' => 'Kaynak: Kainta İnşaat saha mühendisleri; 2026-10-03. Boya V2 saha referansı.',
            ],
            [
                'key' => 'paint_labor_large_max_m2',
                'label' => 'Büyük metraj üst saha bandı',
                'value' => '200',
                'type' => 'decimal',
                'description' => 'Kaynak: Kainta İnşaat saha mühendisleri; 2026-10-03. Boya V2 saha referansı.',
            ],
            [
                'key' => 'paint_budget_tolerance',
                'label' => 'Tahmini bütçe toleransı',
                'value' => '0.10',
                'type' => 'decimal',
                'description' => 'Kaynak: Kainta İnşaat saha mühendisleri; 2026-10-03. Boya V2 saha referansı.',
            ],
        ];

        foreach ($settings as $row) {
            DB::table('pricing_settings')->updateOrInsert(
                ['key' => $row['key']],
                array_merge($row, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BOYA V2 MARKET UNIT PRICES
        |--------------------------------------------------------------------------
        */

        $prices = [
            [
                'category' => 'boya',
                'key' => 'paint_wall_standard_15l',
                'name' => 'İç Cephe Boyası 15 L — Piyasa Referansı',
                'price' => 4395.00,
                'unit' => 'TL/15L',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus 4.190 TL + Koçtaş 4.600 TL benchmark',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Geçici perakende benchmark; günlük market araştırması devreye girince otomatik güncellenecek.',
            ],
            [
                'category' => 'boya',
                'key' => 'paint_ceiling_10l',
                'name' => 'Tavan Boyası 10 L — Piyasa Referansı',
                'price' => 990.00,
                'unit' => 'TL/10L',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus — Polisan Elegans Extra',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Geçici perakende referansı.',
            ],
            [
                'category' => 'boya',
                'key' => 'paint_primer_15l',
                'name' => 'Geçiş Astarı 15 L',
                'price' => 2990.00,
                'unit' => 'TL/15L',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus — Dyo Geçiş Astarı',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Geçici perakende referansı.',
            ],
            [
                'category' => 'boya_sarf',
                'key' => 'paint_cover_10m2',
                'name' => 'Koruma Örtüsü 10 m²',
                'price' => 49.90,
                'unit' => 'TL/10m2',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus — Dizayn',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Zemin koruma örtüsü.',
            ],
            [
                'category' => 'boya_sarf',
                'key' => 'paint_masking_tape_25m',
                'name' => 'Maskeleme Bandı 25 m',
                'price' => 59.90,
                'unit' => 'TL/rulo',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus — Vodabond 48 mm x 25 m',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => '25 metre/rulo.',
            ],
            [
                'category' => 'boya_sarf',
                'key' => 'paint_roller_25cm',
                'name' => 'İç Cephe Boya Rulosu 25 cm',
                'price' => 169.00,
                'unit' => 'TL/adet',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus / Koçtaş — Rulon',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Her iki markette görülen perakende referansı.',
            ],
            [
                'category' => 'boya_sarf',
                'key' => 'paint_brush',
                'name' => 'Kestirme Fırça',
                'price' => 109.00,
                'unit' => 'TL/adet',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus — Kaptan No:5',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Geçici perakende referansı.',
            ],
            [
                'category' => 'boya_sarf',
                'key' => 'paint_tray',
                'name' => 'Profesyonel Boya Tavası',
                'price' => 179.00,
                'unit' => 'TL/adet',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus — Dizayn 30x43',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Geçici perakende referansı.',
            ],
            [
                'category' => 'boya_sarf',
                'key' => 'paint_sandpaper_sheet',
                'name' => 'Zımpara Kağıdı',
                'price' => 30.00,
                'unit' => 'TL/adet',
                'price_pool' => 'market',
                'source_name' => 'Bauhaus — Kum 320',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Bilgi amaçlı; otomatik toplam maliyete henüz katılmayacak.',
            ],
            [
                'category' => 'boya_sarf',
                'key' => 'paint_wall_putty_4_5kg',
                'name' => 'Duvar Macunu 4,5 kg',
                'price' => 459.90,
                'unit' => 'TL/4.5kg',
                'price_pool' => 'market',
                'source_name' => 'Koçtaş — Dyorit',
                'source_date' => '2026-10-03',
                'vat_included' => 0,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Bilgi amaçlı; yüzey hazırlık sarfiyatı doğrulanana kadar toplam maliyete katılmaz.',
            ],
        ];

        foreach ($prices as $row) {
            DB::table('unit_prices')->updateOrInsert(
                ['key' => $row['key']],
                array_merge($row, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BOYA V2 TOOL PRICE BINDINGS
        |--------------------------------------------------------------------------
        |
        | Ticari multiplier yalnız backend verisidir.
        |
        */

        $bindings = [
            [
                'role' => 'labor_reference',
                'source_type' => 'official_poz',
                'source_ref' => 'ÇŞİDB/YFK:10.100.1023',
                'price_key' => 'official_10_100_1023',
                'institution' => 'ÇŞİDB/YFK',
                'poz_no' => '10.100.1023',
                'period' => null,
                'multiplier' => 1.0000,
                'is_required' => 0,
                'notes' => 'Boyacı ustası resmi saatlik referans; m² işçilik hesabında doğrudan kullanılmaz.',
            ],
            [
                'role' => 'scaffold_3_51',
                'source_type' => 'official_poz',
                'source_ref' => 'ÇŞİDB/YFK:15.185.1021',
                'price_key' => 'official_15_185_1021',
                'institution' => 'ÇŞİDB/YFK',
                'poz_no' => '15.185.1021',
                'period' => null,
                'multiplier' => 1.6000,
                'is_required' => 0,
                'notes' => 'Dış cephe erişim fiyatlama kuralı.',
            ],
            [
                'role' => 'scaffold_51_plus',
                'source_type' => 'official_poz',
                'source_ref' => 'ÇŞİDB/YFK:15.185.1022',
                'price_key' => 'official_15_185_1022',
                'institution' => 'ÇŞİDB/YFK',
                'poz_no' => '15.185.1022',
                'period' => null,
                'multiplier' => 1.6000,
                'is_required' => 0,
                'notes' => 'Dış cephe erişim fiyatlama kuralı.',
            ],
        ];

        $marketBindings = [
            'wall_paint' => 'paint_wall_standard_15l',
            'ceiling_paint' => 'paint_ceiling_10l',
            'primer' => 'paint_primer_15l',
            'cover' => 'paint_cover_10m2',
            'masking_tape' => 'paint_masking_tape_25m',
            'roller' => 'paint_roller_25cm',
            'brush' => 'paint_brush',
            'tray' => 'paint_tray',
            'sandpaper' => 'paint_sandpaper_sheet',
            'putty' => 'paint_wall_putty_4_5kg',
        ];

        foreach ($marketBindings as $role => $key) {
            $bindings[] = [
                'role' => $role,
                'source_type' => 'market_key',
                'source_ref' => $key,
                'price_key' => $key,
                'institution' => null,
                'poz_no' => null,
                'period' => null,
                'multiplier' => 1.0000,
                'is_required' => 0,
                'notes' => 'Boya V2 merkezi piyasa fiyat bağlantısı.',
            ];
        }

        foreach ($bindings as $row) {
            $existing = DB::table('tool_price_bindings')
                ->where('tool_slug', 'boya-hesaplayici')
                ->where('role', $row['role'])
                ->first();

            $values = array_merge($row, [
                'tool_slug' => 'boya-hesaplayici',
                'updated_at' => $now,
            ]);

            if ($existing) {
                DB::table('tool_price_bindings')
                    ->where('id', $existing->id)
                    ->update($values);
            } else {
                $values['created_at'] = $now;

                DB::table('tool_price_bindings')
                    ->insert($values);
            }
        }
    }


    public function down(): void
    {
        $settingKeys = [
            'paint_labor_standard_m2',
            'paint_labor_large_threshold',
            'paint_labor_large_min_m2',
            'paint_labor_large_base_m2',
            'paint_labor_large_max_m2',
            'paint_budget_tolerance',
        ];

        $priceKeys = [
            'paint_wall_standard_15l',
            'paint_ceiling_10l',
            'paint_primer_15l',
            'paint_cover_10m2',
            'paint_masking_tape_25m',
            'paint_roller_25cm',
            'paint_brush',
            'paint_tray',
            'paint_sandpaper_sheet',
            'paint_wall_putty_4_5kg',
        ];

        $roles = [
            'labor_reference',
            'scaffold_3_51',
            'scaffold_51_plus',
            'wall_paint',
            'ceiling_paint',
            'primer',
            'cover',
            'masking_tape',
            'roller',
            'brush',
            'tray',
            'sandpaper',
            'putty',
        ];

        DB::table('tool_price_bindings')
            ->where('tool_slug', 'boya-hesaplayici')
            ->whereIn('role', $roles)
            ->delete();

        DB::table('unit_prices')
            ->whereIn('key', $priceKeys)
            ->delete();

        DB::table('pricing_settings')
            ->whereIn('key', $settingKeys)
            ->delete();
    }
};

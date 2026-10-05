<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('paint_color_brands')->updateOrInsert(
            ['slug' => 'worksitem-renk-paleti'],
            [
                'name' => 'WORKSITEM Renk Paleti',
                'is_active' => 1,
                'is_sponsored' => 0,
                'sponsor_label' => null,
                'logo_path' => null,
                'website_url' => null,
                'product_url' => null,
                'dealer_url' => null,
                'catalog_source' => 'WORKSITEM özgün dijital renk paleti',
                'catalog_version' => '2026-10-v1',
                'rights_status' => 'internal_owned',
                'rights_reference' => 'WORKSITEM tarafından oluşturulan bağımsız dijital önizleme paleti.',
                'rights_expires_at' => null,
                'sort_order' => 10,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $brandId = DB::table('paint_color_brands')
            ->where('slug', 'worksitem-renk-paleti')
            ->value('id');

        $colors = [
            ['WS-101','Kırık Beyaz','#F2EFE7','Açık Tonlar'],
            ['WS-102','Sıcak Krem','#E8DDC7','Açık Tonlar'],
            ['WS-201','Kum Beji','#D8C9B2','Bej Tonları'],
            ['WS-202','Toprak Beji','#BDA88E','Bej Tonları'],
            ['WS-301','Taş Gri','#AAA9A3','Gri Tonları'],
            ['WS-302','Antrasit','#44484B','Gri Tonları'],
            ['WS-401','Adaçayı','#A5AF9B','Doğa Tonları'],
            ['WS-402','Zeytin Gri','#7E8575','Doğa Tonları'],
            ['WS-501','Soluk Mavi','#A8BCC7','Mavi Tonları'],
            ['WS-502','Duman Mavisi','#788F9C','Mavi Tonları'],
        ];

        foreach ($colors as $i => [$code,$name,$hex,$category]) {
            DB::table('paint_colors')->updateOrInsert(
                [
                    'brand_id' => $brandId,
                    'code' => $code,
                    'name' => $name,
                ],
                [
                    'series' => 'WORKSITEM',
                    'hex' => $hex,
                    'category' => $category,
                    'is_active' => 1,
                    'sort_order' => $i + 1,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        $brandId = DB::table('paint_color_brands')
            ->where('slug', 'worksitem-renk-paleti')
            ->value('id');

        if ($brandId) {
            DB::table('paint_colors')
                ->where('brand_id', $brandId)
                ->delete();

            DB::table('paint_color_brands')
                ->where('id', $brandId)
                ->delete();
        }
    }
};

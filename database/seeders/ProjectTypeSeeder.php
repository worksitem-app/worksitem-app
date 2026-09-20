<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Villa',
                'description' => 'Müstakil villa ve lüks konut projeleri',
                'sort_order' => 1,
            ],
            [
                'name' => 'Ev',
                'description' => 'Müstakil ev ve konut projeleri',
                'sort_order' => 2,
            ],
            [
                'name' => 'Bina / Apartman',
                'description' => 'Konut ve karma yapı projeleri',
                'sort_order' => 3,
            ],
            [
                'name' => 'Fabrika',
                'description' => 'Üretim tesisi ve sanayi yapıları',
                'sort_order' => 4,
            ],
            [
                'name' => 'Depo / Lojistik',
                'description' => 'Depo, antrepo ve lojistik yapıları',
                'sort_order' => 5,
            ],
            [
                'name' => 'Ofis',
                'description' => 'Ofis ve ticari çalışma alanları',
                'sort_order' => 6,
            ],
            [
                'name' => 'Mağaza / Restoran',
                'description' => 'Mağaza, restoran ve ticari mekanlar',
                'sort_order' => 7,
            ],
            [
                'name' => 'Otel / Konaklama',
                'description' => 'Otel ve konaklama projeleri',
                'sort_order' => 8,
            ],
            [
                'name' => 'Tadilat',
                'description' => 'Mevcut yapı tadilat ve renovasyon işleri',
                'sort_order' => 9,
            ],
        ];

        foreach ($types as $type) {
            DB::table('project_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'slug' => Str::slug($type['name']),
                    'description' => $type['description'],
                    'sort_order' => $type['sort_order'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

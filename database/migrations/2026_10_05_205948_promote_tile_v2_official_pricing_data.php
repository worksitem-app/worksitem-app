<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $bindings = [
            [
                'role' => 'labor_reference',
                'poz_no' => '10.100.1004',
                'notes' => 'Seramik kaplama ustası resmi saatlik referans; m² uygulama fiyatı değildir.',
            ],

            // SERAMİK ZEMİN
            [
                'role' => 'ceramic_floor_30_33',
                'poz_no' => '15.375.1002',
                'notes' => '30x30 / 33x33 beyaz seramik yer karosu, 3 mm derz, yapıştırıcılı uygulama.',
            ],
            [
                'role' => 'ceramic_floor_40',
                'poz_no' => '15.375.1003',
                'notes' => '40x40 beyaz seramik yer karosu, 3 mm derz, yapıştırıcılı uygulama.',
            ],
            [
                'role' => 'ceramic_floor_45',
                'poz_no' => '15.375.1004',
                'notes' => '42,5x42,5 / 45x45 beyaz seramik yer karosu, 3 mm derz, yapıştırıcılı uygulama.',
            ],

            // SERAMİK DUVAR
            [
                'role' => 'ceramic_wall_20_25_30',
                'poz_no' => '15.380.1003',
                'notes' => '20x25 / 20x30 beyaz seramik duvar karosu, 3 mm derz, yapıştırıcılı uygulama.',
            ],
            [
                'role' => 'ceramic_wall_25_33_40',
                'poz_no' => '15.380.1005',
                'notes' => '25x33 / 25x40 beyaz seramik duvar karosu, 3 mm derz, yapıştırıcılı uygulama.',
            ],
            [
                'role' => 'ceramic_wall_20_30_33_x60',
                'poz_no' => '15.380.1006',
                'notes' => '20x60 / 30x60 / 33x60 beyaz seramik duvar karosu, 3 mm derz, yapıştırıcılı uygulama.',
            ],
            [
                'role' => 'ceramic_wall_mid_format',
                'poz_no' => '15.380.1007',
                'notes' => '20x50 / 25x50 / 30x45 / 33x45 beyaz seramik duvar karosu resmi uygulama referansı.',
            ],

            // SIRLI PORSELEN ZEMİN
            [
                'role' => 'porcelain_glazed_floor_60',
                'poz_no' => '15.385.1008',
                'notes' => '60x60 beyaz sırlı porselen zemin, 3 mm derz, yapıştırıcılı uygulama.',
            ],
            [
                'role' => 'porcelain_glazed_floor_30x60',
                'poz_no' => '15.385.1010',
                'notes' => '30x60 beyaz sırlı porselen zemin, 3 mm derz, yapıştırıcılı uygulama.',
            ],
            [
                'role' => 'porcelain_glazed_wall_30x60',
                'poz_no' => '15.385.1050',
                'notes' => '30x60 beyaz sırlı porselen duvar/cephe, 3 mm derz, yapıştırıcılı uygulama.',
            ],

            // MAT SIRSIZ PORSELEN / REKTİFİYELİ
            [
                'role' => 'porcelain_matt_floor_60',
                'poz_no' => '15.390.1008',
                'notes' => '60x60 mat sırsız rektifiyeli porselen zemin resmi uygulama referansı.',
            ],
            [
                'role' => 'porcelain_matt_floor_30x60',
                'poz_no' => '15.390.1010',
                'notes' => '30x60 mat sırsız rektifiyeli porselen zemin resmi uygulama referansı.',
            ],
            [
                'role' => 'porcelain_matt_wall_30x60',
                'poz_no' => '15.390.1050',
                'notes' => '30x60 mat sırsız rektifiyeli porselen duvar/cephe resmi uygulama referansı.',
            ],

            // PARLAK SIRSIZ PORSELEN / REKTİFİYELİ
            [
                'role' => 'porcelain_polished_floor_60',
                'poz_no' => '15.390.1028',
                'notes' => '60x60 parlak sırsız rektifiyeli porselen zemin resmi uygulama referansı.',
            ],
            [
                'role' => 'porcelain_polished_floor_30x60',
                'poz_no' => '15.390.1030',
                'notes' => '30x60 parlak sırsız rektifiyeli porselen zemin resmi uygulama referansı.',
            ],
            [
                'role' => 'porcelain_polished_wall_30x60',
                'poz_no' => '15.390.1070',
                'notes' => '30x60 parlak sırsız rektifiyeli porselen duvar/cephe resmi uygulama referansı.',
            ],
        ];

        foreach ($bindings as $row) {
            DB::table('tool_price_bindings')->updateOrInsert(
                [
                    'tool_slug' => 'seramik-maliyet-hesaplayici',
                    'role' => $row['role'],
                ],
                [
                    'source_type' => 'official_poz',
                    'source_ref' => 'ÇŞİDB/YFK:' . $row['poz_no'],
                    'price_key' => 'official_' . str_replace('.', '_', $row['poz_no']),
                    'institution' => 'ÇŞİDB/YFK',
                    'poz_no' => $row['poz_no'],
                    'period' => null,
                    'multiplier' => 1.0000,
                    'is_required' => 0,
                    'notes' => $row['notes'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('tool_price_bindings')
            ->where('tool_slug', 'seramik-maliyet-hesaplayici')
            ->whereIn('role', [
                'labor_reference',
                'ceramic_floor_30_33',
                'ceramic_floor_40',
                'ceramic_floor_45',
                'ceramic_wall_20_25_30',
                'ceramic_wall_25_33_40',
                'ceramic_wall_20_30_33_x60',
                'ceramic_wall_mid_format',
                'porcelain_glazed_floor_60',
                'porcelain_glazed_floor_30x60',
                'porcelain_glazed_wall_30x60',
                'porcelain_matt_floor_60',
                'porcelain_matt_floor_30x60',
                'porcelain_matt_wall_30x60',
                'porcelain_polished_floor_60',
                'porcelain_polished_floor_30x60',
                'porcelain_polished_wall_30x60',
            ])
            ->delete();
    }
};

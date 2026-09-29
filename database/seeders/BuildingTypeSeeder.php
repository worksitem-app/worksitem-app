<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BuildingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $sourceName = 'ÇŞİDB — 2026 Yapı Yaklaşık Birim Maliyetleri';
        $sourceDate = '2026-02-03';

        $types = [
            // KONUT
            ['konut', 'Konut', 10, 'konut_kirsal_200_alti', 'Bağ / dağ / köy / yayla evi — 200 m² altı', '2C'],
            ['konut', 'Konut', 20, 'konut_kayadan_oyma', 'Kayadan oyma konut', '3A'],
            ['konut', 'Konut', 30, 'konut_apartman_3_kata_kadar', 'Apartman tipi — 3 kata kadar', '3A'],
            ['konut', 'Konut', 40, 'konut_21_50m_ve_3_kat_ustu', 'Konut — 21,50 m yüksekliğe kadar ve 3 kattan fazla', '3B'],
            ['konut', 'Konut', 50, 'konut_mustakil_200_alti', 'Müstakil / ikiz konut — 200 m² altı', '3B'],
            ['konut', 'Konut', 60, 'konut_21_50_30_50m', 'Konut — 21,50 m üzeri / 30,50 m dahil', '3C'],
            ['konut', 'Konut', 70, 'konut_mustakil_200_500', 'Müstakil / ikiz konut — 200–500 m²', '3C'],
            ['konut', 'Konut', 80, 'konut_30_50_51_50m', 'Konut — 30,50 m üzeri / 51,50 m dahil', '4A'],
            ['konut', 'Konut', 90, 'konut_mustakil_500_ustu', 'Müstakil / ikiz konut — 500 m² ve üzeri', '4B'],
            ['konut', 'Konut', 100, 'konut_51_50m_ustu', 'Konut — 51,50 m üzeri', '4B'],
            ['konut', 'Konut', 110, 'konut_vali_kaymakam_konutu', 'Vali / kaymakam konutu', '4C'],
            ['konut', 'Konut', 120, 'konut_karma_avm_ofis_rezidans', 'Karma kullanım — AVM + ofis / rezidans', '5A'],

            // VİLLA
            ['villa', 'Villa', 10, 'villa_200_alti', 'Müstakil / ikiz villa — 200 m² altı', '3B'],
            ['villa', 'Villa', 20, 'villa_200_500', 'Müstakil / ikiz villa — 200–500 m²', '3C'],
            ['villa', 'Villa', 30, 'villa_500_ustu', 'Müstakil / ikiz villa — 500 m² ve üzeri', '4B'],

            // DEPO
            ['depo', 'Depo', 10, 'depo_kayadan_oyma', 'Kayadan oyma depo', '1B'],
            ['depo', 'Depo', 20, 'depo_su_deposu', 'Betonarme / yığma su deposu', '1C'],
            ['depo', 'Depo', 30, 'depo_genel', 'Genel depo', '2A'],
            ['depo', 'Depo', 40, 'depo_ozel', 'Özel depo — kimyasal / radyoaktif / soğuk hava', '3A'],

            // FABRİKA / SANAYİ
            ['fabrika', 'Fabrika / Sanayi', 10, 'fabrika_0_500', 'Sanayi tesisi — hareketli yük 0–500 kg/m²', '2C'],
            ['fabrika', 'Fabrika / Sanayi', 20, 'fabrika_501_3000', 'Sanayi tesisi — hareketli yük 501–3.000 kg/m²', '3B'],
            ['fabrika', 'Fabrika / Sanayi', 30, 'fabrika_idari_bina', 'Sanayi tesisi idari binası', '3B'],
            ['fabrika', 'Fabrika / Sanayi', 40, 'fabrika_3001_ustu', 'Sanayi tesisi — hareketli yük 3.001 kg/m² ve üzeri', '4A'],

            // OTEL
            ['otel', 'Otel', 10, 'otel_apart', 'Apart otel', '3B'],
            ['otel', 'Otel', 20, 'otel_kayadan_oyma', 'Kayadan oyma otel', '4A'],
            ['otel', 'Otel', 30, 'otel_1_2_yildiz', '1–2 yıldızlı otel', '4A'],
            ['otel', 'Otel', 40, 'otel_3_yildiz', '3 yıldızlı otel', '4C'],
            ['otel', 'Otel', 50, 'otel_4_yildiz', '4 yıldızlı otel', '5B'],
            ['otel', 'Otel', 60, 'otel_5_yildiz', '5 yıldızlı otel', '5D'],

            // CAMİ
            ['cami', 'Cami', 10, 'cami_500_alti', 'İbadethane — 500 kişi altı', '3B'],
            ['cami', 'Cami', 20, 'cami_500_1500', 'İbadethane — 500–1.500 kişi altı', '4B'],

            // OKUL
            ['okul', 'Okul', 10, 'okul_anaokulu', 'Kreş / anaokulu / okul öncesi', '3A'],
            ['okul', 'Okul', 20, 'okul_ilkokul_ortaokul', 'İlkokul / ortaokul', '3B'],
            ['okul', 'Okul', 30, 'okul_lise', 'Lise ve dengi okul', '3C'],
            ['okul', 'Okul', 40, 'okul_yuksekogretim', 'Enstitü / fakülte / yüksekokul', '4A'],
            ['okul', 'Okul', 50, 'okul_ozel_egitim', 'Özel eğitim okulu', '4A'],
            ['okul', 'Okul', 60, 'okul_ozel_egitim_buyuk', 'Spor / konferans ve ek tesisli büyük özel okul', '4B'],

            // HASTANE
            ['hastane', 'Hastane', 10, 'hastane_hayvan', 'Hayvan hastanesi', '3B'],
            ['hastane', 'Hastane', 20, 'hastane_agiz_dis', 'Ağız ve diş sağlığı merkezi', '3C'],
            ['hastane', 'Hastane', 30, 'hastane_tip_merkezi', 'Tıp merkezi', '4A'],
            ['hastane', 'Hastane', 40, 'hastane_200_alti', 'Hastane — 200 yatak altı', '4C'],
            ['hastane', 'Hastane', 50, 'hastane_egitim_arastirma', 'Eğitim ve araştırma hastanesi', '5A'],
            ['hastane', 'Hastane', 60, 'hastane_200_400', 'Hastane — 200–400 yatak altı', '5B'],
            ['hastane', 'Hastane', 70, 'hastane_400_ustu', 'Hastane — 400 yatak ve üzeri', '5C'],
            ['hastane', 'Hastane', 80, 'hastane_sehir', 'Şehir hastanesi', '5D'],

            // SERA
            ['sera', 'Sera', 10, 'sera_plastik', 'Plastik örtülü sera', '1A'],
            ['sera', 'Sera', 20, 'sera_cam', 'Cam / sert plastik sera', '1B'],

            // OTOPARK
            ['otopark', 'Otopark', 10, 'otopark_kapali_katli', 'Garaj / kapalı / katlı otopark', '3A'],
        ];

        DB::transaction(function () use ($types, $sourceName, $sourceDate) {
            foreach ($types as [
                $categoryKey,
                $categoryName,
                $sortOrder,
                $key,
                $name,
                $classCode
            ]) {
                $classId = DB::table('building_classes')
                    ->where('code', $classCode)
                    ->where('is_active', true)
                    ->value('id');

                if (!$classId) {
                    throw new RuntimeException(
                        "Yapı sınıfı bulunamadı: {$classCode}"
                    );
                }

                DB::table('building_types')->updateOrInsert(
                    ['key' => $key],
                    [
                        'building_class_id' => $classId,
                        'category_key' => $categoryKey,
                        'category_name' => $categoryName,
                        'sort_order' => $sortOrder,
                        'name' => $name,
                        'description' =>
                            "2026 resmî yapı sınıfı eşlemesi: {$classCode}",
                        'source_name' => $sourceName,
                        'source_date' => $sourceDate,
                        'is_active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });
    }
}

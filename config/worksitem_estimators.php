<?php

/*
|--------------------------------------------------------------------------
| Hesap motoru (banyo tarzı) araçları ve varsayılan fiyatlar
|--------------------------------------------------------------------------
| Fiyatlar TL, KDV dahil yaklaşık piyasa değerleridir (malzeme + işçilik).
| Seramik ve boyada admin "Araç Fiyatları" / "Fiyat Merkezi" kayıtları varsa
| onlar kullanılır; yoksa buradaki değerler. Fiyat dizileri: [ekonomik, orta, premium].
*/

return [
    'tools' => [
        'mutfak-maliyet-hesaplayici' => \App\Estimators\KitchenEstimator::class,
        'seramik-maliyet-hesaplayici' => \App\Estimators\TileEstimator::class,
        'boya-hesaplayici' => \App\Estimators\PaintEstimator::class,
        'wc-maliyet-hesabi' => \App\Estimators\WcEstimator::class,
        'parke-maliyet-hesaplayici' => \App\Estimators\ParquetEstimator::class,
        'zemin-kaplama' => \App\Estimators\FloorEstimator::class,
        'mantolama-maliyeti' => \App\Estimators\InsulationEstimator::class,
        'pvc-dograma-hesaplayici' => \App\Estimators\PvcEstimator::class,
        'sap-hesaplayici' => \App\Estimators\ScreedEstimator::class,
        'kara-siva-hesaplama' => \App\Estimators\RoughPlasterEstimator::class,
        'alci-siva-hesaplama' => \App\Estimators\GypsumPlasterEstimator::class,
        'duvar-maliyet-hesaplayici' => \App\Estimators\WallEstimator::class,
        'duvar-orgu' => \App\Estimators\WallMasonryEstimator::class,
        'bahce-duvari-maliyeti' => \App\Estimators\GardenWallEstimator::class,
        'iskele-metraji-maliyeti' => \App\Estimators\ScaffoldEstimator::class,
        'kalip-plywood-maliyeti' => \App\Estimators\FormworkEstimator::class,
        'hafriyat-kazi-hesabi' => \App\Estimators\ExcavationEstimator::class,
        'saha-betonu-hesaplayici' => \App\Estimators\SlabEstimator::class,
        'alcipan-hesaplayici' => \App\Estimators\DrywallEstimator::class,
        'asma-tavan-hesaplayici' => \App\Estimators\CeilingEstimator::class,
        'su-yalitimi-hesaplayici' => \App\Estimators\WaterproofingEstimator::class,
        'epoksi-zemin-hesaplayici' => \App\Estimators\EpoxyEstimator::class,
        'beton-onarim-maliyeti' => \App\Estimators\ConcreteRepairEstimator::class,
        'sandvic-panel-hesaplayici' => \App\Estimators\SandwichPanelEstimator::class,
        'beton-hesaplayici' => \App\Estimators\ConcreteEstimator::class,
        'su-tesisat-hesaplama' => \App\Estimators\PlumbingEstimator::class,
        'cati-maliyet-hesaplayici' => \App\Estimators\RoofEstimator::class,
        'radyator-petek-hesaplayici' => \App\Estimators\RadiatorEstimator::class,
        'gunes-paneli-hesaplama' => \App\Estimators\SolarEstimator::class,
        'deprem-izolator-maliyeti' => \App\Estimators\IsolatorEstimator::class,
        'tahmini-bina-maliyeti' => \App\Estimators\BuildingCostEstimator::class,
        'statik-proje-bedeli' => \App\Estimators\StaticFeeEstimator::class,
    ],

    'prices_reviewed' => '2026-10-13',
    'yfk_period' => env('WORKSITEM_YFK_PERIOD', '2026-08'),

    'mutfak' => [
        'cabinet_m' => [9000, 14000, 24000],        // alt + üst dolap, mtül, montaj dahil
        'front_only_factor' => 0.45,                // yalnız kapak yenilemede dolap kalemi oranı
        'counter_m' => ['laminat' => 2800, 'granit' => 7500, 'kuvars' => 9500, 'porselen' => 11000],
        'backsplash_m' => [1600, 2400, 4000],       // tezgah arası (yaklaşık 60 cm yükseklik), mtül
        'demolition' => ['fixed' => 3500, 'per_m' => 400],
        'floor_m2' => [1500, 2200, 3600],           // kırım + yeni zemin kaplama, m²
        'mep' => ['fixed' => 9000, 'per_m' => 900], // su, gider ve elektrik revizyonu
        'paint_m2' => 300,                          // zemin m² başına duvar/tavan boyası
        'extras' => [
            'ankastre' => [22000, 38000, 75000],
            'boy_dolabi' => [10000, 15000, 26000],
            'ada' => [30000, 48000, 85000],
            'led' => ['fixed' => 3500, 'per_m' => 500],
        ],
    ],

    'seramik' => [
        'tile_m2' => [450, 750, 1600],              // karo malzeme, m² (KDV dahil)
        'labor_m2' => 450,                          // döşeme işçiliği, m² (pricing_settings: tile_labor_standard_m2)
        'size_labor' => ['30x60' => 1.0, '60x60' => 1.0, '60x120' => 1.35, '120x120' => 1.6],
        'size_waste' => ['30x60' => 0.10, '60x60' => 0.10, '60x120' => 0.12, '120x120' => 0.15],
        'box_m2' => ['30x60' => 1.44, '60x60' => 1.44, '60x120' => 1.44, '120x120' => 2.88],
        'adhesive_bag' => 362.9 * 1.2,              // 25 kg C2TE (unit_prices: tile_adhesive_standard_25kg, KDV eklenir)
        'grout_pack' => 285.7 * 1.2,                // 5 kg (tile_grout_5kg)
        'demolition_m2' => 300,
        'screed_m2' => 450,                         // tesviye şapı
        'waterproof_m2' => 550,
        'profile_m' => 75,
        'skirting_m' => 260,
        // v76 (tahmini piyasa değerleri, KDV dahil; Fiyat Merkezi › pricing_settings ile güncellenir)
        'epoxy_grout_3kg' => 1500 * 1.2,            // epoksi derz dolgu 3 kg set — tile_epoxy_grout_3kg
        'epoxy_grout_kg_m2' => 0.6,                 // 60×60, 2–3 mm derzde ≈0,5–0,7 kg/m²
        'epoxy_grout_labor_m2' => 120,              // epoksi derz uygulama farkı — tile_epoxy_grout_labor_m2
        'level_clip' => 2.5,                        // klips (+ kama amortismanı), adet — tile_level_clip
        'level_clips_m2' => ['30x60' => 12, '60x60' => 8, '60x120' => 6, '120x120' => 4],
        'cleaning_m2' => 35,                        // inşaat sonrası temizlik kimyasalı + işçilik — tile_cleaning_m2
    ],

    'boya' => [
        'wall_paint_15l' => 4395 * 1.2,             // tool_price_bindings: wall_paint (KDV eklenir)
        'ceiling_paint_10l' => 990 * 1.2,
        'primer_15l' => 2990 * 1.2,
        'putty_4_5kg' => 459.9 * 1.2,
        'quality_factor' => [0.65, 1.0, 1.6],
        'labor_m2' => 250,                          // pricing_settings: paint_labor_standard_m2 — DAİRE m²'si başına ustalık (2 kat, duvar + tavan)
        'coverage_m2_per_l' => 11,
        'wall_factor' => 2.7,                       // daire net m² → duvar alanı (2,6 m tavan)
        'extras' => ['furniture' => 2500, 'door' => 1800, 'radiator' => 900, 'plaster_m2' => 380],
        // Dış cephe (Kainta saha referansı; Fiyat Merkezi › pricing_settings ile güncellenir)
        'facade' => [
            'paint_15l' => 5200 * 1.2,                  // silikonlu dış cephe boyası 15 L (orta sınıf, KDV dahil) — pricing_settings: paint_facade_15l
            'primer_15l' => 3200 * 1.2,                 // dış cephe astarı 15 L — paint_facade_primer_15l
            'quality_factor' => [0.75, 1.0, 1.45],      // akrilik / silikonlu / silikon-elastomerik
            'coverage_m2_per_l' => 8,                   // pürüzlü sıva yüzeyinde kat başına
            'primer_m2_per_l' => 10,
            'labor_m2' => 200,                          // cephe m² başına ustalık (astar + 2 kat, iskele hariç) — paint_facade_labor_m2
            'window_share' => 0.15,                     // çevre × yükseklikten düşülen pencere payı
            'floor_height' => 3.0,
            'repair_m2' => ['iyi' => 15, 'orta' => 60, 'kotu' => 180],   // çatlak/sıva tamiri + file (cephe m²)
            'extras' => ['wash_m2' => 18, 'sill_m' => 120, 'mesh_m2' => 95],
        ],
    ],

    'parke' => [
        'material_m2' => ['laminat' => [380, 550, 850], 'spc' => [600, 850, 1300], 'ahsap' => [1400, 2200, 3800]],
        'labor_m2' => ['laminat' => 180, 'spc' => 200, 'ahsap' => 350],
        'underlay_m2' => [45, 70, 120],
        'removal_m2' => 90,
        'screed_m2' => 450,
        'skirting_m' => 220,
        'threshold' => 350,
        'furniture' => 2500,
    ],

    // Zemin kaplama: [malzeme m², işçilik m²] — kalite çarpanı malzemeye uygulanır
    'zemin' => [
        'types' => [
            'laminat' => ['label' => 'Laminat parke', 'material' => 550, 'labor' => 180, 'waste' => 0.05],
            'spc' => ['label' => 'SPC / vinil', 'material' => 850, 'labor' => 200, 'waste' => 0.05],
            'seramik' => ['label' => 'Seramik / porselen', 'material' => 750, 'labor' => 450, 'waste' => 0.10, 'adhesive_m2' => 100],
            'mermer' => ['label' => 'Mermer / granit', 'material' => 2200, 'labor' => 750, 'waste' => 0.10, 'adhesive_m2' => 120],
            'ahsap' => ['label' => 'Lamine / masif ahşap', 'material' => 2200, 'labor' => 350, 'waste' => 0.07],
        ],
        'quality_factor' => [0.7, 1.0, 1.7],
        'removal_m2' => ['yumusak' => 90, 'sert' => 300],
        'screed_m2' => 450,
        'skirting_m' => 220,
    ],

    'mantolama' => [
        'board_m2' => ['eps' => [22, 28, 34], 'grafit' => [28, 36, 44], 'tasyunu' => [60, 75, 90]],   // 1 cm kalınlık başına m² fiyatı
        'system_m2' => 230,           // yapıştırma + sıva harcı, file, dübel, köşe profili (m²)
        'finish_m2' => ['boya' => 90, 'dekoratif' => 160],
        'labor_m2' => 380,
        'scaffold_m2' => 160,         // iskele kira + kurulum (m² cephe)
        'quality_factor' => [0.88, 1.0, 1.25],
        'sill_m' => 450,              // denizlik (mtül)
        'old_removal_m2' => 120,
    ],

    'pvc' => [
        'frame_m2' => [2200, 3000, 4400],          // profil + takviye + donanım, m² (beyaz)
        'color_factor' => 1.15,
        'glass_m2' => ['cift' => 1900, 'isi' => 2100, 'uclu' => 3200],
        'opening' => ['sabit' => 0, 'tek' => 1200, 'cift' => 2000, 'surme' => 3500],   // pencere başına donanım farkı
        'install_each' => 900,
        'removal_each' => 1000,                    // eski doğrama sökümü + sıva tamiri
        'shutter_m2' => 2800,
        'screen_each' => 900,
        'sill_m' => 650,
    ],

    // ── Dalga 2: kaba inşaat ──
    'sap' => [
        'mortar_m3' => 2900,                        // çimento + kum şap harcı, m³
        'selfleveling_kg' => 12,                    // hazır tesviye şapı (self-leveling), kg
        'labor_m2' => ['klasik' => 160, 'helikopter' => 260, 'tesviye' => 120, 'yuzer' => 200],
        'eps_m2' => 70,                             // yüzer şap: 2 cm yalıtım levhası + naylon
        'fiber_m2' => 25, 'mesh_m2' => 200, 'edge_m' => 20,
    ],
    'siva' => [
        'mortar_m3' => 3200,                        // geleneksel çimento-kireç-kum sıva harcı
        'machine_bag_25kg' => 150, 'machine_kg_m2_cm' => 13,
        'labor_m2' => ['geleneksel' => 200, 'makine' => 150],
        'exterior_factor' => 1.3, 'mesh_m2' => 25, 'scaffold_m2' => 160,
    ],
    'alci' => [
        'perlit_kg' => 6.6, 'saten_kg' => 11.2, 'makine_kg' => 8.7,
        'labor_m2' => ['perlit' => 170, 'saten' => 90, 'makine' => 130],
        'corner_m' => 45, 'mesh_m2' => 25,
    ],
    'duvar' => [
        // malzeme: [ad, adet/m², adet fiyatı, harç/yapıştırıcı m² fiyatı, işçilik m²]
        'materials' => [
            'tugla135' => ['13,5 cm yatay delikli tuğla', 25, 9.5, 96, 260],
            'tugla19' => ['19 cm yatay delikli tuğla', 25, 13.5, 120, 280],
            'gazbeton10' => ['10 cm gazbeton', 6.7, 70, 35, 200],
            'gazbeton15' => ['15 cm gazbeton', 6.7, 100, 40, 210],
            'gazbeton20' => ['20 cm gazbeton', 6.7, 130, 45, 220],
            'bims19' => ['19 cm bims blok', 10, 42, 90, 240],
        ],
        'plaster_m2' => 380,                        // kaba + ince sıva, tek yüz (malzeme + işçilik)
        'lintel_m' => 650,                          // kapı/pencere lento
    ],
    'bahce' => [
        'stone_m3' => 7500, 'concrete_m3' => 12000, 'block_m2' => 950, 'plaster_m2' => 380,
        'footing_m3' => 9000, 'fence_m' => 1800, 'coping_m' => 450, 'paint_m2' => 120,
    ],
    'iskele' => [
        'm2' => ['cephe' => 260, 'boru' => 190],    // 1 ay kira + kurulum + söküm
        'extra_month' => 0.35, 'net_m2' => 55, 'transport' => 6000,
    ],
    'kalip' => [
        'm2' => ['plywood' => 1175.39, 'ahsap' => 997.21],   // ÇŞB poz birim fiyatları (işçilik dahil)
        'ratio' => ['doseme' => 6, 'kolon' => 10, 'radye' => 3],
        'shoring_m2' => 120,
    ],
    'hafriyat' => [
        'soils' => ['normal' => [1.20, 120], 'sert' => [1.35, 170], 'kaya' => [1.50, 320]],   // [kabarma, kazı TL/m³]
        'truck_trip' => 2400, 'dump_m3' => 80, 'shoring_m2' => 1500,
    ],
    'saha' => [
        'concrete_m3' => ['c20' => 3400 * 1.2, 'c25' => 3600 * 1.2, 'c30' => 3900 * 1.2, 'c35' => 4250 * 1.2],
        'pump' => 9000,
        'mesh_m2' => ['tek' => 115 * 1.2, 'cift' => 230 * 1.2],
        'finish_m2' => ['helikopter' => 220, 'korund' => 360, 'cekme' => 45],   // korund: helikopter + ≈5 kg/m² korund/metalik serpme (v76, tahmini)
        'labor_m3' => 450, 'fill_m3' => 450, 'joint_m2' => 25, 'nylon_m2' => 8,
        'densifier_m2' => 90,          // sıvı yüzey sertleştirici (lityum silikat) + toz önleyici, uygulama dahil (v76, tahmini)
        'curing_m2' => 25,             // kür kimyasalı ≈0,2 L/m², püskürtme (v76, tahmini)
    ],

    // v76 Beton onarım ve grout (tahmini piyasa değerleri, KDV dahil; Fiyat Merkezi › pricing_settings ile güncellenir)
    'beton_onarim' => [
        'mortar_kg' => ['cimento' => 38, 'epoksi' => 300],     // R4 tamir harcı 25 kg ≈950 TL; epoksi tamir harcı 15 kg set ≈4.500 TL
        'grout_kg' => ['cimento' => 30, 'epoksi' => 260],      // akışkan grout 25 kg ≈750 TL; epoksi grout 15 kg set ≈3.900 TL
        'prep_m2' => 450, 'prep_cm' => 90,                     // kırım + kumlama/su jeti + temizlik
        'labor_m2' => 650, 'labor_cm' => 160,                  // harç uygulama
        'element_factor' => ['zemin' => 0.85, 'perde' => 1.0, 'kolon' => 1.2, 'tavan' => 1.45, 'cephe' => 1.3],
        'corrosion_m2' => ['hafif' => 280, 'agir' => 520],     // pas temizliği + korozyon önleyici (malzeme + işçilik)
        'primer_m2' => 90,
        'rebar_add_m2' => 1100,                                 // ilave donatı, bindirme, ankraj (kesit kaybında)
        'injection_m' => 1400,                                  // epoksi çatlak enjeksiyonu, mtül
        'coating_m2' => 260,
        'scaffold_m2' => 180, 'scaffold_min' => 8000,
        'grout_prep_each' => 900, 'grout_labor_each' => 1600,   // plaka başına
        'anchor_kg' => 1.2, 'anchor_labor_each' => 220,
    ],

    'tesisat' => [
        'bath' => [9000, 13000, 20000],             // banyo/WC başına temiz + pis su (malzeme + işçilik)
        'kitchen' => [5500, 8000, 12000],
        'main_m2' => [90, 130, 200],                // ev m² başına ana hat
        'chase_point' => 1200,                      // komple: kanal açma (ıslak hacim başına)
        'repair_point' => 900,                      // kanal kapatma, sıva/seramik tamiri
        'collector' => 9000, 'booster' => 28000, 'boiler' => 4500, 'meter' => 3000,
    ],
];

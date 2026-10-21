<?php

/*
 * GES planlayıcı (adres → çatı → panel yerleşimi → 2D/3D → üretim ve geri dönüş).
 * Fiyatlar GES maliyet aracıyla aynı kaynaktan gelir (App\Estimators\SolarEstimator::P).
 */
return [
    // Paneller: [ad, W, uzun kenar m, kısa kenar m]
    'panels' => [
        '450' => ['label' => '450 W (1,76 × 1,13 m)', 'w' => 450, 'l' => 1.762, 's' => 1.134],
        '550' => ['label' => '550 W (2,28 × 1,13 m)', 'w' => 550, 'l' => 2.278, 's' => 1.134],
        '610' => ['label' => '610 W (2,38 × 1,13 m)', 'w' => 610, 'l' => 2.382, 's' => 1.134],
    ],
    'default_panel' => '550',
    'system_loss' => 14,                 // PVGIS kayıp yüzdesi (kablo, inverter, kirlenme)
    'setback' => ['konut' => 0.5, 'fabrika' => 1.0],   // çatı kenarından boşluk (m); fabrikada bakım/yangın koridoru
    'flat_tilt' => 15,                   // düz çatıda panel eğimi (°)
    'scale' => [[100, 0.88], [500, 0.78]],   // kWp eşiği → fiyat çarpanı (büyük sistemde kWp maliyeti düşer)
    'flat_mount_factor' => 1.15,         // düz çatı eğimli konstrüksiyon (balast/ayak) farkı
    // Depolama: batarya kWh = kWp × oran; anlaşma gücü üzerinde depolamada otomatik güç kontrolü ve enerji kesme
    // sistemi yükümlülüğü için pano/otomasyon (tahmini, keşifte netleşir)
    'battery_ratio' => 2.0,
    // Arazi GES (yatırım) ek kalemleri — tahmini, Kainta saha referansıyla güncellenmeli; trafo/hat bedeli bağlantı görüşüyle netleşir
    'land' => ['project' => 250000, 'prep_m2' => 40, 'fence_m' => 1500, 'site_kwp' => 400],
    'storage_control' => ['konut' => 35000, 'fabrika' => 150000],
    // Dış servisler (sunucu tarafından çağrılır; tarayıcıdan doğrudan çağrı yapılmaz)
    'nominatim' => 'https://nominatim.openstreetmap.org/search',
    'overpass' => 'https://overpass-api.de/api/interpreter',
    'overpass_mirrors' => [
        'https://overpass.kumi.systems/api/interpreter',
        'https://overpass.private.coffee/api/interpreter',
        'https://overpass-api.de/api/interpreter',
    ],
    'overpass_timeout' => 5,
    'pvgis' => 'https://re.jrc.ec.europa.eu/api/v5_3/PVcalc',
    'user_agent' => 'Worksitem/1.0 (info@worksitem.com)',
    // Uydu görüntüsü (Leaflet): Esri World Imagery, atıf zorunlu
    'imagery' => [
        'url' => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        'attribution' => 'Görüntü © Esri, Maxar, Earthstar Geographics · Bina verisi © OpenStreetMap katkıda bulunanlar',
        'max_zoom' => 20,
    ],
    // v88: Leaflet kendi sunucumuzdan (npm leaflet@1.9.4, bütünlük npm özetiyle doğrulandı; BSD-2). Sürüm yolda → önbellek güvenli
    /*
    | v90 Malzeme listesi (BOM). Fiyatlar KDV DAHİL, TAHMİNİ başlangıç değerleri (perakende siteler, Ekim 2026).
    | Admin › Birim Fiyatlar'da "ges" kategorisinde aynı anahtarlarla düzenlenir; ONAYLI kayıt varsa o kullanılır.
    */
    'bom' => [
        'prices' => [
            'ges_panel_w' => 10.5,                    // TL/W (550 W TOPCon ≈ 5.800 TL)
            'ges_inv_mono_3' => 17500, 'ges_inv_mono_5' => 26000, 'ges_inv_mono_6' => 30000,
            'ges_inv_tri_5' => 35000, 'ges_inv_tri_8' => 42000, 'ges_inv_tri_10' => 47000, 'ges_inv_tri_12' => 50000,
            'ges_inv_tri_15' => 53000, 'ges_inv_tri_20' => 72500, 'ges_inv_tri_25' => 75000, 'ges_inv_tri_30' => 85000,
            'ges_inv_tri_40' => 100000, 'ges_inv_tri_50' => 115000, 'ges_inv_tri_60' => 130000, 'ges_inv_tri_80' => 165000,
            'ges_inv_tri_100' => 200000,
            'ges_battery_kwh' => 11000,               // LiFePO4, TL/kWh
            'ges_mount_kiremit' => 1900, 'ges_mount_trapez' => 1300, 'ges_mount_beton' => 2800,
            'ges_mount_arazi' => 3300, 'ges_mount_cephe' => 3500,   // TL/panel
            'ges_labor_panel' => 1700,                // montaj işçiliği TL/panel
            'ges_dc_cable_m' => 55,                   // 6 mm² solar kablo TL/m
            'ges_mc4_pair' => 150,
            'ges_dc_box_mppt' => 4500,                // DC sigorta + DC parafudr + DC ayırıcı, MPPT girişi başına
            'ges_ac_box_mono' => 4500, 'ges_ac_box_tri' => 7500, 'ges_ac_box_tri_large' => 15000,
            'ges_ac_cable_6' => 150, 'ges_ac_cable_10' => 230, 'ges_ac_cable_16' => 330, 'ges_ac_cable_35' => 650, 'ges_ac_cable_70' => 1200,   // TL/m
            'ges_grounding_base' => 4000, 'ges_grounding_kwp' => 150,
            'ges_monitor' => 2500, 'ges_meter' => 6000,
            'ges_access_fixed' => 15000,              // 3+ kat: çatıya iskele / vinç
            'ges_facade_scaffold_m2' => 180,          // cephe iskelesi TL/m²
            'ges_project_konut' => 15000, 'ges_project_fabrika' => 45000,
        ],
        'labels' => [
            'ges_panel_w' => ['Güneş paneli (W başı)', 'TL/W'],
            'ges_battery_kwh' => ['Lityum (LiFePO4) batarya', 'TL/kWh'],
            'ges_mount_kiremit' => ['Konstrüksiyon — kiremit çatı (kanca + ray + klemens)', 'TL/panel'],
            'ges_mount_trapez' => ['Konstrüksiyon — trapez / sandviç panel (mini ray)', 'TL/panel'],
            'ges_mount_beton' => ['Konstrüksiyon — düz beton çatı (eğimli sehpa)', 'TL/panel'],
            'ges_mount_arazi' => ['Konstrüksiyon — arazi (çakma kazık sistem)', 'TL/panel'],
            'ges_mount_cephe' => ['Konstrüksiyon — dikey cephe (duvar ankrajlı ray)', 'TL/panel'],
            'ges_labor_panel' => ['Montaj işçiliği', 'TL/panel'],
            'ges_dc_cable_m' => ['Solar DC kablo 6 mm²', 'TL/m'],
            'ges_mc4_pair' => ['MC4 konnektör (çift)', 'TL/çift'],
            'ges_dc_box_mppt' => ['DC koruma (sigorta, parafudr, ayırıcı)', 'TL/MPPT'],
            'ges_ac_box_mono' => ['AC koruma panosu — monofaze', 'TL/adet'],
            'ges_ac_box_tri' => ['AC koruma panosu — trifaze', 'TL/adet'],
            'ges_ac_box_tri_large' => ['AC koruma panosu — trifaze 30 kW üstü', 'TL/adet'],
            'ges_grounding_base' => ['Topraklama ve eş potansiyel (sabit)', 'TL'],
            'ges_grounding_kwp' => ['Topraklama ve eş potansiyel (kWp başı)', 'TL/kWp'],
            'ges_monitor' => ['Uzaktan izleme modülü (Wi-Fi)', 'TL/adet'],
            'ges_meter' => ['Çift yönlü sayaç ve bağlantı', 'TL/adet'],
            'ges_access_fixed' => ['Çatı erişimi (iskele / vinç), 3+ kat', 'TL'],
            'ges_facade_scaffold_m2' => ['Cephe iskelesi', 'TL/m²'],
            'ges_project_konut' => ['Proje, başvuru ve kabul — konut', 'TL'],
            'ges_project_fabrika' => ['Proje, başvuru ve kabul — işyeri', 'TL'],
        ],
        'dc_ac_ratio' => 1.2,                         // DC/AC; inverter = en küçük standart güç ≥ DC ÷ 1,2
        'mono_max_kw' => 5,                           // bunun üstü trifaze bağlantı
        'hybrid_factor' => 1.6,                       // bataryada hibrit inverter fiyat çarpanı
        'kiremit_labor_factor' => 1.12, 'facade_labor_factor' => 1.5,
        'cold_voc_factor' => 1.13,                    // -20 °C açık devre gerilimi artışı (≈ -0,28 %/°C)
        'hot_vmp_factor' => 0.85,                     // 70 °C hücrede MPP gerilimi düşüşü
        'contingency_pct' => 5,
        // Genel panel elektrik değerleri (STC)
        'panel_elec' => [
            '450' => ['voc' => 41.5, 'vmp' => 34.6, 'isc' => 13.9],
            '550' => ['voc' => 49.6, 'vmp' => 41.7, 'isc' => 14.0],
            '610' => ['voc' => 53.9, 'vmp' => 44.9, 'isc' => 14.4],
        ],
        // İnverter aileleri: gerilim penceresi, MPPT sayısı, MPPT başına en çok dizi
        'inverters' => [
            'mono' => ['sizes' => [3, 5, 6], 'vmax' => 550, 'mppt_min' => 80, 'mppt' => ['3' => 1, '5' => 2, '6' => 2], 'strings_per_mppt' => 1],
            'tri' => ['sizes' => [5, 8, 10, 12, 15, 20, 25, 30, 40, 50, 60, 80, 100], 'vmax' => 1000, 'mppt_min' => 200,
                'mppt' => ['5' => 2, '8' => 2, '10' => 2, '12' => 2, '15' => 2, '20' => 2, '25' => 3, '30' => 3, '40' => 4, '50' => 4, '60' => 6, '80' => 8, '100' => 10], 'strings_per_mppt' => 2],
        ],
    ],
    'leaflet' => ['css' => '/vendor/leaflet/1.9.4/leaflet.css', 'js' => '/vendor/leaflet/1.9.4/leaflet.js'],
    // v80: alan/çevre Turf.js (WGS84 jeodezik hesap), grafikler Chart.js. Yalnız gereken modüller
    // (tam turf paketi ~550 KB; bunlar birkaç KB). Yüklenemezse sayfa eski hesaba/SVG'ye döner.
    'libs' => [
        'turf_area' => 'https://cdn.jsdelivr.net/npm/@turf/area@7.4.0/+esm',
        'turf_length' => 'https://cdn.jsdelivr.net/npm/@turf/length@7.4.0/+esm',
        'chart' => 'https://cdn.jsdelivr.net/npm/chart.js@4.5.1/auto/+esm',
    ],
];

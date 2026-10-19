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
    'overpass_timeout' => 8,
    'pvgis' => 'https://re.jrc.ec.europa.eu/api/v5_3/PVcalc',
    'user_agent' => 'Worksitem/1.0 (info@worksitem.com)',
    // Uydu görüntüsü (Leaflet): Esri World Imagery, atıf zorunlu
    'imagery' => [
        'url' => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        'attribution' => 'Görüntü © Esri, Maxar, Earthstar Geographics · Bina verisi © OpenStreetMap katkıda bulunanlar',
        'max_zoom' => 20,
    ],
    'leaflet' => ['css' => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', 'js' => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'],
];

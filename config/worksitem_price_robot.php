<?php

/*
|--------------------------------------------------------------------------
| Fiyat robotu (her sabah)
|--------------------------------------------------------------------------
| 1) Önce resmî poz: kalemde 'poz' varsa ve rayiç tablosunda fiyatı varsa o kullanılır.
| 2) Yoksa Google'da (Gemini + Google Arama) güncel Türkiye fiyatları aranır;
|    kaynakların ortancası (KDV dahil) × çarpan yayına yazılır.
|    - Poz yerine geçen web fiyatı:   × poz_multiplier   (1,40 — müteahhit kârı ve genel gider payı)
|    - Doğrudan piyasa fiyatı kalemi: × market_multiplier (1,00)
| 3) Uzman beklenmeden yayına girer; Kainta İnşaat Uzmanı admin › Fiyat Robotu'nda kontrol eder.
|    Güvenlik: önceki değerin yarısından az / iki katından fazla ya da tek kaynaklı sonuç
|    yayına girmez, "Onay bekliyor"a düşer.
| Fiyat dizileri [ekonomik, orta, premium]: orta değer araştırılır, diğerleri mevcut oranla ölçeklenir.
| Arama maliyeti: Google Arama ayda 5.000 sorguya kadar ücretsiz (Gemini API, Ekim 2026).
*/

return [
    'enabled' => (bool) env('PRICE_ROBOT_ENABLED', true),
    'daily_limit' => (int) env('PRICE_ROBOT_DAILY_LIMIT', 20),
    'refresh_days' => (int) env('PRICE_ROBOT_REFRESH_DAYS', 30),   // yalnız yeni kalemler + bu kadar gün önce araştırılanlar
    'poz_multiplier' => 1.40,
    'market_multiplier' => 1.00,
    'min_sources' => 2,
    'max_jump' => 2.0,     // önceki / yeni oranı bu aralığın dışındaysa onay bekler
    'model' => env('PRICE_ROBOT_MODEL', env('GEMINI_PRICE_RESEARCH_MODEL', 'gemini-3.6-flash')),

    // path: config yolu · q: Google'da aranacak ifade (Türkçe) · unit: fiyatın birimi · divide: bulunan fiyat ÷ (örn. 5 cm levha → cm başına) · factor: × (örn. m² → 60 cm yükseklikte mtül)
    'items' => [
        // Mutfak
        ['path' => 'worksitem_estimators.mutfak.cabinet_m', 'tool' => 'mutfak-maliyet-hesaplayici', 'label' => 'Mutfak dolabı (alt + üst, montaj dahil)', 'q' => 'mutfak dolabı metre fiyatı alt üst dolap montaj dahil', 'unit' => 'mtül'],
        ['path' => 'worksitem_estimators.mutfak.counter_m.laminat', 'tool' => 'mutfak-maliyet-hesaplayici', 'label' => 'Laminat tezgah', 'q' => 'laminat mutfak tezgahı metre fiyatı montaj dahil', 'unit' => 'mtül'],
        ['path' => 'worksitem_estimators.mutfak.counter_m.granit', 'tool' => 'mutfak-maliyet-hesaplayici', 'label' => 'Granit tezgah', 'q' => 'granit mutfak tezgahı metre fiyatı montaj dahil', 'unit' => 'mtül'],
        ['path' => 'worksitem_estimators.mutfak.counter_m.kuvars', 'tool' => 'mutfak-maliyet-hesaplayici', 'label' => 'Kuvars (çimstone) tezgah', 'q' => 'kuvars çimstone mutfak tezgahı metre fiyatı montaj dahil', 'unit' => 'mtül'],
        ['path' => 'worksitem_estimators.mutfak.backsplash_m', 'tool' => 'mutfak-maliyet-hesaplayici', 'label' => 'Tezgah arası kaplama (60 cm yükseklik)', 'q' => 'mutfak tezgah arası seramik kaplama m2 fiyatı malzeme ve işçilik dahil', 'unit' => 'm²', 'factor' => 0.6],
        ['path' => 'worksitem_estimators.mutfak.extras.ankastre', 'tool' => 'mutfak-maliyet-hesaplayici', 'label' => 'Ankastre set (fırın + ocak + davlumbaz)', 'q' => 'ankastre set fiyatı fırın ocak davlumbaz', 'unit' => 'set'],

        // Parke ve zemin
        ['path' => 'worksitem_estimators.parke.material_m2.laminat', 'tool' => 'parke-maliyet-hesaplayici', 'label' => 'Laminat parke (8 mm)', 'q' => '8 mm laminat parke m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.parke.material_m2.spc', 'tool' => 'parke-maliyet-hesaplayici', 'label' => 'SPC parke', 'q' => 'SPC parke m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.parke.material_m2.ahsap', 'tool' => 'parke-maliyet-hesaplayici', 'label' => 'Lamine ahşap parke', 'q' => 'lamine ahşap parke m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.parke.labor_m2.laminat', 'tool' => 'parke-maliyet-hesaplayici', 'label' => 'Laminat parke döşeme işçiliği', 'q' => 'laminat parke döşeme işçilik m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.parke.skirting_m', 'tool' => 'parke-maliyet-hesaplayici', 'label' => 'Süpürgelik (montaj dahil)', 'q' => 'parke süpürgelik metre fiyatı montaj dahil', 'unit' => 'mtül'],
        ['path' => 'worksitem_estimators.zemin.types.mermer.material', 'tool' => 'zemin-kaplama', 'label' => 'Mermer zemin kaplama malzemesi', 'q' => 'mermer zemin kaplama m2 fiyatı malzeme', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.zemin.types.mermer.labor', 'tool' => 'zemin-kaplama', 'label' => 'Mermer döşeme işçiliği', 'q' => 'mermer döşeme işçilik m2 fiyatı', 'unit' => 'm²'],

        // Mantolama
        ['path' => 'worksitem_estimators.mantolama.board_m2.eps', 'tool' => 'mantolama-maliyeti', 'label' => 'EPS mantolama levhası (cm başına)', 'q' => '5 cm karbonlu EPS mantolama levhası m2 fiyatı', 'unit' => 'm² · 1 cm', 'divide' => 5],
        ['path' => 'worksitem_estimators.mantolama.board_m2.tasyunu', 'tool' => 'mantolama-maliyeti', 'label' => 'Taşyünü mantolama levhası (cm başına)', 'q' => '5 cm taşyünü mantolama levhası m2 fiyatı', 'unit' => 'm² · 1 cm', 'divide' => 5],
        ['path' => 'worksitem_estimators.mantolama.labor_m2', 'tool' => 'mantolama-maliyeti', 'label' => 'Mantolama işçiliği', 'q' => 'mantolama işçilik m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.mantolama.scaffold_m2', 'tool' => 'mantolama-maliyeti', 'label' => 'Cephe iskelesi (1 ay, kurulum dahil)', 'q' => 'cephe iskelesi m2 kiralama fiyatı kurulum dahil', 'unit' => 'm²'],

        // PVC
        ['path' => 'worksitem_estimators.pvc.glass_m2.isi', 'tool' => 'pvc-dograma-hesaplayici', 'label' => 'Isıcam (4+16+4)', 'q' => 'ısıcam 4+16+4 m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.pvc.install_each', 'tool' => 'pvc-dograma-hesaplayici', 'label' => 'PVC pencere montajı', 'q' => 'PVC pencere montaj ücreti adet', 'unit' => 'adet'],
        ['path' => 'worksitem_estimators.pvc.shutter_m2', 'tool' => 'pvc-dograma-hesaplayici', 'label' => 'Alüminyum panjur', 'q' => 'alüminyum panjur m2 fiyatı montaj dahil', 'unit' => 'm²'],

        // Şap, sıva, alçı
        ['path' => 'worksitem_estimators.sap.labor_m2.klasik', 'tool' => 'sap-hesaplayici', 'label' => 'Şap işçiliği (klasik)', 'q' => 'şap atma işçilik m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.sap.selfleveling_kg', 'tool' => 'sap-hesaplayici', 'label' => 'Self-leveling tesviye şapı', 'q' => '25 kg self leveling tesviye şapı fiyatı', 'unit' => 'kg', 'divide' => 25],
        ['path' => 'worksitem_estimators.siva.labor_m2.geleneksel', 'tool' => 'kara-siva-hesaplama', 'label' => 'Kaba sıva işçiliği', 'q' => 'kaba sıva işçilik m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.siva.machine_bag_25kg', 'tool' => 'kara-siva-hesaplama', 'label' => 'Makine sıvası (25 kg)', 'q' => 'makine sıvası 25 kg torba fiyatı', 'unit' => 'torba'],
        ['path' => 'worksitem_estimators.alci.labor_m2.perlit', 'tool' => 'alci-siva-hesaplama', 'label' => 'Perlitli alçı sıva işçiliği', 'q' => 'alçı sıva işçilik m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.alci.saten_kg', 'tool' => 'alci-siva-hesaplama', 'label' => 'Saten alçı', 'q' => 'saten alçı 25 kg fiyatı', 'unit' => 'kg', 'divide' => 25],
        ['path' => 'worksitem_estimators.alci.perlit_kg', 'tool' => 'alci-siva-hesaplama', 'label' => 'Perlitli sıva alçısı', 'q' => 'perlitli sıva alçısı 35 kg fiyatı', 'unit' => 'kg', 'divide' => 35],

        // Duvar
        ['path' => 'worksitem_estimators.duvar.materials.tugla135.2', 'tool' => 'duvar-maliyet-hesaplayici', 'label' => '13,5 cm yatay delikli tuğla (adet)', 'q' => '13.5 luk yatay delikli tuğla adet fiyatı', 'unit' => 'adet'],
        ['path' => 'worksitem_estimators.duvar.materials.gazbeton15.2', 'tool' => 'duvar-maliyet-hesaplayici', 'label' => '15 cm gazbeton (adet)', 'q' => '15 lik gazbeton blok adet fiyatı 60x25', 'unit' => 'adet'],
        ['path' => 'worksitem_estimators.duvar.materials.tugla135.4', 'tool' => 'duvar-maliyet-hesaplayici', 'label' => 'Tuğla duvar örme işçiliği', 'q' => 'tuğla duvar örme işçilik m2 fiyatı', 'unit' => 'm²'],

        // Su tesisatı
        ['path' => 'worksitem_estimators.tesisat.bath', 'tool' => 'su-tesisat-hesaplama', 'label' => 'Banyo su tesisatı yenileme (temiz + pis su)', 'q' => 'banyo su tesisatı yenileme fiyatı malzeme işçilik dahil', 'unit' => 'banyo'],
        ['path' => 'worksitem_estimators.tesisat.kitchen', 'tool' => 'su-tesisat-hesaplama', 'label' => 'Mutfak su tesisatı yenileme', 'q' => 'mutfak su tesisatı yenileme fiyatı', 'unit' => 'mutfak'],
        ['path' => 'worksitem_estimators.tesisat.booster', 'tool' => 'su-tesisat-hesaplama', 'label' => 'Hidrofor (montaj dahil)', 'q' => 'ev tipi hidrofor fiyatı montaj dahil', 'unit' => 'adet'],
        // 2. dalga: kaba inşaat
        ['path' => 'worksitem_estimators.bahce.block_m2', 'tool' => 'bahce-duvari-maliyeti', 'label' => 'Bahçe duvarı briket örgü (m²)', 'q' => 'briket bahçe duvarı örme m2 fiyatı işçilik dahil', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.bahce.fence_m', 'tool' => 'bahce-duvari-maliyeti', 'label' => 'Bahçe teli / panel çit (metre)', 'q' => 'panel çit metre fiyatı montaj dahil', 'unit' => 'mtül'],
        ['path' => 'worksitem_estimators.iskele.m2.cephe', 'tool' => 'iskele-metraji-maliyeti', 'label' => 'Cephe iskelesi (1 ay, kurulum dahil)', 'q' => 'cephe iskelesi m2 kiralama fiyatı kurulum söküm dahil', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.hafriyat.truck_trip', 'tool' => 'hafriyat-kazi-hesabi', 'label' => 'Hafriyat kamyonu sefer ücreti', 'q' => 'hafriyat kamyonu sefer fiyatı döküm dahil', 'unit' => 'sefer'],
        ['path' => 'worksitem_estimators.saha.pump', 'tool' => 'saha-betonu-hesaplayici', 'label' => 'Beton pompası (döküm başına)', 'q' => 'beton pompası kiralama fiyatı döküm başına', 'unit' => 'döküm'],
        ['path' => 'worksitem_estimators.saha.finish_m2.helikopter', 'tool' => 'saha-betonu-hesaplayici', 'label' => 'Helikopter perdah (m²)', 'q' => 'helikopter perdah m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.saha.mesh_m2.tek', 'tool' => 'saha-betonu-hesaplayici', 'label' => 'Çelik hasır (Q131, m²)', 'q' => 'Q131 çelik hasır m2 fiyatı', 'unit' => 'm²'],
    ],
];

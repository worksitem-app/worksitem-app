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
    'daily_limit' => (int) env('PRICE_ROBOT_DAILY_LIMIT', 55),   // ≈350 kalem ÷ 7 gün; ayda ≈1.550 arama (ücretsiz kota 5.000)
    'refresh_days' => (int) env('PRICE_ROBOT_REFRESH_DAYS', 7),    // her kalem en geç bu kadar günde bir yeniden araştırılır (50 kalem ≈ ayda 215 arama, ücretsiz kota 5.000)
    'poz_multiplier' => 1.40,
    'market_multiplier' => 1.00,
    'min_sources' => 2,
    'max_jump' => 2.0,     // önceki / yeni oranı bu aralığın dışındaysa onay bekler
    'model' => env('PRICE_ROBOT_MODEL', env('GEMINI_PRICE_RESEARCH_MODEL', 'gemini-3.6-flash')),

    // path: config yolu · q: Google'da aranacak ifade (Türkçe) · unit: fiyatın birimi · divide: bulunan fiyat ÷ (örn. 5 cm levha → cm başına) · factor: × (örn. m² → 60 cm yükseklikte mtül)
    'items' => array_merge([
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

        // v76: yapı kimyasalları (seramik, saha betonu)
        ['path' => 'worksitem_estimators.seramik.epoxy_grout_3kg', 'tool' => 'seramik-maliyet-hesaplayici', 'label' => 'Epoksi derz dolgu (3 kg set)', 'q' => 'epoksi derz dolgu 3 kg fiyatı', 'unit' => 'set', 'setting_key' => 'tile_epoxy_grout_3kg'],
        ['path' => 'worksitem_estimators.seramik.level_clip', 'tool' => 'seramik-maliyet-hesaplayici', 'label' => 'Karo seviyeleme klipsi (adet)', 'q' => 'seramik seviyeleme klipsi 100 adet fiyatı', 'unit' => 'adet', 'divide' => 100, 'setting_key' => 'tile_level_clip'],
        ['path' => 'worksitem_estimators.saha.densifier_m2', 'tool' => 'saha-betonu-hesaplayici', 'label' => 'Sıvı beton yüzey sertleştirici uygulaması', 'q' => 'lityum silikat beton yüzey sertleştirici uygulama m2 fiyatı', 'unit' => 'm²'],
        ['path' => 'worksitem_estimators.beton_onarim.mortar_kg.cimento', 'tool' => 'beton-onarim-maliyeti', 'label' => 'Çimento esaslı yapısal tamir harcı', 'q' => 'R4 yapısal tamir harcı 25 kg fiyatı', 'unit' => 'kg', 'divide' => 25, 'setting_key' => 'repair_mortar_cement_kg'],
        ['path' => 'worksitem_estimators.beton_onarim.grout_kg.cimento', 'tool' => 'beton-onarim-maliyeti', 'label' => 'Çimento esaslı akışkan grout', 'q' => 'akışkan grout harcı 25 kg fiyatı', 'unit' => 'kg', 'divide' => 25, 'setting_key' => 'repair_grout_cement_kg'],

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
        // Banyo ve WC (admin fiyat defteri: unit_prices) — unit_keys sırası: ekonomik, orta, premium
        ['path' => 'worksitem_bathroom.unit.fixtures', 'tool' => 'banyo-maliyet-hesaplayici', 'label' => 'Banyo vitrifiye + armatür seti', 'q' => 'banyo vitrifiye seti fiyatı klozet lavabo batarya', 'unit' => 'set', 'unit_keys' => ['banyo_vitrifiye_eko', 'banyo_vitrifiye_orta', 'banyo_vitrifiye_premium']],
        ['path' => 'worksitem_bathroom.unit.tile_m2', 'tool' => 'banyo-maliyet-hesaplayici', 'label' => 'Banyo seramiği (malzeme + işçilik)', 'q' => 'banyo seramik döşeme m2 fiyatı malzeme ve işçilik dahil', 'unit' => 'm²', 'unit_keys' => ['banyo_seramik_m2_eko', 'banyo_seramik_m2_orta', 'banyo_seramik_m2_premium']],
        ['path' => 'worksitem_bathroom.showers.kabin.price', 'tool' => 'banyo-maliyet-hesaplayici', 'label' => 'Duşakabin (montaj dahil)', 'q' => 'duşakabin fiyatı 90x90 montaj dahil', 'unit' => 'adet', 'unit_keys' => ['banyo_dus_kabin_eko', 'banyo_dus_kabin_orta', 'banyo_dus_kabin_premium']],
        ['path' => 'worksitem_bathroom.extras.ex_dolap.price', 'tool' => 'banyo-maliyet-hesaplayici', 'label' => 'Banyo dolabı + lavabo + ayna', 'q' => 'lavabolu banyo dolabı aynalı fiyatı 80 cm', 'unit' => 'takım', 'unit_keys' => ['banyo_dolap_eko', 'banyo_dolap_orta', 'banyo_dolap_premium']],
        ['path' => 'worksitem_bathroom.unit.waterproof_m2', 'tool' => 'banyo-maliyet-hesaplayici', 'label' => 'Banyo zemin su yalıtımı', 'q' => 'banyo su yalıtımı m2 fiyatı işçilik dahil', 'unit' => 'm²', 'unit_keys' => ['banyo_yalitim_m2']],
    ], require __DIR__.'/worksitem_price_robot_items.php'),

    /*
    | Kapsama: her araç ya robotta (items), ya bağlı olduğu aracın fiyatlarını kullanır (linked),
    | ya resmî kaynaktan güncellenir (official), ya da fiyat içermez (no_price). Test bunu zorunlu kılar.
    */
    'coverage' => [
        'linked' => [
            'wc-maliyet-hesabi' => 'banyo-maliyet-hesaplayici',
            'duvar-orgu' => 'duvar-maliyet-hesaplayici',
            'banyo-planlayici' => 'banyo-maliyet-hesaplayici',
            'wc-planlayici' => 'banyo-maliyet-hesaplayici',
            'mutfak-planlayici' => 'mutfak-maliyet-hesaplayici',
        ],
        'official' => [
            'alcipan-hesaplayici' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'cati-maliyet-hesaplayici' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'epoksi-zemin-hesaplayici' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'havalandirma-hesaplayici' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'yukseltilmis-doseme-hesaplayici' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'birim-fiyat-analiz' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'ihale-modulu' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'otomatik-kesif-fiyatlandirma' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'dijital-metraj' => 'ÇŞB yapım işleri birim fiyatları (poz)',
            'tahmini-bina-maliyeti' => 'ÇŞB yapı yaklaşık birim maliyetleri tebliği',
            'statik-proje-bedeli' => 'ÇŞB yapı yaklaşık birim maliyetleri tebliği',
            'sgk-asgari-iscilik' => 'SGK asgari işçilik oranları',
            'insaat-muhendisi-maas-hesabi' => 'piyasa maaş varsayımı (config/worksitem_mevzuat_2026.php)',
        ],
        'no_price' => [
            'azami-konut-kredisi' => 'kullanıcı girdisi (gelir, faiz, vade)',
            'daire-kira-degeri' => 'kullanıcı girdisi (piyasa değeri)',
            'kat-karsiligi-analiz' => 'kullanıcı girdisi (arsa ve satış değerleri)',
            'basit-kiris-statigi' => 'mühendislik hesabı', 'bina-agirlik-hesabi' => 'mühendislik hesabı',
            'bombeli-tank-hacmi' => 'mühendislik hesabı', 'donati-tahvil-hesaplayici' => 'mühendislik hesabı',
            'karot-dayanim' => 'mühendislik hesabı', 'kolon-filiz-boyu' => 'mühendislik hesabı',
            'kolon-tasima-kapasitesi' => 'mühendislik hesabı', 'yapi-dinamigi' => 'mühendislik hesabı',
            'pompa-gucu-hesabi' => 'mühendislik hesabı', 'klima-btu-hesaplayici' => 'mühendislik hesabı',
            'malzeme-agirligi' => 'mühendislik hesabı', 'metrekup-hacim-hesaplama' => 'mühendislik hesabı',
            'kesim-optimizasyonu' => 'mühendislik hesabı', 'pafta-olcu-hesaplayici' => 'mühendislik hesabı',
            'havalandirma-kanali' => 'mühendislik hesabı',
            'taks-kaks-hesaplayici' => 'imar hesabı', 'siginak-yonetmeligi' => 'imar hesabı',
            'belge-olusturucu' => 'belge şablonu', 'duvar-rengi-gorsellestirici' => 'renk görselleştirme',
            'oda-planlayici' => 'yapay zekâ tasarım',
        ],
    ],
];

<?php

/*
|--------------------------------------------------------------------------
| Canlıya çıkış: araç yayın ön ayarları
|--------------------------------------------------------------------------
| php artisan tools:publish ilk-dalga        → listedekileri yayına alır
| php artisan tools:publish ilk-dalga --dry-run
| Sonradan admin › Sistem Yönetimi › Araç Yayın Durumu'ndan tek tek değiştirilebilir.
| Listede olmayan araçlara dokunulmaz (kapatmak için --only).
*/

return [
    'presets' => [
        // 1. dalga (14.10.2026): 25 tadilat aracı + zaten yayında olanlar (AI özellikleri hariç)
        'ilk-dalga' => [
            // tadilat maliyeti ve planlayıcılar
            'banyo-maliyet-hesaplayici', 'banyo-planlayici', 'wc-maliyet-hesabi', 'wc-planlayici',
            'mutfak-maliyet-hesaplayici', 'seramik-maliyet-hesaplayici', 'boya-hesaplayici', 'duvar-rengi-gorsellestirici',
            'parke-maliyet-hesaplayici', 'zemin-kaplama', 'mantolama-maliyeti', 'pvc-dograma-hesaplayici',
            'su-tesisat-hesaplama', 'alcipan-hesaplayici', 'asma-tavan-hesaplayici', 'su-yalitimi-hesaplayici',
            'epoksi-zemin-hesaplayici', 'radyator-petek-hesaplayici', 'klima-btu-hesaplayici', 'cati-maliyet-hesaplayici',
            // ince ve kaba işler (ev sahibinin de aradığı)
            'beton-hesaplayici', 'sap-hesaplayici', 'alci-siva-hesaplama', 'kara-siva-hesaplama', 'duvar-maliyet-hesaplayici',
        ],

        // 2. dalga (14.10.2026): yapay zekâ özellikleri dışındaki kalan 48 araç
        // (havalandirma-kanali eski adres: havalandirma-hesaplayici'ya yönlenir)
        'ikinci-dalga' => [
            'asansor-zorunlulugu', 'azami-konut-kredisi', 'bahce-duvari-maliyeti', 'basit-kiris-statigi',
            'belge-olusturucu', 'bina-agirlik-hesabi', 'birim-fiyat-analiz', 'bombeli-tank-hacmi',
            'cimento-kum-hesaplama', 'daire-kira-degeri', 'demir-metraji-hesaplayici', 'deprem-izolator-maliyeti',
            'dijital-metraj', 'donati-tahvil-hesaplayici', 'duvar-orgu', 'enerji-tuketim-hesaplama',
            'gunes-paneli-hesaplama', 'hafriyat-kazi-hesabi', 'havalandirma-hesaplayici', 'ihale-modulu',
            'insaat-muhendisi-maas-hesabi', 'is-makinesi-maliyeti', 'iscilik-maliyeti', 'iskele-metraji-maliyeti',
            'jenerator-hesaplama', 'kablo-kesiti-hesabi', 'kalip-plywood-maliyeti', 'karot-dayanim',
            'kat-karsiligi-analiz', 'kesim-optimizasyonu', 'kolon-filiz-boyu', 'kolon-tasima-kapasitesi',
            'lojistik-maliyeti', 'malzeme-agirligi', 'merdiven-basamak-hesabi', 'metrekup-hacim-hesaplama',
            'pafta-olcu-hesaplayici', 'pompa-gucu-hesabi', 'saha-betonu-hesaplayici', 'sandvic-panel-hesaplayici',
            'sgk-asgari-iscilik', 'siginak-yonetmeligi', 'statik-proje-bedeli', 'tahmini-bina-maliyeti',
            'taks-kaks-hesaplayici', 'yagmur-borusu-hesabi', 'yapi-dinamigi', 'yukseltilmis-doseme-hesaplayici',
        ],

        // Yapay zekâ özellikleri: AI_SPEND_ENABLED=true ve sağlayıcı bütçe limitleri tanımlandıktan sonra.
        // (Seramik görselleştirici araç listesinde değil; AI kredi sistemiyle korunur.)
        'yapay-zeka' => [
            'oda-planlayici', 'otomatik-kesif-fiyatlandirma',
        ],
    ],
];

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

        // 2. dalga: kaba inşaat ve teknik maliyet araçları (hesap motoru)
        'ikinci-dalga' => [
            'duvar-orgu', 'bahce-duvari-maliyeti', 'iskele-metraji-maliyeti', 'kalip-plywood-maliyeti', 'hafriyat-kazi-hesabi',
            'saha-betonu-hesaplayici', 'sandvic-panel-hesaplayici', 'gunes-paneli-hesaplama', 'tahmini-bina-maliyeti',
            'statik-proje-bedeli', 'deprem-izolator-maliyeti',
        ],

        // Yapay zekâ özellikleri: AI_SPEND_ENABLED=true ve sağlayıcı bütçe limitleri tanımlandıktan sonra.
        // (Seramik görselleştirici araç listesinde değil; AI kredi sistemiyle korunur.)
        'yapay-zeka' => [
            'oda-planlayici', 'otomatik-kesif-fiyatlandirma',
        ],
    ],
];

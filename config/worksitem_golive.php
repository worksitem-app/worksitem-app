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
        // 1. dalga: Kainta'ya iş getiren tadilat araçları + zaten yayında olanlar (AI özellikleri hariç)
        'ilk-dalga' => [
            'banyo-maliyet-hesaplayici', 'mutfak-maliyet-hesaplayici', 'wc-maliyet-hesabi',
            'seramik-maliyet-hesaplayici', 'boya-hesaplayici', 'parke-maliyet-hesaplayici', 'zemin-kaplama',
            'mantolama-maliyeti', 'pvc-dograma-hesaplayici', 'su-tesisat-hesaplama',
            'beton-hesaplayici', 'duvar-rengi-gorsellestirici',
        ],

        // 2. dalga: kaba inşaat ve teknik maliyet araçları (hesap motoru)
        'ikinci-dalga' => [
            'sap-hesaplayici', 'kara-siva-hesaplama', 'alci-siva-hesaplama', 'duvar-maliyet-hesaplayici', 'duvar-orgu',
            'bahce-duvari-maliyeti', 'iskele-metraji-maliyeti', 'kalip-plywood-maliyeti', 'hafriyat-kazi-hesabi',
            'saha-betonu-hesaplayici', 'cati-maliyet-hesaplayici', 'alcipan-hesaplayici', 'asma-tavan-hesaplayici',
            'su-yalitimi-hesaplayici', 'epoksi-zemin-hesaplayici', 'sandvic-panel-hesaplayici', 'radyator-petek-hesaplayici',
            'gunes-paneli-hesaplama', 'tahmini-bina-maliyeti', 'statik-proje-bedeli', 'deprem-izolator-maliyeti',
        ],

        // Yapay zekâ özellikleri: AI_SPEND_ENABLED=true ve sağlayıcı bütçe limitleri tanımlandıktan sonra.
        // (Seramik görselleştirici araç listesinde değil; AI kredi sistemiyle korunur.)
        'yapay-zeka' => [
            'oda-planlayici', 'otomatik-kesif-fiyatlandirma',
        ],
    ],
];

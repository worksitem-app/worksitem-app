<?php

/*
 * Teklif pazaryeri (kullanıcı ↔ kayıtlı firma).
 * Komisyon: "eşleşme bedeli" — kullanıcı teklifi kabul edince firma öder, ödeme sonrası iletişim bilgileri açılır.
 * Kurucu kararı 15.10.2026: %5, en az 250 TL, en çok 7.500 TL (KDV hariç); fatura mali müşavir tarafından kesilir.
 */
return [
    'enabled' => (bool) env('MARKET_ENABLED', false),   // kullanıcı tarafı (teklif al) avukat onayına kadar kapalı; firma kaydı ve paneli açık

    // Kurucu firma programı (kurucu kararı 15.10.2026): bu tarihe kadar eşleşme bedeli alınmaz; firma eşleşmeyi tek tıkla onaylar.
    // Sonrası için ücret kayıt sayfasında ve sözleşmede baştan yazılıdır. Boş bırakılırsa ücret hemen uygulanır.
    'free_until' => env('MARKET_FREE_UNTIL', '2027-03-31'),

    'fee_rate' => (float) env('MARKET_FEE_RATE', 0.05),
    'fee_min' => (int) env('MARKET_FEE_MIN', 250),
    'fee_max' => (int) env('MARKET_FEE_MAX', 7500),

    'max_invites' => (int) env('MARKET_MAX_INVITES', 5),   // bir talep en fazla kaç firmaya gider
    'request_days' => 14,                                 // talep açık kalma süresi
    'fee_hours' => 48,                                    // kabul sonrası firmanın ödeme süresi
    'panel_session_days' => 30,

    'start_prefs' => ['hemen' => 'Hemen (2 hafta içinde)', '1ay' => '1 ay içinde', '3ay' => '1–3 ay içinde', 'belirsiz' => 'Henüz belli değil'],

    // Araç → iş kolu (+ önerilen alt hizmet). Listede olmayan araçta "firmalardan teklif al" gösterilmez.
    'tools' => [
        'banyo-maliyet-hesaplayici' => ['ince-insaat', 'Banyo Tadilatı ve Dekorasyonu'],
        'banyo-planlayici' => ['ince-insaat', 'Banyo Tadilatı ve Dekorasyonu'],
        'wc-maliyet-hesabi' => ['ince-insaat', 'Banyo Tadilatı ve Dekorasyonu'],
        'wc-planlayici' => ['ince-insaat', 'Banyo Tadilatı ve Dekorasyonu'],
        'mutfak-maliyet-hesaplayici' => ['ahsap-ve-mobilya', 'Mutfak Tadilatı ve Dekorasyonu'],
        'mutfak-planlayici' => ['ahsap-ve-mobilya', 'Mutfak Tadilatı ve Dekorasyonu'],
        'boya-hesaplayici' => ['ince-insaat', null],
        'duvar-rengi-gorsellestirici' => ['ince-insaat', null],
        'seramik-maliyet-hesaplayici' => ['zemin-kaplama', null],
        'zemin-kaplama' => ['zemin-kaplama', null],
        'epoksi-zemin-hesaplayici' => ['zemin-kaplama', null],
        'parke-maliyet-hesaplayici' => ['zemin-kaplama', 'Parke Döşeme'],
        'yukseltilmis-doseme-hesaplayici' => ['zemin-kaplama', null],
        'alci-siva-hesaplama' => ['ince-insaat', null],
        'alcipan-hesaplayici' => ['ince-insaat', 'Alçıpan Bölme Duvar'],
        'asma-tavan-hesaplayici' => ['ince-insaat', 'Alçıpan Asma Tavan'],
        'sap-hesaplayici' => ['ince-insaat', null],
        'kara-siva-hesaplama' => ['kaba-insaat', null],
        'duvar-maliyet-hesaplayici' => ['kaba-insaat', null],
        'duvar-orgu' => ['kaba-insaat', null],
        'bahce-duvari-maliyeti' => ['kaba-insaat', null],
        'beton-hesaplayici' => ['kaba-insaat', null],
        'saha-betonu-hesaplayici' => ['kaba-insaat', null],
        'kalip-plywood-maliyeti' => ['kaba-insaat', null],
        'hafriyat-kazi-hesabi' => ['alt-yapi', null],
        'mantolama-maliyeti' => ['izolasyon', null],
        'su-yalitimi-hesaplayici' => ['izolasyon', null],
        'pvc-dograma-hesaplayici' => ['aluminyum-pvc', 'PVC Pencere ve Kapı Sistemleri'],
        'cati-maliyet-hesaplayici' => ['cati', null],
        'sandvic-panel-hesaplayici' => ['cati', null],
        'iskele-metraji-maliyeti' => ['dis-cephe', null],
        'tahmini-bina-maliyeti' => ['insaat-taahhut', null],
        'kat-karsiligi-analiz' => ['insaat-taahhut', null],
        'statik-proje-bedeli' => ['mimarlik-ve-muhendislik', null],
        'deprem-izolator-maliyeti' => ['yapi-guclendirme', null],
        'klima-btu-hesaplayici' => ['mekanik-tesisat', null],
        'havalandirma-hesaplayici' => ['mekanik-tesisat', null],
        'radyator-petek-hesaplayici' => ['mekanik-tesisat', null],
        'su-tesisat-hesaplama' => ['mekanik-tesisat', null],
        'gunes-paneli-hesaplama' => ['elektrik', null],
        'jenerator-hesaplama' => ['elektrik', null],
        'kablo-kesiti-hesabi' => ['elektrik', null],
    ],
];

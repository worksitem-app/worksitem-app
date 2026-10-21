<?php

/*
|--------------------------------------------------------------------------
| Pilot dönem (21.10.2026 kurucu kararı)
|--------------------------------------------------------------------------
| Keşif, teklif ve uygulama yalnız pilot bölgede (İstanbul) ve yalnız Kainta İnşaat tarafından verilir.
| Firma üyeliği, firma rehberi ve kayıtlı firmalardan teklif alma KAPALI; altyapı (kayıt, panel,
| eşleşme, sözleşmeler) kodda hazır bekler. Açmak için (avukat ve mali müşavir onayından sonra):
|   WORKSITEM_PILOT=false  → firma kaydı açılır (MARKET_ENABLED / FIRMS_DIRECTORY_ENABLED ayrıca)
|   ya da yalnız firma kaydı: FIRMS_SIGNUP_ENABLED=true
| Hesaplama araçları pilotta da tüm Türkiye için çalışır.
*/

return [
    'enabled' => (bool) env('WORKSITEM_PILOT', true),

    // Hizmet verilen iller (keşif ve uygulama). Karşılaştırma büyük/küçük harf ve Türkçe karakter duyarsız.
    'cities' => ['İstanbul'],
    'area_label' => 'İstanbul',

    // Pilotta tek hizmet sağlayıcı (sözleşme ve faturada künyedeki unvan kullanılır)
    'provider' => 'Kainta İnşaat',

    // Pilotta firma üyeliği varsayılan kapalı
    'firm_signup' => (bool) env('FIRMS_SIGNUP_ENABLED', false),
];

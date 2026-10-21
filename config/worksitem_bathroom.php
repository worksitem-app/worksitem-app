<?php

/*
|--------------------------------------------------------------------------
| Banyo tadilat maliyeti v2 — fiyat tablosu
|--------------------------------------------------------------------------
|
| TÜM fiyatlar TL, KDV dahil, "orta" piyasa değeridir (malzeme + işçilik).
| Sayfadaki hesap (resources/js inline) ve sunucudaki tablo
| (App\Services\BathroomEstimator) bu dosyayı kullanır; ikisi aynı sonucu verir.
|
| Fiyat değiştirmek için yalnızca bu dosyayı düzenleyin, sonra:
|   php artisan config:cache
|
| Kalite kademeli fiyatlar: [ekonomik, orta, premium]
|
*/

return [

    'prices_reviewed' => 'Ekim 2026',

    // Yerinde keşif bedeli (tüm araçlarda, PDF raporda ve keşif onay sayfasında yazar). KDV hariçtir.
    'inspection_fee' => (int) env('WORKSITEM_INSPECTION_FEE', 2500),
    'inspection_vat_included' => (bool) env('WORKSITEM_INSPECTION_VAT_INCLUDED', false),
    // Keşiften sonra bu süre içinde Kainta İnşaat ile sözleşme imzalanırsa keşif bedeli iş bedelinden düşülür
    'inspection_credit_days' => (int) env('WORKSITEM_INSPECTION_CREDIT_DAYS', 15),

    // Aralık bandı: tipik değerin altı / üstü (usta, marka, şehir farkı)
    'band' => ['low' => 0.85, 'high' => 1.20],

    // Beklenmedik giderler (eski tesisat, şap, nem hasarı) — ayrı satır
    'contingency_rate' => 0.10,

    'door_area' => 1.6, // m², duvar alanından düşülür

    'types' => [
        'wc' => ['label' => 'Misafir WC', 'hint' => 'Klozet + lavabo', 'w' => 1.2, 'l' => 1.5, 'h' => 2.6, 'shower' => false, 'fixture_factor' => 0.7],
        'standart' => ['label' => 'Standart banyo', 'hint' => 'Duş veya küvetli', 'w' => 1.8, 'l' => 2.4, 'h' => 2.6, 'shower' => true, 'fixture_factor' => 1.0],
        'ebeveyn' => ['label' => 'Ebeveyn banyosu', 'hint' => 'Geniş, çift lavabolu', 'w' => 2.5, 'l' => 3.2, 'h' => 2.7, 'shower' => true, 'fixture_factor' => 1.3],
    ],

    'scopes' => [
        'yuzey' => ['label' => 'Yüzey yenileme', 'hint' => 'Kaplama ve vitrifiye yenilenir, tesisat aynı kalır', 'days' => [4, 7]],
        'komple' => ['label' => 'Komple yenileme', 'hint' => 'Kırım, yeni tesisat, su yalıtımı, sıfırdan banyo', 'days' => [8, 15]],
        'tasima' => ['label' => 'Komple + yerleşim değişikliği', 'hint' => 'Klozet, duş veya lavabonun yeri değişir', 'days' => [12, 20]],
    ],

    'qualities' => [
        'eko' => ['label' => 'Ekonomik', 'hint' => 'Yerli standart ürünler', 'i' => 0],
        'orta' => ['label' => 'Orta', 'hint' => 'Bilinen markalar', 'i' => 1],
        'premium' => ['label' => 'Premium', 'hint' => 'Tasarım ürünleri, büyük ebat seramik', 'i' => 2],
    ],

    'showers' => [
        'kabin' => ['label' => 'Duşakabin', 'price' => [9000, 14000, 28000]],
        'kuvet' => ['label' => 'Küvet', 'price' => [12000, 18000, 40000]],
        'gomme' => ['label' => 'Gömme duş (cam panel)', 'price' => [15000, 24000, 45000]],
    ],

    'unit' => [
        'demolition_m2' => 350,          // kırım + moloz atımı, kaplama m² başına (komple/taşıma)
        'surface_removal' => 3000,       // yüzey yenilemede vitrifiye sökümü (sabit)
        'plumbing_base' => 16000,        // komple tesisat (su + pis su) sabit
        'plumbing_m2' => 1500,           // zemin m² başına ek tesisat
        'relocation_factor' => 1.6,      // taşıma: tesisat çarpanı
        'relocation_fixed' => 8000,      // taşıma: ek sabit (kanal açma, eğim)
        'waterproof_m2' => 1200,         // zemin su yalıtımı m²
        'waterproof_shower_zone' => 4800,// duş bölgesi duvar yalıtımı (sabit)
        'tile_m2' => [750, 950, 1600],   // seramik malzeme + yapıştırıcı + derz + işçilik
        'fixtures' => [12000, 20000, 45000], // klozet + lavabo + bataryalar + aksesuar
    ],

    'extras' => [
        'ex_dolap' => ['label' => 'Lavabo dolabı + ayna', 'price' => [8000, 15000, 35000], 'default' => true],
        'ex_rezervuar' => ['label' => 'Gömme rezervuar', 'price' => [9000, 12000, 20000], 'default' => false],
        'ex_havlupan' => ['label' => 'Havlupan', 'price' => [3500, 6000, 12000], 'default' => false],
        'ex_tavan' => ['label' => 'Asma tavan + spot', 'per_m2' => 1100, 'fixed' => 3000, 'default' => false],
        'ex_elektrik' => ['label' => 'Elektrik ve aydınlatma yenileme', 'fixed' => 6000, 'default' => false],
        // v76 (tahmini): zemin + duş bölgesi epoksi derz farkı ve teslim öncesi temizlik
        'ex_epoksi_derz' => ['label' => 'Epoksi derz dolgu (zemin ve duş)', 'per_m2' => 900, 'fixed' => 1500, 'default' => false],
        'ex_temizlik' => ['label' => 'Teslim öncesi temizlik ve bakım', 'per_m2' => 120, 'fixed' => 1200, 'default' => false],
    ],

    // Döviz karşılığı (TCMB efektif değil, döviz satış kuru). Kur yoksa satır gizlenir.
    'fx' => [
        'source_url' => 'https://www.tcmb.gov.tr/kurlar/today.xml',
        'currencies' => ['USD' => '$', 'EUR' => '€', 'GBP' => '£'],
    ],

    // SEO tablosu: zemin alanına göre (standart banyo, komple yenileme)
    'seo_sizes' => [3, 4, 5, 6, 8],
];

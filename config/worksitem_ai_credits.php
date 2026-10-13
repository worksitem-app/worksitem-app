<?php

/*
|--------------------------------------------------------------------------
| AI kullanım hakkı (PO kararı, 12.10.2026)
|--------------------------------------------------------------------------
|
| Temel hesaplayıcılar ücretsizdir. Yapay zekâ özellikleri telefonla (SMS
| koduyla) giriş yapan kullanıcıya günde belirli sayıda ücretsizdir; sonrası
| kredi ile kullanılır. iyzico açılana kadar kredi talebi WhatsApp ile alınır
| ve admin panelinden (Gelir › AI kredileri) yüklenir.
|
*/

return [
    'enabled' => (bool) env('AI_CREDITS_ENABLED', true),

    'free_per_day' => (int) env('AI_FREE_PER_DAY', 1),

    // Giriş oturumu süresi (gün)
    'session_days' => 30,

    // Kredi paketleri (TL, KDV dahil) — satış iyzico açılınca online olur
    'packages' => [
        ['credits' => 10, 'price' => 99],
        ['credits' => 30, 'price' => 249, 'badge' => 'Önerilen'],
        ['credits' => 100, 'price' => 699],
    ],

    // Kota uygulanan AI uç noktaları (sayfaya giriş/kredi penceresi bu adresleri çağıran sayfalara eklenir)
    'endpoints' => [
        '/ai-tasarim/ic-mekan/start',
        '/maliyet-hesapla/guest-render',
        '/araclar/oda-planlayici/generate',
        '/araclar/otomatik-kesif-fiyatlandirma/hesapla',
        '/api/seramik-gorsellestirici/render',
        '/araclar/banyo-planlayici/render',
    ],

    // İstek başına kredi (varsayılan 1). 1'den fazla olan seçeneklerde günlük ücretsiz hak kullanılmaz.
    'costs' => [
        'tools.banyo_planner_render' => ['param' => 'quality', 'map' => ['hd' => 2]],
    ],

    // Kullanıcıya görünen özellik adları (route adı => etiket)
    'features' => [
        'ai.interior.start' => 'Yapay zekâ iç mekân tasarımı',
        'projects.guest.render' => 'Proje görseli',
        'projects.render' => 'Proje görseli',
        'tools.room.generate' => 'Oda planlayıcı',
        'tools.auto_boq.calc' => 'Otomatik keşif ve fiyatlandırma',
        'api.tile_visualizer.render' => 'Seramik görselleştirici',
        'tools.banyo_planner_render' => 'Banyo planı gerçekçi görsel',
    ],
];

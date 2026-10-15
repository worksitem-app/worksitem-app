<?php

/*
 * Uygulama (PWA + Google Play TWA) ve web bildirimleri.
 *
 * VAPID anahtarları: `php artisan push:keys --write` ile .env'e yazılır (gizli anahtar ekrana basılmaz).
 * TWA: Play Console › Uygulama bütünlüğü › Uygulama imzalama › SHA-256 sertifika parmak izi → .env TWA_SHA256
 * (birden çok parmak izi virgülle). /.well-known/assetlinks.json buradan üretilir.
 */
return [
    'push' => [
        'enabled' => (bool) env('PUSH_ENABLED', true),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_pem_b64' => env('VAPID_PRIVATE_PEM_B64'),
        'subject' => env('VAPID_SUBJECT', 'mailto:info@worksitem.com'),
        // Yalnız bilinen tarayıcı bildirim servislerine istek atılır (SSRF koruması)
        'hosts' => ['fcm.googleapis.com', 'updates.push.services.mozilla.com', 'push.apple.com', 'notify.windows.com', 'push.services.mozilla.com'],
        'ttl' => 86400,
        'timeout' => 5,
    ],
    'twa' => [
        'package' => env('TWA_PACKAGE', 'com.worksitem.app'),
        'sha256' => array_values(array_filter(array_map('trim', explode(',', (string) env('TWA_SHA256', ''))))),
        'play_url' => env('TWA_PLAY_URL'),
    ],
];

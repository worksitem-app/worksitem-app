<?php

/*
| Pro rapor (detaylı, çok sayfalı PDF)
| mode: 'lead' → ücretsiz, telefon SMS koduyla doğrulanır (doğrulanmış talep üretir)
|       'paid' → ücretli (iyzico açılınca); price TL + KDV
*/
return [
    'mode' => env('PRO_REPORT_MODE', 'lead'),
    'price' => (int) env('PRO_REPORT_PRICE', 149),
    // Doğrulanan telefon bu süre boyunca kod istemeden yeniden indirebilir (dakika)
    'session_minutes' => 120,
];

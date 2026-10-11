<?php

return [
    'notification_email' => env(
        'LEAD_NOTIFICATION_EMAIL',
        'info@worksitem.com'
    ),

    // Günlük CEO raporu alıcısı (önceden komut içinde env('PRICE_MAIL') ile okunuyordu)
    'ceo_report_email' => env('PRICE_MAIL'),
];

<?php

/*
| Online ödeme (iyzico): yapay zekâ kredisi ve ücretli Pro rapor.
| Açmak için .env: ONLINE_PAYMENTS_ENABLED=true (iyzico canlı anahtarları tanımlı olmalı).
| Pro raporu ücretli yapmak için ayrıca: PRO_REPORT_MODE=paid (fiyat: PRO_REPORT_PRICE + KDV).
*/

return [
    'online_enabled' => (bool) env('ONLINE_PAYMENTS_ENABLED', false),
    'vat_rate' => 0.20,
    // Ücretli Pro raporu aynı hesap için bu kadar gün yeniden indirilebilir
    'pro_report_days' => 30,
];

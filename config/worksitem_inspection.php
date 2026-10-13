<?php

/*
|--------------------------------------------------------------------------
| Yerinde keşif: kabul → ödeme → mühendis iletişimi
|--------------------------------------------------------------------------
|
| Bedel, KDV ve mahsup süresi config/worksitem_bathroom.php içindedir.
| iyzico bilgileri gelene kadar ödeme bağlantısı Kurumsal Çağrı Merkezi
| tarafından WhatsApp üzerinden gönderilir (IYZICO_KESIF_ENABLED=false).
|
*/

return [
    'call_center_label' => 'Kainta İnşaat Kurumsal Çağrı Merkezi',
    'phone' => env('KAINTA_CALL_CENTER_PHONE', '+90 850 346 4983'),
    'whatsapp' => env('KAINTA_CALL_CENTER_WHATSAPP', '908503464983'),

    // Hizmeti veren ve faturalayan taraf
    'provider' => 'Kainta İnşaat',

    // Kart seçilince doğrudan iyzico ödeme sayfasına gidilir (anahtarlar services.iyzico'da).
    // Kapalıysa ödeme bağlantısı WhatsApp ile gönderilir.
    'iyzico_enabled' => (bool) env('IYZICO_KESIF_ENABLED', false),

    // Kabul metni sürümü (lead kaydına yazılır). Sözleşme metni değişirse artırın.
    'terms_version' => '2026-10-v2',

    'vat_rate' => (int) env('WORKSITEM_INSPECTION_VAT_RATE', 20),

    // Ödeme yöntemleri (PO kararı: kart önerilen + havale/EFT)
    'payment_methods' => [
        'kart' => ['label' => 'Kredi / banka kartı', 'hint' => 'iyzico güvencesiyle 3D Secure ödeme bağlantısı', 'badge' => 'Önerilen'],
        'havale' => ['label' => 'Havale / EFT', 'hint' => 'Komisyonsuz; açıklamaya talep numaranızı yazın', 'badge' => null],
    ],

    // Hizmet koşulları (sözleşmelerde yazar — PO onaylı varsayımlar)
    'appointment_days' => 5,   // ödeme onayından sonra randevu en geç (iş günü)
    'report_days' => 5,        // keşiften sonra rapor + yazılı teklif (iş günü)
    'reschedule_hours' => 24,  // randevudan bu kadar saat öncesine kadar ücretsiz yeniden planlama
    'cancel_email' => env('KESIF_CANCEL_EMAIL', 'info@worksitem.com'),
];

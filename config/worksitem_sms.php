<?php

/*
|--------------------------------------------------------------------------
| Müşteri SMS'leri (Netgsm) — yalnızca bilgilendirme amaçlı, pazarlama değil
|--------------------------------------------------------------------------
| Metni değiştirmek için yalnızca bu dosyayı düzenleyin (sonra config:cache).
| Yer tutucular: :no (talep no), :ad, :tarih, :saat, :adres, :kod
*/

return [
    'lead_received' => [
        'enabled' => (bool) env('SMS_LEAD_RECEIVED', true),
        'text' => 'Kainta İnşaat: Talebiniz alındı (:no). Uzmanımız en geç 1 iş günü içinde sizi arayacak. WhatsApp destek: 0850 346 49 83',
        // Kötüye kullanım koruması (v106): formdan doğrulanmamış numaraya giden SMS'in günlük tavanları
        'max_per_phone_day' => (int) env('SMS_LEAD_MAX_PER_PHONE_DAY', 2),
        'max_per_ip_day' => (int) env('SMS_LEAD_MAX_PER_IP_DAY', 5),
        'global_day' => (int) env('SMS_LEAD_GLOBAL_DAY', 300),
    ],
    // Tüm SMS'ler (OTP, randevu, ekip bildirimi dahil) için son güvenlik tavanı — aşılırsa gönderim durur ve loga yazılır
    'hard_cap_day' => (int) env('SMS_HARD_CAP_DAY', 1500),
    'appointment_set' => [
        'enabled' => (bool) env('SMS_APPOINTMENT_SET', true),
        'text' => 'Kainta İnşaat: Keşif randevunuz :tarih saat :saat. Adres: :adres. Değişiklik için WhatsApp: 0850 346 49 83',
    ],
    'appointment_reminder' => [
        'enabled' => (bool) env('SMS_APPOINTMENT_REMINDER', true),
        'text' => 'Kainta İnşaat hatırlatma: Yarın saat :saat keşif randevunuz var. Adres: :adres. Değişiklik için WhatsApp: 0850 346 49 83',
    ],
    'otp' => [
        'text' => 'Worksitem doğrulama kodunuz: :kod. Kod 5 dakika geçerlidir, kimseyle paylaşmayın.',
        'ttl_minutes' => 5,
        'max_attempts' => 5,      // yanlış kod denemesi
        'max_sends' => 3,         // 10 dakikada aynı numaraya en fazla
        // SMS maliyet koruması (ücretsiz Pro rapor ve AI girişi için)
        'max_per_ip_day' => (int) env('OTP_MAX_PER_IP_DAY', 6),     // aynı IP'den günde en fazla kod
        'max_per_phone_day' => (int) env('OTP_MAX_PER_PHONE_DAY', 5), // aynı numaraya günde en fazla kod
        'global_day' => (int) env('OTP_GLOBAL_DAY', 400),           // tüm site günlük tavan (kötüye kullanımda fatura büyümesin)
    ],
];

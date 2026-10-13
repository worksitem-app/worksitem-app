<?php

/*
|--------------------------------------------------------------------------
| Hizmet sağlayıcı (Kainta İnşaat) kimlik bilgileri
|--------------------------------------------------------------------------
|
| Yasal metinlerde, keşif ön bilgilendirme formu ve mesafeli hizmet
| sözleşmesinde kullanılır. Boş bırakılan satır belgede gösterilmez,
| ancak `php artisan app:preflight` eksik olarak uyarır.
| Mesafeli satışta unvan, MERSİS (varsa), adres ve iletişim bilgisi zorunludur.
|
*/

return [
    'legal_name' => env('COMPANY_LEGAL_NAME', 'Kainta İnşaat Mimarlık & Mühendislik'),
    'brand' => 'Worksitem',
    'mersis' => env('COMPANY_MERSIS'),
    'tax_office' => env('COMPANY_TAX_OFFICE'),
    'tax_no' => env('COMPANY_TAX_NO'),
    'address' => env('COMPANY_ADDRESS', 'Fatih Sultan Mehmet Mahallesi, Poligon Caddesi, Buyaka 2 Sitesi, 3 Blok, No: 8 C/1, 34771 Ümraniye/İstanbul'),
    'phone' => env('COMPANY_PHONE', '+90 850 346 49 83'),
    'email' => env('COMPANY_EMAIL', 'info@worksitem.com'),
    'kep' => env('COMPANY_KEP'),
    'web' => 'worksitem.com',

    // Banka havalesi / EFT (keşif ödemesi). Boşsa IBAN müşteriye WhatsApp'tan iletilir.
    'iban' => env('COMPANY_IBAN'),
    'iban_holder' => env('COMPANY_IBAN_HOLDER'),
    'bank_name' => env('COMPANY_BANK_NAME'),
];

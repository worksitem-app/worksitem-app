<?php

/*
 * Firma üyeliği: usta, müteahhit, tedarikçi ve markalar Worksitem'e ücretsiz kaydolur;
 * Kainta İnşaat Uzmanları onaylar. Herkese açık firma rehberi avukat onayına kadar kapalıdır
 * (FIRMS_DIRECTORY_ENABLED=true ile açılır). Kayıt formu her zaman açıktır.
 */
return [
    'directory_enabled' => (bool) env('FIRMS_DIRECTORY_ENABLED', false),

    // Öne çıkan (Pro) üyelik: rehberde üstte, rozetli, sitesine bağlantılı. Fiyat KDV hariç, yıllık.
    'pro_price' => (int) env('FIRM_PRO_PRICE', 2400),
    'pro_days' => 365,

    'types' => [
        'usta' => 'Usta / ekip',
        'muteahhit' => 'Müteahhit / taahhüt firması',
        'tadilat' => 'Tadilat / dekorasyon firması',
        'mimar' => 'Mimarlık / iç mimarlık',
        'muhendis' => 'Mühendislik / proje bürosu',
        'tedarikci' => 'Malzeme tedarikçisi / bayi',
        'uretici' => 'Üretici / marka',
    ],

    // İş kolları ve alt hizmetler: config/worksitem_trades.php (Kainta hizmet listesi)
    'max_trades' => 6,
    'max_subservices' => 40,
    'max_cities' => 10,

    'cities' => ['Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Aksaray', 'Amasya', 'Ankara', 'Antalya', 'Ardahan', 'Artvin', 'Aydın', 'Balıkesir', 'Bartın', 'Batman', 'Bayburt', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli', 'Diyarbakır', 'Düzce', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkari', 'Hatay', 'Iğdır', 'Isparta', 'İstanbul', 'İzmir', 'Kahramanmaraş', 'Karabük', 'Karaman', 'Kars', 'Kastamonu', 'Kayseri', 'Kilis', 'Kırıkkale', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa', 'Mardin', 'Mersin', 'Muğla', 'Muş', 'Nevşehir', 'Niğde', 'Ordu', 'Osmaniye', 'Rize', 'Sakarya', 'Samsun', 'Şanlıurfa', 'Siirt', 'Sinop', 'Şırnak', 'Sivas', 'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli', 'Uşak', 'Van', 'Yalova', 'Yozgat', 'Zonguldak'],
];

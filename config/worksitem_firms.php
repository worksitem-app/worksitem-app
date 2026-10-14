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

    // Hizmet alanları: anahtar => [etiket, ilgili araçlar]
    'services' => [
        'banyo' => ['Banyo tadilatı', ['banyo-maliyet-hesaplayici', 'banyo-planlayici', 'wc-maliyet-hesabi']],
        'mutfak' => ['Mutfak dolabı ve tadilatı', ['mutfak-maliyet-hesaplayici', 'mutfak-planlayici']],
        'boya' => ['Boya ve badana', ['boya-hesaplayici']],
        'seramik' => ['Seramik ve fayans', ['seramik-maliyet-hesaplayici']],
        'parke' => ['Parke ve zemin', ['parke-maliyet-hesaplayici']],
        'alci' => ['Alçı, alçıpan ve asma tavan', ['asma-tavan-hesaplayici']],
        'mantolama' => ['Mantolama ve yalıtım', ['mantolama-maliyeti']],
        'pvc' => ['PVC ve alüminyum doğrama', ['pvc-dograma-hesaplayici']],
        'cati' => ['Çatı', []],
        'elektrik' => ['Elektrik tesisatı', ['kablo-kesiti-hesabi']],
        'tesisat' => ['Sıhhi tesisat ve doğalgaz', []],
        'beton' => ['Hazır beton ve kaba inşaat', ['beton-hesaplayici', 'saha-betonu-hesaplayici']],
        'anahtar' => ['Anahtar teslim inşaat', ['tahmini-bina-maliyeti']],
        'proje' => ['Statik / mimari proje', ['statik-proje-bedeli']],
        'malzeme' => ['Yapı malzemesi satışı', []],
        'vitrifiye' => ['Vitrifiye ve armatür', []],
    ],

    'cities' => ['Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Aksaray', 'Amasya', 'Ankara', 'Antalya', 'Ardahan', 'Artvin', 'Aydın', 'Balıkesir', 'Bartın', 'Batman', 'Bayburt', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli', 'Diyarbakır', 'Düzce', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkari', 'Hatay', 'Iğdır', 'Isparta', 'İstanbul', 'İzmir', 'Kahramanmaraş', 'Karabük', 'Karaman', 'Kars', 'Kastamonu', 'Kayseri', 'Kilis', 'Kırıkkale', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa', 'Mardin', 'Mersin', 'Muğla', 'Muş', 'Nevşehir', 'Niğde', 'Ordu', 'Osmaniye', 'Rize', 'Sakarya', 'Samsun', 'Şanlıurfa', 'Siirt', 'Sinop', 'Şırnak', 'Sivas', 'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli', 'Uşak', 'Van', 'Yalova', 'Yozgat', 'Zonguldak'],
];

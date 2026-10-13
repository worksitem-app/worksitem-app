<?php

/*
| Teklif formlarında uygunsuz dil filtresi.
| words: yalnızca tam kelime olarak eşleşir (kısa kelimeler; "sikke", "götürmek" gibi masum kelimeleri engellememek için)
| stems: kelimenin içinde geçmesi yeterli
| Liste küçük harf, Türkçe karakterli yazılır. Yeni kelime eklemek için yalnızca bu dosyayı düzenleyin.
*/

return [
    'words' => ['amk', 'aq', 'mk', 'oç', 'sik', 'göt', 'piç', 'ibne', 'salak', 'aptal', 'gerizekalı', 'fuck', 'shit'],
    'stems' => ['orospu', 'siktir', 'sikerim', 'sikeyim', 'yarrak', 'amına', 'amcık', 'pezevenk', 'kahpe', 'şerefsiz', 'yavşak', 'gavat', 'ananı', 'anani', 'gerizekal'],
    'fields' => ['name', 'location', 'notes', 'city', 'district'],

    // Sahte / anlamsız giriş kontrolü (ad soyad, şehir, ilçe). Bu kelimeler tek başına kelime olarak geçerse reddedilir.
    'fake_words' => ['test', 'deneme', 'asd', 'asdf', 'asdasd', 'qwe', 'qwerty', 'abc', 'xxx', 'xyz', 'isim', 'soyisim', 'soyad', 'adsoyad', 'bilinmiyor', 'yok', 'sdf', 'dfg', 'fgh', 'jkl'],
    // Klavye sırası: bu satırlardan art arda 4 harf geçerse anlamsız sayılır (asdf, qwer, zxcv ...)
    'keyboard_rows' => ['qwertyuıopğü', 'qwertyuiop', 'asdfghjklşi', 'asdfghjkl', 'zxcvbnmöç', 'zxcvbnm'],
    // Teklif formlarında öneri listesi
    'cities' => ['Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Aksaray', 'Amasya', 'Ankara', 'Antalya', 'Ardahan', 'Artvin', 'Aydın', 'Balıkesir', 'Bartın', 'Batman', 'Bayburt', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli', 'Diyarbakır', 'Düzce', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkâri', 'Hatay', 'Iğdır', 'Isparta', 'İstanbul', 'İzmir', 'Kahramanmaraş', 'Karabük', 'Karaman', 'Kars', 'Kastamonu', 'Kayseri', 'Kilis', 'Kırıkkale', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa', 'Mardin', 'Mersin', 'Muğla', 'Muş', 'Nevşehir', 'Niğde', 'Ordu', 'Osmaniye', 'Rize', 'Sakarya', 'Samsun', 'Şanlıurfa', 'Siirt', 'Sinop', 'Şırnak', 'Sivas', 'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli', 'Uşak', 'Van', 'Yalova', 'Yozgat', 'Zonguldak'],
];

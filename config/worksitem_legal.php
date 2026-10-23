<?php

/*
|--------------------------------------------------------------------------
| Hesaplama sonuçları için hukuki uyarı metinleri
|--------------------------------------------------------------------------
|
| Tüm araç sonuçlarında, araç sayfalarının altında ve PDF raporlarda bu metinler
| kullanılır. Metni değiştirmek için yalnızca bu dosyayı düzenleyin.
|
| Not (hukuk görüşü özeti): "Worksitem hiçbir şeyden sorumlu değildir" gibi
| sınırsız bir muafiyet kaydı geçersizdir (TBK m.115: kast ve ağır ihmalden
| doğan sorumluluğu kaldıran sözleşme hükmü kesin hükümsüzdür; TKHK m.5:
| tüketici aleyhine haksız şartlar batıldır). Bu nedenle metin; sonucun
| teklif olmadığını, geçerlilik süresi bulunmadığını, fiyatların değiştiğini
| ve girilen bilgilerin kullanıcının sorumluluğunda olduğunu açıkça söyler,
| sorumluluğu ise kanunun izin verdiği ölçüde sınırlar.
|
*/

return [

    'version' => '2026-10-12',

    // Hukuk danışmanı onayı (kullanım koşulları, keşif ön bilgilendirme formu ve mesafeli hizmet sözleşmesi)
    'approved_at' => '12.10.2026',

    // Tam uyarının sayfa altına (footer üstüne) otomatik eklendiği yollar — tüm araçlar
    'paths' => ['araclar', 'araclar/*', 'maliyet-hesapla', 'maliyet-hesapla/*', 'ai-tasarim/*', 'duvar-rengi-gorsellestirici'],
    'except' => ['maliyet-hesapla/basarili'],

    // Sonuç kutusunun hemen altında (kısa)
    'result_short' => 'Bilgilendirme amaçlı yaklaşık hesaptır; teklif değildir ve geçerlilik süresi yoktur. Fiyatlar günlük olarak ve ülkeye, şehre, markaya, ustaya göre değişir. Girdiğiniz bilgilerin doğruluğu ve bu sonuca dayanarak vereceğiniz kararlar sizin sorumluluğunuzdadır.',

    // Araç sayfalarının altında ve PDF raporda (tam)
    'title' => 'Hukuki uyarı',
    'paragraphs' => [
        'Worksitem araçlarının verdiği tüm sonuçlar, kullanıcının girdiği bilgilere ve ortalama piyasa birim fiyatlarına göre otomatik olarak hesaplanan, yalnızca bilgilendirme amaçlı yaklaşık değerlerdir. Bu sonuçlar bir fiyat teklifi, satış vaadi, keşif, metraj, mühendislik hesabı veya sözleşme değildir ve herhangi bir geçerlilik süresi taşımaz.',
        'Malzeme, işçilik ve döviz fiyatları günlük olarak değişir; ülkeye, şehre, ilçeye, markaya, ustaya, binanın durumuna ve işin zamanına göre farklılık gösterir. Otomatik hesaplama ve yapay zekâ araçları hata yapabilir. Kesin bedel yalnızca yerinde keşif sonrasında verilen yazılı teklif ve imzalı sözleşme ile belirlenir.',
        'Araçlara girilen ölçü, miktar, konum ve diğer tüm bilgilerin doğruluğundan ve sonuçlara dayanarak alınan satın alma, bütçe, sözleşme ve uygulama kararlarından kullanıcı sorumludur. Worksitem, kanunen sınırlandırılamayan hâller (kast ve ağır ihmal) dışında, bu sonuçların kullanılmasından doğabilecek doğrudan veya dolaylı zararlardan sorumlu tutulamaz. Taşıyıcı sistem, statik, ruhsat ve güvenlikle ilgili kararlar için mutlaka yetkili bir mühendise danışın.',
    ],
];

<?php

/*
|--------------------------------------------------------------------------
| Araç rehberleri (Dalga 1)
|--------------------------------------------------------------------------
|
| <x-tools.tool-guide slug="..."/> bileşeni bu dosyayı okur ve araç
| sayfasının altına şunları ekler:
|   - Hesap yöntemi (formülün sade anlatımı)
|   - Sık Sorulan Sorular + FAQPage yapısal verisi
|   - "Yaklaşık değerdir" uyarısı ve içerik gözden geçirme tarihi
|   - Paylaş linki ve Yazdır/PDF düğmesi (public/js/ws-tool-extras.js)
|   - İsteğe bağlı olası fiyat aralığı (band)
|
| Alanlar:
|   fields  : paylaş linkine yazılacak girdi id'leri (kişisel veri ASLA eklenmez)
|   calc    : hesapla düğmesinin seçicisi (paylaşılan link açılınca tıklanır)
|   total   : sonucun yazıldığı öğe (aralık bunun altına eklenir)
|   result  : sonuç kutusu (paylaş/yazdır düğmeleri buraya eklenir)
|   band    : [alt çarpan, üst çarpan] — null ise aralık gösterilmez
|   engineering : true ise mühendis onayı uyarısı gösterilir
|
| Fiyat veya formül değişirse "method" metnini de güncelleyin; testler
| seçicilerin sayfada gerçekten bulunduğunu denetler.
|
*/

return [

    'reviewed' => 'Ekim 2026',

    'tools' => [

        'banyo-maliyet-hesaplayici' => [
            'name' => 'Banyo Tadilat Maliyeti Hesaplayıcı',
            // v119: ex_epoksi_derz / ex_temizlik eklendi — PDF, Pro rapor ve paylaş bağlantısı ekrandaki tutarı versin
            'fields' => ['bathType', 'width', 'length', 'height', 'scope', 'quality', 'shower', 'ex_dolap', 'ex_rezervuar', 'ex_havlupan', 'ex_tavan', 'ex_elektrik', 'ex_epoksi_derz', 'ex_temizlik'],
            'calc' => '#calculateBtn',
            'total' => '#bTotal',
            'result' => '#resultBody',
            'pdf' => '/araclar/banyo-maliyet-hesaplayici/rapor.pdf',
            'band' => null, // araç kendi aralığını gösteriyor
            'method' => [
                'Zemin alanı = en × boy. Kaplanacak alan = zemin + duvarlar (oda çevresi × tavan yüksekliği) − 1,6 m² kapı payı. Duvarların tavana kadar seramik kaplanacağı varsayılır.',
                'Komple yenilemede kırım ve moloz bedeli kaplanacak alan üzerinden; yüzey yenilemede yalnızca vitrifiye sökümü olarak hesaplanır.',
                'Tesisat ve su yalıtımı yalnızca komple yenilemede eklenir. Yerleşim değişikliğinde tesisat kalemi 1,6 ile çarpılır ve kanal açma için sabit bir bedel eklenir.',
                'Seramik, vitrifiye, duş ve ekstralar seçilen kaliteye (ekonomik, orta, premium) göre fiyatlanır. Misafir WC\'de duş kalemi yoktur, ebeveyn banyosunda vitrifiye 1,3 kat alınır.',
                'Kalemlerin toplamına eski tesisat veya şap gibi sürprizler için %10 beklenmedik gider payı eklenir.',
                'Aralık, tipik değerin %15 altı ile %20 üstü arasındadır. Döviz karşılıkları TCMB döviz satış kuruyla günde iki kez güncellenir.',
            ],
            'faq' => [
                ['q' => 'Banyo tadilatı kaç gün sürer?', 'a' => 'Yüzey yenileme genellikle 4–7 iş günü, kırım ve tesisat dahil komple yenileme 8–15 iş günü, yerleşim değişikliği olan işler 12–20 iş günü sürer. Su yalıtımı testi ve ürün tedariki süreyi uzatabilir.'],
                ['q' => 'Hesaplanan aralığa neler dahil?', 'a' => 'Seçiminize göre söküm ve moloz, su ve gider tesisatı, su yalıtımı, zemin ve duvar seramiği, vitrifiye ve armatürler, duşakabin veya küvet, seçtiğiniz ekstralar ve %10 beklenmedik gider payı dahildir. Fiyatlar malzeme ve işçilik dahil ortalama değerlerdir.'],
                ['q' => 'Eski seramiği kırmadan üzerine yeni seramik döşenebilir mi?', 'a' => 'Mevcut kaplama sağlam, düz ve zemine iyi yapışmışsa uygun yapıştırıcıyla üzerine döşeme yapılabilir. Ancak zemin kotu yükselir, kapı ve süzgeç seviyeleri değişir ve eski su yalıtımı yenilenmemiş olur. Yalıtımın durumundan emin değilseniz kırım önerilir.'],
                ['q' => 'Banyoda su yalıtımı neden önemli?', 'a' => 'Zemin ve duş bölgesindeki su yalıtımı alt kata su sızmasını ve betonarmenin zarar görmesini önler. Yalıtım bittikten sonra seramik döşenmeden önce 24–48 saat su bekletme testi yapılması iyi bir uygulamadır.'],
                ['q' => 'Yerinde keşif ücretli mi?', 'a' => 'Evet. Kesin fiyat için yapılan yerinde keşif ücretlidir: {kesif_bedeli}. Bedel, randevu onayından önce yazılı olarak bildirilir. Keşiften sonraki {kesif_gun} gün içinde aynı iş için Kainta İnşaat ile sözleşme imzalarsanız keşif bedeli toplam iş bedelinden düşülür.'],
                ['q' => 'Dolar, euro ve sterlin karşılığı nasıl hesaplanıyor?', 'a' => 'TL tutar, Türkiye Cumhuriyet Merkez Bankası\'nın yayımladığı döviz satış kuruna bölünerek gösterilir. Kur günde iki kez güncellenir ve tarihi sonucun altında yazar. Bilgi amaçlıdır; ödeme TL üzerinden yapılır.'],
            ],
        ],

        'mutfak-maliyet-hesaplayici' => [
            'name' => 'Mutfak Tadilat Maliyeti Hesaplayıcı',
            'fields' => ['floorArea', 'cabinetLength', 'floorCondition', 'cabinetQuality', 'counterTop'],
            'calc' => '#calculateBtn',
            'total' => '#totalPrice',
            'result' => '#resultBox',
            'band' => [0.85, 1.20],
            'method' => [
                'Zemin = mutfak alanı × seçilen zemin işleminin m² fiyatı.',
                'Dolap = dolap metretülü × seçilen dolap kalitesinin metretül fiyatı.',
                'Tezgâh = dolap uzunluğu × seçilen tezgâh malzemesinin metretül fiyatı (tezgâh uzunluğu dolapla aynı kabul edilir).',
                'Tesisat ve montaj = 5.000 TL sabit + metretül başına 750 TL elektrik/su revizyonu.',
                'Olası aralık; marka, aksesuar ve usta farkları için tahminin %15 altı ile %20 üstü arasında gösterilir.',
            ],
            'faq' => [
                ['q' => 'Mutfak dolabı metretülü nasıl ölçülür?', 'a' => 'Alt dolapların duvar boyunca toplam uzunluğu ölçülür. L veya U mutfakta her duvar ayrı ölçülüp toplanır; köşe birleşiminde aynı bölüm iki kez sayılmamalıdır. Üst dolapların metretül fiyatına dahil olup olmadığını teklifte ayrıca kontrol edin.'],
                ['q' => 'Hangi tezgâh malzemesi daha dayanıklı?', 'a' => 'Laminat en ekonomik seçenektir ama sıcağa ve suya karşı hassastır. Kuvars kompozit tezgâhlar leke ve çizilmeye dayanıklıdır. Granit ve doğal taş sıcağa en dayanıklı olanlardır ancak gözenekli yapıları nedeniyle düzenli bakım isteyebilir.'],
                ['q' => 'Beyaz eşya ve ankastre cihazlar fiyata dahil mi?', 'a' => 'Hayır. Hesap zemin, dolap, tezgâh ile tesisat ve montajı kapsar. Beyaz eşya, ankastre cihazlar ve aydınlatma armatürleri ayrıca hesaplanmalıdır.'],
                ['q' => 'Mutfak yenileme ne kadar sürer?', 'a' => 'Ölçü alındıktan sonra dolap üretimi genellikle 2–4 hafta, söküm, tesisat ve montaj ise birkaç gün sürer. Taş tezgâhlar dolaplar monte edildikten sonra yerinde ölçülerek üretildiği için birkaç gün daha eklenir.'],
            ],
        ],

        'parke-maliyet-hesaplayici' => [
            'name' => 'Parke Maliyeti Hesaplayıcı',
            'fields' => ['length', 'width', 'doors', 'parkeClass', 'geometry'],
            'calc' => '#calculateBtn',
            'total' => '#totalPrice',
            'result' => '#resultBox',
            'band' => [0.85, 1.20],
            'method' => [
                'Net alan = oda boyu × oda eni.',
                'Fireli alan = net alan × oda geometrisine göre fire katsayısı (düz oda %5, girintili/kolonlu oda %10).',
                'Parke bedeli = fireli alan × (seçilen parkenin işçilik dahil m² fiyatı + 45 TL/m² şilte).',
                'Süpürgelik = (oda çevresi − kapı sayısı × 1 m) × %5 kesim firesi × 140 TL/mtül.',
                'Olası aralık; marka ve usta farkları için tahminin %15 altı ile %20 üstü arasında gösterilir.',
            ],
            'faq' => [
                ['q' => 'Parke alırken ne kadar fire payı bırakmalıyım?', 'a' => 'Düz dikdörtgen odalarda genellikle %5–8, girintili veya kolonlu odalarda %10, çapraz ya da balıksırtı döşemede %12–15 fire payı bırakılır. Paketler bölünemediği için ihtiyacınızı paket metrekaresine bölüp yukarı yuvarlayın.'],
                ['q' => 'Laminat parkede sınıf ve AC değeri ne anlama gelir?', 'a' => 'Sınıf 31 / AC3 konutlarda normal kullanım, sınıf 32 / AC4 yoğun kullanılan konut ve hafif ticari alanlar, sınıf 33 / AC5 yoğun ticari alanlar için uygundur. Değer yükseldikçe aşınma dayanımı artar.'],
                ['q' => 'Parke altına neden şilte serilir?', 'a' => 'Şilte zemindeki küçük pürüzleri tolere eder, adım sesini azaltır ve betondan gelen neme karşı bariyer görevi görür. Yeni şap üzerine döşemeden önce şapın yeterince kuruduğundan emin olunmalıdır.'],
                ['q' => 'Parke döşeme kaç gün sürer?', 'a' => '80–100 m² bir daire için döşeme ve süpürgelik montajı genellikle 1–3 iş günüdür. Parkenin döşemeden önce 24–48 saat oda sıcaklığında paketleriyle bekletilmesi önerilir.'],
            ],
        ],

        'tahmini-bina-maliyeti' => [
            'name' => 'Tahmini Bina Maliyeti Hesaplayıcı',
            'fields' => ['area', 'quality', 'buildingClass', 'duration', 'overhead', 'profit', 'chkZemin', 'chkRuhsat', 'chkSantiye', 'chkYapiDenetim', 'riskToggle'],
            'calc' => '#calculateBtn',
            'total' => '#totalPrice',
            'result' => '#resultBox',
            'band' => [0.90, 1.15],
            'engineering' => true,
            'method' => [
                'Resmî maliyet = inşaat alanı × seçilen yapı türünün m² birim maliyeti.',
                'Net maliyet = resmî maliyet × kalite katsayısı (ekonomik 0,85 · standart 1,00 · lüks 1,25).',
                'Seçilen izin ve hazırlık kalemleri eklenir: zemin etüdü, ruhsat/iskân harçları (~%1,5), şantiye kurulumu (%2), yapı denetim payı (~%1,5).',
                'Genel gider ve kâr yüzdeleri eklenir, ardından %20 KDV ve resmî maliyetin %4\'ü kadar proje bedeli eklenir.',
                'Nakit akışı grafiği toplam tutarı seçilen süreye çan eğrisiyle dağıtır; risk seçeneği açıksa aylık %3 fiyat artışı uygulanır.',
                'Olası aralık tahminin %10 altı ile %15 üstü arasında gösterilir.',
            ],
            'faq' => [
                ['q' => 'Yapı yaklaşık birim maliyeti nedir?', 'a' => 'Çevre, Şehircilik ve İklim Değişikliği Bakanlığı her yıl yapı sınıflarına göre m² başına yaklaşık birim maliyetleri yayımlar. Bu değerler proje bedeli, ruhsat harcı ve yapı denetim ücreti hesaplarında esas alınır; gerçek piyasa maliyeti çoğu zaman bu değerlerin üzerindedir.'],
                ['q' => 'Arsa bedeli hesaba dahil mi?', 'a' => 'Hayır. Hesap yalnızca yapım maliyetini, seçilen izin kalemlerini, genel gideri, kârı ve KDV\'yi kapsar. Arsa bedeli, tapu ve finansman giderleri ayrıca eklenmelidir.'],
                ['q' => 'Kat sayısı ve bodrum maliyeti nasıl etkiler?', 'a' => 'Kat sayısı arttıkça taşıyıcı sistem, asansör, yangın ve tesisat gereksinimleri m² maliyetini yükseltir. Bodrum katlar kazı, iksa ve su yalıtımı nedeniyle normal katlardan pahalıdır. Bu araç kat sayısını ayrıca sormaz; yapı türü ve kalite seçimi bu farkı kısmen yansıtır.'],
                ['q' => 'Bu sonuç ruhsat başvurusunda kullanılabilir mi?', 'a' => 'Hayır. Sonuç ön fizibilite içindir. Resmî maliyet hesapları ve projeler yetkili mimar ve mühendislerce hazırlanmalıdır.'],
            ],
        ],

        'mantolama-maliyeti' => [
            'name' => 'Mantolama ve Yalıtım Maliyeti Hesaplayıcı',
            'fields' => ['area', 'type', 'scaffold'],
            'calc' => '#calcBtn',
            'total' => '#resPrice',
            'result' => '#resBox',
            'band' => [0.85, 1.20],
            'method' => [
                'Toplam = uygulama yüzey alanı × (seçilen yalıtım türünün malzeme ve işçilik dahil m² fiyatı + iskele m² bedeli).',
                'İskele yalnızca dış cephe uygulamalarında eklenir.',
                'Olası aralık; yalıtım kalınlığı, malzeme markası ve cephe zorluğuna göre tahminin %15 altı ile %20 üstü arasında gösterilir.',
            ],
            'faq' => [
                ['q' => 'Mantolama için hangi kalınlık gerekir?', 'a' => 'Gerekli kalınlık iklim bölgesine ve malzemeye göre TS 825 ısı yalıtımı hesabıyla belirlenir. Konutlarda uygulamada sıkça 5–8 cm aralığı görülür; soğuk bölgelerde daha kalın levha gerekebilir.'],
                ['q' => 'EPS, XPS ve taş yünü arasındaki fark nedir?', 'a' => 'EPS ekonomik ve yaygındır. XPS daha yoğundur ve suya dayanıklıdır; zemin ve subasman bölgelerinde tercih edilir. Taş yünü yanmaz ve buhar geçirgendir ancak daha pahalıdır. Yüksek binalarda yangın yönetmeliği yanmaz malzeme veya yangın bariyeri gerektirebilir.'],
                ['q' => 'Mantolama ne kadar tasarruf sağlar?', 'a' => 'Tasarruf binanın mevcut yalıtım durumuna, iklim bölgesine ve kullanım alışkanlıklarına göre değişir. Yalıtımsız binalarda ısınma giderinde belirgin düşüş görülür; kesin rakam için enerji kimlik belgesi ve ısı kaybı hesabı gerekir.'],
                ['q' => 'Apartmanda mantolama için kat maliklerinin onayı gerekir mi?', 'a' => 'Dış cephe ortak alandır ve karar kat malikleri kurulunda alınır. Kat Mülkiyeti Kanunu\'na göre ısı yalıtımı gibi enerji verimliliği uygulamaları için kat maliklerinin sayı ve arsa payı çoğunluğu yeterlidir. İşe başlamadan önce güncel durumu yöneticiniz veya bir hukukçuyla teyit edin.'],
            ],
        ],

        'cati-maliyet-hesaplayici' => [
            'name' => 'Çatı Maliyeti Hesaplayıcı',
            'fields' => ['directArea', 'length', 'width', 'slope', 'eave', 'roofForm', 'ridge', 'covering', 'waste', 'underlay', 'waterproofing', 'demolition'],
            'calc' => '#calculate',
            'total' => '#total',
            'result' => '#result',
            'band' => [0.85, 1.20],
            'method' => [
                'Gerçek çatı alanı girilmezse bina boyu, eni, saçak payı ve çatı eğiminden hesaplanır (eğim arttıkça eğik yüzey alanı büyür).',
                'Sipariş alanı = çatı alanı × (1 + fire oranı). Fire en fazla %25 alınır.',
                'Kaplama, su yalıtımı örtüsü ve membran kalemleri çatı alanı × ilgili birim fiyat; mahya kalemi mahya uzunluğu × birim fiyat olarak hesaplanır.',
                'Birim fiyatı bulunamayan kalem varsa sonuç "eksik kalem" olarak işaretlenir.',
                'Olası aralık tahminin %15 altı ile %20 üstü arasında gösterilir.',
            ],
            'faq' => [
                ['q' => 'Çatı alanı nasıl hesaplanır?', 'a' => 'Eğimli çatıda alan, binanın taban alanından büyüktür. Taban ölçüleri ve eğim biliniyorsa eğik yüzey alanı eğim katsayısıyla bulunur; saçak taşmaları da alana eklenir. Ölçüm imkânınız varsa gerçek çatı alanını doğrudan girmek en doğrusudur.'],
                ['q' => 'Çatı kaplamasında ne kadar fire payı bırakılır?', 'a' => 'Basit beşik çatılarda genellikle %10 civarı, çok sayıda dere, mahya ve baca bulunan karmaşık çatılarda %15 ve üzeri fire payı bırakılır.'],
                ['q' => 'Kiremit altına su yalıtımı örtüsü şart mı?', 'a' => 'Kiremit tek başına rüzgârla gelen yağmur ve kar suyunu tamamen engellemez. Kiremit altı su yalıtımı örtüsü veya membran, çatı arasına su girmesini önleyen ikinci savunma hattıdır ve uzun ömürlü bir çatı için önerilir.'],
                ['q' => 'Çatı tadilatı için izin gerekir mi?', 'a' => 'Mevcut çatı kaplamasının aynı formda yenilenmesi ile çatı formunun, yüksekliğin veya taşıyıcı sistemin değiştirilmesi farklı değerlendirilir. Taşıyıcı sistem veya çatı formu değişecekse belediyeye danışın ve gerekirse proje hazırlatın. Apartmanlarda kat malikleri kurulu kararı da gerekir.'],
            ],
        ],

        'alcipan-hesaplayici' => [
            'name' => 'Alçıpan Maliyeti Hesaplayıcı',
            'fields' => ['application', 'profile', 'board', 'length', 'height', 'doors', 'openings', 'directArea', 'demolition'],
            'calc' => '#calculateBtn',
            'total' => '#rTotal',
            'result' => '#result',
            'band' => [0.85, 1.20],
            'method' => [
                'Brüt alan = uzunluk × yükseklik (veya doğrudan girilen alan).',
                'Net alan = brüt alan − kapı ve pencere boşlukları.',
                'Toplam = net alan × seçilen sistemin (uygulama tipi, profil ve levha türü) birim fiyatı. Birim fiyat ve dönemi sonuç kutusunda poz numarasıyla gösterilir.',
                'Olası aralık tahminin %15 altı ile %20 üstü arasında gösterilir.',
            ],
            'faq' => [
                ['q' => 'Hangi alçıpan levhayı seçmeliyim?', 'a' => 'Kuru alanlarda standart (beyaz) levha, banyo ve mutfak gibi nemli alanlarda suya dayanıklı (yeşil) levha, kaçış yolları ve tesisat şaftlarında yangına dayanıklı levha kullanılır. Hem nem hem yangın riski olan yerlerde su + yangın dayanımlı levhalar tercih edilir.'],
                ['q' => 'Asma tavan ile bölme duvar arasında fiyat farkı neden var?', 'a' => 'Asma tavanda askı elemanları ve tavan profilleri, bölme duvarda ise iki yüze levha ve duvar profilleri kullanılır. Malzeme miktarı ve işçilik farklı olduğu için birim fiyatlar da farklıdır.'],
                ['q' => 'Alçıpan uygulaması kaç gün sürer?', 'a' => 'Profil ve levha montajı genellikle hızlıdır; asıl süreyi derz dolgu, bantlama ve zımpara katları belirler. Katların kuruması için ortalama bir oda 2–4 iş günü sürebilir.'],
                ['q' => 'Fiyata boya dahil mi?', 'a' => 'Hayır. Hesap alçıpan sisteminin kurulumunu kapsar. Astar ve boya ayrıca fiyatlandırılır; Worksitem boya hesaplayıcısını kullanabilirsiniz.'],
            ],
        ],

        'klima-btu-hesaplayici' => [
            'name' => 'Klima BTU Hesaplayıcı',
            'fields' => ['area', 'people', 'facade', 'insulation'],
            'calc' => '#calculateBtn',
            'total' => '#totalLoad',
            'result' => '#resultBox',
            'band' => null,
            'method' => [
                'Temel yük = oda alanı × 350 BTU/m² (Türkiye ortalaması için kabul edilen değer).',
                'Kişi yükü = kişi sayısı × 600 BTU.',
                'Toplam yük = (temel yük + kişi yükü) × cephe katsayısı (kuzey 0,9 · doğu/batı 1,0 · güney 1,15) × yalıtım katsayısı (yalıtımlı 0,9 · standart 1,0 · yalıtımsız/çatı katı 1,2).',
                'Önerilen cihaz, toplam yükü karşılayan en yakın standart kapasitedir (9.000 · 12.000 · 18.000 · 24.000 · 36.000 · 48.000 BTU).',
            ],
            'faq' => [
                ['q' => 'Kaç m² için kaç BTU klima gerekir?', 'a' => 'Bu araç, ortalama koşullarda m² başına 350 BTU kabul eder; buna kişi sayısı, cephe yönü ve yalıtım durumu eklenir. Örneğin yalıtımı standart, doğu cepheli ve 2 kişinin kullandığı 20 m² bir oda için yaklaşık 8.200 BTU hesaplanır ve 9.000 BTU cihaz önerilir.'],
                ['q' => 'Gereğinden büyük klima almak sorun olur mu?', 'a' => 'Evet. Büyük kapasiteli cihaz odayı çok hızlı soğutup sık sık durur; nemi yeterince alamaz, konfor düşer ve enerji verimi azalır. Gereğinden küçük cihaz ise sürekli tam güçte çalışır.'],
                ['q' => 'Güney cephe ve çatı katı neden daha fazla BTU ister?', 'a' => 'Güney cepheler gün boyu daha fazla güneş alır, çatı katları ise çatıdan doğrudan ısı kazanır. Bu nedenle araç güney cepheye %15, yalıtımsız veya çatı katı odalara %20 ek yük ekler.'],
                ['q' => 'Mutfak veya geniş camlı salon için ne yapmalıyım?', 'a' => 'Ocak ve fırın gibi ısı kaynakları ile geniş cam yüzeyler ek ısı yükü oluşturur. Bu tür alanlarda bir üst kapasiteyi değerlendirin veya bir mekanik tesisat uzmanından ısı kazancı hesabı isteyin.'],
            ],
        ],

        'demir-metraji-hesaplayici' => [
            'name' => 'Demir Metrajı Hesaplayıcı',
            'fields' => ['volume', 'elementType'],
            'calc' => '#calculateBtn',
            'total' => '#totalPrice',
            'result' => '#resultBox',
            'band' => null,
            'engineering' => true,
            'method' => [
                'Net demir = beton hacmi (m³) × yapı elemanına göre ortalama donatı oranı (temel ~80, kiriş/döşeme ~100, kolon/perde ~120, genel ortalama ~90 kg/m³).',
                'Kesim ve büküm firesi olarak %5 eklenir: brüt ton = net ton × 1,05.',
                'Tutar = brüt ton × fiyat veritabanındaki onaylı inşaat demiri ton fiyatı (malzeme; işçilik hariç). Onaylı fiyat yoksa tutar gösterilmez.',
            ],
            'faq' => [
                ['q' => '1 m³ betona ne kadar demir gider?', 'a' => 'Konut yapılarında ortalama 80–120 kg/m³ arasıdır. Temellerde daha az, kolon ve perdelerde daha fazla donatı kullanılır. Kesin miktar statik projedeki donatı metrajından alınır.'],
                ['q' => 'Bu hesap statik projenin yerine geçer mi?', 'a' => 'Hayır. Bu araç ortalama oranlarla ön bütçe verir. Gerçek donatı miktarı, çapları ve aralıkları deprem yönetmeliğine göre hazırlanan statik projede belirlenir ve şantiyede yapı denetim tarafından kontrol edilir.'],
                ['q' => 'Demirde fire payı neden %5 alınır?', 'a' => 'Çubuklar genellikle 12 m boyda gelir; kesim artıkları, bindirme boyları ve büküm kayıpları nedeniyle ihtiyaçtan fazla demir alınır. Düzenli kesim planıyla fire %3–5 aralığında tutulabilir.'],
                ['q' => 'Fiyata işçilik dahil mi?', 'a' => 'Hayır. Gösterilen tutar yalnızca malzeme bedelidir. Demir kesme, bükme ve bağlama işçiliği ile nakliye ayrıca fiyatlandırılır.'],
            ],
        ],

        'kat-karsiligi-analiz' => [
            'name' => 'Kat Karşılığı Fizibilite Analizi',
            'fields' => ['area', 'kaks', 'ownerShare', 'costPerM2', 'salePrice', 'exRate', 'extraRate'],
            'calc' => '#calculateBtn',
            'total' => '#totalCost',
            'result' => '#resultBox',
            'band' => [0.90, 1.15],
            'method' => [
                'Emsale dahil alan = arsa alanı × emsal (KAKS). Brüt inşaat alanı = emsale dahil alan × (1 + seçilen emsal harici oran); emsal harici oran en çok %30 seçilebilir.',
                'Satılabilir alan yalnız emsale dahil alandır; müteahhit payı alanı = emsale dahil alan × (100 − arsa sahibi payı) / 100.',
                'Toplam maliyet = brüt alanın tamamı × m² inşaat maliyeti, üzerine seçilen ek gider yüzdesi (ruhsat, harç, proje, finansman gibi) eklenir.',
                'Beklenen gelir = müteahhit payı alanı × m² satış fiyatı. Net kâr = gelir − maliyet; kâr marjı = net kâr / maliyet. Tutarlara KDV dahil değildir.',
                'Maliyet için olası aralık tahminin %10 altı ile %15 üstü arasında gösterilir.',
            ],
            'faq' => [
                ['q' => 'Kat karşılığında arsa sahibine ne kadar pay verilir?', 'a' => 'Pay; arsanın konumu, emsali, satış fiyatları ve inşaat maliyetine göre değişir. Müteahhidin makul bir kâr bırakabileceği en yüksek pay kabaca 1 − (toplam maliyet / toplam satış geliri) formülüyle bulunur; bu araçta arsa sahibi payını değiştirerek kâr marjının nasıl değiştiğini görebilirsiniz.'],
                ['q' => 'Emsal (KAKS) nedir?', 'a' => 'Kat Alanları Katsayısı, arsa alanı ile çarpıldığında yapılabilecek toplam inşaat alanını verir. Örneğin 500 m² arsada emsal 1,5 ise emsale dahil inşaat alanı 750 m²\'dir. Güncel değer imar durumu belgesinden öğrenilir.'],
                ['q' => 'Emsal harici alan nedir, satışa katılır mı?', 'a' => 'Merdiven, asansör boşluğu, açık balkon gibi bazı alanlar mevzuata göre emsale dahil edilmez; bunların toplamı emsale dahil alanın %30\'unu geçemez. Bu alanlar inşa edildiği için maliyete girer ama ayrı satılabilir alan değildir; araç bu yüzden onları maliyete katar, satış gelirine katmaz. Oranı projenize göre seçin.'],
                ['q' => 'Kat karşılığı sözleşmesinde nelere dikkat edilmeli?', 'a' => 'Sözleşme noterde düzenleme şeklinde yapılmalı; teslim süresi, gecikme cezası, daire dağılımı, malzeme listesi, teminatlar ve tapu devrinin inşaat ilerlemesine bağlanması açıkça yazılmalıdır. İmzadan önce bir avukata danışmanız önerilir.'],
            ],
        ],

    ],
];

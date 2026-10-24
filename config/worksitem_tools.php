<?php

return array (
  'tool_status' =>
  array (
    // Yalnızca staging QA: tüm araçları "QA TEST" rozetiyle açar (canlıda .env'de tanımlanmamalı)
    'staging_test_mode' => env('WORKSITEM_TOOL_QA_STAGING_UNLOCK', false),
    'default' => 'review',
    'overrides' =>
    array (
      'seramik-maliyet-hesaplayici' => 'active',
      'beton-hesaplayici' => 'active',
      'boya-hesaplayici' => 'active',
      'duvar-rengi-gorsellestirici' => 'active',
    ),
  ),
  'categories' => 
  array (
    'yatirim' => 
    array (
      'icon' => '📊',
      'desc' => 'Yatırım, maliyet ve fizibilite için yaklaşık ön hesaplama aracı.',
    ),
    'kaba' => 
    array (
      'icon' => '🏗️',
      'desc' => 'Kaba inşaat, metraj ve teknik ön hesaplama aracı.',
    ),
    'ince' => 
    array (
      'icon' => '🧱',
      'desc' => 'İnce işler için yaklaşık metraj ve maliyet hesaplama aracı.',
    ),
    'mekanik' => 
    array (
      'icon' => '⚙️',
      'desc' => 'Mekanik, elektrik ve enerji işleri için teknik ön hesaplama aracı.',
    ),
    'mevzuat' => 
    array (
      'icon' => '📋',
      'desc' => 'Mevzuat, işçilik ve proje değerlendirmelerine yardımcı araç.',
    ),
  ),
  'featured' => 
  array (
    0 => 'tahmini-bina-maliyeti',
    1 => 'otomatik-kesif-fiyatlandirma',
    2 => 'dijital-metraj',
    3 => 'kat-karsiligi-analiz',
    4 => 'birim-fiyat-analiz',
    5 => 'ihale-modulu',
    6 => 'saha-betonu-hesaplayici',
    7 => 'beton-hesaplayici',
    8 => 'cati-maliyet-hesaplayici',
    9 => 'asma-tavan-hesaplayici',
    10 => 'su-yalitimi-hesaplayici',
    11 => 'epoksi-zemin-hesaplayici',
    12 => 'sandvic-panel-hesaplayici',
    13 => 'havalandirma-hesaplayici',
    14 => 'yukseltilmis-doseme-hesaplayici',
    15 => 'banyo-maliyet-hesaplayici',
    16 => 'mutfak-maliyet-hesaplayici',
    17 => 'mantolama-maliyeti',
    18 => 'taks-kaks-hesaplayici',
  ),
  /*
   * Sonucu yapısal/teknik karar için yanlış kullanılabilecek araçlar.
   * Bu sayfalara App\Http\Middleware\EngineeringToolNotice belirgin bir
   * "mühendislik hesabı yerine geçmez" uyarısı ekler (bkz. /legal/kullanim-kosullari).
   */
  // true: incelemedeki araçlara "noindex, follow" (varsayılan: kapalı — sayfalar SEO için indekslenir)
  'noindex_review_tools' => (bool) env('WORKSITEM_NOINDEX_REVIEW_TOOLS', false),
  'engineering_tools' => 
  array (
    0 => 'basit-kiris-statigi',
    1 => 'kolon-tasima-kapasitesi',
    2 => 'kolon-filiz-boyu',
    3 => 'yapi-dinamigi',
    4 => 'karot-dayanim',
    5 => 'donati-tahvil-hesaplayici',
    6 => 'bina-agirlik-hesabi',
    7 => 'siginak-yonetmeligi',
    8 => 'asansor-zorunlulugu',
    9 => 'deprem-izolator-maliyeti',
    10 => 'beton-onarim-maliyeti',
  ),
  'tools' => 
  array (
    'alci-siva-hesaplama' => 
    array (
      'title' => 'Alçı Sıva Hesaplama',
      'cat' => 'kaba',
      'desc' => 'Duvar ve tavan alanına göre alçı sıva torba sayısı, malzeme ve işçilik maliyeti.',
    ),
    'alcipan-hesaplayici' => 
    array (
      'title' => 'Alçıpan Bölme & Giydirme Duvar Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Alçıpan bölme ve giydirme duvarda levha, profil, vida, derz bandı ve işçilik maliyeti.',
    ),
    'asansor-zorunlulugu' => 
    array (
      'title' => 'Asansör Zorunluluğu',
      'cat' => 'mevzuat',
      'desc' => 'İmar yönetmeliğine göre asansör zorunluluğu ve durak sayısına göre yaklaşık kurulum bütçesi.',
    ),
    'azami-konut-kredisi' => 
    array (
      'title' => 'Azami Konut Kredisi Hesabı',
      'cat' => 'yatirim',
      'desc' => 'Satın almadan önce kullanılabilecek azami konut kredisi ve yaklaşık finansman limiti.',
    ),
    'bahce-duvari-maliyeti' => 
    array (
      'title' => 'Bahçe Duvarı Maliyeti',
      'cat' => 'kaba',
      'desc' => 'Uzunluk ve yüksekliğe göre bahçe duvarı temeli, örgüsü, sıvası ve toplam maliyeti.',
    ),
    'banyo-planlayici' => 
    array (
      'title' => '2D / 3D Banyo Planlayıcı',
      'cat' => 'ince',
      'desc' => 'Banyo ölçülerinize göre lavabo, klozet ve duşu yerleştirin; 2D plan ve 3D görünümde seramik deneyin.',
    ),
    'banyo-maliyet-hesaplayici' => 
    array (
      'title' => 'Banyo Tadilat Maliyeti',
      'cat' => 'ince',
      'desc' => 'Banyo yenileme ve tadilat işleri için yaklaşık maliyet hesabı.',
    ),
    'basit-kiris-statigi' => 
    array (
      'title' => 'Basit Kiriş Statiği',
      'cat' => 'kaba',
      'desc' => 'Düzgün yayılı yük altında iki ucu mafsallı kirişin en büyük momenti ve kesme kuvveti.',
    ),
    'belge-olusturucu' => 
    array (
      'title' => 'Teknik Belge Oluşturucu',
      'cat' => 'yatirim',
      'desc' => 'Anahtar teslim, kat karşılığı, taşeron sözleşmesi, teklif mektubu ve kabul tutanağı şablonları.',
    ),
    'beton-hesaplayici' => 
    array (
      'title' => 'Beton Hesaplayıcı',
      'cat' => 'kaba',
      'desc' => 'Kolon, kiriş, döşeme ve temel için m³ beton, pompa ve kür maliyeti.',
    ),
    'bina-agirlik-hesabi' => 
    array (
      'title' => 'Bina Ağırlık Hesabı',
      'cat' => 'kaba',
      'desc' => 'Deprem analizi için betonarme yapının yaklaşık toplam ağırlığı ve kütlesi (ton).',
    ),
    'birim-fiyat-analiz' => 
    array (
      'title' => 'Birim Fiyat Analizi',
      'cat' => 'yatirim',
      'desc' => 'Poz ve birim fiyat bazlı maliyet çalışmalarına yardımcı hesaplama aracı.',
    ),
    'bombeli-tank-hacmi' => 
    array (
      'title' => 'Bombeli Tank Hacmi',
      'cat' => 'mekanik',
      'desc' => 'Bombeli kapaklı yatay silindirik yakıt ve su tankının toplam hacmi (litre).',
    ),
    'boya-hesaplayici' => 
    array (
      'title' => 'Boya Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Oda ölçüsüne göre kaç kutu boya, astar ve macun gerektiği; iç ve dış cephe boya maliyeti.',
    ),
    'cati-maliyet-hesaplayici' => 
    array (
      'title' => 'Çatı Maliyet & Kaplama Motoru',
      'cat' => 'kaba',
      'desc' => 'Çatı alanı ve uygulama verilerine göre yaklaşık maliyet hesabı.',
    ),
    'cimento-kum-hesaplama' => 
    array (
      'title' => 'Çimento ve Kum Hesaplama',
      'cat' => 'kaba',
      'desc' => 'Kara sıva ve şap için torba çimento ve inşaat kumu miktarı, yaklaşık bütçesiyle.',
    ),
    'daire-kira-degeri' => 
    array (
      'title' => 'Daire Kira Değeri',
      'cat' => 'yatirim',
      'desc' => 'Piyasa değeri, durum ve hedef geri dönüş süresine göre adil aylık kira bedeli.',
    ),
    'demir-metraji-hesaplayici' => 
    array (
      'title' => 'Demir Metrajı Hesaplayıcı',
      'cat' => 'kaba',
      'desc' => 'Betonarme eleman türüne göre donatı miktarı (ton) ve yaklaşık demir maliyeti.',
    ),
    'deprem-izolator-maliyeti' => 
    array (
      'title' => 'Deprem İzolatörü Maliyeti',
      'cat' => 'kaba',
      'desc' => 'Bina yüküne göre sismik izolatör adedi, montaj, grout ve mühendislik maliyeti.',
    ),
    'beton-onarim-maliyeti' => 
    array (
      'title' => 'Beton Onarım ve Grout Maliyeti',
      'cat' => 'kaba',
      'desc' => 'Dökülen paspayı ve paslı donatı için tamir harcı, korozyon önleyici ve grout maliyeti.',
    ),
    'donati-tahvil-hesaplayici' => 
    array (
      'title' => 'Donatı Tahvil Hesaplayıcı',
      'cat' => 'kaba',
      'desc' => 'Projedeki çap yoksa mevcut donatının eşdeğer aralığıyla şantiyede demir tahvili.',
    ),
    'duvar-maliyet-hesaplayici' => 
    array (
      'title' => 'Duvar Maliyet Hesaplayıcı',
      'cat' => 'kaba',
      'desc' => 'Tuğla, gazbeton veya bims duvarın malzeme, harç ve işçilik maliyeti.',
    ),
    'duvar-orgu' => 
    array (
      'title' => 'Duvar Örgü Hesabı',
      'cat' => 'kaba',
      'desc' => 'Duvar alanına göre tuğla, blok ve harç adedi ile örgü maliyeti.',
    ),
    'duvar-rengi-gorsellestirici' => 
    array (
      'title' => 'Duvar Rengi Görselleştirici',
      'cat' => 'ince',
      'desc' => 'Doğrulanmış renk kataloglarındaki tonları oda veya cephe fotoğrafınızda deneyin.',
    ),
    'dijital-metraj' => 
    array (
      'title' => 'Dijital Metraj',
      'cat' => 'yatirim',
      'icon' => '📐',
      'desc' => 'PDF veya görsel proje üzerinden ölçekli alan ve metraj çalışması yapın.',
    ),
    'enerji-tuketim-hesaplama' => 
    array (
      'title' => 'Enerji Tüketim Hesaplama',
      'cat' => 'mekanik',
      'desc' => 'Cihaz gücü ve kullanım süresine göre aylık elektrik tüketimi ve fatura tutarı.',
    ),
    'gunes-paneli-hesaplama' => 
    array (
      'title' => 'Güneş Paneli / GES Hesaplama',
      'cat' => 'mekanik',
      'desc' => 'Çatı ve tüketime göre güneş paneli sayısı, kurulu güç, üretim ve GES maliyeti.',
    ),
    'hafriyat-kazi-hesabi' => 
    array (
      'title' => 'Hafriyat ve Kazı Hesabı',
      'cat' => 'kaba',
      'desc' => 'Kazı hacmi, kabarma payı, kamyon sefer sayısı ve hafriyat maliyeti.',
    ),
    'havalandirma-kanali' => 
    array (
      'title' => 'Havalandırma Kanalı Hesabı',
      'cat' => 'mekanik',
      'desc' => 'Hava debisi ve hıza göre havalandırma kanal kesiti ve boyutu.',
    ),
    'ihale-modulu' => 
    array (
      'title' => 'İhale ve Yaklaşık Maliyet Modülü',
      'cat' => 'yatirim',
      'desc' => 'İhale ve yaklaşık maliyet çalışmalarına yardımcı teknik araç.',
    ),
    'insaat-muhendisi-maas-hesabi' => 
    array (
      'title' => 'İnşaat Mühendisi Maaş Hesabı',
      'cat' => 'mevzuat',
      'desc' => 'Deneyim ve pozisyona göre inşaat mühendisi için kaba net maaş tahmini (piyasa varsayımı).',
    ),
    'is-makinesi-maliyeti' => 
    array (
      'title' => 'İş Makinesi Maliyeti',
      'cat' => 'kaba',
      'desc' => 'Ekskavatör, kepçe ve vincin yakıt ve operatör dahil saatlik ve günlük kiralama maliyeti.',
    ),
    'iscilik-maliyeti' => 
    array (
      'title' => 'İşçilik Maliyeti',
      'cat' => 'mevzuat',
      'desc' => 'Yevmiyeli işçinin SGK, vergi, yol ve yemek dahil işverene gerçek maliyeti.',
    ),
    'iskele-metraji-maliyeti' => 
    array (
      'title' => 'İskele Metrajı ve Maliyeti',
      'cat' => 'kaba',
      'desc' => 'Cephe ölçüsüne göre iskele m², kurulum, kira süresi ve toplam maliyet.',
    ),
    'jenerator-hesaplama' => 
    array (
      'title' => 'Jeneratör Hesaplama',
      'cat' => 'mekanik',
      'desc' => 'Toplam yük ve kalkış akımına göre gereken jeneratör gücü (kVA).',
    ),
    'kablo-kesiti-hesabi' => 
    array (
      'title' => 'Kablo Kesiti Hesabı',
      'cat' => 'mekanik',
      'desc' => 'Güç, mesafe ve gerilime göre uygun kablo kesiti ve yaklaşık elektrik altyapı bütçesi.',
    ),
    'kalip-plywood-maliyeti' => 
    array (
      'title' => 'Kalıp ve Plywood Maliyeti',
      'cat' => 'kaba',
      'desc' => 'Kalıp alanına göre plywood, kereste, iskele ve kalıp işçiliği maliyeti.',
    ),
    'kara-siva-hesaplama' => 
    array (
      'title' => 'Kara Sıva Hesaplama',
      'cat' => 'kaba',
      'desc' => 'Duvar alanı ve kalınlığa göre kara sıva çimento, kum ve işçilik maliyeti.',
    ),
    'karot-dayanim' => 
    array (
      'title' => 'Karot Dayanım Hesabı',
      'cat' => 'kaba',
      'desc' => 'Karot numunesinin kırılma yüküne göre eşdeğer silindir basınç dayanımı (MPa).',
    ),
    'kat-karsiligi-analiz' => 
    array (
      'title' => 'Kat Karşılığı Fizibilite Analizi',
      'cat' => 'yatirim',
      'desc' => 'Arsa ve proje verileriyle kat karşılığı fizibilitesini değerlendirin.',
    ),
    'kesim-optimizasyonu' => 
    array (
      'title' => 'Kesim Optimizasyonu',
      'cat' => 'yatirim',
      'desc' => 'Profil, boru ve ahşap kesiminde standart boylara göre ihtiyaç miktarı ve fire oranı.',
    ),
    'klima-btu-hesaplayici' => 
    array (
      'title' => 'Klima BTU Hesaplayıcı',
      'cat' => 'mekanik',
      'desc' => 'Oda büyüklüğü, cephe ve kullanıma göre gereken klima kapasitesi (BTU).',
    ),
    'kolon-filiz-boyu' => 
    array (
      'title' => 'Kolon Filiz Boyu',
      'cat' => 'kaba',
      'desc' => 'TS500\'e göre kolon donatısında kenetlenme ve bindirme boyu, üst kat filiz uzunluğu.',
    ),
    'kolon-tasima-kapasitesi' => 
    array (
      'title' => 'Kolon Taşıma Kapasitesi',
      'cat' => 'kaba',
      'desc' => 'Kolon kesiti, beton ve donatıya göre eksenel taşıma kapasitesi.',
    ),
    'lojistik-maliyeti' => 
    array (
      'title' => 'Lojistik Maliyeti',
      'cat' => 'yatirim',
      'desc' => 'Tonaj, mesafe, yakıt ve sefere göre hafriyat ve dökme malzeme nakliye maliyeti.',
    ),
    'malzeme-agirligi' => 
    array (
      'title' => 'Malzeme Ağırlığı Hesabı',
      'cat' => 'kaba',
      'desc' => 'Yoğunluk ve hacme göre yapı malzemesi ağırlığı ve nakliye tonajı.',
    ),
    'mantolama-maliyeti' => 
    array (
      'title' => 'Mantolama Maliyeti',
      'cat' => 'mekanik',
      'desc' => 'Cephe alanına göre ısı yalıtım levhası, yapıştırma ve sıva harcı, son kat ve iskele maliyeti.',
    ),
    'merdiven-basamak-hesabi' => 
    array (
      'title' => 'Merdiven Basamak Hesabı',
      'cat' => 'kaba',
      'desc' => '2R+T=63 kuralıyla basamak ölçüleri ve mermer kaplama için yaklaşık bütçe.',
    ),
    'metrekup-hacim-hesaplama' => 
    array (
      'title' => 'Metreküp ve Hacim Hesaplama',
      'cat' => 'kaba',
      'desc' => 'Havuz, depo, temel veya kazının hacmi, taban alanı ve litre kapasitesi.',
    ),
    'mutfak-maliyet-hesaplayici' => 
    array (
      'title' => 'Mutfak Tadilat Maliyeti',
      'cat' => 'ince',
      'desc' => 'Mutfak yenileme ve tadilat işleri için yaklaşık maliyet hesabı.',
    ),
    'oda-planlayici' => 
    array (
      'title' => 'Oda Planlayıcı',
      'cat' => 'ince',
      'desc' => 'Odanızın fotoğrafını yükleyin; yapay zekâ odayı modern, İskandinav ya da endüstriyel stilde yeniden tasarlasın.',
    ),
    'otomatik-kesif-fiyatlandirma' => 
    array (
      'title' => 'AI Ön Keşif ve Ön Fiyatlandırma',
      'cat' => 'yatirim',
      'icon' => '🤖',
      'desc' => 'Dosya tabanlı ön keşif ve ön fiyatlandırma sürecini başlatın.',
    ),
    'pafta-olcu-hesaplayici' => 
    array (
      'title' => 'Pafta Ölçü Hesaplayıcı',
      'cat' => 'yatirim',
      'desc' => 'Plan ya da paftada ölçülen uzunluğun ölçeğe göre arazideki gerçek karşılığı.',
    ),
    'parke-maliyet-hesaplayici' => 
    array (
      'title' => 'Parke Maliyet Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Oda alanına göre laminat veya masif parke, şilte, süpürgelik ve işçilik maliyeti.',
    ),
    'pompa-gucu-hesabi' => 
    array (
      'title' => 'Pompa Gücü Hesabı',
      'cat' => 'mekanik',
      'desc' => 'Debi ve basma yüksekliğine göre gereken su pompası gücü.',
    ),
    'pvc-dograma-hesaplayici' => 
    array (
      'title' => 'PVC Doğrama Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Pencere ölçüsüne göre PVC profil, ısıcam, montaj ve doğrama maliyeti.',
    ),
    'radyator-petek-hesaplayici' => 
    array (
      'title' => 'Radyatör / Petek Hesaplayıcı',
      'cat' => 'mekanik',
      'desc' => 'Oda hacmi ve yalıtıma göre gereken petek boyu ve radyatör maliyeti.',
    ),
    'saha-betonu-hesaplayici' => 
    array (
      'title' => 'Saha Betonu Hesaplayıcı',
      'cat' => 'kaba',
      'desc' => 'Endüstriyel zemin için beton, hasır çelik, perdah, sertleştirici ve kür maliyeti.',
    ),
    'sap-hesaplayici' => 
    array (
      'title' => 'Şap Hesaplayıcı',
      'cat' => 'kaba',
      'desc' => 'Alan ve kalınlığa göre şap torba sayısı, katkı ve şap işçiliği maliyeti.',
    ),
    'seramik-maliyet-hesaplayici' => 
    array (
      'title' => 'Seramik Maliyet Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Zemin ve duvar seramiğinde karo, yapıştırıcı, derz, fire ve işçilik maliyeti; kaç kutu, kaç torba.',
    ),
    'sgk-asgari-iscilik' => 
    array (
      'title' => 'SGK Asgari İşçilik Hesabı',
      'cat' => 'mevzuat',
      'desc' => 'İskan ve ilişiksizlik için SGK asgari işçilik, eksik prim ve fark tutarı.',
    ),
    'siginak-yonetmeligi' => 
    array (
      'title' => 'Sığınak Yönetmeliği',
      'cat' => 'mevzuat',
      'desc' => 'Yapı alanına göre sığınak zorunluluğu ve gereken sığınak alanı.',
    ),
    'statik-proje-bedeli' => 
    array (
      'title' => 'Statik Proje Bedeli',
      'cat' => 'yatirim',
      'desc' => 'Yapı alanı ve sınıfına göre statik proje hizmet bedeli.',
    ),
    'su-tesisat-hesaplama' => 
    array (
      'title' => 'Su Tesisatı Hesaplama',
      'cat' => 'mekanik',
      'desc' => 'Islak hacim sayısına göre boru, ek parça, armatür ve su tesisatı işçiliği maliyeti.',
    ),
    'tahmini-bina-maliyeti' => 
    array (
      'title' => 'Bina İnşaat Maliyeti',
      'cat' => 'yatirim',
      'icon' => '🏢',
      'desc' => 'Temel proje verileriyle yaklaşık bina yapım maliyetini değerlendirin.',
    ),
    'taks-kaks-hesaplayici' => 
    array (
      'title' => 'TAKS / KAKS Hesaplayıcı',
      'cat' => 'yatirim',
      'desc' => 'İmar durumundaki TAKS ve KAKS değerine göre emsal ve brüt inşaat alanı, kat sayısı.',
    ),
    'ges-planlayici' =>
    array (
      'title' => 'GES Planlayıcı: Çatıya Panel Yerleşimi (2D / 3D)',
      'cat' => 'mekanik',
      'desc' => 'Adresinizi yazın; çatınız uydu haritasında gelsin, paneller otomatik dizilsin, 2D ve 3D görün; yıllık üretim, maliyet ve geri dönüş süresi hesaplansın.',
    ),
    'mutfak-planlayici' =>
    array (
      'title' => '2D / 3D Mutfak Planlayıcı',
      'cat' => 'ince',
      'desc' => 'Mutfağınızın ölçülerini girin; dolapları, eviyeyi, ocağı ve buzdolabını yerleştirin, 2D plan ve 3D görünümde deneyin.',
    ),
    'wc-planlayici' =>
    array (
      'title' => '2D / 3D WC Planlayıcı',
      'cat' => 'ince',
      'desc' => 'WC ölçülerinize göre lavabo ve klozeti yerleştirin; 2D plan ve 3D görünümde seramik ve boya deneyin.',
    ),
    'wc-maliyet-hesabi' => 
    array (
      'title' => 'WC Tadilat Maliyeti',
      'cat' => 'ince',
      'desc' => 'Klozet, lavabo, seramik ve tesisatla WC tadilatı maliyeti.',
    ),
    'yagmur-borusu-hesabi' => 
    array (
      'title' => 'Yağmur Borusu Hesabı',
      'cat' => 'mekanik',
      'desc' => 'Çatı alanına göre yağmur borusu çapı ve yaklaşık maliyet.',
    ),
    'yapi-dinamigi' => 
    array (
      'title' => 'Yapı Dinamiği',
      'cat' => 'kaba',
      'desc' => 'TBDY-2018 ampirik formülüyle binanın yaklaşık doğal titreşim periyodu (saniye).',
    ),
    'zemin-kaplama' => 
    array (
      'title' => 'Zemin Kaplama Hesabı',
      'cat' => 'ince',
      'desc' => 'Parke, seramik, fayans veya mermer zeminde fire dahil malzeme ve yaklaşık uygulama bütçesi.',
    ),
    'asma-tavan-hesaplayici' => 
    array (
      'title' => 'Asma Tavan Sistemleri Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Alüminyum, galvaniz, lamel ve PVC asma tavan sistemlerinin malzeme ve işçilik maliyeti.',
    ),
    'su-yalitimi-hesaplayici' => 
    array (
      'title' => 'Su Yalıtımı Metraj & Maliyet Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Teras, banyo, bodrum ve çatı için su yalıtım sistemi, malzeme ve uygulama maliyeti.',
    ),
    'epoksi-zemin-hesaplayici' => 
    array (
      'title' => 'Epoksi Zemin Maliyet Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Fabrika, depo ve otopark zemininde epoksi ve poliüretan kaplama, astar ve tesviye maliyeti.',
    ),
    'sandvic-panel-hesaplayici' => 
    array (
      'title' => 'Sandviç Panel Çatı & Cephe Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Çatı ve cephe alanına göre sandviç panel, aksesuar ve montaj maliyeti.',
    ),
    'yukseltilmis-doseme-hesaplayici' => 
    array (
      'title' => 'Yükseltilmiş Döşeme Metraj Hesaplayıcı',
      'cat' => 'ince',
      'desc' => 'Ofis ve veri merkezi için yükseltilmiş döşeme paneli, ayak ve montaj metrajı.',
    ),
    'havalandirma-hesaplayici' => 
    array (
      'title' => 'Havalandırma Kanalı & HVAC Maliyet Hesaplayıcı',
      'cat' => 'mekanik',
      'desc' => 'Mekân hacmine göre havalandırma kanalı, menfez ve HVAC maliyeti.',
    ),
  ),
);

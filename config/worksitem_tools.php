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
    ),
    'alcipan-hesaplayici' => 
    array (
      'title' => 'Alçıpan Bölme & Giydirme Duvar Hesaplayıcı',
      'cat' => 'ince',
    ),
    'asansor-zorunlulugu' => 
    array (
      'title' => 'Asansör Zorunluluğu',
      'cat' => 'mevzuat',
    ),
    'azami-konut-kredisi' => 
    array (
      'title' => 'Azami Konut Kredisi Hesabı',
      'cat' => 'yatirim',
    ),
    'bahce-duvari-maliyeti' => 
    array (
      'title' => 'Bahçe Duvarı Maliyeti',
      'cat' => 'kaba',
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
    ),
    'belge-olusturucu' => 
    array (
      'title' => 'Teknik Belge Oluşturucu',
      'cat' => 'yatirim',
    ),
    'beton-hesaplayici' => 
    array (
      'title' => 'Beton Hesaplayıcı',
      'cat' => 'kaba',
    ),
    'bina-agirlik-hesabi' => 
    array (
      'title' => 'Bina Ağırlık Hesabı',
      'cat' => 'kaba',
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
    ),
    'boya-hesaplayici' => 
    array (
      'title' => 'Boya Hesaplayıcı',
      'cat' => 'ince',
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
    ),
    'daire-kira-degeri' => 
    array (
      'title' => 'Daire Kira Değeri',
      'cat' => 'yatirim',
    ),
    'demir-metraji-hesaplayici' => 
    array (
      'title' => 'Demir Metrajı Hesaplayıcı',
      'cat' => 'kaba',
    ),
    'deprem-izolator-maliyeti' => 
    array (
      'title' => 'Deprem İzolatörü Maliyeti',
      'cat' => 'kaba',
    ),
    'beton-onarim-maliyeti' => 
    array (
      'title' => 'Beton Onarım ve Grout Maliyeti',
      'cat' => 'kaba',
    ),
    'donati-tahvil-hesaplayici' => 
    array (
      'title' => 'Donatı Tahvil Hesaplayıcı',
      'cat' => 'kaba',
    ),
    'duvar-maliyet-hesaplayici' => 
    array (
      'title' => 'Duvar Maliyet Hesaplayıcı',
      'cat' => 'kaba',
    ),
    'duvar-orgu' => 
    array (
      'title' => 'Duvar Örgü Hesabı',
      'cat' => 'kaba',
    ),
    'duvar-rengi-gorsellestirici' => 
    array (
      'title' => 'Duvar Rengi Görselleştirici',
      'cat' => 'ince',
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
    ),
    'gunes-paneli-hesaplama' => 
    array (
      'title' => 'Güneş Paneli / GES Hesaplama',
      'cat' => 'mekanik',
    ),
    'hafriyat-kazi-hesabi' => 
    array (
      'title' => 'Hafriyat ve Kazı Hesabı',
      'cat' => 'kaba',
    ),
    'havalandirma-kanali' => 
    array (
      'title' => 'Havalandırma Kanalı Hesabı',
      'cat' => 'mekanik',
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
    ),
    'is-makinesi-maliyeti' => 
    array (
      'title' => 'İş Makinesi Maliyeti',
      'cat' => 'kaba',
    ),
    'iscilik-maliyeti' => 
    array (
      'title' => 'İşçilik Maliyeti',
      'cat' => 'mevzuat',
    ),
    'iskele-metraji-maliyeti' => 
    array (
      'title' => 'İskele Metrajı ve Maliyeti',
      'cat' => 'kaba',
    ),
    'jenerator-hesaplama' => 
    array (
      'title' => 'Jeneratör Hesaplama',
      'cat' => 'mekanik',
    ),
    'kablo-kesiti-hesabi' => 
    array (
      'title' => 'Kablo Kesiti Hesabı',
      'cat' => 'mekanik',
    ),
    'kalip-plywood-maliyeti' => 
    array (
      'title' => 'Kalıp ve Plywood Maliyeti',
      'cat' => 'kaba',
    ),
    'kara-siva-hesaplama' => 
    array (
      'title' => 'Kara Sıva Hesaplama',
      'cat' => 'kaba',
    ),
    'karot-dayanim' => 
    array (
      'title' => 'Karot Dayanım Hesabı',
      'cat' => 'kaba',
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
    ),
    'klima-btu-hesaplayici' => 
    array (
      'title' => 'Klima BTU Hesaplayıcı',
      'cat' => 'mekanik',
    ),
    'kolon-filiz-boyu' => 
    array (
      'title' => 'Kolon Filiz Boyu',
      'cat' => 'kaba',
    ),
    'kolon-tasima-kapasitesi' => 
    array (
      'title' => 'Kolon Taşıma Kapasitesi',
      'cat' => 'kaba',
    ),
    'lojistik-maliyeti' => 
    array (
      'title' => 'Lojistik Maliyeti',
      'cat' => 'yatirim',
    ),
    'malzeme-agirligi' => 
    array (
      'title' => 'Malzeme Ağırlığı Hesabı',
      'cat' => 'kaba',
    ),
    'mantolama-maliyeti' => 
    array (
      'title' => 'Mantolama Maliyeti',
      'cat' => 'mekanik',
    ),
    'merdiven-basamak-hesabi' => 
    array (
      'title' => 'Merdiven Basamak Hesabı',
      'cat' => 'kaba',
    ),
    'metrekup-hacim-hesaplama' => 
    array (
      'title' => 'Metreküp ve Hacim Hesaplama',
      'cat' => 'kaba',
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
    ),
    'parke-maliyet-hesaplayici' => 
    array (
      'title' => 'Parke Maliyet Hesaplayıcı',
      'cat' => 'ince',
    ),
    'pompa-gucu-hesabi' => 
    array (
      'title' => 'Pompa Gücü Hesabı',
      'cat' => 'mekanik',
    ),
    'pvc-dograma-hesaplayici' => 
    array (
      'title' => 'PVC Doğrama Hesaplayıcı',
      'cat' => 'ince',
    ),
    'radyator-petek-hesaplayici' => 
    array (
      'title' => 'Radyatör / Petek Hesaplayıcı',
      'cat' => 'mekanik',
    ),
    'saha-betonu-hesaplayici' => 
    array (
      'title' => 'Saha Betonu Hesaplayıcı',
      'cat' => 'kaba',
    ),
    'sap-hesaplayici' => 
    array (
      'title' => 'Şap Hesaplayıcı',
      'cat' => 'kaba',
    ),
    'seramik-maliyet-hesaplayici' => 
    array (
      'title' => 'Seramik Maliyet Hesaplayıcı',
      'cat' => 'ince',
    ),
    'sgk-asgari-iscilik' => 
    array (
      'title' => 'SGK Asgari İşçilik Hesabı',
      'cat' => 'mevzuat',
    ),
    'siginak-yonetmeligi' => 
    array (
      'title' => 'Sığınak Yönetmeliği',
      'cat' => 'mevzuat',
    ),
    'statik-proje-bedeli' => 
    array (
      'title' => 'Statik Proje Bedeli',
      'cat' => 'yatirim',
    ),
    'su-tesisat-hesaplama' => 
    array (
      'title' => 'Su Tesisatı Hesaplama',
      'cat' => 'mekanik',
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
    ),
    'yagmur-borusu-hesabi' => 
    array (
      'title' => 'Yağmur Borusu Hesabı',
      'cat' => 'mekanik',
    ),
    'yapi-dinamigi' => 
    array (
      'title' => 'Yapı Dinamiği',
      'cat' => 'kaba',
    ),
    'zemin-kaplama' => 
    array (
      'title' => 'Zemin Kaplama Hesabı',
      'cat' => 'ince',
    ),
    'asma-tavan-hesaplayici' => 
    array (
      'title' => 'Asma Tavan Sistemleri Hesaplayıcı',
      'cat' => 'ince',
    ),
    'su-yalitimi-hesaplayici' => 
    array (
      'title' => 'Su Yalıtımı Metraj & Maliyet Hesaplayıcı',
      'cat' => 'ince',
    ),
    'epoksi-zemin-hesaplayici' => 
    array (
      'title' => 'Epoksi Zemin Maliyet Hesaplayıcı',
      'cat' => 'ince',
    ),
    'sandvic-panel-hesaplayici' => 
    array (
      'title' => 'Sandviç Panel Çatı & Cephe Hesaplayıcı',
      'cat' => 'ince',
    ),
    'yukseltilmis-doseme-hesaplayici' => 
    array (
      'title' => 'Yükseltilmiş Döşeme Metraj Hesaplayıcı',
      'cat' => 'ince',
    ),
    'havalandirma-hesaplayici' => 
    array (
      'title' => 'Havalandırma Kanalı & HVAC Maliyet Hesaplayıcı',
      'cat' => 'mekanik',
    ),
  ),
);

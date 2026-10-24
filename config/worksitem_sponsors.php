<?php

/*
|--------------------------------------------------------------------------
| Sponsor (marka) alanları
|--------------------------------------------------------------------------
| Reklam olduğu her kartta "Sponsorlu" etiketiyle açıkça belirtilir
| (Ticari Reklam ve Haksız Ticari Uygulamalar Yönetmeliği: örtülü reklam yasağı).
| Bağlantılar rel="sponsored nofollow" ile verilir (Google politikası).
*/

/*
| v114: poz tanımı → ürün grubu (Yapı Kimyasalları Sponsorluk Haritası'ndaki 40 grup, 43.290 poz üzerinde denendi).
| Yeni sponsor/ürün grubu: buraya bir satır ekleyin, sonra  php artisan sponsors:poz-esle --rapor
| Anahtar hiyerarşisi: 'yalitim_bitum' eşleşirse genel 'yalitim' düşer; sponsor kartında 'yalitim' sponsorları da gösterilir.
| Desenler küçük harfe çevrilmiş metinde aranır (Türkçe İ/I doğru çevrilir); poz tanımı parça ise ('(40 x 40 cm)…') grubun son başlığı bağlam olur.
*/
$pozRules = [
    'yalitim_cimento' => ['label' => 'Çimento esaslı su yalıtımı', 'match' => '/çimento esaslı.{0,80}(su yalıtım|yalıtım harcı)|(su yalıtım|yalıtım harcı).{0,60}çimento esaslı|kristalize/u', 'except' => null],
    'yalitim_poliurea' => ['label' => 'Poliürea su yalıtımı', 'match' => '/poli[üu]re[ae] (bazlı|esaslı)|polyurea/u', 'except' => '/zemin kaplama/u'],
    'yalitim_likit' => ['label' => 'Likit su yalıtımı (PU, akrilik)', 'match' => '/(poli[üu]retan|akrilik|elastomerik reçine|elastromerik reçine).{0,40}(esaslı|bazlı).{0,120}(su yalıtım|sıvı (plastik )?kaplama|likit)|likit membran|sıvı membran/u', 'except' => '/zemin kaplama/u'],
    'yalitim_bitum' => ['label' => 'Bitümlü membran ve örtüler', 'match' => '/bitüm|bitum|asfalt emülsiyon|shingle/u', 'except' => '/yol|asfalt betonu|kaplama tabakası|bağlayıcı tabaka|aşınma tabakası|binder/u'],
    'yalitim_membran' => ['label' => 'Sentetik membran ve su yalıtım örtüsü', 'match' => '/(tpo|pvc|epdm|hdpe|sentetik|tekstil|polimer).{0,30}(membran|örtü)|jeomembran|su yalıtım örtüsü|nefes alan/u', 'except' => '/sandviç|panel|bitüm/u'],
    'yalitim_bant' => ['label' => 'Su yalıtım bandı ve su tutucu', 'match' => '/su ?tutucu bant|waterstop|su yalıtım bandı|dilatasyon.{0,30}yalıtım bandı|yalıtım bandı/u', 'except' => '/alçı/u'],
    'yalitim' => ['label' => 'Su yalıtımı', 'match' => '/su yalıtım|su geçirimsiz|nem yalıtım|sürme yalıtım|su izolasyon/u', 'except' => null],
    'yapistirici_reaktif' => ['label' => 'Reaktif (reçine) karo yapıştırıcısı', 'match' => '/(reaksiyon reçine|epoksi|reçine) esaslı.{0,120}karo yapıştırıcı/u', 'except' => null],
    'yapistirici' => ['label' => 'Karo yapıştırıcısı', 'match' => '/karo yapıştırıcı|seramik yapıştırıcı|(seramik|karo|fayans|porselen|granit).{0,160}yapıştırıcı|c2te|c1te/u', 'except' => '/pvc|linolyum|kalıp ayırıcı/u'],
    'derz_reaktif' => ['label' => 'Reaktif (reçine) derz dolgu', 'match' => '/(reaksiyon reçine|epoksi) esaslı.{0,80}derz dolgu/u', 'except' => '/mastik/u'],
    'derz' => ['label' => 'Derz dolgu', 'match' => '/(seramik|karo|fayans|porselen|granit|mozaik).{0,200}derz (aralıklı|dolgu)|derz dolgusu|derz dolgu (harcı|malzemesi)|derz harcı/u', 'except' => '/horasan|moloz|taş duvar|kireç|mastik|alçı/u'],
    'boya_dis' => ['label' => 'Dış cephe boyası', 'match' => '/dış cephe.{0,40}boya|boya.{0,60}dış cephe/u', 'except' => '/ahşap|metal|demir|iç cephe/u'],
    'boya_ic' => ['label' => 'İç cephe boyası', 'match' => '/iç cephe.{0,40}boya|boya.{0,60}iç cephe|tavan boyası/u', 'except' => '/ahşap|metal|demir|yol|zemin|epoksi|dış cephe/u'],
    'astar_macun' => ['label' => 'Astar, macun ve yüzey hazırlık', 'match' => '/(boya|su bazlı|silikon|akrilik|dolgu|alçı) astar|astar(ı)? uygulan|macun|tamir alçısı|saten alçı/u', 'except' => '/metal|galvaniz|sac|ahşap|epoksi astar|makine/u'],
    'dekoratif' => ['label' => 'Dekoratif boya ve sıva', 'match' => '/dekoratif (boya|sıva|kaplama)|mineral sıva|silikon(lu)? (esaslı )?sıva|grenli|tekstürlü|taş görünümlü/u', 'except' => '/zemin/u'],
    'mantolama' => ['label' => 'Mantolama (ısı yalıtım sistemi) harç ve aksesuarları', 'match' => '/mantolama|ısı yalıtım sistemi|ısı yalıtım levha(sı|ları)? (yapıştırıcı|sıva)|yalıtım (levhası|levha) (yapıştırıcısı|sıvası)|yalıtım dübel|sıva filesi/u', 'except' => '/boru|kanal|demir|kaynak|kolon/u'],
    'sap' => ['label' => 'Şap ve zemin tesviye', 'match' => '/(^|[^a-zçğıöşü])şap([^a-zçğıöşü]|$)|tesviye (tabakası|harcı|şapı)|zemin tesviye|kendinden yayılan|kendiliğinden (yerleşen|yayılan)|self.?level/u', 'except' => '/horasan|priz kutu|moloz/u'],
    'epoksi_zemin' => ['label' => 'Epoksi ve poliüretan zemin kaplama', 'match' => '/(epoksi|poli[üu]retan|poliürea).{0,40}(esaslı|bazlı).{0,40}zemin kaplama|zemin kaplaması.{0,60}(epoksi|poliüretan)|endüstriyel zemin/u', 'except' => null],
    'sertlestirici' => ['label' => 'Yüzey sertleştirici', 'match' => '/yüzey sertleştirici|korund/u', 'except' => '/toprak/u'],
    'kur' => ['label' => 'Kür malzemesi', 'match' => '/kür (malzemesi|maddesi|kimyasalı|uygulaması)|kür yapılması/u', 'except' => null],
    'beton_katki' => ['label' => 'Beton ve harç katkısı, lif', 'match' => '/beton katkı|harç katkı|katkı maddesi|akışkanlaştırıcı|priz (geciktirici|hızlandırıcı|çabuklaştırıcı)|antifriz|hava sürükleyici|su geçirimsizlik katkı|(sentetik|polipropilen|çelik|makro).{0,10}(lif|fiber)/u', 'except' => null],
    'tamir' => ['label' => 'Tamir ve onarım harcı', 'match' => '/tamir harc|onarım harc|korozyon tamiri|mikrobeton|yapısal onarım/u', 'except' => null],
    'grout' => ['label' => 'Grout ve döküm harcı', 'match' => '/grout|döküm harcı|ekspansif harç/u', 'except' => '/jet grout|saatlik|makine/u'],
    'ankraj' => ['label' => 'Kimyasal ankraj', 'match' => '/ankraj.{0,60}(epoksi|reçine|kimyasal)|kimyasal ankraj|enjeksiyon reçinesi ile çubuk/u', 'except' => null],
    'korozyon' => ['label' => 'Pas ve korozyon koruma', 'match' => '/korozyon (önleyici|inhibitör|hızını yavaşlatan)|antipas|pas (önleyici|sökücü)/u', 'except' => null],
    'enjeksiyon' => ['label' => 'Çatlak enjeksiyonu', 'match' => '/(epoksi|poliüretan) enjeksiyon|çatlak enjeksiyon|enjeksiyon ile çatlak/u', 'except' => null],
    'mastik' => ['label' => 'Mastik ve sızdırmazlık', 'match' => '/mastik|poliüretan köpük|pu köpük|montaj köpüğü|sızdırmazlık (bandı|mastiği|malzemesi)/u', 'except' => '/boru|conta|vida/u'],
    'seramik' => ['label' => 'Seramik ve porselen karo', 'match' => '/seramik (yer|duvar)?\\s?karo|seramik kaplama|seramik ile|seramik (yer|duvar) kaplama|porselen karo|granit seramik|sırlı porselen|fayans (ile|kaplama)/u', 'except' => '/terrazo|karosiman|radyant|sabunluk|ısıtıcı|lavabo|klozet/u'],
    'isi_yalitim' => ['label' => 'Isı yalıtım levhası (EPS, XPS, taş yünü)', 'match' => '/(eps|xps|taş ?yünü|cam ?yünü|ekstrüde polistiren|ekspande polistren|genleştirilmiş polistiren|poliizosiyanurat).{0,60}(levha|şilte|plaka|ısı yalıtım)|ısı yalıtım (levhası|malzemesi|şiltesi)/u', 'except' => '/boru|kanal|tesisat|sandviç|panel|baca/u'],
];

return [
    // Araç → o araçta kullanılabilen sponsor kategorileri
    'tools' => [
        'banyo-maliyet-hesaplayici' => [
            'label' => 'Banyo maliyet hesaplayıcı',
            'categories' => [
                'seramik' => 'Seramik ve karo',
                'vitrifiye' => 'Vitrifiye ve armatür',
                'dus' => 'Duşakabin, küvet',
                'yalitim' => 'Islak hacim su yalıtımı',
                'yapistirici' => 'Yapıştırıcı ve derz dolgu',
                'genel' => 'Genel',
            ],
        ],
        'banyo-planlayici' => [
            'label' => '2D / 3D banyo planlayıcı',
            'categories' => [
                'seramik' => 'Seramik ve karo',
                'vitrifiye' => 'Vitrifiye (lavabo, klozet)',
                'dus' => 'Duşakabin, küvet, cam panel',
                'armatur' => 'Batarya, havlupan, aksesuar',
                'mobilya' => 'Banyo dolabı',
            ],
        ],
        'wc-planlayici' => [
            'label' => '2D / 3D WC planlayıcı',
            'categories' => [
                'seramik' => 'Seramik ve karo',
                'vitrifiye' => 'Vitrifiye (lavabo, klozet)',
                'armatur' => 'Batarya ve aksesuar',
            ],
        ],
        'mutfak-maliyet-hesaplayici' => [
            'label' => 'Mutfak maliyet hesaplayıcı',
            'categories' => [
                'dolap' => 'Mutfak dolabı',
                'tezgah' => 'Tezgah (granit, kuvars, porselen)',
                'ankastre' => 'Ankastre ve beyaz eşya',
                'armatur' => 'Eviye ve batarya',
                'genel' => 'Genel (tüm mutfak)',
            ],
        ],
        'seramik-maliyet-hesaplayici' => [
            'label' => 'Seramik metraj ve maliyet',
            'categories' => [
                'seramik' => 'Seramik ve porselen karo',
                'yapistirici' => 'Karo yapıştırıcısı',
                'derz' => 'Derz dolgu (çimento ve epoksi)',
                'seviyeleme' => 'Karo seviyeleme sistemi',
                'temizlik' => 'Temizlik ve bakım ürünleri',
                'yalitim' => 'Su yalıtımı',
                'genel' => 'Genel',
            ],
        ],
        'boya-hesaplayici' => [
            'label' => 'Boya hesaplayıcı',
            'categories' => [
                'boya' => 'İç cephe boyası',
                'dis_cephe' => 'Dış cephe boyası',
                'astar' => 'Astar',
                'hazirlik' => 'Macun ve yüzey hazırlık',
                'dekoratif' => 'Dekoratif boya',
                'ekipman' => 'Ekipman',
                'genel' => 'Genel',
            ],
        ],
        'wc-maliyet-hesabi' => [
            'label' => 'WC tadilat maliyeti',
            'categories' => [
                'vitrifiye' => 'Vitrifiye ve armatür',
                'seramik' => 'Seramik ve karo',
                'yapistirici' => 'Yapıştırıcı ve derz dolgu',
                'genel' => 'Genel',
            ],
        ],
        'parke-maliyet-hesaplayici' => [
            'label' => 'Parke maliyeti',
            'categories' => [
                'parke' => 'Parke ve zemin kaplama',
                'supurgelik' => 'Süpürgelik ve aksesuar',
                'genel' => 'Genel',
            ],
        ],
        'zemin-kaplama' => [
            'label' => 'Zemin kaplama',
            'categories' => [
                'parke' => 'Parke',
                'seramik' => 'Seramik ve karo',
                'yapistirici' => 'Yapıştırıcı ve derz dolgu',
                'genel' => 'Genel',
            ],
        ],
        'mantolama-maliyeti' => [
            'label' => 'Mantolama maliyeti',
            'categories' => [
                'yalitim' => 'Isı yalıtım levhası',
                'sistem' => 'Yapıştırma ve sıva harcı, aksesuar',
                'dekoratif' => 'Dekoratif kaplama ve son kat',
                'iskele' => 'İskele',
                'genel' => 'Genel',
            ],
        ],
        'pvc-dograma-hesaplayici' => [
            'label' => 'PVC doğrama',
            'categories' => [
                'profil' => 'PVC profil',
                'cam' => 'Isıcam',
                'panjur' => 'Panjur',
                'montaj' => 'Montaj köpüğü ve sızdırmazlık',
                'genel' => 'Genel',
            ],
        ],
        'sap-hesaplayici' => [
            'label' => 'Şap maliyeti',
            'categories' => [
                'sap' => 'Hazır şap ve düzeltme şapı',
                'katki' => 'Katkı ve astar',
                'genel' => 'Genel',
            ],
        ],
        'kara-siva-hesaplama' => [
            'label' => 'Kara sıva',
            'categories' => [
                'siva' => 'Hazır sıva ve çimento',
                'genel' => 'Genel',
            ],
        ],
        'alci-siva-hesaplama' => [
            'label' => 'Alçı sıva',
            'categories' => [
                'alci' => 'Alçı ve saten',
                'genel' => 'Genel',
            ],
        ],
        'duvar-maliyet-hesaplayici' => [
            'label' => 'Duvar maliyeti',
            'categories' => [
                'blok' => 'Tuğla ve gazbeton',
                'harc' => 'Harç ve yapıştırıcı',
                'genel' => 'Genel',
            ],
        ],
        'duvar-orgu' => [
            'label' => 'Duvar örgü',
            'categories' => [
                'blok' => 'Tuğla ve gazbeton',
                'harc' => 'Harç ve yapıştırıcı',
                'genel' => 'Genel',
            ],
        ],
        'bahce-duvari-maliyeti' => [
            'label' => 'Bahçe duvarı',
            'categories' => [
                'beton' => 'Hazır beton ve demir',
                'korkuluk' => 'Korkuluk ve panel çit',
                'genel' => 'Genel',
            ],
        ],
        'iskele-metraji-maliyeti' => [
            'label' => 'İskele',
            'categories' => [
                'iskele' => 'İskele kiralama',
                'guvenlik' => 'İş güvenliği',
                'genel' => 'Genel',
            ],
        ],
        'kalip-plywood-maliyeti' => [
            'label' => 'Kalıp',
            'categories' => [
                'kalip' => 'Kalıp ve plywood',
                'genel' => 'Genel',
            ],
        ],
        'hafriyat-kazi-hesabi' => [
            'label' => 'Hafriyat',
            'categories' => [
                'makine' => 'İş makinesi',
                'nakliye' => 'Nakliye',
                'genel' => 'Genel',
            ],
        ],
        'saha-betonu-hesaplayici' => [
            'label' => 'Saha betonu',
            'categories' => [
                'beton' => 'Hazır beton',
                'hasir' => 'Çelik hasır',
                'sertlestirici' => 'Yüzey sertleştirici',
                'kur' => 'Kür malzemesi',
                'genel' => 'Genel',
            ],
        ],
        'alcipan-hesaplayici' => [
            'label' => 'Alçıpan',
            'categories' => [
                'alcipan' => 'Alçıpan ve profil',
                'yalitim' => 'Yalıtım',
                'genel' => 'Genel',
            ],
        ],
        'asma-tavan-hesaplayici' => [
            'label' => 'Asma tavan',
            'categories' => [
                'tavan' => 'Tavan sistemleri',
                'aydinlatma' => 'Aydınlatma',
                'genel' => 'Genel',
            ],
        ],
        'su-yalitimi-hesaplayici' => [
            'label' => 'Su yalıtımı',
            'categories' => [
                'yalitim' => 'Su yalıtım malzemesi',
                'cimento' => 'Çimento esaslı ve kristalize yalıtım',
                'likit' => 'Poliürea, poliüretan ve akrilik likit',
                'membran' => 'Bitüm ve membran',
                'bant' => 'Su yalıtım bandı ve tamamlayıcı',
                'genel' => 'Genel',
            ],
        ],
        'beton-onarim-maliyeti' => [
            'label' => 'Beton onarım ve grout',
            'categories' => [
                'tamir' => 'Tamir harcı',
                'grout' => 'Grout harcı',
                'korozyon' => 'Korozyon önleyici ve aderans astarı',
                'enjeksiyon' => 'Enjeksiyon ve koruyucu kaplama',
                'genel' => 'Genel',
            ],
        ],
        'epoksi-zemin-hesaplayici' => [
            'label' => 'Epoksi zemin',
            'categories' => [
                'epoksi' => 'Epoksi ve poliüretan kaplama',
                'astar' => 'Zemin astarı',
                'tesviye' => 'Tesviye harcı',
                'genel' => 'Genel',
            ],
        ],
        'sandvic-panel-hesaplayici' => [
            'label' => 'Sandviç panel',
            'categories' => [
                'panel' => 'Panel',
                'cati' => 'Çatı aksesuarı',
                'genel' => 'Genel',
            ],
        ],
        'radyator-petek-hesaplayici' => [
            'label' => 'Petek hesabı',
            'categories' => [
                'petek' => 'Panel radyatör',
                'kombi' => 'Kombi',
                'genel' => 'Genel',
            ],
        ],
        'gunes-paneli-hesaplama' => [
            'label' => 'Güneş paneli',
            'categories' => [
                'panel' => 'Güneş paneli',
                'inverter' => 'İnverter ve batarya',
                'kurulum' => 'GES kurulum firmaları',
                'genel' => 'Genel',
            ],
        ],
        'deprem-izolator-maliyeti' => [
            'label' => 'Deprem izolatörü',
            'categories' => [
                'izolator' => 'Sismik izolatör',
                'muhendislik' => 'Mühendislik',
                'grout' => 'Grout harcı',
                'genel' => 'Genel',
            ],
        ],
        'tahmini-bina-maliyeti' => [
            'label' => 'Bina maliyeti',
            'categories' => [
                'malzeme' => 'Yapı malzemesi',
                'muteahhit' => 'Müteahhit',
                'genel' => 'Genel',
            ],
        ],
        'statik-proje-bedeli' => [
            'label' => 'Statik proje',
            'categories' => [
                'muhendislik' => 'Mühendislik büroları',
                'yazilim' => 'Yazılım',
                'genel' => 'Genel',
            ],
        ],
        'beton-hesaplayici' => [
            'label' => 'Beton hesaplayıcı',
            'categories' => [
                'beton' => 'Hazır beton',
                'pompa' => 'Pompa',
                'katki' => 'Beton katkısı',
                'kur' => 'Kür malzemesi',
                'genel' => 'Genel',
            ],
        ],
        'su-tesisat-hesaplama' => [
            'label' => 'Su tesisatı',
            'categories' => [
                'boru' => 'Boru ve ek parça',
                'armatur' => 'Armatür',
                'genel' => 'Genel',
            ],
        ],
        'cati-maliyet-hesaplayici' => [
            'label' => 'Çatı maliyeti',
            'categories' => [
                'kiremit' => 'Kiremit ve çatı örtüsü',
                'yalitim' => 'Teras ve çatı su yalıtımı',
                'genel' => 'Genel',
            ],
        ],
        'mutfak-planlayici' => [
            'label' => '2D / 3D mutfak planlayıcı',
            'categories' => [
                'dolap' => 'Mutfak dolabı',
                'tezgah' => 'Tezgah',
                'ankastre' => 'Ankastre ve beyaz eşya',
                'armatur' => 'Eviye ve batarya',
            ],
        ],
        'klima-btu-hesaplayici' => [
            'label' => 'Klima BTU hesaplayıcı',
            'categories' => [
                'klima' => 'Klima ve iklimlendirme',
                'montaj' => 'Montaj ve servis',
                'genel' => 'Genel',
            ],
        ],
        'duvar-rengi-gorsellestirici' => [
            'label' => 'Duvar rengi görselleştirici',
            'categories' => [
                'boya' => 'İç cephe boyası',
                'dekor' => 'Dekoratif kaplama ve duvar kâğıdı',
                'renk' => 'Renklendirme sistemi',
                'genel' => 'Genel',
            ],
        ],
        // v113: 43 bin resmî poz sayfası. Kategori poz tanımından seçilir (poz_matching); kart yalnız eşleşen pozda çıkar.
        // v114: 43 bin resmî poz sayfası; kategoriler yukarıdaki $pozRules'tan (etiketler kartta görünür)
        'poz-sayfalari' => [
            'label' => 'Poz sayfaları (resmî birim fiyat)',
            'categories' => array_map(fn ($r) => $r['label'], $pozRules),
        ],
    ],

    /*
    | Kalem sponsorluğu: sonuç kartındaki kalemin yanında "Sponsorlu · Marka" etiketi.
    | araç → [kalem anahtarı → kategori]  ya da  [kalem → ['field' => giriş alanı, 'map' => [değer önekleri → kategori], 'default' => kategori]]
    */
    'item_map' => [
        'seramik-maliyet-hesaplayici' => ['seramik' => 'seramik', 'seramik_duvar' => 'seramik', 'yapistirici' => 'yapistirici', 'derz_epoksi' => 'derz', 'seviye' => 'seviyeleme', 'temizlik' => 'temizlik', 'yalitim' => 'yalitim'],
        'su-yalitimi-hesaplayici' => ['imalat' => ['field' => 'system', 'map' => ['cement' => 'cimento', 'crystalline' => 'cimento', 'hybrid_polyurea' => 'likit', 'pure_polyurea' => 'likit', 'pu_liquid' => 'likit', 'acrylic_liquid' => 'likit', 'roof_' => 'membran', 'pvc_' => 'membran', 'hdpe_' => 'membran', 'tpo_' => 'membran', 'geosynthetic' => 'membran'], 'default' => 'yalitim']],
        'epoksi-zemin-hesaplayici' => ['epoxy' => 'epoksi', 'uv_topcoat' => 'epoksi', 'leveling' => 'tesviye'],
        'boya-hesaplayici' => ['boya' => ['field' => 'target', 'map' => ['dis_cephe' => 'dis_cephe'], 'default' => 'boya'], 'astar' => 'astar', 'macun' => 'hazirlik', 'tamir' => 'hazirlik'],
        'mantolama-maliyeti' => ['levha' => 'yalitim', 'sistem' => 'sistem', 'sonkat' => 'dekoratif', 'iskele' => 'iskele'],
        'sap-hesaplayici' => ['malzeme' => 'sap', 'katki' => 'katki'],
        'beton-hesaplayici' => ['beton' => 'beton', 'pompa' => 'pompa', 'kur' => 'kur'],
        'saha-betonu-hesaplayici' => ['beton' => 'beton', 'hasir' => 'hasir', 'perdah' => ['field' => 'finish', 'map' => ['helikopter' => 'sertlestirici', 'korund' => 'sertlestirici'], 'default' => ''], 'sertlestirici' => 'sertlestirici', 'kur' => 'kur'],
        'beton-onarim-maliyeti' => ['harc' => 'tamir', 'grout' => 'grout', 'pas' => 'korozyon', 'astar' => 'korozyon', 'enjeksiyon' => 'enjeksiyon', 'kaplama' => 'enjeksiyon'],
        'zemin-kaplama' => [],
        'wc-maliyet-hesabi' => [],
    ],

    /*
    | Pro rapor malzeme listesi: malzeme adındaki ifade → sponsor kategorisi (ilk eşleşen; araçta o kategori yoksa atlanır).
    | Satırda yalnız "Sponsorlu eşdeğer" notu çıkar; miktar ve tutar değişmez.
    */
    'material_map' => [
        'tamir harc' => 'tamir',
        'grout' => 'grout',
        'yapıştırıcı' => 'yapistirici',
        'epoksi derz' => 'derz',
        'derz dolgu' => 'derz',
        'klips' => 'seviyeleme',
        'temizleyici' => 'temizlik',
        'su yalıtım' => 'yalitim',
        'yalıtım bandı' => 'bant',
        'tesviye' => 'tesviye',
        'şap' => 'sap',
        'kür' => 'kur',
        'sertleştirici' => 'sertlestirici',
        'astar' => 'astar',
        'macun' => 'hazirlik',
        'dış cephe boya' => 'dis_cephe',
        'boya' => 'boya',
        'karo' => 'seramik',
    ],

    /*
    | Tematik paketler (Markalar için sayfası + `php artisan sponsors:package`).
    | araç → kategoriler. Fiyatlar aşağıda 'pricing' altında.
    */
    /*
    | v113: poz tanımı → poz sayfası marka kartı kategorisi (küçük harfle aranır).
    | Bir poz birden fazla kategoriye girebilir (ör. seramik + yapıştırıcı); en fazla max_cards kart çıkar.
    */
    // Söküm, kırım, taşıma pozlarında ve laboratuvar deneyi / makine kirası kalemlerinde marka kartı çıkmaz
    'poz_matching_exclude' => '/sökülmesi|söküm|kırılması|yıkılması|taşınması|nakli|kazınması|temizlenmesi|tayini|deneyi|deneyleri|deney numune|analizi|saatlik ücreti|kiralanması|bulunması|ustası|usta yardımcısı|işçisi/u',
    // Bu desenle başlayan poz tanımı parça sayılır ('(40 x 40 cm) …'); grubun son başlığı bağlama eklenir
    'poz_fragment' => '/^[\\(\\d\\-–\\.%a-zçğıöşü"\\\'“]/u',
    'poz_matching' => $pozRules,

    'packages' => [
        'su-yalitimi' => [
            'label' => 'Su Yalıtımı',
            'desc' => 'Teras, banyo, bodrum ve çatı yalıtımı hesaplayan kullanıcılara.',
            'tools' => [
                'su-yalitimi-hesaplayici' => ['yalitim', 'cimento', 'likit', 'membran', 'bant'],
                'banyo-maliyet-hesaplayici' => ['yalitim'],
                'seramik-maliyet-hesaplayici' => ['yalitim'],
                'cati-maliyet-hesaplayici' => ['yalitim'],
                'pvc-dograma-hesaplayici' => ['montaj'],
                'poz-sayfalari' => ['yalitim', 'yalitim_cimento', 'yalitim_poliurea', 'yalitim_likit', 'yalitim_bitum', 'yalitim_membran', 'yalitim_bant', 'mastik'],
            ],
        ],
        'seramik-uygulama' => [
            'label' => 'Seramik Uygulama',
            'desc' => 'Yapıştırıcı, derz dolgu, seviyeleme ve temizlik: kaç torba gerektiği yazan satırın yanında.',
            'tools' => [
                'seramik-maliyet-hesaplayici' => ['yapistirici', 'derz', 'seviyeleme', 'temizlik'],
                'banyo-maliyet-hesaplayici' => ['yapistirici'],
                'wc-maliyet-hesabi' => ['yapistirici'],
                'zemin-kaplama' => ['yapistirici'],
                'poz-sayfalari' => ['yapistirici', 'yapistirici_reaktif', 'derz', 'derz_reaktif'],
            ],
        ],
        'endustriyel-zemin' => [
            'label' => 'Endüstriyel Zemin',
            'desc' => 'Epoksi, sertleştirici, kür, şap ve beton katkısı: fabrika, depo ve otopark zeminleri.',
            'tools' => [
                'epoksi-zemin-hesaplayici' => ['epoksi', 'astar', 'tesviye'],
                'saha-betonu-hesaplayici' => ['sertlestirici', 'kur'],
                'sap-hesaplayici' => ['sap', 'katki'],
                'beton-hesaplayici' => ['katki', 'kur'],
                'poz-sayfalari' => ['sap', 'epoksi_zemin', 'sertlestirici', 'kur', 'beton_katki'],
            ],
        ],
        'boya-cephe' => [
            'label' => 'Boya ve Cephe',
            'desc' => 'İç ve dış cephe boyası, dekoratif boya, renklendirme ve mantolama sistemi.',
            'tools' => [
                'boya-hesaplayici' => ['boya', 'dis_cephe', 'astar', 'hazirlik', 'dekoratif'],
                'duvar-rengi-gorsellestirici' => ['boya', 'dekor', 'renk'],
                'mantolama-maliyeti' => ['sistem', 'dekoratif'],
                'poz-sayfalari' => ['boya_ic', 'boya_dis', 'astar_macun', 'dekoratif', 'mantolama'],
            ],
        ],
        'onarim-grout' => [
            'label' => 'Onarım ve Grout',
            'desc' => 'Tamir harcı, korozyon önleyici, enjeksiyon ve grout: onarım, güçlendirme ve makine montajı.',
            'tools' => [
                'beton-onarim-maliyeti' => ['tamir', 'grout', 'korozyon', 'enjeksiyon'],
                'deprem-izolator-maliyeti' => ['grout'],
                'poz-sayfalari' => ['tamir', 'grout', 'ankraj', 'korozyon', 'enjeksiyon'],
            ],
        ],
        // 'tam-kategori': yukarıdaki beş yapı kimyasalı paketinin hepsi, tek marka (SponsorPackages::get('tam-kategori') birleştirir)

        // Ek paket: Tam Kategori'ye dahil değildir; seramik ve vitrifiye markası için ayrıca eklenir
        'seramik-banyo' => [
            'label' => 'Seramik ve Banyo',
            'addon' => true,
            'desc' => 'Karo, vitrifiye, duşakabin ve armatür: banyo, WC ve seramik hesabında ve 2D / 3D planlayıcılarda.',
            'tools' => [
                'seramik-maliyet-hesaplayici' => ['seramik'],
                'banyo-maliyet-hesaplayici' => ['seramik', 'vitrifiye', 'dus'],
                'wc-maliyet-hesabi' => ['seramik', 'vitrifiye'],
                'zemin-kaplama' => ['seramik'],
                'banyo-planlayici' => ['seramik', 'vitrifiye', 'dus', 'armatur'],
                'wc-planlayici' => ['seramik', 'vitrifiye', 'armatur'],
                'poz-sayfalari' => ['seramik'],
            ],
        ],
    ],

    /*
    | Pilot dönem fiyatları (TL / ay, KDV hariç). Sunum, Markalar için sayfası ve `sponsors:package` aynı rakamı buradan okur.
    | Tek aile paketi kalem başına en çok 3 marka; Tam Kategori kategoride tek marka (münhasır).
    */
    'pricing' => [
        'pilot_months' => 3,
        'monthly' => [
            'su-yalitimi' => 18000,
            'seramik-uygulama' => 18000,
            'endustriyel-zemin' => 18000,
            'boya-cephe' => 18000,
            'onarim-grout' => 18000,
            'tam-kategori' => 55000,
            'seramik-banyo' => 20000,
        ],
        // Kurucu sponsor: Tam Kategori 6 ay, aylık bedel düşük; yenilemede 12 ay aynı bedel
        'founding' => ['package' => 'tam-kategori', 'months' => 6, 'monthly' => 45000],
    ],

    // Aynı anda gösterilecek en fazla sponsor kartı
    'max_cards' => 3,

    // Hiç aktif sponsor yokken "Markanız burada" kartı gösterilsin mi
    'house_ad' => true,

    'utm' => ['utm_source' => 'worksitem', 'utm_medium' => 'sponsor'],
];

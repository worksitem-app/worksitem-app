<?php

/*
|--------------------------------------------------------------------------
| Sponsor (marka) alanları
|--------------------------------------------------------------------------
| Reklam olduğu her kartta "Sponsorlu" etiketiyle açıkça belirtilir
| (Ticari Reklam ve Haksız Ticari Uygulamalar Yönetmeliği: örtülü reklam yasağı).
| Bağlantılar rel="sponsored nofollow" ile verilir (Google politikası).
*/

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
            ],
        ],
        'boya-cephe' => [
            'label' => 'Boya ve Cephe',
            'desc' => 'İç ve dış cephe boyası, dekoratif boya, renklendirme ve mantolama sistemi.',
            'tools' => [
                'boya-hesaplayici' => ['boya', 'dis_cephe', 'astar', 'hazirlik', 'dekoratif'],
                'duvar-rengi-gorsellestirici' => ['boya', 'dekor', 'renk'],
                'mantolama-maliyeti' => ['sistem', 'dekoratif'],
            ],
        ],
        'onarim-grout' => [
            'label' => 'Onarım ve Grout',
            'desc' => 'Tamir harcı, korozyon önleyici, enjeksiyon ve grout: onarım, güçlendirme ve makine montajı.',
            'tools' => [
                'beton-onarim-maliyeti' => ['tamir', 'grout', 'korozyon', 'enjeksiyon'],
                'deprem-izolator-maliyeti' => ['grout'],
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

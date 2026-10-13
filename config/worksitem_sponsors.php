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
                'yalitim' => 'Su yalıtımı ve yapı kimyasalları',
                'genel' => 'Genel (tüm banyo)',
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
                'yapistirici' => 'Yapıştırıcı, derz, yapı kimyasalı',
                'yalitim' => 'Su yalıtımı',
                'genel' => 'Genel',
            ],
        ],
        'boya-hesaplayici' => [
            'label' => 'Boya hesaplayıcı',
            'categories' => [
                'boya' => 'İç cephe boyası',
                'astar' => 'Astar ve macun',
                'ekipman' => 'Rulo, fırça, ekipman',
                'genel' => 'Genel',
            ],
        ],
        'wc-maliyet-hesabi' => [
            'label' => 'WC tadilat maliyeti',
            'categories' => [
                'vitrifiye' => 'Vitrifiye ve armatür',
                'seramik' => 'Seramik',
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
                'parke' => 'Parke / SPC',
                'seramik' => 'Seramik ve mermer',
                'genel' => 'Genel',
            ],
        ],
        'mantolama-maliyeti' => [
            'label' => 'Mantolama maliyeti',
            'categories' => [
                'yalitim' => 'Isı yalıtım levhası',
                'sistem' => 'Harç, sıva, dekoratif sıva',
                'iskele' => 'İskele',
                'genel' => 'Genel',
            ],
        ],
        'pvc-dograma-hesaplayici' => [
            'label' => 'PVC doğrama',
            'categories' => [
                'profil' => 'PVC profil',
                'cam' => 'Isıcam',
                'panjur' => 'Panjur ve sineklik',
                'genel' => 'Genel',
            ],
        ],
        'sap-hesaplayici' => [
            'label' => 'Şap maliyeti',
            'categories' => [
                'sap' => 'Hazır şap ve çimento',
                'katki' => 'Katkı ve yapı kimyasalı',
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
                'hasir' => 'Hasır ve demir',
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
                'genel' => 'Genel',
            ],
        ],
        'epoksi-zemin-hesaplayici' => [
            'label' => 'Epoksi zemin',
            'categories' => [
                'epoksi' => 'Epoksi ve zemin kaplama',
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
                'pompa' => 'Pompa hizmeti',
                'katki' => 'Beton katkısı',
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
                'yalitim' => 'Çatı yalıtımı',
                'genel' => 'Genel',
            ],
        ],
    ],

    // Aynı anda gösterilecek en fazla sponsor kartı
    'max_cards' => 3,

    // Hiç aktif sponsor yokken "Markanız burada" kartı gösterilsin mi
    'house_ad' => true,

    'utm' => ['utm_source' => 'worksitem', 'utm_medium' => 'sponsor'],
];

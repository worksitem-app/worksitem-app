<?php

/*
|--------------------------------------------------------------------------
| 2D / 3D banyo planlayıcı
|--------------------------------------------------------------------------
| Ölçüler santimetre. Her ürün: genişlik (duvar boyunca) × derinlik (duvardan
| içeri) × yükseklik. "sponsor" => null olan ürünler sayfada "Örnek ürün"
| etiketiyle görünür; sponsor firma ürünleri eklenince onlar listelenir
| (2. aşama: admin › Sponsor ürün kataloğu).
| wr / dr: kullanıcının düzenleme penceresinde seçebileceği genişlik / derinlik aralığı (cm).
*/

return [
    'limits' => ['min' => 80, 'max' => 1000, 'h_min' => 200, 'h_max' => 400],

    // Banyo hesabından gelmezse kullanılan ölçüler (cm)
    'defaults' => ['w' => 180, 'l' => 240, 'h' => 260, 'door_w' => 80],
    // WC planlayıcı (/araclar/wc-planlayici) başlangıç ölçüleri
    'wc_defaults' => ['w' => 110, 'l' => 160, 'door_w' => 70],

    // Oda şekilleri: dikdörtgen, dört yönde L (köşesi kesik) ve pahlı köşe
    'shapes' => [
        'rect' => 'Dikdörtgen',
        'ltr' => 'L · sağ üst',
        'ltl' => 'L · sol üst',
        'lbr' => 'L · sağ alt',
        'lbl' => 'L · sol alt',
        'cham' => 'Pahlı köşe',
    ],

    // Parça türleri: yerleşim önceliği, köşe tercihi, varsayılan ürün
    'types' => [
        'kabin' => ['label' => 'Duşakabin', 'short' => 'Duş', 'corner' => true, 'order' => 1],
        'kuvet' => ['label' => 'Küvet', 'short' => 'Küvet', 'corner' => true, 'order' => 1],
        'gomme' => ['label' => 'Gömme duş (cam panel)', 'short' => 'Duş', 'corner' => true, 'order' => 1],
        'lavabo' => ['label' => 'Lavabo', 'short' => 'Lavabo', 'corner' => false, 'order' => 2],
        'klozet' => ['label' => 'Klozet', 'short' => 'Klozet', 'corner' => false, 'order' => 3],
        'havlupan' => ['label' => 'Havlupan', 'short' => 'Havlupan', 'corner' => false, 'order' => 4],
        // Oda elemanları (yapı): yükseklik oda yüksekliği kadar
        'icduvar' => ['label' => 'İç duvar', 'short' => 'İç duvar', 'corner' => false, 'order' => 5, 'element' => true],
        'engel' => ['label' => 'Kolon / şaft', 'short' => 'Kolon', 'corner' => true, 'order' => 0, 'element' => true],
    ],

    // Banyo hesaplayıcısındaki duş seçimi → planlayıcı parça türü
    'shower_map' => ['kabin' => 'kabin', 'kuvet' => 'kuvet', 'gomme' => 'gomme'],

    'products' => [
        'kabin' => [
            ['id' => 'kabin-90', 'name' => 'Kare duşakabin 90×90', 'w' => 90, 'd' => 90, 'h' => 200, 'color' => '#FFFFFF', 'wr' => [70, 120], 'dr' => [70, 120], 'sponsor' => null],
            ['id' => 'kabin-80', 'name' => 'Kare duşakabin 80×80', 'w' => 80, 'd' => 80, 'h' => 200, 'color' => '#FFFFFF', 'wr' => [70, 120], 'dr' => [70, 120], 'sponsor' => null],
            ['id' => 'kabin-120', 'name' => 'Dikdörtgen duşakabin 120×80', 'w' => 120, 'd' => 80, 'h' => 200, 'color' => '#FFFFFF', 'wr' => [90, 160], 'dr' => [70, 100], 'sponsor' => null],
        ],
        'kuvet' => [
            ['id' => 'kuvet-170', 'name' => 'Akrilik küvet 170×70', 'w' => 170, 'd' => 70, 'h' => 55, 'color' => '#FFFFFF', 'wr' => [150, 190], 'dr' => [70, 90], 'sponsor' => null],
            ['id' => 'kuvet-150', 'name' => 'Kısa küvet 150×70', 'w' => 150, 'd' => 70, 'h' => 55, 'color' => '#FFFFFF', 'wr' => [140, 170], 'dr' => [70, 80], 'sponsor' => null],
        ],
        'gomme' => [
            ['id' => 'gomme-120', 'name' => 'Walk-in cam panel 120×90', 'w' => 120, 'd' => 90, 'h' => 200, 'color' => '#FFFFFF', 'wr' => [80, 160], 'dr' => [70, 120], 'sponsor' => null],
            ['id' => 'gomme-100', 'name' => 'Walk-in cam panel 100×80', 'w' => 100, 'd' => 80, 'h' => 200, 'color' => '#FFFFFF', 'wr' => [80, 160], 'dr' => [70, 120], 'sponsor' => null],
        ],
        'lavabo' => [
            ['id' => 'lavabo-60', 'name' => 'Duvara monte lavabo 60 cm', 'w' => 60, 'd' => 45, 'h' => 85, 'color' => '#FFFFFF', 'cabinet' => false, 'wr' => [40, 80], 'dr' => [35, 50], 'sponsor' => null],
            ['id' => 'lavabo-dolap-80', 'name' => 'Dolaplı lavabo 80 cm', 'w' => 80, 'd' => 46, 'h' => 85, 'color' => '#FFFFFF', 'cabinet' => '#B08A5B', 'wr' => [60, 160], 'dr' => [40, 55], 'sponsor' => null],
            ['id' => 'lavabo-dolap-60', 'name' => 'Dolaplı lavabo 60 cm (antrasit)', 'w' => 60, 'd' => 46, 'h' => 85, 'color' => '#FFFFFF', 'cabinet' => '#3A3F47', 'wr' => [50, 100], 'dr' => [40, 55], 'sponsor' => null],
        ],
        'klozet' => [
            ['id' => 'klozet-asma', 'name' => 'Asma klozet (gömme rezervuar)', 'w' => 38, 'd' => 54, 'h' => 40, 'color' => '#FFFFFF', 'tank' => false, 'wr' => [36, 40], 'dr' => [48, 58], 'sponsor' => null],
            ['id' => 'klozet-yer', 'name' => 'Yere oturan klozet (rezervuarlı)', 'w' => 38, 'd' => 65, 'h' => 78, 'color' => '#FFFFFF', 'tank' => true, 'wr' => [36, 40], 'dr' => [60, 70], 'sponsor' => null],
        ],
        'havlupan' => [
            ['id' => 'havlupan-50', 'name' => 'Havlupan 50×80', 'w' => 50, 'd' => 8, 'h' => 80, 'color' => '#D9DDE2', 'wr' => [40, 70], 'dr' => [8, 8], 'sponsor' => null],
            ['id' => 'havlupan-siyah', 'name' => 'Havlupan 50×80 (mat siyah)', 'w' => 50, 'd' => 8, 'h' => 80, 'color' => '#1E2126', 'wr' => [40, 70], 'dr' => [8, 8], 'sponsor' => null],
        ],
    ],

    // Oda elemanları (ürün değil; yükseklik = oda yüksekliği)
    'elements' => [
        'icduvar' => [
            ['id' => 'icduvar', 'name' => 'İç duvar (bölme)', 'w' => 100, 'd' => 10, 'h' => 0, 'wr' => [30, 400], 'dr' => [8, 25], 'color' => '#E6E7EA', 'sponsor' => null],
        ],
        'engel' => [
            ['id' => 'engel', 'name' => 'Kolon / tesisat şaftı', 'w' => 40, 'd' => 30, 'h' => 0, 'wr' => [10, 200], 'dr' => [10, 200], 'color' => '#D5D9DF', 'sponsor' => null],
        ],
    ],

    // Pencere varsayılanları (cm)
    'window' => ['w' => 60, 'sill' => 140, 'h' => 60],

    // Varsayılan seramikler (seramik görselleştirici kataloğundan)
    'tiles' => ['floor' => 'traverten-gri', 'wall' => 'carrara-beyaz'],

    // 3D görünüm kütüphanesi (sabit sürüm)
    'three' => [
        'module' => 'https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js',
        'addons' => 'https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/',
    ],
];

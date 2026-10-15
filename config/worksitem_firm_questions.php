<?php

/*
 * Firma hizmet bilgileri: kayıttan sonra (ve firma panelinde) sorulan kısa sorular.
 * Amaç: firmaya uygun iş göndermek (en küçük iş, bölge, ekip) ve saha fiyatı toplamak (fiyat soruları).
 * Fiyat cevapları Admin › Gelir › Saha fiyat beyanları'nda ortanca ile karşılaştırılır; ortancadan 2 kattan fazla
 * sapan cevap "kontrol et" olarak işaretlenir. Bir firmaya en fazla 'max' soru gösterilir.
 *
 * type: money (TL, birim 'unit'), select (options), multi (options), text
 */
return [
    'max' => 7,

    // Hizmet veren firmalar (usta, müteahhit, tadilat, mimar, mühendis)
    'common' => [
        'min_job' => ['q' => 'En küçük kaç TL\'lik işe gidersiniz?', 'type' => 'money', 'unit' => 'TL (iş başına)', 'hint' => 'Bunun altındaki işler size gönderilmez.'],
        'team' => ['q' => 'Kaç kişilik ekiple çalışıyorsunuz?', 'type' => 'select', 'options' => ['1' => 'Tek başıma', '2-5' => '2–5 kişi', '6-15' => '6–15 kişi', '16+' => '16 kişiden fazla']],
        'start' => ['q' => 'Yeni bir işe genelde ne kadar sürede başlayabilirsiniz?', 'type' => 'select', 'options' => ['hafta' => '1 hafta içinde', '2hafta' => '2 hafta içinde', 'ay' => '1 ay içinde', 'uzun' => '1 aydan uzun']],
        'warranty' => ['q' => 'İşçilik garantisi veriyor musunuz?', 'type' => 'select', 'options' => ['yok' => 'Hayır', '1' => '1 yıl', '2' => '2 yıl ve üzeri']],
    ],

    // Malzeme tedarikçisi / bayi / nalbur / yapı market ve üretici
    'supplier' => [
        'groups' => ['q' => 'Hangi ürün gruplarını satıyorsunuz?', 'type' => 'multi', 'options' => [
            'alcipan' => 'Alçıpan, profil, alçı', 'cimento' => 'Çimento, harç, yapıştırıcı', 'boya' => 'Boya ve yalıtım', 'seramik' => 'Seramik ve karo',
            'vitrifiye' => 'Vitrifiye ve armatür', 'elektrik' => 'Elektrik malzemesi', 'tesisat' => 'Tesisat malzemesi', 'hirdavat' => 'Hırdavat ve nalbur', 'demir' => 'İnşaat demiri', 'kereste' => 'Kereste, OSB, plywood',
        ]],
        'delivery' => ['q' => 'Şantiyeye / eve teslimat yapıyor musunuz?', 'type' => 'select', 'options' => ['ucretsiz' => 'Evet, ücretsiz', 'ucretli' => 'Evet, ücretli', 'yok' => 'Hayır, mağazadan teslim']],
        'brands' => ['q' => 'Satışını yaptığınız başlıca markalar', 'type' => 'text', 'hint' => 'Virgülle ayırın; ör. Knauf, Kalekim, Filli Boya'],
        'p_alcipan' => ['q' => '12,5 mm standart alçıpan levha satış fiyatınız', 'type' => 'money', 'unit' => 'TL / adet', 'price' => true, 'when' => 'alcipan'],
        'p_cimento' => ['q' => '50 kg çimento (CEM II 42,5) satış fiyatınız', 'type' => 'money', 'unit' => 'TL / torba', 'price' => true, 'when' => 'cimento'],
        'p_boya' => ['q' => '15 L iç cephe boya (orta segment) satış fiyatınız', 'type' => 'money', 'unit' => 'TL / kova', 'price' => true, 'when' => 'boya'],
        'p_yapistirici' => ['q' => '25 kg seramik yapıştırıcı satış fiyatınız', 'type' => 'money', 'unit' => 'TL / torba', 'price' => true, 'when' => 'cimento'],
        'p_demir' => ['q' => 'Ø12 inşaat demiri satış fiyatınız', 'type' => 'money', 'unit' => 'TL / ton', 'price' => true, 'when' => 'demir'],
    ],

    // İş koluna göre fiyat soruları (hizmet veren firmalar). Fiyatlar KDV dahil, Worksitem hesaplarıyla karşılaştırılır.
    'trades' => [
        'ince-insaat' => [
            'p_alcipan_bolme' => ['q' => 'Alçıpan bölme duvar (tek kat levha, malzeme + işçilik)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
            'p_boya_iscilik' => ['q' => 'İç cephe boya ustalığı (2 kat, malzeme hariç)', 'type' => 'money', 'unit' => 'TL / m² duvar', 'price' => true],
            'p_banyo' => ['q' => '5 m² banyonun komple yenilenmesi (orta kalite, anahtar teslim)', 'type' => 'money', 'unit' => 'TL / iş', 'price' => true],
        ],
        'zemin-kaplama' => [
            'p_seramik_iscilik' => ['q' => 'Seramik döşeme işçiliği (60×60, malzeme hariç)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
            'p_laminat' => ['q' => 'Laminat parke döşeme işçiliği (malzeme hariç)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
        ],
        'izolasyon' => [
            'p_mantolama' => ['q' => 'Mantolama (5 cm EPS, malzeme + işçilik, iskele hariç)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
            'p_su_yalitimi' => ['q' => 'Teras / ıslak hacim su yalıtımı (malzeme + işçilik)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
        ],
        'aluminyum-pvc' => [
            'p_pvc' => ['q' => 'PVC pencere (çift cam, takılı)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
        ],
        'cati' => [
            'p_kiremit' => ['q' => 'Kiremit çatı yenileme (ahşap konstrüksiyon dahil)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
        ],
        'elektrik' => [
            'p_elektrik_daire' => ['q' => '100 m² dairenin elektrik tesisatı yenileme (anahtar teslim)', 'type' => 'money', 'unit' => 'TL / iş', 'price' => true],
        ],
        'mekanik-tesisat' => [
            'p_kombi' => ['q' => 'Kombi montajı (cihaz hariç)', 'type' => 'money', 'unit' => 'TL / iş', 'price' => true],
            'p_su_tesisat' => ['q' => 'Banyo su tesisatının yenilenmesi (PPRC, malzeme + işçilik)', 'type' => 'money', 'unit' => 'TL / iş', 'price' => true],
        ],
        'ahsap-ve-mobilya' => [
            'p_mutfak' => ['q' => 'Mutfak dolabı (alt + üst, lake / akrilik, montaj dahil)', 'type' => 'money', 'unit' => 'TL / metre', 'price' => true],
        ],
        'kaba-insaat' => [
            'p_duvar' => ['q' => 'Gazbeton / tuğla duvar örme (malzeme + işçilik)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
        ],
        'dis-cephe' => [
            'p_dis_boya' => ['q' => 'Dış cephe boyası (iskele hariç, malzeme + işçilik)', 'type' => 'money', 'unit' => 'TL / m²', 'price' => true],
        ],
    ],
];

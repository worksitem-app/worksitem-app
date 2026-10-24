<?php

/*
| 2026 mevzuat ve varsayım sabitleri (yatırım / mevzuat araçları).
| Kaynakları her değerin yanında. Yıl değişince yalnız bu dosya güncellenir.
*/

return [

    // ÇŞB 2026 yapı yaklaşık birim maliyetleri (TL/m², KDV hariç, %25 genel gider+kâr dahil)
    // Kaynak: RG 03.02.2026 sayı 33157 tebliği
    'yapi_sinif' => [
        '3A' => 19800,
        '3B' => 21050,
        '3C' => 23400,
        '4A' => 26450,
        '4B' => 33900,
        '4C' => 40500,
        '5A' => 42350,
    ],

    // BDDK 29.01.2026 kararı: konut kredisi kredi/değer oranı (ekspertiz değeri dilimi × enerji sınıfı)
    // [üst sınır TL, [A-B, C, D ve altı]]; birinci/ikinci el ayrımı kalktı
    'bddk_ltv' => [
        [5000000, [0.90, 0.80, 0.70]],
        [7000000, [0.80, 0.70, 0.60]],
        [10000000, [0.70, 0.60, 0.50]],
        [20000000, [0.50, 0.40, 0.30]],
        [null, [0.40, 0.30, 0.20]],
    ],
    // Ekim 2026 piyasası aylık %2,87–3,24 (24saatgazetesi 02.10.2026); kullanıcı değiştirebilir
    'konut_faiz_aylik' => 3.00,
    'konut_vade_ay' => 120,

    // 2026 asgari ücret ve SGK (EY 2026 asgari ücret notu; Alomaliye Asgari İşçilik 2026 rehberi)
    'asgari_brut' => 33030.00,
    'asgari_net' => 28075.50,
    'sgk_isci' => 0.15,           // %14 SGK + %1 işsizlik
    'sgk_isveren' => 0.2375,      // %21,75 SGK (MYÖ %12) + %2 işsizlik
    'sgk_tesvik_puan' => 0.02,    // 5510 s.K. imalat dışı (inşaat) 2 puan
    'gv_ilk_dilim' => 0.15,       // 190.000 TL'ye kadar %15 (PwC 2026 tarifesi)
    'damga' => 0.00759,

    // Asgari işçilik: fark işçilik prim oranı %38,75 (işçi %15 + işveren %23,75) — Alomaliye 2026 rehberi
    'asgari_iscilik_prim' => 0.3875,
    // Asgari İşçilik Tespit Komisyonu oranları (bina, %)
    'asgari_iscilik_oran' => [
        'karkas' => 9, 'yigma' => 12, 'yari_prefabrik' => 7, 'tam_prefabrik' => 6, 'celik' => 9,
    ],

    // PAİY m.34 (RG 01.07.2026 sayı 33297): 3 kat → asansör yeri, 4+ kat → asansör zorunlu
    'asansor_yer_kat' => 3,
    'asansor_zorunlu_kat' => 4,

    // Sığınak Yönetmeliği m.7 (RG 07.11.2025 sayı 33070)
    'siginak' => [
        'konut_bb_esik' => 10,
        'emsal_esik' => 1500,
        'resmi_esik' => 1000,
        'sanayi_esik' => 2000,
        'kisi_m2' => 1,
        'oda_kisi' => [1 => 2, 2 => 3, 3 => 4],
        'emsal_kisi_bolen' => 20,
        'sanayi_min_m2' => 20,
    ],

    // Lojistik: piyasa varsayımı (resmî kaynak yok) — kapasite ton, km ücreti TL/km (yakıt+aşınma)
    'lojistik_arac' => [
        'kamyonet' => ['ad' => 'Kamyonet (3,5 t)', 'ton' => 3.5, 'km' => 15],
        'on_teker' => ['ad' => '10 tekerli kamyon (18 t)', 'ton' => 18, 'km' => 30],
        'damper' => ['ad' => 'Damperli kamyon (25 t)', 'ton' => 25, 'km' => 35],
        'tir' => ['ad' => 'TIR (27 t)', 'ton' => 27, 'km' => 40],
    ],

    // Mühendis maaşı: TMMOB 2023'ten beri asgari ücret açıklamıyor; taban piyasa tahmini (varsayım, net)
    'muhendis_taban_net' => 38000,
    'muhendis_yil_artis' => 2500,
];

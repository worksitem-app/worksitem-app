<?php
return [
    'boya' => ['ekonomik_m2' => 150.00, 'standart_m2' => 247.23, 'premium_m2'  => 427.79, 'iscilik_orani' => 0.40, 'litre_sarfiyat' => 7],
    'karo' => ['standart_duvar_m2' => 956.65, 'porselen_zemin_m2' => 1041.36, 'kirim_sokum_m2' => 347.26, 'iscilik_orani' => 0.45, 'kutu_m2' => 1.44],
    'seramik' => ['standart_duvar_m2' => 956.65, 'silli_porselen_m2' => 1041.36, 'kirim_sokum_m2' => 347.26],
    'mutfak' => ['dolap_ekonomik_m' => 6000, 'dolap_standart_m' => 9000, 'dolap_premium_m' => 14000, 'tezgah_laminat_m' => 3500, 'tezgah_granit_m' => 5500, 'tezgah_kuvars_m' => 9500, 'tesisat_evye_montaj' => 4500],
    'banyo' => ['su_yalitimi_ciftkat_m2' => 605.69, 'komple_tesisat_degisimi' => 15000, 'yuzeysel_tesisat' => 5000, 'vitrifiye_montaj_m2' => 2500],
    'bina_maliyeti' => ['sinif_3b_m2' => 16000, 'sinif_4a_m2' => 21050, 'sinif_4b_m2' => 26000],
    'demir' => ['nervurlu_ton' => 51322.40],
    'beton' => ['c20_25_m3' => 3903.64, 'c25_30_m3' => 4046.71, 'c30_37_m3' => 4189.79, 'yuzey_sertlestirici_m2' => 205.31],
    'kalip' => ['ahsap_m2' => 997.21, 'plywood_m2' => 1175.39],
    'hafriyat' => ['derin_kazi_m3' => 92.06],
    'iskele' => ['tam_guvenlikli_m2' => 224.54, 'celik_boru_m3' => 146.20, 'guvenlik_agi_m2' => 44.20],
    'duvar' => ['tugla_13_5_m2' => 908.65, 'tugla_19_m2' => 1033.73, 'gazbeton_10_m2' => 825.20, 'gazbeton_20_m2' => 1260.30],
    'cati' => ['ahsap_iskelet_osb_m2' => 1801.98, 'ahsap_iskelet_tahta_m2' => 1616.59],
    'parke' => ['laminat_ac3_m2' => 696.43, 'laminat_ac4_m2' => 785.24, 'pvc_supurgelik_m' => 87.23],
    'alcipan' => ['tek_iskelet_duvar_m2' => 1200.18, 'cift_iskelet_tavan_m2' => 1403.43],
    'mantolama' => ['eps_5cm_m2' => 1203.59, 'tasyunu_5cm_m2' => 1431.65, 'tasyunu_6cm_m2' => 1493.39],
    'merdiven' => ['beyaz_mermer_m' => 1573.25],
    'pvc_dograma' => ['beyaz_profil_kg' => 361.10, 'renkli_profil_kg' => 405.00, 'standart_cam_m2' => 1971.18, 'isi_kontrol_cam_m2' => 2114.13],
    'enerji' => [
        'ges_kw_maliyeti' => 25000, // 1 kW Güneş Enerjisi kurulumu ort. 25.000 TL
        'jenerator_kva_maliyeti' => 4500, // 1 kVA Jeneratör ort. 4.500 TL
        'elektrik_kwh_fiyati' => 2.25, // Mesken kWh fiyatı
    ],
    'harc' => [
        'cimento_50kg_torba' => 220, // 50kg Portland Çimentosu
        'kum_ton' => 450, // 1 Ton inşaat kumu
        'alci_25kg_torba' => 120, // Perlitli sıva alçısı
        'saten_alci_25kg_torba' => 140, // Saten yüzey alçısı
    ],
    'enerji' => [
        'ges_kw_maliyeti' => 25000, // 1 kW Güneş Enerjisi kurulumu ort. 25.000 TL
        'jenerator_kva_maliyeti' => 4500, // 1 kVA Jeneratör ort. 4.500 TL
        'elektrik_kwh_fiyati' => 2.25, // Mesken kWh fiyatı
    ],
    'harc' => [
        'cimento_50kg_torba' => 220, // 50kg Portland Çimentosu
        'kum_ton' => 450, // 1 Ton inşaat kumu
        'alci_25kg_torba' => 120, // Perlitli sıva alçısı
        'saten_alci_25kg_torba' => 140, // Saten yüzey alçısı
    ],
    'tesisat' => [
        'pprc_boru_m' => 45.50, // Sıcak/soğuk su borusu metresi
        'pvc_boru_m' => 85.00, // Pis su (Pimaş) borusu metresi
        'fitting_adet' => 15.00, // Ortalama dirsek/maşon/T bağlantı
    ],
    'siva' => [
        'kara_siva_m2' => 255.40, // ÇŞB Poz 15.275.1102 (İç/Dış cephe normal sıva yapılması)
    ],
    // v102: hesaplayıcılarda sabit yazılı fiyatlar buraya taşındı; fiyat robotu her hafta günceller (KDV dahil)
    'asansor' => ['paket' => 400000, 'durak' => 35000],            // kabin + makine + pano paketi; durak başına kat kapısı, ray, kablo
    'kablo' => ['siva_alti_m' => 40, 'siva_ustu_m' => 60, 'yeralti_m' => 90, 'bakir_mm2_m' => 15],   // işçilik + kesit (mm²) başına metre
    'is_makinesi' => ['ekskavator_gun' => 8000, 'beko_gun' => 5000, 'mobil_vinc_gun' => 12000, 'kule_vinc_ay' => 250000],
    'akaryakit' => ['motorin_l' => 45],
    'iscilik' => ['operator_gun' => 2500, 'duz_isci_gun' => 1500, 'yemek_yol_gun' => 350],
    'lojistik' => ['sefer_ucreti' => 3500],                         // 25 t kamyon, şehir içi sefer
    'yagmur' => ['boru_70_m' => 242.69, 'boru_100_m' => 315.65, 'boru_125_m' => 366.40],   // PVC iniş borusu, montaj dahil, metre
];
<?php

/*
 * A/B testleri: aynı yerde iki farklı metin; her ziyaretçi bir sürümü görür (çerezle sabit kalır).
 * Görüntülenme ve tıklama Admin › Raporlar › A/B testleri'nde; yeterli veri olunca kazanan "winner" ile sabitlenir
 * (winner yazılınca herkes o sürümü görür ve test durur).
 */
return [
    'tests' => [
        'home_hero_cta' => [
            'label' => 'Anasayfa ana düğme',
            'variants' => ['a' => 'Ön Bütçemi Hesapla →', 'b' => 'Ücretsiz Hesapla: Bütçemi Gör →'],
            'winner' => null,
        ],
        'tool_lead_submit' => [
            'label' => 'Araç sayfası teklif formu düğmesi',
            'variants' => ['a' => 'Teklif talebini gönder', 'b' => 'Uzmandan yazılı teklif iste'],
            'winner' => null,
        ],
        'firm_signup_cta' => [
            'label' => 'Firma kayıt sayfası ana düğme (dönüşüm: tamamlanan kayıt)',
            'variants' => ['a' => 'Ücretsiz kaydolun →', 'b' => 'İşleri bana gönderin →'],
            'winner' => null,
        ],
    ],
];

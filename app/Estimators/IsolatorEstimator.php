<?php

namespace App\Estimators;

class IsolatorEstimator extends KitEstimator
{
    // Kauçuk/kurşun çekirdekli izolatör birim fiyatları (taşıma kapasitesine göre, KDV hariç)
    private const UNIT = ['konut' => 175035, 'agir' => 208199, 'hastane' => 240644];

    public function key(): string { return 'izolator'; }

    public function slug(): string { return 'deprem-izolator-maliyeti'; }

    public function perUnitLabel(): ?string { return 'izolatör başına'; }

    public function contingencyRate(): float { return 0.05; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Deprem izolatörü maliyeti',
            'h1' => "Deprem (sismik) izolatörü maliyeti hesaplama ($y)",
            'lede' => 'Taşıyıcı kolon sayısını ve yapı sınıfını girin; izolatör, montaj, izolasyon katı işleri ve mühendislik kalem kalem hesaplansın. Mevcut binaya uygulamada güçlendirme işleri ayrıca eklenir.',
            'seo_title' => "Deprem İzolatörü Fiyatı $y: Sismik İzolasyon Maliyeti | Worksitem",
            'seo_desc' => "$y deprem izolatörü fiyatları: kolon sayısı ve yapı sınıfına göre sismik izolatör, montaj, izolasyon katı ve mühendislik dahil yaklaşık maliyeti hesaplayın.",
            'service' => 'Sismik İzolasyon',
            'wait' => 'Kolon sayısını girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Sismik izolasyon firmaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Kolon başına', '$ € £ karşılığı'],
            'method' => [
                'İzolatör adedi = zemindeki taşıyıcı kolon (ve perde ucu) sayısı.',
                'İzolatör birim fiyatı taşıma kapasitesi sınıfına göre alınır; KDV %20 eklenir.',
                'Montaj ve izolasyon katı işleri (kriko, ankraj, döşeme) yeni binada izolatör bedelinin %25\'i, mevcut binada %80\'i alınır.',
                'Proje, prototip testi ve denetim izolatör bedelinin %8\'i; %5 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Yeni / mevcut bina', 'Mevcut binaya izolatör eklemek kolon kesme ve kriko işleri nedeniyle yeni binadan çok daha pahalıdır.'],
                ['Yük', 'Yüksek ve ağır yapılarda daha büyük kapasiteli izolatör gerekir.'],
                ['Zemin', 'Zemin etüdü ve yakın fay mesafesi izolatör tipini belirler.'],
                ['Mevzuat', 'Kamu hastaneleri gibi bazı yapılarda deprem bölgesine göre izolasyon zorunludur.'],
            ],
            'faq' => [
                ['Deprem izolatörü tanesi kaç TL?', 'Konut sınıfı (2.500–5.000 kN) bir izolatör KDV dahil yaklaşık '.number_format(self::UNIT['konut'] * 1.2, 0, ',', '.').' TL\'dir; montaj ve mühendislik ayrıca eklenir.'],
                ['40 kolonlu bir binada maliyet ne olur?', 'Yeni yapılan konut sınıfı bir binada montaj ve mühendislik dahil yaklaşık '.$this->exampleRange(['columns' => 40, 'floors' => 6, 'class' => 'konut', 'scope' => 'yeni']).' tutar.'],
                ['Mevcut binaya izolatör yapılabilir mi?', 'Teknik olarak mümkündür ama kolonların kesilip yükün geçici desteklenmesi gerekir; maliyet ve süre belirgin artar.'],
                ['Hesaba neler dahil?', 'İzolatör, montaj, izolasyon katı işleri, proje ve test. Statik güçlendirme ve mimari değişiklikler dahil değildir. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/statik-proje-bedeli', 'Statik proje bedeli'], ['/araclar/kolon-tasima-kapasitesi', 'Kolon taşıma kapasitesi'], ['/araclar/bina-agirlik-hesabi', 'Bina ağırlığı'], ['/araclar/tahmini-bina-maliyeti', 'Bina maliyeti']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'numbers', 'title' => 'Bina', 'cols' => 2, 'fields' => [
                ['key' => 'columns', 'label' => 'Taşıyıcı kolon sayısı', 'unit' => 'adet', 'min' => 4, 'max' => 2000, 'step' => 1, 'placeholder' => 'Örn. 40', 'required' => true],
                ['key' => 'floors', 'label' => 'Kat sayısı', 'unit' => 'kat', 'min' => 1, 'max' => 60, 'step' => 1, 'placeholder' => 'Örn. 6', 'default' => 5],
            ]],
            ['type' => 'cards', 'key' => 'class', 'title' => 'Yük sınıfı', 'short' => 'Yük sınıfı', 'required' => true, 'cols' => 3, 'options' => [
                'konut' => ['label' => 'Standart konut', 'hint' => '2.501–5.000 kN'],
                'agir' => ['label' => 'Ağır / yüksek yapı', 'hint' => '5.001–7.500 kN'],
                'hastane' => ['label' => 'Hastane / büyük yapı', 'hint' => '7.501–10.000 kN'],
            ]],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Uygulama', 'short' => 'Uygulama', 'required' => true, 'cols' => 2, 'options' => [
                'yeni' => ['label' => 'Yeni bina', 'hint' => 'Projede izolasyon katı'],
                'mevcut' => ['label' => 'Mevcut bina', 'hint' => 'Kolon kesme, kriko, güçlendirme'],
            ]],
        ];
    }

    protected function compute(array $in): array
    {
        $n = (int) round((float) $in['columns']);
        $unit = self::UNIT[$in['class']] * 1.2;
        $iso = $n * $unit;
        $items = [
            'izolator' => ['label' => 'Sismik izolatör ('.$n.' adet)', 'mid' => $iso, 'color' => '#FFB800'],
            'montaj' => ['label' => $in['scope'] === 'mevcut' ? 'Kolon kesme, kriko, montaj ve izolasyon katı' : 'Montaj, ankraj ve izolasyon katı işleri', 'mid' => $iso * ($in['scope'] === 'mevcut' ? 0.8 : 0.25), 'color' => '#101318'],
            'muhendislik' => ['label' => 'Proje, prototip testi ve denetim', 'mid' => $iso * 0.08, 'color' => '#2F6F8F'],
        ];
        $days = $in['scope'] === 'mevcut' ? [60, 150] : [15, 40];

        return ['items' => $items, 'per_unit' => $n, 'days' => $days, 'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['İzolatör', $n.' adet']]];
    }

    public function materials(array $in): array
    {
        $n = (int) round((float) $in['columns']);

        return [
            ['Sismik izolatör', $n, 'adet', $this->steps()[1]['options'][$in['class']]['hint']],
            ['Alt / üst bağlantı plakası', $n * 2, 'adet', ''],
            ['Ankraj bulonu', $n * 8, 'adet', 'İzolatör başına ≈8'],
            ['Esnek tesisat bağlantısı', max(4, (int) ceil($n / 5)), 'adet', 'Doğalgaz, su, atık su geçişleri'],
        ];
    }

    public function phases(array $in): array
    {
        return ($in['scope'] ?? '') === 'mevcut'
            ? [['Proje ve izinler', .15], ['Geçici destek ve kolon kesme', .35], ['İzolatör montajı', .3], ['Tesisat ve test', .2]]
            : [['Proje ve prototip test', .3], ['İzolasyon katı ve ankraj', .35], ['İzolatör montajı', .25], ['Kontrol', .1]];
    }
}

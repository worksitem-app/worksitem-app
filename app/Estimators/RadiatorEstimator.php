<?php

namespace App\Estimators;

class RadiatorEstimator extends KitEstimator
{
    private const P = ['h60' => 2600, 'h50' => 2200, 'h90' => 3600, 'mount' => 1200, 'valve' => 450, 'thermo' => 1300, 'piping_m2' => 650, 'boiler' => [36000, 42000, 60000], 'boiler_install' => 9000];

    private const OUT = ['h60' => 1700, 'h50' => 1300, 'h90' => 2400];   // kcal/h per metre (Tip 22, ΔT50)

    public function key(): string { return 'radyator'; }

    public function slug(): string { return 'radyator-petek-hesaplayici'; }

    public function perUnitLabel(): ?string { return 'm² başına'; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Petek (radyatör) maliyeti',
            'h1' => "Petek (radyatör) ihtiyacı ve maliyeti hesaplama ($y)",
            'lede' => 'Alanı ve binanın yalıtım durumunu girin; kaç metre petek gerektiği, petek, vana, montaj ve isterseniz komple tesisat ile kombi maliyeti kalem kalem hesaplansın.',
            'seo_title' => "Petek Hesabı $y: Kaç Metre Petek Gerekir, Radyatör Maliyeti | Worksitem",
            'seo_desc' => "$y petek hesaplama: oda veya ev m²'sine göre ısı ihtiyacını, kaç metre panel radyatör gerektiğini ve petek, vana, montaj, tesisat ve kombi maliyetini ücretsiz hesaplayın.",
            'service' => 'Kalorifer / Petek',
            'wait' => 'Alanı girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Isıtma markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Kaç metre petek', '$ € £ karşılığı'],
            'method' => [
                'Isı ihtiyacı = alan × yükseklik × yalıtıma göre kcal/m³ (iyi 30, standart 40, yalıtımsız 50).',
                'Petek metresi = ısı ihtiyacı ÷ seçilen tipin metre başına ısı çıkışı (Tip 22, ΔT50).',
                'Petek metre fiyatı, petek başına montaj ve vana (normal veya termostatik) eklenir; komple tesisatta m² başına boru/kolektör.',
                'Kombi seçilirse ısı ihtiyacına uygun yoğuşmalı kombi ve montajı eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Yalıtım', 'Mantolamasız eski binada ısı ihtiyacı yalıtımlı binaya göre %60\'a kadar fazladır.'],
                ['Petek yüksekliği', 'Pencere altı yüksekliğe göre 50 veya 60 cm seçilir; 90 cm daha az metre ile aynı ısıyı verir.'],
                ['Termostatik vana', 'Oda bazında sıcaklık kontrolü ile doğalgaz tüketimini %10–15 azaltabilir.'],
                ['Tesisat', 'Eski çelik tesisat yenilenmeden yalnız petek değişimi yapmak tıkanma riskini sürdürür.'],
            ],
            'faq' => [
                ['100 m² eve kaç metre petek gerekir?', 'Standart yalıtımlı, 2,6 m tavanlı 100 m² için yaklaşık 10.400 kcal/h ısı gerekir; 60 cm Tip 22 petekte bu yaklaşık '.number_format(10400 / 1700, 1, ',', '.').' metredir.'],
                ['Petek değişimi kaç TL?', '100 m² standart yalıtımlı evde 60 cm panel petek, vana ve montaj dahil yaklaşık '.$this->exampleRange(['insulation' => 'standart', 'area' => 100, 'type' => 'h60', 'scope' => 'petek']).' tutar.'],
                ['Kombi kaç kW olmalı?', '100–120 m² dairede 24 kW yoğuşmalı kombi genellikle yeterlidir; yalıtım ve sıcak su kullanımı belirleyicidir.'],
                ['Hesaba neler dahil?', 'Petek, vana, montaj; seçerseniz komple tesisat ve kombi. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/klima-btu-hesaplayici', 'Klima BTU'], ['/araclar/mantolama-maliyeti', 'Mantolama'], ['/araclar/enerji-tuketim-hesaplama', 'Enerji tüketimi'], ['/araclar/su-tesisat-hesaplama', 'Su tesisatı']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'insulation', 'title' => 'Binanın yalıtımı', 'short' => 'Yalıtım durumu', 'required' => true, 'cols' => 3, 'options' => [
                'iyi' => ['label' => 'Çok iyi', 'hint' => 'Yeni, mantolamalı'],
                'standart' => ['label' => 'Standart', 'hint' => 'Isıcamlı, kısmi yalıtım'],
                'yalitimsiz' => ['label' => 'Yalıtımsız', 'hint' => 'Eski bina'],
            ]],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 3, 'fields' => [
                ['key' => 'area', 'label' => 'Isıtılacak alan', 'unit' => 'm²', 'min' => 5, 'max' => 5000, 'step' => 1, 'placeholder' => 'Örn. 100', 'required' => true],
                ['key' => 'height', 'label' => 'Tavan yüksekliği', 'unit' => 'm', 'min' => 2.2, 'max' => 6, 'step' => 0.1, 'placeholder' => '2,6', 'default' => 2.6],
                ['key' => 'count', 'label' => 'Petek sayısı', 'unit' => 'adet', 'min' => 1, 'max' => 300, 'step' => 1, 'placeholder' => 'Boş: otomatik'],
            ], 'help' => 'Petek sayısını bilmiyorsanız boş bırakın; her 15 m² için bir petek alınır.'],
            ['type' => 'cards', 'key' => 'type', 'title' => 'Petek tipi', 'short' => 'Petek tipi', 'required' => true, 'cols' => 3, 'options' => [
                'h50' => ['label' => '50 cm panel (Tip 22)', 'hint' => '≈1.300 kcal/m'],
                'h60' => ['label' => '60 cm panel (Tip 22)', 'hint' => '≈1.700 kcal/m'],
                'h90' => ['label' => '90 cm panel (Tip 22)', 'hint' => '≈2.400 kcal/m'],
            ]],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Kapsam', 'short' => 'Kapsam', 'required' => true, 'cols' => 2, 'options' => [
                'petek' => ['label' => 'Sadece petek değişimi', 'hint' => 'Tesisat kalır'],
                'tesisat' => ['label' => 'Komple tesisat + petek', 'hint' => 'Yeni boru ve kolektör'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['thermo' => 'Termostatik vana', 'boiler' => 'Yoğuşmalı kombi', 'flush' => 'Tesisat temizliği (kimyasal)']],
        ];
    }

    private function load(array $in): array
    {
        $kcal = (float) $in['area'] * (float) ($in['height'] ?? 2.6) * ['iyi' => 30, 'standart' => 40, 'yalitimsiz' => 50][$in['insulation']];
        $m = $kcal / self::OUT[$in['type']];
        $n = ! empty($in['_given_count']) ? (int) $in['count'] : max(1, (int) ceil((float) $in['area'] / 15));

        return [$kcal, $m, $n];
    }

    protected function compute(array $in): array
    {
        [$kcal, $m, $n] = $this->load($in);
        $kw = $kcal / 860;
        $bi = $kw <= 28 ? 0 : ($kw <= 35 ? 1 : 2);
        $items = [
            'petek' => ['label' => 'Panel petek ('.self::n1($m).' m)', 'mid' => $m * self::P[$in['type']], 'color' => '#FFB800'],
            'vana' => ['label' => $this->has($in, 'extras', 'thermo') ? 'Termostatik vana takımı' : 'Vana takımı', 'mid' => $n * ($this->has($in, 'extras', 'thermo') ? self::P['thermo'] : self::P['valve']), 'color' => '#8A6200'],
            'montaj' => ['label' => 'Petek montajı', 'mid' => $n * self::P['mount'], 'color' => '#101318'],
            'tesisat' => ['label' => 'Kalorifer tesisatı (boru, kolektör)', 'mid' => $in['scope'] === 'tesisat' ? (float) $in['area'] * self::P['piping_m2'] : 0, 'color' => '#2F6F8F'],
            'kombi' => ['label' => 'Yoğuşmalı kombi + montaj', 'mid' => $this->has($in, 'extras', 'boiler') ? self::P['boiler'][$bi] + self::P['boiler_install'] : 0, 'color' => '#5B6472'],
            'temizlik' => ['label' => 'Tesisat temizliği', 'mid' => $this->has($in, 'extras', 'flush') ? 3500 : 0, 'color' => '#7FB3C8'],
        ];
        $days = $in['scope'] === 'tesisat' ? [3, 6] : [1, 2];

        return ['items' => $items, 'per_unit' => (float) $in['area'], 'days' => $days,
            'meta' => [['Isı ihtiyacı', number_format($kcal, 0, ',', '.').' kcal/h (≈'.self::n1($kw).' kW)'], ['Petek', self::n1($m).' m · '.$n.' adet']]];
    }

    public function materials(array $in): array
    {
        [$kcal, $m, $n] = $this->load($in);

        return [
            ['Panel petek ('.$this->steps()[2]['options'][$in['type']]['label'].')', self::n1($m), 'm', $n.' parçaya bölünür (≈'.self::n1($m / $n).' m/adet)'],
            [$this->has($in, 'extras', 'thermo') ? 'Termostatik vana + rakor' : 'Radyatör vanası + rakor', $n, 'takım', ''],
            ['Petek askı / konsol', $n * 2, 'adet', ''],
            ['Purjör', $n, 'adet', ''],
        ];
    }

    public function phases(array $in): array
    {
        return ($in['scope'] ?? '') === 'tesisat'
            ? [['Eski tesisat sökümü', .2], ['Boru ve kolektör', .45], ['Petek montajı', .2], ['Basınç testi ve devreye alma', .15]]
            : [['Sistemin boşaltılması', .2], ['Petek değişimi', .6], ['Doldurma ve hava alma', .2]];
    }
}

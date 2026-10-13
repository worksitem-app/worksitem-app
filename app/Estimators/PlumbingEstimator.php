<?php

namespace App\Estimators;

class PlumbingEstimator extends KitEstimator
{
    public function key(): string { return 'su_tesisati'; }

    public function slug(): string { return 'su-tesisat-hesaplama'; }

    public function perUnitLabel(): ?string { return 'ev m² başına'; }

    private function p(): array { return (array) config('worksitem_estimators.tesisat'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Su tesisatı maliyeti',
            'h1' => "Su tesisatı yenileme maliyeti hesaplama ($y)",
            'lede' => 'Evin büyüklüğünü, banyo ve mutfak sayısını girin, kapsamı ve boru kalitesini seçin; temiz su, pis su, ana hat, kanal açma, tamir ve ekstralar kalem kalem hesaplansın.',
            'seo_title' => "Su Tesisatı Fiyatı $y: Ev Tesisatı Yenileme Maliyeti | Worksitem",
            'seo_desc' => "$y su tesisatı yenileme fiyatı: daire büyüklüğü, banyo ve mutfak sayısına göre temiz su, pis su, kanal açma, tamir ve kollektör dahil maliyeti ücretsiz hesaplayın.",
            'service' => 'Su Tesisatı',
            'wait' => 'Ev büyüklüğünü ve ıslak hacim sayısını girin, seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Tesisat malzemesi markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Banyo / mutfak bazlı', '$ € £ karşılığı'],
            'method' => [
                'Her banyo/WC ve mutfak için temiz + pis su tesisatı kaliteye göre sabit kalem olarak alınır.',
                'Ana hat (sayaçtan ıslak hacimlere) ev m² başına eklenir.',
                'Komple yenilemede ıslak hacim başına kanal açma ve kapatma/tamir eklenir; yeni yapıda tamir yoktur.',
                'Kollektör, hidrofor-depo ve kombi bağlantısı seçilirse eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Kapsam', 'Duvar kırılarak yenilenen tesisat, mevcut kanaldan boru değişiminden belirgin pahalıdır.'],
                ['Boru', 'Kompozit / çok katmanlı borular ve sessiz pis su boruları maliyeti artırır ama ömrü ve konforu yükseltir.'],
                ['Islak hacim', 'Her ek banyo ve WC hem boru hem işçilik maliyetini artırır.'],
                ['Tamir', 'Kanal kapatma sonrası seramik ve sıva tamiri çoğu zaman tesisattan ayrı fiyatlandırılır.'],
            ],
            'faq' => [
                ['100 m² dairenin su tesisatı kaç TL?', '1 banyo, 1 WC ve 1 mutfaklı 100 m² bir dairede orta kalitede komple yenileme '.$y.' fiyatlarıyla yaklaşık '.$this->exampleRange(['area' => 100, 'baths' => 2, 'kitchens' => 1, 'scope' => 'komple', 'quality' => 'orta']).' tutar.'],
                ['Su tesisatı kaç günde yenilenir?', 'Bir dairede kırım, döşeme ve basınç testi dahil genellikle 3–6 iş günü sürer; tamir ve seramik ayrıca zaman alır.'],
                ['Basınç testi neden yapılır?', 'Tesisat kapatılmadan önce sızıntı olmadığını görmek için hat en az 24 saat basınç altında bekletilir.'],
                ['Hesaba neler dahil?', 'Boru, ek parça, işçilik ve (komple yenilemede) kanal açma-kapatma; seçerseniz kollektör, hidrofor ve kombi bağlantısı. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/banyo-maliyet-hesaplayici', 'Banyo tadilat maliyeti'], ['/araclar/mutfak-maliyet-hesaplayici', 'Mutfak tadilat maliyeti'], ['/araclar/radyator-petek-hesaplayici', 'Petek hesabı'], ['/araclar/pompa-gucu-hesabi', 'Pompa gücü']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'numbers', 'title' => 'Ev', 'cols' => 3, 'fields' => [
                ['key' => 'area', 'label' => 'Ev büyüklüğü (net)', 'unit' => 'm²', 'min' => 20, 'max' => 2000, 'step' => 5, 'placeholder' => 'Örn. 100', 'required' => true],
                ['key' => 'baths', 'label' => 'Banyo + WC sayısı', 'unit' => 'adet', 'min' => 0, 'max' => 20, 'step' => 1, 'placeholder' => 'Örn. 2', 'required' => true],
                ['key' => 'kitchens', 'label' => 'Mutfak sayısı', 'unit' => 'adet', 'min' => 0, 'max' => 10, 'step' => 1, 'placeholder' => '1', 'default' => 1],
            ]],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Kapsam', 'short' => 'Kapsam', 'required' => true, 'cols' => 3, 'options' => [
                'komple' => ['label' => 'Komple yenileme', 'hint' => 'Duvar kırılır, yeni hat'],
                'degisim' => ['label' => 'Boru değişimi', 'hint' => 'Mevcut kanaldan, az kırım'],
                'yeni' => ['label' => 'Yeni yapı', 'hint' => 'Kaba tesisat, tamir yok'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Boru ve malzeme', 'short' => 'Malzeme kalitesi', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'PPRC + PVC'],
                'orta' => ['label' => 'Orta', 'hint' => 'Kompozit PPRC + sessiz pis su'],
                'premium' => ['label' => 'Premium', 'hint' => 'Çok katmanlı + kollektörlü'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['collector' => 'Kollektör (dağıtıcı) sistemi', 'booster' => 'Su deposu + hidrofor', 'boiler' => 'Kombi bağlantısı', 'meter' => 'Sayaç ve ana vana yenileme']],
        ];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $a = (float) $in['area'];
        $b = (int) round((float) $in['baths']);
        $k = (int) round((float) ($in['kitchens'] ?? 1));
        $points = $b + $k;
        $scope = $in['scope'];
        $f = $scope === 'degisim' ? 0.85 : 1.0;
        $ex = 0;
        foreach (['collector', 'booster', 'boiler', 'meter'] as $x) {
            $ex += $this->has($in, 'extras', $x) ? $p[$x] : 0;
        }
        $items = [
            'kanal' => ['label' => 'Kanal açma ve moloz', 'mid' => $scope === 'komple' ? $points * $p['chase_point'] * 2 : ($scope === 'degisim' ? $points * $p['chase_point'] * 0.5 : 0), 'color' => '#5B6472'],
            'banyo' => ['label' => 'Banyo / WC tesisatı ('.$b.' adet)', 'mid' => $b * $this->q($in, $p['bath']) * $f, 'color' => '#2F6F8F'],
            'mutfak' => ['label' => 'Mutfak tesisatı ('.$k.' adet)', 'mid' => $k * $this->q($in, $p['kitchen']) * $f, 'color' => '#FFB800'],
            'ana' => ['label' => 'Ana hat (sayaçtan ıslak hacimlere)', 'mid' => $a * $this->q($in, $p['main_m2']), 'color' => '#101318'],
            'tamir' => ['label' => 'Kanal kapatma, sıva / seramik tamiri', 'mid' => $scope === 'yeni' ? 0 : $points * $p['repair_point'] * ($scope === 'komple' ? 2 : 1), 'color' => '#C9A55A'],
            'ekstra' => ['label' => 'Seçilen ekstralar', 'mid' => $ex, 'color' => '#3F7D5B'],
        ];
        $days = [max(2, $points + 1), max(3, $points * 2 + 1)];

        return ['items' => $items, 'per_unit' => $a, 'days' => $days, 'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Islak hacim', $points.' nokta']]];
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];
        $b = (int) round((float) $in['baths']);
        $k = (int) round((float) ($in['kitchens'] ?? 1));

        return [
            ['Temiz su borusu (Ø20–25)', (int) ceil($a * 0.6 + ($b + $k) * 12), 'm', 'Sıcak + soğuk, yaklaşık'],
            ['Pis su borusu (Ø50 / Ø100)', (int) ceil(($b * 8) + ($k * 5)), 'm', 'Yaklaşık'],
            ['Ek parça (dirsek, te, nipel)', (int) ceil(($b + $k) * 25), 'adet', 'Yaklaşık'],
            ['Ankastre ara musluk', ($b * 3) + ($k * 2), 'adet', 'Islak hacim başına'],
            ['Yer süzgeci', $b, 'adet', 'Banyo başına'],
        ];
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([
            ($in['scope'] ?? '') !== 'yeni' ? ['Söküm ve kanal açma', .25] : null,
            ['Boru döşeme', .4], ['Basınç testi', .15],
            ($in['scope'] ?? '') !== 'yeni' ? ['Kanal kapatma ve tamir', .2] : null,
        ]));
    }
}

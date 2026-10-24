<?php

namespace App\Estimators;

/** Duvar örme maliyeti (tuğla, gazbeton, bims) + isteğe bağlı sıva */
class WallEstimator extends KitEstimator
{
    public function key(): string { return 'duvar'; }

    public function slug(): string { return 'duvar-maliyet-hesaplayici'; }

    public function perUnitLabel(): ?string { return 'duvar m² başına'; }

    protected function p(): array { return (array) config('worksitem_estimators.duvar'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Duvar maliyeti',
            'h1' => "Duvar örme maliyeti hesaplama ($y)",
            'lede' => 'Duvarın uzunluğunu ve yüksekliğini girin, tuğla, gazbeton veya bims seçin; malzeme, harç, işçilik, lento ve sıva kalem kalem hesaplansın. Kaç adet tuğla veya blok gerektiği Pro raporda.',
            'seo_title' => "Duvar Örme Maliyeti $y: Tuğla, Gazbeton m² Fiyatı | Worksitem",
            'seo_desc' => "$y duvar örme fiyatları: tuğla, gazbeton ve bims duvarın m² maliyetini harç, işçilik, lento ve sıva dahil, kaç adet blok gerektiğiyle ücretsiz hesaplayın.",
            'service' => 'Duvar Örme',
            'wait' => 'Duvar ölçülerini girin ve malzemeyi seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Tuğla ve gazbeton markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Adet ve harç hesabı', '$ € £ karşılığı'],
            'method' => [
                'Net duvar alanı = uzunluk × yükseklik − kapı ve pencere boşlukları.',
                'Blok adedi m² başına adet × %5 fire; harç (tuğla, bims) veya yapıştırıcı (gazbeton) m² başına eklenir.',
                'İşçilik malzemeye göre m² başına; kapı/pencere sayısı kadar lento eklenir.',
                'Sıva seçilirse kaba + ince sıva (tek veya çift yüz) eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Malzeme', 'Gazbeton ısı yalıtımı ve hız sağlar; tuğlaya göre m² maliyeti yaklaşık %30–60 yüksektir.'],
                ['Kalınlık', 'Dış duvarlarda 19–20 cm, iç bölmelerde 10–13,5 cm yaygındır.'],
                ['Sıva', 'Çift yüz sıva duvar maliyetini yaklaşık iki katına çıkarabilir.'],
                ['Boşluklar', 'Her kapı ve pencere için lento gerekir; boşluklar düşülse de işçilik artar.'],
            ],
            'faq' => [
                ['1 m² duvar örmek kaç TL?', '13,5 cm yatay delikli tuğla ile malzeme, harç ve işçilik dahil '.now()->year.' fiyatlarıyla m² başına yaklaşık '.$this->exampleRange(['material' => 'tugla135', 'length' => 10, 'height' => 2.8, 'plaster' => 'yok'], true).' tutar (sıva hariç).'],
                ['1 m² duvar için kaç tuğla gerekir?', '13,5 ve 19 cm yatay delikli tuğlada yaklaşık 25 adet, 60×25 gazbetonda yaklaşık 6,7 adet, 39×19×19 bims blokta 12,5 adet gerekir; %5 fire eklenmelidir.'],
                ['Gazbeton mu, tuğla mı?', 'Gazbeton hafif ve yalıtımlıdır, hızlı örülür; tuğla daha ekonomiktir. Islak hacimde ve dış duvarda üretici önerilerine uyulmalıdır.'],
                ['Hesaba neler dahil?', 'Blok, harç/yapıştırıcı, işçilik ve lento; seçerseniz sıva. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/duvar-orgu', 'Duvar örgü (adet) hesabı'], ['/araclar/kara-siva-hesaplama', 'Kara sıva'], ['/araclar/alci-siva-hesaplama', 'Alçı sıva'], ['/araclar/bahce-duvari-maliyeti', 'Bahçe duvarı']],
        ];
    }

    public function steps(): array
    {
        $mat = [];
        foreach ($this->p()['materials'] as $k => $m) {
            $mat[$k] = ['label' => $m[0], 'hint' => '≈ '.str_replace('.', ',', (string) $m[1]).' adet/m²'];
        }

        return [
            ['type' => 'cards', 'key' => 'material', 'title' => 'Duvar malzemesi', 'short' => 'Malzeme', 'required' => true, 'cols' => 3, 'options' => $mat],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 3, 'fields' => [
                ['key' => 'length', 'label' => 'Toplam uzunluk', 'unit' => 'm', 'min' => 0.5, 'max' => 5000, 'step' => 0.1, 'placeholder' => 'Örn. 12', 'required' => true],
                ['key' => 'height', 'label' => 'Yükseklik', 'unit' => 'm', 'min' => 0.5, 'max' => 10, 'step' => 0.1, 'placeholder' => 'Örn. 2,8', 'required' => true],
                ['key' => 'openings', 'label' => 'Kapı + pencere sayısı', 'unit' => 'adet', 'min' => 0, 'max' => 500, 'step' => 1, 'placeholder' => '0', 'default' => 0],
            ], 'help' => 'Her kapı/pencere için ortalama 2 m² düşülür ve bir lento eklenir.'],
            ['type' => 'cards', 'key' => 'plaster', 'title' => 'Sıva', 'short' => 'Sıva', 'required' => true, 'cols' => 3, 'options' => [
                'yok' => ['label' => 'Sıva yok', 'hint' => 'Yalnız örgü'],
                'tek' => ['label' => 'Tek yüz sıva', 'hint' => 'Kaba + ince'],
                'cift' => ['label' => 'Çift yüz sıva', 'hint' => 'İki taraf'],
            ]],
        ];
    }

    protected function net(array $in): float
    {
        return max(0.5, (float) $in['length'] * (float) $in['height'] - (int) ($in['openings'] ?? 0) * 2);
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        [$label, $per, $unit, $mortar, $labor] = $p['materials'][$in['material']];
        $a = $this->net($in);
        $faces = ['yok' => 0, 'tek' => 1, 'cift' => 2][$in['plaster']];
        $items = [
            'blok' => ['label' => $label.' (%5 fire)', 'mid' => $a * $per * 1.05 * $unit, 'color' => '#FFB800'],
            'harc' => ['label' => str_starts_with($in['material'], 'gazbeton') ? 'Gazbeton yapıştırıcısı' : 'Örgü harcı', 'mid' => $a * $mortar, 'color' => '#8A6200'],
            'iscilik' => ['label' => 'Örgü işçiliği', 'mid' => $a * $labor, 'color' => '#101318'],
            'lento' => ['label' => 'Lento', 'mid' => (int) ($in['openings'] ?? 0) * $p['lintel_m'] * 1.6, 'color' => '#5B6472'],
            'siva' => ['label' => $faces === 2 ? 'Sıva (çift yüz)' : 'Sıva (tek yüz)', 'mid' => $a * $faces * $p['plaster_m2'], 'color' => '#C9A55A'],
        ];
        $days = [max(1, (int) ceil($a / 25)), max(1, (int) ceil($a / 14))];
        if ($faces) {
            $days = [$days[0] + max(1, (int) ceil($a * $faces / 60)), $days[1] + max(1, (int) ceil($a * $faces / 35))];
        }

        return ['items' => $items, 'per_unit' => $a, 'days' => $days, 'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Net duvar alanı', self::n1($a).' m²']]];
    }

    public function materials(array $in): array
    {
        $p = $this->p();
        [$label, $per] = $p['materials'][$in['material']];
        $a = $this->net($in);
        $rows = [[$label, (int) ceil($a * $per * 1.05), 'adet', self::n1($a).' m² × '.str_replace('.', ',', (string) $per).' adet + %5 fire']];
        if (str_starts_with($in['material'], 'gazbeton')) {
            $rows[] = ['Gazbeton yapıştırıcısı (25 kg)', (int) ceil($a * 5 / 25), 'torba', '≈5 kg/m²'];
        } else {
            $rows[] = ['Çimento (50 kg)', (int) ceil($a * 0.03 * 250 / 50), 'torba', 'Harç ≈0,03 m³/m²'];
            $rows[] = ['Kum', self::n1($a * 0.03 * 1.15), 'm³', ''];
        }
        if ((int) ($in['openings'] ?? 0) > 0) {
            $rows[] = ['Hazır lento (≈1,6 m)', (int) $in['openings'], 'adet', 'Kapı ve pencere başına'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([
            ['Aplikasyon ve ilk sıra', .1], ['Duvar örgüsü', .5], ['Lento ve hatıl', .1],
            ($in['plaster'] ?? 'yok') !== 'yok' ? ['Sıva', .25] : null, ['Temizlik', .05],
        ]));
    }
}

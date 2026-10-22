<?php

namespace App\Estimators;

class GypsumPlasterEstimator extends KitEstimator
{
    public function key(): string { return 'alci_siva'; }

    public function slug(): string { return 'alci-siva-hesaplama'; }

    public function perUnitLabel(): ?string { return 'm² başına'; }

    private function p(): array { return (array) config('worksitem_estimators.alci'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Alçı sıva maliyeti',
            'h1' => "Alçı sıva maliyeti hesaplama ($y)",
            'lede' => 'Duvar ve tavan alanını girin, perlitli, saten veya makine alçısını seçin; alçı malzemesi, işçilik, köşe profili ve file kalem kalem hesaplansın. Kaç torba alçı gerektiği Pro raporda.',
            'seo_title' => "Alçı Sıva Fiyatı $y: m² Saten ve Perlitli Alçı Maliyeti | Worksitem",
            'seo_desc' => "$y alçı sıva fiyatları: perlitli alçı, saten alçı ve makine alçısının m² maliyetini işçilik, köşe profili ve file dahil, kaç torba gerektiğiyle ücretsiz hesaplayın.",
            'service' => 'Alçı Sıva',
            'wait' => 'Alanı girin ve alçı türünü seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Alçı ve yapı kimyasalı markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Torba hesabı', '$ € £ karşılığı'],
            'method' => [
                'Perlitli alçı yaklaşık 10 kg/m² (1 cm), makine alçısı 10 kg/m², saten alçı 2 kat için 1,5 kg/m² alınır.',
                '"Perlitli + saten" seçeneğinde iki kat malzeme ve işçilik toplanır.',
                'Köşe profili ve file seçilirse eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Yüzey düzgünlüğü', 'Kaba sıvası bozuk duvarlarda perlitli alçı kalınlaşır, malzeme artar.'],
                ['Saten kat', 'Boyaya hazır pürüzsüz yüzey için perlitli alçının üzerine saten alçı gerekir.'],
                ['Makine alçısı', 'Tek katta hem dolgu hem perdah yapar; büyük alanlarda hızlıdır.'],
                ['Tavan', 'Tavanda uygulama daha yorucu ve yavaştır; işçilik süresini uzatır.'],
            ],
            'faq' => [
                ['100 m² alçı sıva kaç TL?', 'Perlitli + saten alçı, malzeme ve işçilik dahil '.now()->year.' fiyatlarıyla yaklaşık '.$this->exampleRange(['type' => 'perlit_saten', 'wall' => 100]).' tutar.'],
                ['1 m² için kaç kg alçı gider?', '1 cm perlitli alçı yaklaşık 10 kg/m², saten alçı 2 kat yaklaşık 1,5 kg/m² gider.'],
                ['Alçı sıva ne zaman boyanır?', 'Alçı tamamen kuruduktan sonra (genellikle 5–7 gün) astar ve boya yapılır.'],
                ['Hesaba neler dahil?', 'Alçı malzemesi ve işçilik; seçerseniz köşe profili ve file. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/kara-siva-hesaplama', 'Kara sıva'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı'], ['/araclar/alcipan-hesaplayici', 'Alçıpan'], ['/araclar/duvar-maliyet-hesaplayici', 'Duvar maliyeti']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'type', 'title' => 'Alçı türü', 'short' => 'Alçı türü', 'required' => true, 'cols' => 2, 'options' => [
                'perlit_saten' => ['label' => 'Perlitli + saten', 'hint' => 'En yaygın: dolgu + boya altı perdah'],
                'perlit' => ['label' => 'Yalnız perlitli', 'hint' => 'Kaba dolgu, ~1 cm'],
                'saten' => ['label' => 'Yalnız saten', 'hint' => 'Boya altı ince perdah'],
                'makine' => ['label' => 'Makine alçısı', 'hint' => 'Tek katta dolgu + perdah'],
            ]],
            ['type' => 'numbers', 'title' => 'Alan', 'cols' => 3, 'fields' => [
                ['key' => 'wall_en', 'label' => 'Duvar uzunluğu (toplam)', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 4'],
                ['key' => 'wall_boy', 'label' => 'Yükseklik', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 3'],
                ['key' => 'wall', 'label' => 'Duvar alanı (biliyorsanız)', 'from' => ['wall_en', 'wall_boy'], 'unit' => 'm²', 'min' => 1, 'max' => 50000, 'step' => 1, 'placeholder' => 'Örn. 120', 'required' => true, 'missing' => 'duvar alanı (en × boy ya da m²)'],
                ['key' => 'ceiling_en', 'label' => 'Tavan eni', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 4'],
                ['key' => 'ceiling_boy', 'label' => 'Tavan boyu', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 3'],
                ['key' => 'ceiling', 'label' => 'Tavan alanı (biliyorsanız)', 'from' => ['ceiling_en', 'ceiling_boy'], 'unit' => 'm²', 'min' => 1, 'max' => 20000, 'step' => 1, 'placeholder' => 'Boş bırakın'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['corner' => 'Köşe profili', 'mesh' => 'Alçı filesi (çatlak riski)']],
        ];
    }

    private function area(array $in): array
    {
        $wall = (float) $in['wall'];
        $ceil = ! empty($in['_given_ceiling']) ? (float) $in['ceiling'] : 0.0;

        return [$wall, $ceil, $wall + $ceil];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        [$wall, $ceil, $a] = $this->area($in);
        $t = $in['type'];
        $eff = $wall + $ceil * 1.2;   // tavan işçiliği daha yavaş
        $items = [];
        if (in_array($t, ['perlit', 'perlit_saten'], true)) {
            $items['perlit'] = ['label' => 'Perlitli alçı', 'mid' => $a * 10 * $p['perlit_kg'], 'color' => '#FFB800'];
            $items['perlit_is'] = ['label' => 'Perlitli alçı işçiliği', 'mid' => $eff * $p['labor_m2']['perlit'], 'color' => '#101318'];
        }
        if (in_array($t, ['saten', 'perlit_saten'], true)) {
            $items['saten'] = ['label' => 'Saten alçı', 'mid' => $a * 1.5 * $p['saten_kg'], 'color' => '#C9A55A'];
            $items['saten_is'] = ['label' => 'Saten alçı işçiliği', 'mid' => $eff * $p['labor_m2']['saten'], 'color' => '#5B6472'];
        }
        if ($t === 'makine') {
            $items['makine'] = ['label' => 'Makine alçısı', 'mid' => $a * 10 * $p['makine_kg'], 'color' => '#FFB800'];
            $items['makine_is'] = ['label' => 'Makine alçısı işçiliği', 'mid' => $eff * $p['labor_m2']['makine'], 'color' => '#101318'];
        }
        $items['profil'] = ['label' => 'Köşe profili', 'mid' => $this->has($in, 'extras', 'corner') ? $wall / 6 * $p['corner_m'] * 2.5 : 0, 'color' => '#8A6200'];
        $items['file'] = ['label' => 'Alçı filesi', 'mid' => $this->has($in, 'extras', 'mesh') ? $a * $p['mesh_m2'] : 0, 'color' => '#7FB3C8'];
        $rate = $t === 'makine' ? 90 : ($t === 'perlit_saten' ? 35 : 55);
        $days = [max(1, (int) ceil($a / ($rate * 1.4))), max(1, (int) ceil($a / $rate))];

        return ['items' => $items, 'per_unit' => $a, 'days' => $days, 'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Toplam alan', self::n1($a).' m²']]];
    }

    public function materials(array $in): array
    {
        [$wall, , $a] = $this->area($in);
        $t = $in['type'];
        $rows = [];
        if (in_array($t, ['perlit', 'perlit_saten'], true)) {
            $rows[] = ['Perlitli sıva alçısı (35 kg)', (int) ceil($a * 10 / 35), 'torba', '≈10 kg/m² (1 cm)'];
        }
        if (in_array($t, ['saten', 'perlit_saten'], true)) {
            $rows[] = ['Saten alçı (25 kg)', (int) ceil($a * 1.5 / 25), 'torba', '≈1,5 kg/m² (2 kat)'];
        }
        if ($t === 'makine') {
            $rows[] = ['Makine alçısı (30 kg)', (int) ceil($a * 10 / 30), 'torba', '≈10 kg/m²'];
        }
        if ($this->has($in, 'extras', 'corner')) {
            $rows[] = ['Köşe profili (2,5 m)', (int) ceil($wall / 6), 'adet', 'Yaklaşık'];
        }
        if ($this->has($in, 'extras', 'mesh')) {
            $rows[] = ['Alçı filesi (50 m²)', (int) ceil($a * 1.1 / 50), 'rulo', '%10 bindirme'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return in_array($in['type'] ?? '', ['perlit_saten'], true)
            ? [['Yüzey hazırlığı ve köşe profili', .15], ['Perlitli alçı', .45], ['Kuruma', .1], ['Saten alçı ve zımpara', .3]]
            : [['Yüzey hazırlığı', .2], ['Alçı uygulaması', .65], ['Zımpara ve temizlik', .15]];
    }
}

<?php

namespace App\Estimators;

class ScreedEstimator extends KitEstimator
{
    private const TYPES = [
        'klasik' => ['label' => 'Klasik tesviye şapı', 'hint' => 'Çimento-kum, mastarla çekme'],
        'helikopter' => ['label' => 'Helikopter perdahlı', 'hint' => 'Endüstriyel, düzgün yüzey'],
        'tesviye' => ['label' => 'Hazır tesviye şapı', 'hint' => 'Kendiliğinden yayılan, 3–10 mm'],
        'yuzer' => ['label' => 'Yüzer şap', 'hint' => 'Isı / ses yalıtımlı'],
    ];

    public function key(): string { return 'sap'; }

    public function slug(): string { return 'sap-hesaplayici'; }

    public function perUnitLabel(): ?string { return 'm² başına'; }

    private function p(): array { return (array) config('worksitem_estimators.sap'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Şap maliyeti',
            'h1' => "Şap maliyeti ve malzeme hesaplama ($y)",
            'lede' => 'Alanı ve kalınlığı girin, şap türünü seçin; çimento, kum veya hazır şap malzemesi, işçilik ve katkılar kalem kalem hesaplansın. Kaç torba çimento, kaç m³ kum gerektiği Pro raporda.',
            'seo_title' => "Şap Fiyatı $y: m² Şap Maliyeti, Çimento ve Kum Hesabı | Worksitem",
            'seo_desc' => "$y şap fiyatları: klasik, helikopter perdahlı, hazır tesviye ve yüzer şapın m² maliyetini çimento, kum, işçilik ve katkı dahil ücretsiz hesaplayın.",
            'service' => 'Şap Uygulaması',
            'wait' => 'Alanı ve kalınlığı girin, şap türünü seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Çimento ve yapı kimyasalı markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Çimento ve kum miktarı', '$ € £ karşılığı'],
            'method' => [
                'Şap hacmi = alan × kalınlık. Klasik, helikopter ve yüzer şapta harç m³ fiyatı (çimento + kum, 1:4) uygulanır.',
                'Hazır tesviye şapında malzeme kg/m²/mm (≈1,6) ile hesaplanır.',
                'İşçilik şap türüne göre m² başına; yüzer şapta yalıtım levhası ve naylon eklenir.',
                'Elyaf katkı, çelik hasır ve kenar bandı seçilirse eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Kalınlık', 'Malzeme kalınlıkla doğrusal artar; 5 cm yerine 8 cm şap malzemeyi %60 artırır.'],
                ['Yüzey kalitesi', 'Helikopter perdah, parke ve epoksi altında daha düzgün yüzey verir; işçiliği daha yüksektir.'],
                ['Yerden ısıtma', 'Borular üzerinde en az 4,5–5 cm şap ve elyaf katkı önerilir.'],
                ['Kuruma süresi', 'Klasik şap cm başına yaklaşık 1 hafta kurur; parke öncesi nem ölçülmelidir.'],
            ],
            'faq' => [
                ['100 m² şap kaç TL?', '5 cm klasik tesviye şapı malzeme ve işçilik dahil '.now()->year.' fiyatlarıyla yaklaşık '.$this->exampleRange(['type' => 'klasik', 'area' => 100, 'thickness' => 5]).' tutar.'],
                ['1 m³ şap için kaç torba çimento gerekir?', '1:4 karışımda yaklaşık 350 kg, yani 7 torba (50 kg) çimento ve 1,2 m³ kum gerekir.'],
                ['Şap kaç günde kurur?', 'Klasik şap 5 cm için yaklaşık 4–5 hafta; hazır hızlı kuruyan şaplarda 1–3 gün sonra üzerine kaplama yapılabilir.'],
                ['Hesaba neler dahil?', 'Malzeme, işçilik ve seçerseniz katkı, hasır, kenar bandı. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/parke-maliyet-hesaplayici', 'Parke maliyeti'], ['/araclar/zemin-kaplama', 'Zemin kaplama'], ['/araclar/epoksi-zemin-hesaplayici', 'Epoksi zemin'], ['/araclar/cimento-kum-hesaplama', 'Çimento ve kum hesabı']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'type', 'title' => 'Şap türü', 'short' => 'Şap türü', 'required' => true, 'cols' => 2, 'options' => self::TYPES],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 2, 'fields' => [
                ['key' => 'area', 'label' => 'Alan', 'unit' => 'm²', 'min' => 1, 'max' => 20000, 'step' => 1, 'placeholder' => 'Örn. 100', 'required' => true],
                ['key' => 'thickness', 'label' => 'Ortalama kalınlık', 'unit' => 'cm', 'min' => 0.3, 'max' => 15, 'step' => 0.5, 'placeholder' => 'Örn. 5', 'required' => true],
            ], 'help' => 'Hazır tesviye şapında kalınlık genellikle 0,3–1 cm; klasik şapta 4–7 cm\'dir.'],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['fiber' => 'Elyaf (fiber) katkı', 'mesh' => 'Çelik hasır', 'edge' => 'Kenar (dilatasyon) bandı']],
        ];
    }

    private function cement(array $in): array
    {
        $vol = (float) $in['area'] * (float) $in['thickness'] / 100;

        return [$vol, (int) ceil($vol * 350 / 50), $vol * 1.2];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $a = (float) $in['area'];
        $t = $in['type'];
        [$vol] = $this->cement($in);
        $mat = $t === 'tesviye' ? $a * (float) $in['thickness'] * 10 * 1.6 * $p['selfleveling_kg'] : $vol * $p['mortar_m3'];
        $items = [
            'malzeme' => ['label' => $t === 'tesviye' ? 'Hazır tesviye şapı' : 'Çimento + kum (şap harcı)', 'mid' => $mat, 'color' => '#FFB800'],
            'yalitim' => ['label' => 'Yalıtım levhası ve naylon', 'mid' => $t === 'yuzer' ? $a * $p['eps_m2'] : 0, 'color' => '#7FB3C8'],
            'iscilik' => ['label' => 'Şap işçiliği', 'mid' => $a * $p['labor_m2'][$t], 'color' => '#101318'],
            'katki' => ['label' => 'Elyaf katkı', 'mid' => $this->has($in, 'extras', 'fiber') ? $a * $p['fiber_m2'] : 0, 'color' => '#C9A55A'],
            'hasir' => ['label' => 'Çelik hasır', 'mid' => $this->has($in, 'extras', 'mesh') ? $a * $p['mesh_m2'] : 0, 'color' => '#5B6472'],
            'bant' => ['label' => 'Kenar bandı', 'mid' => $this->has($in, 'extras', 'edge') ? 4 * sqrt($a) * 1.3 * $p['edge_m'] : 0, 'color' => '#8A6200'],
        ];
        $days = [max(1, (int) ceil($a / 150)), max(1, (int) ceil($a / 80))];

        return [
            'items' => $items,
            'per_unit' => $a,
            'days' => $days,
            'meta' => [['Uygulama süresi', $days[0].'–'.$days[1].' iş günü'], [$t === 'tesviye' ? 'Malzeme' : 'Şap hacmi', $t === 'tesviye' ? number_format($a * (float) $in['thickness'] * 16, 0, ',', '.').' kg' : self::n1($vol).' m³']],
            'notes' => $t === 'tesviye' ? [] : ['Kuruma süresi cm başına yaklaşık 1 haftadır; kaplamadan önce nem ölçtürün.'],
        ];
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];
        if ($in['type'] === 'tesviye') {
            $kg = $a * (float) $in['thickness'] * 10 * 1.6;

            return [['Hazır tesviye şapı (25 kg)', (int) ceil($kg / 25), 'torba', number_format($kg, 0, ',', '.').' kg · 1,6 kg/m²/mm'], ['Astar (zemin)', (int) ceil($a / 50), 'bidon (5 kg)', 'Yaklaşık 0,1 kg/m²']];
        }
        [$vol, $bags, $sand] = $this->cement($in);
        $rows = [
            ['Çimento (50 kg)', $bags, 'torba', 'Yaklaşık 350 kg/m³ (1:4)'],
            ['Kum (şap kumu)', self::n1($sand), 'm³', 'Hacim × 1,2'],
        ];
        if ($in['type'] === 'yuzer') {
            $rows[] = ['EPS / XPS levha (2 cm)', (int) ceil($a * 1.05), 'm²', '%5 fire'];
            $rows[] = ['Polietilen örtü', (int) ceil($a * 1.1), 'm²', 'Bindirme dahil'];
        }
        if ($this->has($in, 'extras', 'fiber')) {
            $rows[] = ['Polipropilen elyaf', self::n1($vol * 0.9), 'kg', 'Yaklaşık 0,9 kg/m³'];
        }
        if ($this->has($in, 'extras', 'mesh')) {
            $rows[] = ['Çelik hasır (Q131)', (int) ceil($a * 1.15 / 10.75), 'adet (2,15×5 m)', 'Bindirme dahil'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return [['Zemin temizliği ve kot alma', .2], ['Şap dökümü ve çekme', .6], ['Perdah ve kür', .2]];
    }
}

<?php

namespace App\Estimators;

class RoughPlasterEstimator extends KitEstimator
{
    public function key(): string { return 'kara_siva'; }

    public function slug(): string { return 'kara-siva-hesaplama'; }

    public function perUnitLabel(): ?string { return 'm² başına'; }

    private function p(): array { return (array) config('worksitem_estimators.siva'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Kara sıva maliyeti',
            'h1' => "Kara (kaba) sıva maliyeti hesaplama ($y)",
            'lede' => 'Sıvanacak alanı ve kalınlığı girin, geleneksel ya da makine sıvası seçin; harç, işçilik, file ve iskele kalem kalem hesaplansın. Kaç torba çimento ve sıva gerektiği Pro raporda.',
            'seo_title' => "Kaba Sıva Fiyatı $y: m² Maliyet ve Malzeme Hesabı | Worksitem",
            'seo_desc' => "$y kaba sıva fiyatları: iç ve dış cephe kara sıvanın m² maliyetini geleneksel veya makine sıvası, işçilik, file ve iskele dahil ücretsiz hesaplayın.",
            'service' => 'Kaba Sıva',
            'wait' => 'Alanı girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Sıva ve yapı kimyasalı markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Torba ve m³ hesabı', '$ € £ karşılığı'],
            'method' => [
                'Harç hacmi = alan × kalınlık. Geleneksel sıvada çimento-kireç-kum harcı m³ fiyatı uygulanır.',
                'Makine (hazır) sıvada yaklaşık 13 kg/m²/cm torbalı sıva alınır.',
                'İşçilik sıva türüne göre m² başına; dış cephede %30 eklenir.',
                'File ve dış cephede iskele seçilirse eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Kalınlık', 'Duvar düzgün değilse sıva 3 cm\'ye çıkar ve malzeme %50 artar.'],
                ['Makine sıvası', 'Hızlı uygulanır, işçilik düşüktür; malzemesi geleneksel harçtan pahalıdır.'],
                ['Dış cephe', 'Hava koşulları, iskele ve güvenlik nedeniyle iç cepheden pahalıdır.'],
                ['File', 'Farklı malzemelerin birleştiği yerlerde file kullanmak çatlağı önler.'],
            ],
            'faq' => [
                ['Kaba sıva m² fiyatı ne kadar?', 'İç cephe, 2 cm geleneksel kaba sıva malzeme ve işçilik dahil '.now()->year.' fiyatlarıyla m² başına yaklaşık '.$this->exampleRange(['method' => 'geleneksel', 'area' => 100, 'thickness' => 2, 'side' => 'ic'], true).' arasıdır.'],
                ['1 m² sıva için ne kadar çimento gerekir?', '2 cm kalınlıkta 1 m² sıva yaklaşık 0,02 m³ harç ister; 250 dozlu (250 kg/m³) harçla bu yaklaşık 5 kg çimento ve 0,023 m³ kum demektir. Kaç torba çimento gerektiği Pro raporda yazar.'],
                ['Makine sıvası mı, geleneksel sıva mı?', 'Büyük alanlarda makine sıvası hız kazandırır; küçük ve tamir işlerinde geleneksel sıva daha ekonomiktir.'],
                ['Hesaba neler dahil?', 'Harç malzemesi ve işçilik; seçerseniz file ve iskele. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/alci-siva-hesaplama', 'Alçı sıva'], ['/araclar/duvar-maliyet-hesaplayici', 'Duvar maliyeti'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı'], ['/araclar/iskele-metraji-maliyeti', 'İskele maliyeti']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'method', 'title' => 'Sıva türü', 'short' => 'Sıva türü', 'required' => true, 'cols' => 2, 'options' => [
                'geleneksel' => ['label' => 'Geleneksel', 'hint' => 'Çimento-kireç-kum harcı'],
                'makine' => ['label' => 'Makine sıvası', 'hint' => 'Hazır torbalı, püskürtme'],
            ]],
            ['type' => 'cards', 'key' => 'side', 'title' => 'Nerede?', 'short' => 'Uygulama yeri', 'required' => true, 'cols' => 2, 'options' => [
                'ic' => ['label' => 'İç cephe', 'hint' => 'Oda duvarları, tavan'],
                'dis' => ['label' => 'Dış cephe', 'hint' => 'Bina dış duvarları'],
            ]],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 2, 'fields' => [
                ['key' => 'area_en', 'label' => 'Duvar uzunluğu (toplam)', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 4'],
                ['key' => 'area_boy', 'label' => 'Yükseklik', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 3'],
                ['key' => 'area', 'label' => 'Alan (biliyorsanız)', 'from' => ['area_en', 'area_boy'], 'unit' => 'm²', 'min' => 1, 'max' => 50000, 'step' => 1, 'placeholder' => 'Örn. 120', 'required' => true, 'missing' => 'alan (en × boy ya da m²)'],
                ['key' => 'thickness', 'label' => 'Kalınlık', 'unit' => 'cm', 'min' => 1, 'max' => 5, 'step' => 0.5, 'placeholder' => '2', 'default' => 2],
            ], 'help' => 'Kapı ve pencere boşluklarını düşün. Kalınlığı bilmiyorsanız boş bırakın; 2 cm alınır.'],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['mesh' => 'Sıva filesi', 'scaffold' => 'İskele (dış cephe)']],
        ];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $a = (float) $in['area'];
        $cm = (float) ($in['thickness'] ?? 2);
        $ext = $in['side'] === 'dis';
        $mat = $in['method'] === 'makine' ? $a * $cm * $p['machine_kg_m2_cm'] / 25 * $p['machine_bag_25kg'] : $a * $cm / 100 * $p['mortar_m3'];
        $items = [
            'iskele' => ['label' => 'İskele', 'mid' => $this->has($in, 'extras', 'scaffold') ? $a * $p['scaffold_m2'] : 0, 'color' => '#5B6472'],
            'malzeme' => ['label' => $in['method'] === 'makine' ? 'Hazır makine sıvası' : 'Sıva harcı (çimento, kireç, kum)', 'mid' => $mat, 'color' => '#FFB800'],
            'file' => ['label' => 'Sıva filesi', 'mid' => $this->has($in, 'extras', 'mesh') ? $a * $p['mesh_m2'] : 0, 'color' => '#C9A55A'],
            'iscilik' => ['label' => 'Sıva işçiliği', 'mid' => $a * $p['labor_m2'][$in['method']] * ($ext ? $p['exterior_factor'] : 1), 'color' => '#101318'],
        ];
        $rate = $in['method'] === 'makine' ? 120 : 60;
        $days = [max(1, (int) ceil($a / ($rate * 1.4))), max(1, (int) ceil($a / $rate))];

        return [
            'items' => $items, 'per_unit' => $a, 'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Harç hacmi', self::n1($a * $cm / 100).' m³']],
        ];
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];
        $cm = (float) ($in['thickness'] ?? 2);
        if ($in['method'] === 'makine') {
            $kg = $a * $cm * 13;
            $rows = [['Makine sıvası (25 kg)', (int) ceil($kg / 25), 'torba', number_format($kg, 0, ',', '.').' kg · 13 kg/m²/cm']];
        } else {
            $vol = $a * $cm / 100;
            $rows = [
                ['Çimento (50 kg)', (int) ceil($vol * 250 / 50), 'torba', 'Yaklaşık 250 kg/m³'],
                ['Kireç (25 kg)', (int) ceil($vol * 100 / 25), 'torba', 'Yaklaşık 100 kg/m³'],
                ['Sıva kumu', self::n1($vol * 1.15), 'm³', 'Hacim × 1,15'],
            ];
        }
        if ($this->has($in, 'extras', 'mesh')) {
            $rows[] = ['Sıva filesi (50 m²)', (int) ceil($a * 1.1 / 50), 'rulo', '%10 bindirme'];
        }
        $rows[] = ['Köşe / sıva profili', (int) ceil($a / 6), 'adet (3 m)', 'Mastar ve köşe için yaklaşık'];

        return $rows;
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([
            $this->has($in, 'extras', 'scaffold') ? ['İskele kurulumu', .15] : null,
            ['Yüzey hazırlığı ve serpme', .2], ['Kaba sıva ve mastar', .5], ['Kür ve temizlik', .15],
        ]));
    }
}

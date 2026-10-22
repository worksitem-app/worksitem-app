<?php

namespace App\Estimators;

class SolarEstimator extends KitEstimator
{
    public const P = ['panel_kwp' => 10000, 'inverter_kwp' => 6000, 'mount_kwp' => ['cati' => 3500, 'arazi' => 5500], 'install_kwp' => 4000, 'project' => 15000, 'battery_kwh' => 12000, 'kwh_price' => 3.5];

    /** Fiyatlar: config('worksitem_estimators.ges') (fiyat robotu günceller); sabit yalnız yedek */
    public static function prices(): array
    {
        return array_replace(self::P, (array) config('worksitem_estimators.ges', []));
    }

    public function key(): string { return 'ges'; }

    public function slug(): string { return 'gunes-paneli-hesaplama'; }

    public function perUnitLabel(): ?string { return 'kWp başına'; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Güneş paneli (GES) maliyeti',
            'h1' => "Güneş paneli (GES) maliyeti ve geri dönüş hesaplama ($y)",
            'lede' => 'Aylık elektrik faturanızı ve bölgenizi girin; ihtiyacınız olan kurulu güç (kWp), panel, inverter, konstrüksiyon ve montaj maliyeti ile yaklaşık geri dönüş süresi hesaplansın.',
            'seo_title' => "Güneş Paneli Fiyatı $y: Ev Tipi GES Maliyeti ve Geri Dönüş | Worksitem",
            'seo_desc' => "$y güneş paneli fiyatları: elektrik faturanıza göre gereken kWp, panel, inverter, konstrüksiyon ve batarya dahil GES maliyetini ve geri dönüş süresini ücretsiz hesaplayın.",
            'service' => 'Güneş Enerjisi (GES)',
            'wait' => 'Faturanızı girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Güneş enerjisi markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Geri dönüş süresi', '$ € £ karşılığı'],
            'method' => [
                'Aylık tüketim (kWh) = fatura ÷ birim fiyat (≈'.str_replace('.', ',', (string) self::prices()['kwh_price']).' TL/kWh).',
                'Kurulu güç (kWp) = aylık tüketim ÷ (günlük güneşlenme × 30 × 0,80 sistem verimi).',
                'Panel, inverter, konstrüksiyon ve montaj kWp başına; proje ve başvuru sabit; batarya seçilirse kWp × 2 kWh kapasite eklenir.',
                'Geri dönüş = toplam maliyet ÷ yıllık fatura tasarrufu (fiyat artışı hesaba katılmaz). %5 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Bölge', 'Akdeniz ve Güneydoğu\'da aynı panel Karadeniz\'e göre %35 daha fazla üretir.'],
                ['Çatı yönü', 'Güney cepheli, 25–35° eğimli ve gölgesiz çatı en iyi verimi verir.'],
                ['Batarya', 'Batarya geceleri bağımsızlık sağlar ama maliyeti belirgin artırır ve geri dönüşü uzatır.'],
                ['Mevzuat', 'Lisanssız üretim yönetmeliği ve dağıtım şirketi onayı gerekir; mahsuplaşma kuralları değişebilir.'],
            ],
            'faq' => [
                ['Ev için güneş paneli kaç TL?', 'Aylık 2.000 TL faturalı, İç Anadolu/Ege\'de bir ev için çatı GES\'i yaklaşık '.$this->exampleRange(['bill' => 2000, 'sun' => '4.5', 'mount' => 'cati', 'quality' => 'orta']).' tutar.'],
                ['Güneş paneli kendini kaç yılda öder?', 'Bataryasız konut sistemlerinde genellikle 4–7 yıl; elektrik fiyatı arttıkça süre kısalır.'],
                ['1 kWp kaç panel eder?', 'Günümüzde 450–550 W paneller yaygındır; 1 kWp yaklaşık 2 panel ve 5 m² çatı alanı demektir.'],
                ['Hesaba neler dahil?', 'Panel, inverter, konstrüksiyon, kablo, montaj, proje ve başvuru; seçerseniz batarya. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/ges-planlayici', 'GES planlayıcı (2D / 3D çatı)'], ['/araclar/enerji-tuketim-hesaplama', 'Enerji tüketimi'], ['/araclar/jenerator-hesaplama', 'Jeneratör'], ['/araclar/mantolama-maliyeti', 'Mantolama'], ['/araclar/cati-maliyet-hesaplayici', 'Çatı maliyeti']],
        ];
    }

    public function contingencyRate(): float { return 0.05; }

    public function planner(): ?array
    {
        return [
            'url' => route('tools.solar_planner'),
            'label' => 'Çatınıza panelleri yerleştirin (2D / 3D)',
            'hint' => 'Adresinizi yazın; çatınız uydu haritasında gelsin, paneller dizilsin, üretim PVGIS verisiyle hesaplansın.',
            'map' => [],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'numbers', 'title' => 'Tüketim', 'cols' => 1, 'fields' => [
                ['key' => 'bill', 'label' => 'Aylık ortalama elektrik faturası', 'unit' => 'TL', 'min' => 200, 'max' => 2000000, 'step' => 50, 'placeholder' => 'Örn. 2000', 'required' => true],
            ]],
            ['type' => 'cards', 'key' => 'sun', 'title' => 'Bölge (güneşlenme)', 'short' => 'Bölge', 'required' => true, 'cols' => 3, 'options' => [
                '4.0' => ['label' => 'Karadeniz / Marmara', 'hint' => '≈4 saat/gün'],
                '4.5' => ['label' => 'İç Anadolu / Ege', 'hint' => '≈4,5 saat/gün'],
                '5.5' => ['label' => 'Akdeniz / Güneydoğu', 'hint' => '≈5,5 saat/gün'],
            ]],
            ['type' => 'cards', 'key' => 'mount', 'title' => 'Kurulum yeri', 'short' => 'Kurulum yeri', 'required' => true, 'cols' => 2, 'options' => [
                'cati' => ['label' => 'Çatı', 'hint' => 'Konut, işyeri çatısı'],
                'arazi' => ['label' => 'Arazi / bahçe', 'hint' => 'Zemine konstrüksiyon'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Ekipman kalitesi', 'short' => 'Ekipman kalitesi', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Tier-2 panel'],
                'orta' => ['label' => 'Orta', 'hint' => 'Tier-1 panel, marka inverter'],
                'premium' => ['label' => 'Premium', 'hint' => 'N-tipi panel, optimizer'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['battery' => 'Batarya (gece kullanımı)', 'monitor' => 'Uzaktan izleme / akıllı sayaç']],
        ];
    }

    private function size(array $in): array
    {
        $kwhMonth = (float) $in['bill'] / self::prices()['kwh_price'];
        $kwp = max(1, round($kwhMonth / ((float) $in['sun'] * 30 * 0.8), 1));

        return [$kwhMonth, $kwp];
    }

    protected function compute(array $in): array
    {
        [, $kwp] = $this->size($in);
        $qf = $this->q($in, [0.85, 1.0, 1.25]);
        $items = [
            'panel' => ['label' => 'Güneş paneli', 'mid' => $kwp * self::prices()['panel_kwp'] * $qf, 'color' => '#FFB800'],
            'inverter' => ['label' => 'İnverter', 'mid' => $kwp * self::prices()['inverter_kwp'] * $qf, 'color' => '#101318'],
            'kons' => ['label' => 'Konstrüksiyon', 'mid' => $kwp * self::prices()['mount_kwp'][$in['mount']], 'color' => '#5B6472'],
            'montaj' => ['label' => 'Kablo, koruma ve montaj', 'mid' => $kwp * self::prices()['install_kwp'], 'color' => '#8A6200'],
            'proje' => ['label' => 'Proje, başvuru ve kabul', 'mid' => self::prices()['project'], 'color' => '#2F6F8F'],
            'batarya' => ['label' => 'Batarya ('.self::n1($kwp * 2).' kWh)', 'mid' => $this->has($in, 'extras', 'battery') ? $kwp * 2 * self::prices()['battery_kwh'] : 0, 'color' => '#7FB3C8'],
            'izleme' => ['label' => 'Uzaktan izleme', 'mid' => $this->has($in, 'extras', 'monitor') ? 6000 : 0, 'color' => '#C9A55A'],
        ];
        $total = array_sum(array_column($items, 'mid')) * (1 + $this->contingencyRate());
        $yearSave = (float) $in['bill'] * 12;
        $payback = $yearSave > 0 ? $total / $yearSave : 0;
        $prod = $kwp * (float) $in['sun'] * 365 * 0.8;

        return ['items' => $items, 'per_unit' => $kwp, 'days' => [2, 5],
            'meta' => [['Kurulu güç', self::n1($kwp).' kWp · ≈'.(int) ceil($kwp * 1000 / 500).' panel'], ['Yıllık üretim', number_format($prod, 0, ',', '.').' kWh']],
            'notes' => ['Yaklaşık geri dönüş: '.self::n1($payback).' yıl (bugünkü elektrik fiyatıyla).', 'Kurulum süresine dağıtım şirketi onay süreci (genellikle 1–3 ay) dahil değildir.']];
    }

    public function materials(array $in): array
    {
        [$kwhMonth, $kwp] = $this->size($in);

        return [
            ['Güneş paneli (500 W)', (int) ceil($kwp * 1000 / 500), 'adet', self::n1($kwp).' kWp'],
            ['İnverter', 1, 'adet', '≈'.self::n1($kwp).' kW'],
            ['Konstrüksiyon', (int) ceil($kwp * 1000 / 500), 'panel seti', $in['mount'] === 'cati' ? 'Çatı tipi' : 'Arazi tipi'],
            ['Solar kablo (4–6 mm²)', (int) ceil(20 + $kwp * 8), 'm', ''],
            ['Gerekli çatı / arazi alanı', (int) ceil($kwp * 5.5), 'm²', '≈5,5 m²/kWp'],
            ['Aylık tüketim (fatura)', number_format($kwhMonth, 0, ',', '.'), 'kWh', '≈'.str_replace('.', ',', (string) self::prices()['kwh_price']).' TL/kWh ile'],
        ];
    }

    public function phases(array $in): array
    {
        return [['Keşif ve proje', .3], ['Konstrüksiyon', .25], ['Panel ve inverter montajı', .3], ['Test ve devreye alma', .15]];
    }
}

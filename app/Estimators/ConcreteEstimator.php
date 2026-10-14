<?php

namespace App\Estimators;

use App\Estimators\Concerns\DbPrices;

/** Hazır beton: eleman tipi + ölçü → hacim, sipariş, mikser, pompa; tedarikçi başvuru alanı korunur */
class ConcreteEstimator extends KitEstimator
{
    use DbPrices;

    private const ELEMENTS = [
        'doseme' => ['label' => 'Döşeme / saha', 'hint' => 'Uzunluk × genişlik × kalınlık'],
        'radye' => ['label' => 'Radye temel', 'hint' => 'Plak temel'],
        'surekli' => ['label' => 'Sürekli temel', 'hint' => 'Uzunluk × genişlik × yükseklik'],
        'tekil' => ['label' => 'Tekil temel', 'hint' => 'Adet × ölçü'],
        'kolon' => ['label' => 'Kolon', 'hint' => 'Adet × kesit × yükseklik'],
        'kiris' => ['label' => 'Kiriş', 'hint' => 'Uzunluk × genişlik × yükseklik'],
        'perde' => ['label' => 'Perde duvar', 'hint' => 'Uzunluk × kalınlık × yükseklik'],
        'hacim' => ['label' => 'Hacmi biliyorum', 'hint' => 'Net m³ girin'],
    ];

    public function key(): string { return 'beton'; }

    public function slug(): string { return 'beton-hesaplayici'; }

    public function perUnitLabel(): ?string { return 'm³ başına'; }

    public function pricesLabel(): string { return $this->reviewedLabel(); }

    public function contingencyRate(): float { return 0.0; }

    private function p(): array { return (array) config('worksitem_estimators.saha'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Beton hesaplayıcı',
            'h1' => "Beton hesaplama: kaç m³ beton, kaç mikser? ($y)",
            'lede' => 'Eleman tipini ve ölçüleri girin; net beton hacmi, fire dahil sipariş miktarı, mikser sayısı, pompa ve yaklaşık hazır beton bütçesi kalem kalem hesaplansın. Beton fiyatları Worksitem fiyat veritabanından gelir.',
            'seo_title' => "Beton Hesaplama $y: Kaç m³ Beton, Kaç Mikser, Kaç TL? | Worksitem",
            'seo_desc' => "$y hazır beton hesaplama: döşeme, temel, kolon, kiriş ve perde için beton hacmini, fire ve mikser sayısını, C20–C35 beton ve pompa maliyetini ücretsiz hesaplayın.",
            'service' => 'Hazır Beton',
            'lead_source' => 'worksitem_beton_tedarikci_teklif',
            'lead_title' => 'Hazır beton fiyat teklifi alın',
            'lead_text' => 'Beton sınıfı, miktar ve konumunuz forma eklenir; bölgenizdeki hazır beton tedarikçilerinden ve Kainta İnşaat\'tan teklif için sizinle iletişime geçilir.',
            'extra_view' => 'tools.partials.beton-supplier',
            'wait' => 'Eleman tipini ve ölçüleri girin; beton miktarı ve maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Hazır beton ve pompa firmaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Mikser ve pompa', '$ € £ karşılığı'],
            'method' => [
                'Net hacim eleman tipine göre ölçülerden hesaplanır (döşeme: uzunluk × genişlik × kalınlık; kolon ve tekil temel: adet × kesit × yükseklik).',
                'Sipariş miktarı = net hacim × (1 + fire). Mikser sayısı 8 m³ kapasiteyle bulunur.',
                'Beton m³ fiyatı seçilen sınıfa göre Worksitem fiyat veritabanından (yoksa yaklaşık piyasa değeri), pompa döküm başına eklenir.',
                'Döküm ve vibrasyon işçiliği m³ başına; numune/laboratuvar ve kür seçilirse eklenir. Aralık −%15 / +%20.',
            ],
            'factors' => [
                ['Beton sınıfı', 'Taşıyıcı sistemde proje sınıfının altına inilmez; C30 ve üzeri m³ fiyatını artırır.'],
                ['Pompa', 'Yüksek kat ve mikserin yanaşamadığı alanlarda mobil pompa gerekir; döküm başına ücretlidir.'],
                ['Fire', 'Kalıp şişmesi ve pompa artığı için %3–5 fire alınır.'],
                ['Mesafe ve saat', 'Santrale uzaklık, gece dökümü ve hafta sonu ek ücrete neden olabilir.'],
            ],
            'faq' => [
                ['1 m³ hazır beton kaç TL?', $y.' fiyatlarıyla C25/30 hazır beton m³ başına yaklaşık '.number_format($this->toolPrice('beton-hesaplayici', 'concrete_c25', $this->p()['concrete_m3']['c25']), 0, ',', '.').' TL\'dir (KDV dahil yaklaşık); pompa ve işçilik ayrıca eklenir.'],
                ['100 m² döşeme için kaç m³ beton gerekir?', '20 cm kalınlıkta net 20 m³, %5 fire ile yaklaşık 21 m³ hazır beton sipariş edilir (3 mikser).'],
                ['Bir mikser kaç m³ beton taşır?', 'Standart transmikser 8–10 m³ taşır; hesapta 8 m³ alınır.'],
                ['Hesaba neler dahil?', 'Hazır beton, pompa ve döküm işçiliği; seçerseniz numune ve kür. Kalıp ve demir ayrı araçlardadır.'],
            ],
            'related' => [['/araclar/kalip-plywood-maliyeti', 'Kalıp maliyeti'], ['/araclar/demir-metraji-hesaplayici', 'Demir metrajı'], ['/araclar/saha-betonu-hesaplayici', 'Saha betonu'], ['/araclar/tahmini-bina-maliyeti', 'Bina maliyeti']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'element', 'title' => 'Eleman / döküm tipi', 'short' => 'Eleman tipi', 'required' => true, 'cols' => 4, 'options' => self::ELEMENTS],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 3, 'fields' => [
                ['key' => 'length', 'label' => 'Uzunluk', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.05, 'placeholder' => 'Örn. 10'],
                ['key' => 'width', 'label' => 'Genişlik / kesit', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.05, 'placeholder' => 'Örn. 10'],
                ['key' => 'thickness', 'label' => 'Kalınlık / yükseklik', 'unit' => 'cm', 'min' => 5, 'max' => 2000, 'step' => 1, 'placeholder' => 'Örn. 20'],
                ['key' => 'count', 'label' => 'Adet', 'unit' => 'adet', 'min' => 1, 'max' => 2000, 'step' => 1, 'placeholder' => '1', 'default' => 1],
                ['key' => 'volume', 'label' => 'Net hacim (biliyorsanız)', 'unit' => 'm³', 'min' => 0.1, 'max' => 100000, 'step' => 0.1, 'placeholder' => 'Örn. 21'],
                ['key' => 'waste', 'label' => 'Fire', 'unit' => '%', 'min' => 0, 'max' => 15, 'step' => 1, 'placeholder' => '5', 'default' => 5],
            ], 'help' => 'Kolon ve tekil temelde uzunluk × genişlik kesittir, kalınlık alanına yüksekliği (cm) yazın. "Hacmi biliyorum" seçtiyseniz yalnız net hacmi girin.'],
            ['type' => 'cards', 'key' => 'class', 'title' => 'Beton sınıfı', 'short' => 'Beton sınıfı', 'required' => true, 'cols' => 4, 'options' => [
                'c20' => ['label' => 'C20/25', 'hint' => 'Grobeton, bahçe'],
                'c25' => ['label' => 'C25/30', 'hint' => 'Konut, en yaygın'],
                'c30' => ['label' => 'C30/37', 'hint' => 'Yüksek dayanım'],
                'c35' => ['label' => 'C35/45', 'hint' => 'Özel yapı'],
            ]],
            ['type' => 'cards', 'key' => 'pump', 'title' => 'Mobil pompa', 'short' => 'Pompa', 'required' => true, 'cols' => 2, 'options' => [
                'evet' => ['label' => 'Pompa gerekli', 'hint' => 'Kat dökümü, uzak nokta'],
                'hayir' => ['label' => 'Gerekmiyor', 'hint' => 'Mikser oluğu yeterli'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['test' => 'Numune ve laboratuvar', 'cure' => 'Kür (örtü / kür kimyasalı)', 'labor' => 'Döküm ve vibrasyon işçiliği']],
        ];
    }

    private function netVolume(array $in): float
    {
        if (($in['element'] ?? '') === 'hacim' || ! empty($in['_given_volume'])) {
            return ! empty($in['_given_volume']) ? (float) $in['volume'] : 0.0;
        }
        if (empty($in['_given_length']) || empty($in['_given_width']) || empty($in['_given_thickness'])) {
            return 0.0;
        }
        $v = (float) $in['length'] * (float) $in['width'] * (float) $in['thickness'] / 100;
        if (in_array($in['element'], ['kolon', 'tekil'], true)) {
            $v *= max(1, (int) round((float) ($in['count'] ?? 1)));
        }

        return $v;
    }

    public function missing(array $in): array
    {
        $m = parent::missing($in);
        if (($in['element'] ?? '') !== '' && $this->netVolume($in) <= 0) {
            $m[] = ($in['element'] === 'hacim') ? 'net hacim' : 'uzunluk, genişlik ve kalınlık (veya net hacim)';
        }

        return $m;
    }

    private function order(array $in): array
    {
        $net = $this->netVolume($in);
        $order = $net * (1 + (float) ($in['waste'] ?? 5) / 100);

        return [$net, $order, (int) ceil($order / 8)];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        [$net, $order, $mixers] = $this->order($in);
        $price = $this->toolPrice('beton-hesaplayici', 'concrete_'.$in['class'], $p['concrete_m3'][$in['class']]);
        $pours = max(1, (int) ceil($order / 120));
        $items = [
            'beton' => ['label' => 'Hazır beton '.strtoupper($in['class']).' ('.self::n1($order).' m³)', 'mid' => $order * $price, 'color' => '#FFB800'],
            'pompa' => ['label' => 'Mobil pompa ('.$pours.' döküm)', 'mid' => $in['pump'] === 'evet' ? $pours * $this->toolPrice('beton-hesaplayici', 'pump', $p['pump']) : 0, 'color' => '#2F6F8F'],
            'iscilik' => ['label' => 'Döküm ve vibrasyon işçiliği', 'mid' => $this->has($in, 'extras', 'labor') ? $order * $p['labor_m3'] : 0, 'color' => '#101318'],
            'test' => ['label' => 'Numune ve laboratuvar', 'mid' => $this->has($in, 'extras', 'test') ? max(1, ceil($order / 50)) * 2500 : 0, 'color' => '#5B6472'],
            'kur' => ['label' => 'Kür', 'mid' => $this->has($in, 'extras', 'cure') ? $order * 60 : 0, 'color' => '#7FB3C8'],
        ];

        return ['items' => $items, 'per_unit' => $order, 'days' => null,
            'meta' => [['Net / sipariş hacmi', self::n1($net).' / '.self::n1($order).' m³'], ['Mikser (8 m³)', $mixers.' sefer']]];
    }

    public function materials(array $in): array
    {
        [$net, $order, $mixers] = $this->order($in);

        return [
            ['Net beton hacmi', self::n1($net), 'm³', self::ELEMENTS[$in['element']]['label']],
            ['Sipariş edilecek hazır beton '.strtoupper($in['class']), self::n1($order), 'm³', '%'.(int) ($in['waste'] ?? 5).' fire dahil'],
            ['Transmikser', $mixers, 'sefer', '8 m³ kapasite'],
            ['Numune küpü', max(1, (int) ceil($order / 50)) * 6, 'adet', 'Her 50 m³ için 6 numune (yaklaşık)'],
        ];
    }

    public function phases(array $in): array
    {
        return [['Kalıp ve donatı kontrolü', .3], ['Döküm ve vibrasyon', .4], ['Kür', .3]];
    }
}

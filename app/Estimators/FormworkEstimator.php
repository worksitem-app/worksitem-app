<?php

namespace App\Estimators;

class FormworkEstimator extends KitEstimator
{
    public function key(): string { return 'kalip'; }

    public function slug(): string { return 'kalip-plywood-maliyeti'; }

    public function perUnitLabel(): ?string { return 'kalıp m² başına'; }

    private function p(): array { return (array) config('worksitem_estimators.kalip'); }

    public function contingencyRate(): float { return 0.05; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Kalıp maliyeti',
            'h1' => "Kalıp ve plywood maliyeti hesaplama ($y)",
            'lede' => 'Kalıp alanını ya da beton hacmini girin, yapı elemanını ve kalıp malzemesini seçin; kalıp, döşeme dikmesi ve söküm Çevre, Şehircilik ve İklim Değişikliği Bakanlığı birim fiyatlarıyla kalem kalem hesaplansın.',
            'seo_title' => "Kalıp Fiyatı $y: Plywood ve Ahşap Kalıp m² Maliyeti | Worksitem",
            'seo_desc' => "$y kalıp fiyatları: plywood (brüt beton) ve ahşap kalıbın m² maliyetini beton hacminden kalıp alanı tahmini, dikme ve işçilik dahil ÇŞB birim fiyatlarıyla hesaplayın.",
            'service' => 'Kalıp İşleri',
            'wait' => 'Kalıp alanını veya beton hacmini girin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Kalıp ve plywood firmaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'ÇŞB birim fiyatı', '$ € £ karşılığı'],
            'method' => [
                'Kalıp alanı biliniyorsa kullanılır; bilinmiyorsa beton hacmi × eleman katsayısı (döşeme 6, kolon/perde 10, radye 3 m²/m³) ile bulunur.',
                'Kalıp m² fiyatı ÇŞB poz birim fiyatıdır (malzeme + işçilik + söküm).',
                'Döşeme kalıbında dikme/iskele m² başına eklenir; %5 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Yapı elemanı', 'Kolon ve perdeler m³ başına daha fazla kalıp ister; radye temelde oran düşüktür.'],
                ['Brüt beton', 'Görünür beton yüzey için plywood kalıp daha düzgün sonuç verir ve tekrar kullanılabilir.'],
                ['Tekrar kullanım', 'Kat tekrarlı binalarda kalıbın birkaç kez kullanılması gerçek maliyeti düşürür.'],
                ['Dikme', 'Yüksek kat ve geniş açıklıklı döşemelerde dikme ve iskele maliyeti artar.'],
            ],
            'faq' => [
                ['Kalıp m² fiyatı ne kadar?', now()->year.' ÇŞB birim fiyatlarına göre plywood kalıp m² başına yaklaşık '.number_format($this->p()['m2']['plywood'], 0, ',', '.').' TL, düz ahşap kalıp '.number_format($this->p()['m2']['ahsap'], 0, ',', '.').' TL\'dir (malzeme + işçilik).'],
                ['1 m³ betona kaç m² kalıp gerekir?', 'Döşeme-kiriş ağırlıklı yapılarda yaklaşık 6, kolon-perdede 10, radye temelde 3 m² kalıp gerekir.'],
                ['Kalıp ne zaman sökülür?', 'Kolon ve perde kalıpları 1–3 gün, döşeme alt kalıbı ve dikmeler beton dayanımına göre genellikle 14–28 gün sonra sökülür.'],
                ['Hesaba neler dahil?', 'Kalıp malzemesi, işçilik, söküm ve döşemede dikme. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/beton-hesaplayici', 'Beton hesaplayıcı'], ['/araclar/demir-metraji-hesaplayici', 'Demir metrajı'], ['/araclar/iskele-metraji-maliyeti', 'İskele maliyeti'], ['/araclar/tahmini-bina-maliyeti', 'Bina maliyeti']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'element', 'title' => 'Yapı elemanı', 'short' => 'Yapı elemanı', 'required' => true, 'cols' => 3, 'options' => [
                'doseme' => ['label' => 'Döşeme ve kiriş', 'hint' => '≈6 m² kalıp / m³'],
                'kolon' => ['label' => 'Kolon ve perde', 'hint' => '≈10 m² kalıp / m³'],
                'radye' => ['label' => 'Radye temel', 'hint' => '≈3 m² kalıp / m³'],
            ]],
            ['type' => 'numbers', 'title' => 'Miktar', 'cols' => 2, 'required_any' => 'kalıp alanı veya beton hacmi', 'fields' => [
                ['key' => 'area', 'label' => 'Kalıp alanı (biliyorsanız)', 'unit' => 'm²', 'min' => 1, 'max' => 100000, 'step' => 1, 'placeholder' => 'Örn. 600'],
                ['key' => 'volume', 'label' => 'Beton hacmi', 'unit' => 'm³', 'min' => 0.5, 'max' => 20000, 'step' => 0.5, 'placeholder' => 'Örn. 100'],
            ]],
            ['type' => 'cards', 'key' => 'material', 'title' => 'Kalıp malzemesi', 'short' => 'Kalıp malzemesi', 'required' => true, 'cols' => 2, 'options' => [
                'plywood' => ['label' => 'Plywood (brüt beton)', 'hint' => 'Düzgün yüzey, tekrar kullanılabilir'],
                'ahsap' => ['label' => 'Düz yüzeyli ahşap', 'hint' => 'Kaba yapı, sıvanacak yüzey'],
            ]],
        ];
    }

    private function area(array $in): float
    {
        if (! empty($in['_given_area'])) {
            return (float) $in['area'];
        }

        return ! empty($in['_given_volume']) ? (float) $in['volume'] * $this->p()['ratio'][$in['element']] : 0.0;
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $a = $this->area($in);
        $items = [
            'kalip' => ['label' => ($in['material'] === 'plywood' ? 'Plywood' : 'Ahşap').' kalıp (malzeme + işçilik + söküm)', 'mid' => $a * $p['m2'][$in['material']], 'color' => '#FFB800'],
            'dikme' => ['label' => 'Döşeme dikmesi ve iskele', 'mid' => $in['element'] === 'doseme' ? $a * 0.6 * $p['shoring_m2'] : 0, 'color' => '#5B6472'],
        ];
        $days = [max(1, (int) ceil($a / 150)), max(2, (int) ceil($a / 80))];

        return ['items' => $items, 'per_unit' => $a, 'days' => $days, 'meta' => [['Kurulum süresi', $days[0].'–'.$days[1].' iş günü'], ['Kalıp alanı', number_format($a, 0, ',', '.').' m²']],
            'notes' => empty($in['_given_area']) ? ['Kalıp alanı beton hacminden tahmin edildi.'] : []];
    }

    public function materials(array $in): array
    {
        $a = $this->area($in);
        $rows = [
            [$in['material'] === 'plywood' ? 'Plywood (122×244 cm, 18 mm)' : 'Kalıp tahtası', $in['material'] === 'plywood' ? (int) ceil($a * 1.1 / 2.98) : self::n1($a * 1.1), $in['material'] === 'plywood' ? 'levha' : 'm²', '%10 fire'],
            ['Kereste (kadron, 5×10)', self::n1($a * 0.012), 'm³', 'Yaklaşık 0,012 m³/m²'],
            ['Kalıp yağı', (int) ceil($a / 40), 'litre', '≈40 m²/L'],
            ['Çivi / bağlantı', number_format($a * 0.15, 0, ',', '.'), 'kg', 'Yaklaşık'],
        ];
        if ($in['element'] === 'doseme') {
            $rows[] = ['Teleskopik dikme', (int) ceil($a * 0.6 / 1.2), 'adet', '≈1,2 m² döşemeye 1 dikme'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return [['Aplikasyon', .1], ['Kalıp kurulumu', .6], ['Kontrol ve donatı teslimi', .3]];
    }
}

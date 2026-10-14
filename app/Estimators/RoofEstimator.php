<?php

namespace App\Estimators;

/** Çatı örtüsü + alt kaplama + çatı altı su yalıtımı + mahya: yalnız resmî YFK birim fiyatları */
class RoofEstimator extends PozEstimator
{
    private const COVERINGS = [
        'clay_alaturka' => ['label' => 'Alaturka kil kiremit', 'hint' => '3 latalı', 'poz' => '15.305.1001', 'ridge_poz' => '15.305.1005', 'underlay' => true],
        'clay_interlocking' => ['label' => 'Kenetli kil kiremit', 'hint' => '2 latalı', 'poz' => '15.305.1003', 'ridge_poz' => '15.305.1005', 'underlay' => true],
        'concrete_tile' => ['label' => 'Beton kiremit', 'hint' => 'Çift latalı', 'poz' => '15.305.1201', 'ridge_poz' => '15.305.1204', 'underlay' => true],
        'trapezoid_timber' => ['label' => 'Trapez sac (ahşap çatı)', 'hint' => 'Galvaniz', 'poz' => '15.325.1009', 'ridge_poz' => null, 'underlay' => false],
        'trapezoid_steel' => ['label' => 'Trapez sac (çelik/betonarme)', 'hint' => 'Galvaniz', 'poz' => '15.325.1109', 'ridge_poz' => null, 'underlay' => false],
        'bitumen_timber' => ['label' => 'Oluklu bitümlü levha (ahşap)', 'hint' => 'Onduline tipi', 'poz' => '15.325.1102', 'ridge_poz' => null, 'underlay' => false],
        'bitumen_rc' => ['label' => 'Oluklu bitümlü levha (betonarme)', 'hint' => 'Onduline tipi', 'poz' => '15.325.1105', 'ridge_poz' => null, 'underlay' => false],
        'bitumen_steel' => ['label' => 'Oluklu bitümlü levha (çelik kiriş)', 'hint' => 'Onduline tipi', 'poz' => '15.325.1106', 'ridge_poz' => null, 'underlay' => false],
    ];

    private const UNDERLAYS = [
        'none' => ['label' => 'Alt kaplama yok', 'hint' => '', 'poz' => null],
        'timber' => ['label' => 'Tahta alt kaplama', 'hint' => 'YFK 15.300.1005', 'poz' => '15.300.1005'],
        'osb' => ['label' => 'OSB/3 alt kaplama', 'hint' => 'YFK 15.300.1006', 'poz' => '15.300.1006'],
    ];

    private const MEMBRANES = [
        'none' => ['label' => 'Su yalıtımı yok', 'hint' => '', 'poz' => null],
        'breathable' => ['label' => 'Nefes alan çatı altı örtüsü', 'hint' => 'YFK 15.330.1003', 'poz' => '15.330.1003'],
        'bitumen3' => ['label' => '3 mm bitümlü örtü', 'hint' => 'YFK 15.330.1005', 'poz' => '15.330.1005'],
    ];

    public function key(): string { return 'cati'; }

    public function slug(): string { return 'cati-maliyet-hesaplayici'; }

    public function perUnitLabel(): ?string { return 'çatı m² başına'; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Çatı maliyeti',
            'h1' => "Çatı maliyeti ve kaplama hesaplama ($y)",
            'lede' => 'Bina ölçülerini ve çatı eğimini girin, çatı örtüsünü, alt kaplamayı ve su yalıtımını seçin; gerçek çatı alanı, mahya ve her katman resmî YFK birim fiyatıyla kalem kalem hesaplansın.',
            'seo_title' => "Çatı Maliyeti $y: Kiremit, Trapez Sac, Onduline m² Fiyatı (YFK) | Worksitem",
            'seo_desc' => "$y çatı yapım ve yenileme maliyeti: kil ve beton kiremit, trapez sac, oluklu bitümlü levha, OSB alt kaplama ve çatı altı su yalıtımının m² maliyetini resmî YFK birim fiyatıyla hesaplayın.",
            'service' => 'Çatı',
            'wait' => 'Ölçüleri girin ve katmanları seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Çatı ve yalıtım markaları',
            'facts' => ['Resmî YFK birim fiyatı', 'Eğimli gerçek alan', '$ € £ karşılığı'],
            'method' => [
                'Gerçek çatı alanı biliniyorsa kullanılır; bilinmiyorsa (uzunluk + 2 × saçak) × (genişlik + 2 × saçak) × √(1 + eğim²) ile bulunur.',
                'Beşik çatıda mahya uzunluğu bina uzunluğu + 2 × saçak alınır; kırma ve özel çatıda mahya/kırma hattını girin.',
                'Örtü, alt kaplama ve su yalıtımı alan × YFK m² fiyatı, mahya kiremidi mahya × YFK m fiyatıyla hesaplanır; %20 KDV eklenir.',
                'Resmî fiyatı bulunmayan katmanda fiyat uydurulmaz; söküm için doğrulanmış poz olmadığından bedele eklenmez.',
            ],
            'factors' => [
                ['Örtü türü', 'Kil kiremit uzun ömürlü ve estetiktir; trapez sac ve bitümlü levha hafif ve hızlıdır.'],
                ['Eğim', 'Eğim arttıkça gerçek çatı alanı ve malzeme miktarı büyür (%35 eğimde ≈%6).'],
                ['Alt katmanlar', 'OSB ve nefes alan örtü çatının ısı ve su performansını belirler.'],
                ['Detaylar', 'Baca, dere, kenar ve yağmur oluğu detayları ayrıca fiyatlandırılır.'],
            ],
            'faq' => [
                ['Çatı m² fiyatı ne kadar?', $this->priceAnswer(now()->year.' resmî YFK birim fiyatlarıyla alaturka kil kiremit örtü KDV dahil çatı m²\'si başına yaklaşık :fiyat tutar; alt kaplama ve yalıtım eklenince sonuç kartında kalem kalem görünür.', 'Seçilen örtü, alt kaplama ve yalıtımın resmî YFK birim fiyatları sonuç kartında kalem kalem görünür; KDV dahil toplam ve m² tutarı birlikte verilir.', ['length' => 12, 'width' => 10, 'area' => null, 'ridge' => null])],
                ['Çatı alanı nasıl hesaplanır?', 'Saçaklar dahil yatay izdüşüm alanı eğim katsayısıyla çarpılır; %35 eğimde katsayı yaklaşık 1,06\'dır.'],
                ['Çatı yenileme kaç gün sürer?', 'Bir apartman çatısının örtü yenilemesi hava koşullarına bağlı olarak genellikle 1–3 hafta sürer.'],
                ['Hesaba neler dahil?', 'YFK pozlarının tanımındaki malzeme ve işçilik; KDV ayrıca eklenir. Çatı iskeleti (makas), söküm, oluk ve iskele dahil değildir.'],
            ],
            'related' => [['/araclar/sandvic-panel-hesaplayici', 'Sandviç panel'], ['/araclar/yagmur-borusu-hesabi', 'Yağmur borusu'], ['/araclar/iskele-metraji-maliyeti', 'İskele'], ['/araclar/mantolama-maliyeti', 'Mantolama']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'covering', 'title' => 'Çatı örtüsü', 'short' => 'Çatı örtüsü', 'required' => true, 'cols' => 2, 'options' => collect(self::COVERINGS)->map(fn ($c) => ['label' => $c['label'], 'hint' => $c['hint'].' · YFK '.$c['poz']])->all()],
            ['type' => 'numbers', 'title' => 'Çatı ölçüleri', 'cols' => 3, 'fields' => [
                ['key' => 'length', 'label' => 'Bina uzunluğu', 'unit' => 'm', 'min' => 2, 'max' => 500, 'step' => 0.1, 'placeholder' => 'Örn. 20'],
                ['key' => 'width', 'label' => 'Bina genişliği', 'unit' => 'm', 'min' => 2, 'max' => 500, 'step' => 0.1, 'placeholder' => 'Örn. 12'],
                ['key' => 'slope', 'label' => 'Eğim', 'unit' => '%', 'min' => 0, 'max' => 120, 'step' => 1, 'placeholder' => '35', 'default' => 35],
                ['key' => 'eave', 'label' => 'Saçak payı', 'unit' => 'm', 'min' => 0, 'max' => 2, 'step' => 0.05, 'placeholder' => '0,5', 'default' => 0.5],
                ['key' => 'area', 'label' => 'Gerçek çatı alanı (biliyorsanız)', 'unit' => 'm²', 'min' => 5, 'max' => 100000, 'step' => 1, 'placeholder' => 'Boş bırakın'],
                ['key' => 'ridge', 'label' => 'Mahya / kırma hattı', 'unit' => 'm', 'min' => 0, 'max' => 2000, 'step' => 0.5, 'placeholder' => 'Boş bırakın'],
            ], 'help' => 'Uzunluk ve genişlik binanın dış ölçüleridir. Kırma çatıda mahya ve kırma hatlarının toplamını yazın.'],
            ['type' => 'cards', 'key' => 'form', 'title' => 'Çatı formu', 'short' => 'Çatı formu', 'required' => true, 'cols' => 4, 'options' => [
                'gable' => ['label' => 'Beşik', 'hint' => 'İki yöne eğimli'],
                'single' => ['label' => 'Tek eğimli', 'hint' => 'Sundurma'],
                'hip' => ['label' => 'Kırma', 'hint' => 'Dört yöne eğimli'],
                'other' => ['label' => 'Özel', 'hint' => 'Karma form'],
            ]],
            ['type' => 'cards', 'key' => 'underlay', 'title' => 'Alt kaplama', 'short' => 'Alt kaplama', 'required' => true, 'cols' => 3, 'options' => collect(self::UNDERLAYS)->map(fn ($c) => ['label' => $c['label'], 'hint' => $c['hint']])->all()],
            ['type' => 'cards', 'key' => 'membrane', 'title' => 'Çatı altı su yalıtımı', 'short' => 'Su yalıtımı', 'required' => true, 'cols' => 3, 'options' => collect(self::MEMBRANES)->map(fn ($c) => ['label' => $c['label'], 'hint' => $c['hint']])->all()],
        ];
    }

    /** @return array{area: float, ridge: float, ridge_estimated: bool} */
    private function geometry(array $in): array
    {
        $eave = (float) ($in['eave'] ?? 0.5);
        $l = ! empty($in['_given_length']) ? (float) $in['length'] : 0.0;
        $w = ! empty($in['_given_width']) ? (float) $in['width'] : 0.0;
        if (! empty($in['_given_area'])) {
            $area = (float) $in['area'];
        } elseif ($l > 0 && $w > 0) {
            $area = ($l + 2 * $eave) * ($w + 2 * $eave) * sqrt(1 + ((float) ($in['slope'] ?? 35) / 100) ** 2);
        } else {
            $area = 0.0;
        }
        if (! empty($in['_given_ridge']) && (float) $in['ridge'] > 0) {
            return ['area' => $area, 'ridge' => (float) $in['ridge'], 'ridge_estimated' => false];
        }
        $ridge = ($in['form'] ?? '') === 'gable' && $l > 0 ? $l + 2 * $eave : 0.0;

        return ['area' => $area, 'ridge' => $ridge, 'ridge_estimated' => $ridge > 0];
    }

    public function missing(array $in): array
    {
        $m = parent::missing($in);
        if ($this->geometry($in)['area'] <= 0) {
            $m[] = 'bina uzunluğu ve genişliği (veya gerçek çatı alanı)';
        }

        return $m;
    }

    protected function compute(array $in): array
    {
        $g = $this->geometry($in);
        $a = $g['area'];
        $cov = self::COVERINGS[$in['covering']];
        $p = $this->poz($cov['poz']);
        if (! $p) {
            return $this->blocked($cov['label'], $cov['poz']);
        }
        $items = ['ortu' => ['label' => $cov['label'].' · YFK '.$cov['poz'], 'mid' => $a * $p['price'], 'color' => '#FFB800']];
        $notes = [];
        if ($cov['ridge_poz'] && $g['ridge'] > 0) {
            $r = $this->poz($cov['ridge_poz']);
            if ($r) {
                $items['mahya'] = ['label' => 'Mahya kiremidi ('.self::n1($g['ridge']).' m) · YFK '.$cov['ridge_poz'], 'mid' => $g['ridge'] * $r['price'], 'color' => '#8A6200'];
            } else {
                $notes[] = 'Mahya kiremidi için resmî fiyat bulunamadı; bedele eklenmedi.';
            }
        } elseif ($cov['ridge_poz'] && in_array($in['form'], ['hip', 'other'], true)) {
            $notes[] = 'Kırma veya özel çatıda mahya bedeli için mahya / kırma hattı uzunluğunu girin.';
        }
        foreach (['underlay' => [self::UNDERLAYS, '#5B6472'], 'membrane' => [self::MEMBRANES, '#2F6F8F']] as $key => [$cat, $color]) {
            $sel = $cat[$in[$key]];
            if (! $sel['poz']) {
                continue;
            }
            if ($key === 'underlay' && ! $cov['underlay']) {
                $notes[] = 'Seçilen örtüde alt kaplama YFK pozunun dışında değerlendirilir; bedele eklenmedi.';
                continue;
            }
            $sp = $this->poz($sel['poz']);
            if (! $sp) {
                return $this->blocked($sel['label'], $sel['poz']);
            }
            $items[$key] = ['label' => $sel['label'].' · YFK '.$sel['poz'], 'mid' => $a * $sp['price'], 'color' => $color];
        }
        if ($g['ridge_estimated']) {
            $notes[] = 'Mahya uzunluğu beşik çatı için bina uzunluğundan tahmin edildi.';
        }
        $days = [max(3, (int) ceil($a / 80)), max(5, (int) ceil($a / 40))];

        return $this->withVat($items, ['per_unit' => $a, 'days' => $days, 'notes' => $notes,
            'meta' => [['Gerçek çatı alanı', self::n1($a).' m²'], ['Tahmini süre', $days[0].'–'.$days[1].' iş günü']]]);
    }

    public function materials(array $in): array
    {
        $g = $this->geometry($in);
        $a = $g['area'];
        $tile = str_starts_with($in['covering'], 'clay') || $in['covering'] === 'concrete_tile';
        $rows = [['Çatı alanı', self::n1($a), 'm²', 'Eğim dahil']];
        if ($tile) {
            $per = $in['covering'] === 'clay_alaturka' ? 22 : ($in['covering'] === 'concrete_tile' ? 10 : 14);
            $rows[] = ['Kiremit', number_format(ceil($a * $per * 1.05), 0, ',', '.'), 'adet', '≈'.$per.' adet/m² + %5'];
            $rows[] = ['Kiremit latası', (int) ceil($a * 3), 'm', 'Yaklaşık'];
        } else {
            $rows[] = ['Levha', self::n1($a * 1.12), 'm²', '%12 bindirme'];
        }
        if ($g['ridge'] > 0) {
            $rows[] = ['Mahya', self::n1($g['ridge']), 'm', $tile ? '≈3 mahya kiremidi / m' : 'Mahya profili'];
        }
        if ($in['underlay'] !== 'none') {
            $rows[] = [$in['underlay'] === 'osb' ? 'OSB/3 levha (122×244)' : 'Tahta kaplama', $in['underlay'] === 'osb' ? (int) ceil($a * 1.08 / 2.98) : self::n1($a * 1.08), $in['underlay'] === 'osb' ? 'levha' : 'm²', '%8 fire'];
        }
        if ($in['membrane'] !== 'none') {
            $rows[] = ['Çatı altı örtüsü', (int) ceil($a * 1.12), 'm²', '%12 bindirme'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return [['İskele ve güvenlik', .1], ['Alt kaplama', .2], ['Su yalıtımı', .15], ['Örtü ve lata', .4], ['Mahya, detaylar, temizlik', .15]];
    }
}

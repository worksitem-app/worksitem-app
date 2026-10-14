<?php

namespace App\Estimators;

class KitchenEstimator extends KitEstimator
{
    public function key(): string
    {
        return 'mutfak';
    }

    public function slug(): string
    {
        return 'mutfak-maliyet-hesaplayici';
    }

    private function p(): array
    {
        return (array) config('worksitem_estimators.mutfak');
    }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Mutfak tadilat maliyeti',
            'h1' => "Mutfak tadilat maliyeti hesaplama ($y)",
            'lede' => 'Dolap uzunluğunu, kapsamı, malzeme kalitesini ve tezgahı seçin; mutfak dolabı, tezgah, zemin, tesisat ve ekstralar kalem kalem hesaplansın. Sonuç kesin teklif değil, yaklaşık bir aralıktır.',
            'seo_title' => "Mutfak Tadilat Maliyeti Hesaplama $y: Dolap, Tezgah, Zemin | Worksitem",
            'seo_desc' => "$y mutfak tadilat fiyatları: dolap metresi, tezgah türü (laminat, granit, kuvars) ve kapsama göre mutfak yenileme maliyetini kalem kalem, ücretsiz hesaplayın.",
            'service' => 'Mutfak Tadilatı',
            'wait' => 'Dolap uzunluğunu girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Mutfağınız için markalar',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Kalem kalem döküm', '$ € £ karşılığı'],
            'method' => [
                'Dolap kalemi: alt + üst dolap mtül fiyatı × dolap uzunluğu (montaj dahil). Yalnız kapak yenilemede dolap kaleminin yaklaşık %45\'i alınır.',
                'Tezgah: seçilen tezgah türünün mtül fiyatı × dolap uzunluğu.',
                'Komple yenilemede kırım, yeni zemin kaplaması (zemin m²), su-gider-elektrik revizyonu ve boya eklenir.',
                'Seçilen ekstralar (ankastre set, boy dolabı, ada, LED) kaliteye göre eklenir.',
                'Ara toplama %10 beklenmedik gider payı eklenir; aralık en olası tutarın −%15 / +%20\'sidir.',
            ],
            'factors' => [
                ['Dolap malzemesi', 'Suntalamdan akrilik veya lake kapağa geçmek dolap kalemini yaklaşık 2,5 katına çıkarabilir.'],
                ['Tezgah türü', 'Laminat en uygun seçenektir; granit ve kuvars (çimstone) mtül fiyatını 3–4 kat artırır.'],
                ['Kapsam', 'Zemin kırımı ve tesisat revizyonu içeren komple yenileme, yalnız dolap değişimine göre belirgin pahalıdır.'],
                ['Üretim süresi', 'Ölçüye özel dolaplar genellikle 2–4 hafta üretim süresi ister; iş takvimini buna göre planlayın.'],
            ],
            'faq' => [
                ['Mutfak dolabının metresi ne kadar?', 'Montaj dahil alt + üst dolap, '.now()->year.' fiyatlarıyla yaklaşık '.number_format($this->p()['cabinet_m'][0], 0, ',', '.').' TL (suntalam) ile '.number_format($this->p()['cabinet_m'][2], 0, ',', '.').' TL (akrilik/lake) arasındadır. Marka, kapak kalınlığı ve donanım (menteşe, ray) fiyatı değiştirir.'],
                ['3,6 metre mutfak tadilatı kaç TL?', 'Orta kalite dolap, granit tezgah ve tezgah arası kaplama ile dolap + tezgah yenileme yaklaşık '.$this->exampleRange(['scope' => 'dolap', 'length' => 3.6, 'quality' => 'orta', 'counter' => 'granit', 'extras' => ['tezgah_arasi']]).' tutar; ankastre set ve komple yenileme bu tutarı artırır.'],
                ['Mutfak tadilatı kaç günde biter?', 'Yalnız kapak ve tezgah değişimi 3–5 iş günü, dolap ve tezgah yenileme 1–2 hafta, komple yenileme ise 3–5 hafta sürer; dolap üretim süresi buna eklenir.'],
                ['Hangi tezgah daha dayanıklı?', 'Kuvars (çimstone) ve porselen tezgahlar çizilmeye ve lekeye en dayanıklı olanlardır; granit doğal taştır ve sıcağa dayanıklıdır; laminat en ekonomik seçenektir.'],
                ['Hesaplanan aralığa neler dahil?', 'Malzeme, işçilik ve montaj dahildir; ankastre cihazlar yalnızca ekstralarda seçerseniz eklenir. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/banyo-maliyet-hesaplayici', 'Banyo tadilat maliyeti'], ['/araclar/seramik-maliyet-hesaplayici', 'Seramik metraj ve maliyet'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı'], ['/maliyet-hesapla', 'Tüm ev tadilat bütçesi']],
        ];
    }

    public function perUnitLabel(): ?string
    {
        return 'dolap metresi başına';
    }

    public function planner(): ?array
    {
        return [
            'url' => route('tools.kitchen_planner'),
            'label' => 'Mutfağımı 2D / 3D gör',
            'hint' => 'Dolap uzunluğunuz plana otomatik aktarılır; ücretsiz',
            'map' => ['length' => 'run', 'scope' => 'scope', 'quality' => 'quality', 'counter' => 'counter'],
            'checks' => ['extras' => ['boy_dolabi', 'ada']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Ne kadar değişecek?', 'short' => 'Kapsam', 'required' => true, 'cols' => 3, 'options' => [
                'kapak' => ['label' => 'Kapak ve tezgah', 'hint' => 'Dolap gövdesi kalır, kapak ve tezgah yenilenir'],
                'dolap' => ['label' => 'Dolap ve tezgah', 'hint' => 'Zemin ve tesisat aynı kalır'],
                'komple' => ['label' => 'Komple yenileme', 'hint' => 'Kırım, zemin, tesisat-elektrik, dolap, tezgah'],
            ]],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 2, 'fields' => [
                ['key' => 'length', 'label' => 'Alt dolap uzunluğu', 'unit' => 'm', 'min' => 1, 'max' => 15, 'step' => 0.1, 'placeholder' => 'Örn. 3,6', 'required' => true],
                ['key' => 'floor', 'label' => 'Mutfak zemin alanı', 'unit' => 'm²', 'min' => 3, 'max' => 60, 'step' => 0.5, 'placeholder' => 'Örn. 9', 'default' => 9],
            ], 'help' => 'L veya U mutfakta tüm alt dolap kollarını toplayın. Zemin alanı yalnız komple yenilemede kullanılır; bilmiyorsanız boş bırakın (9 m² alınır).'],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Dolap kalitesi', 'short' => 'Dolap kalitesi', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Suntalam gövde ve kapak'],
                'orta' => ['label' => 'Orta', 'hint' => 'MDFLam / membran kapak'],
                'premium' => ['label' => 'Premium', 'hint' => 'Akrilik veya lake kapak'],
            ]],
            ['type' => 'cards', 'key' => 'counter', 'title' => 'Tezgah', 'short' => 'Tezgah', 'required' => true, 'cols' => 4, 'options' => [
                'laminat' => ['label' => 'Laminat', 'hint' => 'En ekonomik'],
                'granit' => ['label' => 'Granit', 'hint' => 'Doğal taş'],
                'kuvars' => ['label' => 'Kuvars', 'hint' => 'Çimstone vb.'],
                'porselen' => ['label' => 'Porselen', 'hint' => 'İnce, dayanıklı'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => [
                'tezgah_arasi' => 'Tezgah arası kaplama',
                'ankastre' => 'Ankastre set (fırın, ocak, davlumbaz)',
                'boy_dolabi' => 'Boy dolabı',
                'ada' => 'Mutfak adası',
                'led' => 'Dolap altı LED aydınlatma',
            ]],
        ];
    }

    public function missing(array $in): array
    {
        return parent::missing($in);
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $len = (float) $in['length'];
        $floor = (float) ($in['floor'] ?? 9);
        $scope = $in['scope'];

        $cab = $len * $this->q($in, $p['cabinet_m']) * ($scope === 'kapak' ? $p['front_only_factor'] : 1);
        $items = [
            'sokum' => ['label' => 'Söküm ve moloz', 'mid' => $scope === 'kapak' ? 0 : $p['demolition']['fixed'] + $len * $p['demolition']['per_m'] + ($scope === 'komple' ? $floor * 250 : 0)],
            'dolap' => ['label' => $scope === 'kapak' ? 'Dolap kapakları ve montaj' : 'Alt + üst dolap ve montaj', 'mid' => $cab, 'color' => '#101318'],
            'tezgah' => ['label' => 'Tezgah ('.$this->steps()[3]['options'][$in['counter']]['label'].')', 'mid' => $len * $p['counter_m'][$in['counter']], 'color' => '#FFB800'],
        ];
        if ($scope === 'komple') {
            $items['zemin'] = ['label' => 'Zemin kırımı ve yeni kaplama', 'mid' => $floor * $this->q($in, $p['floor_m2']), 'color' => '#2F6F8F'];
            $items['tesisat'] = ['label' => 'Su, gider ve elektrik revizyonu', 'mid' => $p['mep']['fixed'] + $len * $p['mep']['per_m'], 'color' => '#7FB3C8'];
            $items['boya'] = ['label' => 'Duvar ve tavan boyası', 'mid' => $floor * $p['paint_m2'], 'color' => '#C9A55A'];
        }
        $ex = 0;
        $exLabels = [];
        if ($this->has($in, 'extras', 'tezgah_arasi')) {
            $items['tezgah_arasi'] = ['label' => 'Tezgah arası kaplama', 'mid' => $len * $this->q($in, $p['backsplash_m']), 'color' => '#8A6200'];
        }
        foreach (['ankastre', 'boy_dolabi', 'ada'] as $k) {
            if ($this->has($in, 'extras', $k)) {
                $ex += $this->q($in, $p['extras'][$k]);
            }
        }
        if ($this->has($in, 'extras', 'led')) {
            $ex += $p['extras']['led']['fixed'] + $len * $p['extras']['led']['per_m'];
        }
        if ($ex > 0) {
            $items['ekstra'] = ['label' => 'Seçilen ekstralar', 'mid' => $ex, 'color' => '#3F7D5B'];
        }

        $days = ['kapak' => [3, 5], 'dolap' => [7, 12], 'komple' => [15, 25]][$scope];

        return [
            'items' => $items,
            'per_unit' => $len,
            'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Dolap uzunluğu', self::n1($len).' m']],
            'notes' => $scope !== 'kapak' ? ['Ölçüye özel dolap üretimi için genellikle 2–4 hafta ayrıca beklenir.'] : [],
        ];
    }

    public function materials(array $in): array
    {
        $len = (float) $in['length'];
        $rows = [
            ['Alt dolap', self::n1($len), 'mtül', 'Kalite: '.$this->steps()[2]['options'][$in['quality']]['label']],
            ['Üst dolap', self::n1(max(0, $len - 0.6)), 'mtül', 'Davlumbaz bölümü düşülmüş yaklaşık'],
            ['Tezgah', self::n1(ceil($len * 10) / 10 + 0.1), 'mtül', $this->steps()[3]['options'][$in['counter']]['label'].' · birleşim ve kesim payı dahil'],
            ['Eviye ve batarya', 1, 'takım', 'Tezgah kesimine göre seçilir'],
            ['Kulp / menteşe / ray', (int) ceil($len / 0.6) * 2, 'adet', 'Yaklaşık kapak sayısı × 2'],
        ];
        if ($this->has($in, 'extras', 'tezgah_arasi')) {
            $rows[] = ['Tezgah arası kaplama', self::n1($len * 0.6 * 1.1), 'm²', '60 cm yükseklik + %10 fire'];
        }
        if (($in['scope'] ?? '') === 'komple') {
            $rows[] = ['Zemin kaplaması', self::n1((float) $in['floor'] * 1.1), 'm²', '%10 kesim firesi dahil'];
            $rows[] = ['Priz / elektrik hattı', 6, 'adet', 'Ankastre ve tezgah üstü prizler'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return match ($in['scope'] ?? 'dolap') {
            'kapak' => [['Ölçü ve eski kapak sökümü', .2], ['Kapak montajı', .4], ['Tezgah montajı ve silikon', .3], ['Temizlik ve teslim', .1]],
            'dolap' => [['Söküm ve hazırlık', .15], ['Dolap montajı', .4], ['Tezgah ölçü ve montajı', .25], ['Tezgah arası, cihaz bağlantıları', .15], ['Teslim', .05]],
            default => [['Söküm ve kırım', .1], ['Su, gider ve elektrik revizyonu', .15], ['Zemin kaplama', .2], ['Boya', .1], ['Dolap montajı', .25], ['Tezgah ve tezgah arası', .15], ['Cihazlar ve teslim', .05]],
        };
    }

    public function checklist(): array
    {
        return array_merge([
            'Dolap gövde ve kapak malzemesini (suntalam, MDFLam, akrilik, lake) ve kalınlığını teklifte yazdırın.',
            'Menteşe ve ray markasını sorun; frenli (yavaş kapanan) donanım standart mı öğrenin.',
            'Tezgah birleşim yerlerinin ve eviye kesiminin nasıl yapılacağını önceden konuşun.',
        ], array_slice(parent::checklist(), 0, 5));
    }
}

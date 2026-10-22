<?php

namespace App\Estimators;

class ParquetEstimator extends KitEstimator
{
    public function key(): string
    {
        return 'parke';
    }

    public function slug(): string
    {
        return 'parke-maliyet-hesaplayici';
    }

    public function perUnitLabel(): ?string
    {
        return 'm² başına';
    }

    private function p(): array
    {
        return (array) config('worksitem_estimators.parke');
    }

    private const TYPES = ['laminat' => 'Laminat parke', 'spc' => 'SPC / vinil parke', 'ahsap' => 'Lamine / masif ahşap'];

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Parke maliyeti',
            'h1' => "Parke döşeme maliyeti hesaplama ($y)",
            'lede' => 'Odanın veya evin alanını girin, parke türünü ve kalitesini seçin; parke, şilte, işçilik, süpürgelik ve fire kalem kalem hesaplansın. Kaç paket parke gerektiği Pro raporda.',
            'seo_title' => "Parke Döşeme Fiyatı $y: Laminat, SPC, Ahşap m² Maliyeti | Worksitem",
            'seo_desc' => "$y parke fiyatları: laminat, SPC ve lamine ahşap parkenin m² maliyetini şilte, süpürgelik, işçilik ve fire dahil kalem kalem, ücretsiz hesaplayın.",
            'service' => 'Parke Döşeme',
            'wait' => 'Alanı girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Parke markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Fire ve paket hesabı', '$ € £ karşılığı'],
            'method' => [
                'Parke miktarı = alan × (1 + fire). Düz odada %5, girintili/çıkıntılı odada %10 fire alınır.',
                'Parke ve şilte m² fiyatı türe ve kaliteye göre; işçilik türe göre m² başına eklenir.',
                'Süpürgelik uzunluğu alandan tahmin edilir (yaklaşık 4 × √alan; çok odalı evde 1,4 katı).',
                'Söküm, tesviye şapı, eşik profili ve eşya taşıma seçilirse eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Parke türü', 'SPC/vinil suya dayanıklıdır ve laminatın yaklaşık 1,5 katıdır; lamine ahşap 3–4 katına çıkar.'],
                ['Zemin düzgünlüğü', 'Bozuk zeminde tesviye şapı gerekir; atlanırsa parke esner ve ses yapar.'],
                ['Oda geometrisi', 'Girintili, kolonlu odalarda kesim ve fire artar.'],
                ['Aşınma sınıfı', 'AC4/AC5 laminat yoğun kullanılan alanlarda daha uzun ömürlüdür.'],
            ],
            'faq' => [
                ['100 m² parke döşeme kaç TL?', 'Orta kalite laminat, şilte, süpürgelik ve işçilik dahil '.now()->year.' fiyatlarıyla yaklaşık '.$this->exampleRange(['type' => 'laminat', 'area' => 100, 'rooms' => 'ev', 'quality' => 'orta', 'scope' => 'hazir', 'extras' => ['supurgelik']]).' tutar.'],
                ['Parke fire payı ne kadar olmalı?', 'Düz döşemede %5–7, girintili odalarda ve balıksırtı desende %10–15 fire alınır.'],
                ['Laminat mı, SPC mi?', 'Islanma riski olan mutfak ve antrede SPC/vinil, yatak odası ve salonda laminat yaygın tercihtir.'],
                ['Parke kaç günde döşenir?', 'Ustalı bir ekip günde 40–60 m² laminat döşer; 100 m² bir ev süpürgelikle birlikte 2–3 iş günü sürer.'],
            ],
            'related' => [['/araclar/zemin-kaplama', 'Zemin kaplama maliyeti'], ['/araclar/seramik-maliyet-hesaplayici', 'Seramik maliyeti'], ['/araclar/sap-hesaplayici', 'Şap hesaplayıcı'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'type', 'title' => 'Parke türü', 'short' => 'Parke türü', 'required' => true, 'cols' => 3, 'options' => [
                'laminat' => ['label' => 'Laminat', 'hint' => 'En yaygın, ekonomik'],
                'spc' => ['label' => 'SPC / vinil', 'hint' => 'Suya dayanıklı'],
                'ahsap' => ['label' => 'Lamine / masif ahşap', 'hint' => 'Doğal ahşap yüzey'],
            ]],
            ['type' => 'numbers', 'title' => 'Alan', 'cols' => 3, 'fields' => [
                ['key' => 'area_en', 'label' => 'En', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 4'],
                ['key' => 'area_boy', 'label' => 'Boy', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 5'],
                ['key' => 'area', 'label' => 'Alan (biliyorsanız)', 'from' => ['area_en', 'area_boy'], 'unit' => 'm²', 'min' => 2, 'max' => 3000, 'step' => 0.5, 'placeholder' => 'Örn. 24', 'required' => true, 'missing' => 'alan (en × boy ya da m²)'],
            ], 'help' => 'Birden fazla oda ise alanları toplayın (net m²).'],
            ['type' => 'cards', 'key' => 'rooms', 'title' => 'Oda yapısı', 'short' => 'Oda yapısı', 'required' => true, 'cols' => 3, 'options' => [
                'tek' => ['label' => 'Tek düz oda', 'hint' => '%5 fire'],
                'girintili' => ['label' => 'Girintili / kolonlu', 'hint' => '%10 fire'],
                'ev' => ['label' => 'Tüm ev (çok oda)', 'hint' => '%7 fire, daha çok süpürgelik'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Kalite', 'short' => 'Kalite', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'AC3, 8 mm'],
                'orta' => ['label' => 'Orta', 'hint' => 'AC4, 8–10 mm, bilinen marka'],
                'premium' => ['label' => 'Premium', 'hint' => 'AC5 / 12 mm, ithal'],
            ]],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Mevcut zemin', 'short' => 'Mevcut zemin', 'required' => true, 'cols' => 3, 'options' => [
                'hazir' => ['label' => 'Hazır', 'hint' => 'Düz şap veya seramik'],
                'sokum' => ['label' => 'Söküm var', 'hint' => 'Eski parke / halı sökülecek'],
                'tesviye' => ['label' => 'Bozuk zemin', 'hint' => 'Tesviye şapı gerekli'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => [
                'supurgelik' => 'Süpürgelik (malzeme + montaj)',
                'esik' => 'Eşik / geçiş profilleri',
                'esya' => 'Eşya taşıma ve koruma',
            ]],
        ];
    }

    private function waste(array $in): float
    {
        return ['tek' => 0.05, 'girintili' => 0.10, 'ev' => 0.07][$in['rooms']] ?? 0.07;
    }

    private function skirting(array $in): float
    {
        $per = 4 * sqrt((float) $in['area']);

        return ($in['rooms'] ?? '') === 'ev' ? $per * 1.4 : $per;
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $a = (float) $in['area'];
        $t = $in['type'];
        $w = $this->waste($in);
        $items = [
            'sokum' => ['label' => 'Eski kaplama sökümü', 'mid' => $in['scope'] === 'sokum' ? $a * $p['removal_m2'] : 0, 'color' => '#5B6472'],
            'sap' => ['label' => 'Tesviye şapı', 'mid' => $in['scope'] === 'tesviye' ? $a * $p['screed_m2'] : 0, 'color' => '#2F6F8F'],
            'parke' => ['label' => self::TYPES[$t].' (%'.round($w * 100).' fire dahil)', 'mid' => $a * (1 + $w) * $this->q($in, $p['material_m2'][$t]), 'color' => '#FFB800'],
            'silte' => ['label' => 'Şilte / altlık', 'mid' => $t === 'spc' ? 0 : $a * $this->q($in, $p['underlay_m2']), 'color' => '#7FB3C8'],
            'iscilik' => ['label' => 'Döşeme işçiliği', 'mid' => $a * $p['labor_m2'][$t], 'color' => '#101318'],
            'supurgelik' => ['label' => 'Süpürgelik', 'mid' => $this->has($in, 'extras', 'supurgelik') ? $this->skirting($in) * $p['skirting_m'] : 0, 'color' => '#8A6200'],
            'ekstra' => ['label' => 'Eşik profili ve eşya taşıma', 'mid' => ($this->has($in, 'extras', 'esik') ? max(1, round($a / 15)) * $p['threshold'] : 0) + ($this->has($in, 'extras', 'esya') ? $p['furniture'] * ($a > 40 ? 2 : 1) : 0), 'color' => '#C9A55A'],
        ];
        $days = [max(1, (int) ceil($a / 60)), max(1, (int) ceil($a / 35))];
        if ($in['scope'] === 'tesviye') {
            $days = [$days[0] + 2, $days[1] + 4];
        }

        return [
            'items' => $items,
            'per_unit' => $a,
            'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Parke miktarı', self::n1($a * (1 + $w)).' m²']],
            'notes' => $in['scope'] === 'tesviye' ? ['Şap kuruması için döşemeden önce en az 7–10 gün beklenir.'] : [],
        ];
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];
        $need = $a * (1 + $this->waste($in));
        $pack = ($in['type'] ?? '') === 'ahsap' ? 1.8 : 2.2;
        $rows = [
            [self::TYPES[$in['type']], self::n1($need), 'm²', 'Fire dahil'],
            ['Parke paketi', (int) ceil($need / $pack), 'paket', 'Paket başına yaklaşık '.self::n1($pack).' m² (markaya göre değişir)'],
        ];
        if (($in['type'] ?? '') !== 'spc') {
            $rows[] = ['Şilte', (int) ceil($a * 1.05), 'm²', 'Birleşim payı dahil'];
        }
        if ($this->has($in, 'extras', 'supurgelik')) {
            $sk = $this->skirting($in);
            $rows[] = ['Süpürgelik', (int) ceil($sk * 1.1 / 2.4), 'boy (2,4 m)', self::n1($sk).' m + %10 kesim'];
        }
        if ($this->has($in, 'extras', 'esik')) {
            $rows[] = ['Eşik / geçiş profili', (int) max(1, round($a / 15)), 'adet', 'Kapı geçişleri'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([
            ($in['scope'] ?? '') === 'sokum' ? ['Söküm', .2] : null,
            ($in['scope'] ?? '') === 'tesviye' ? ['Tesviye şapı ve kuruma', .45] : null,
            ['Şilte ve parke döşeme', .45],
            $this->has($in, 'extras', 'supurgelik') ? ['Süpürgelik ve profiller', .2] : null,
            ['Temizlik ve teslim', .1],
        ]));
    }

    public function visuals(): array
    {
        return self::openVisuals([
            ['oda-planlayici', route('tools.room'), 'Odanızı yeni zeminle görün', 'Fotoğrafını yükleyin; yapay zekâ odanızı seçtiğiniz stilde yeniden tasarlasın.'],
        ]);
    }
}

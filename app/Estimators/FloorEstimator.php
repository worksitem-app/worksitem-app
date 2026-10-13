<?php

namespace App\Estimators;

class FloorEstimator extends KitEstimator
{
    public function key(): string
    {
        return 'zemin';
    }

    public function slug(): string
    {
        return 'zemin-kaplama';
    }

    public function perUnitLabel(): ?string
    {
        return 'm² başına';
    }

    private function p(): array
    {
        return (array) config('worksitem_estimators.zemin');
    }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Zemin kaplama maliyeti',
            'h1' => "Zemin kaplama maliyeti hesaplama ($y)",
            'lede' => 'Laminat, SPC, seramik, mermer veya ahşap: zemin alanını ve kaplama türünü seçin, mevcut zemine göre söküm ve tesviye dahil maliyet kalem kalem hesaplansın. Kaplamaları birbiriyle karşılaştırmak için türü değiştirmeniz yeterli.',
            'seo_title' => "Zemin Kaplama Fiyatları $y: Laminat, Seramik, Mermer m² Maliyeti | Worksitem",
            'seo_desc' => "$y zemin kaplama fiyatı: laminat, SPC, seramik, mermer ve ahşap zemin kaplamanın m² maliyetini söküm, şap, süpürgelik ve işçilik dahil ücretsiz hesaplayın.",
            'service' => 'Zemin Kaplama',
            'wait' => 'Alanı girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Zemin kaplama markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', '5 kaplama türü', '$ € £ karşılığı'],
            'method' => [
                'Malzeme = alan × (1 + türe göre fire) × m² fiyatı × kalite çarpanı.',
                'Seramik ve mermerde yapıştırıcı/harç ayrıca, işçilik türe göre m² başına eklenir.',
                'Mevcut kaplama sökülecekse yumuşak (parke, halı) veya sert (seramik, mermer) söküm fiyatı uygulanır.',
                'Tesviye şapı ve süpürgelik seçilirse eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Kaplama türü', 'Mermer/granit m² maliyeti laminatın 4–5 katıdır; işçilik de daha uzun sürer.'],
                ['Söküm', 'Eski seramik veya mermerin kırılması parke sökümünden üç kat pahalıdır ve moloz çıkarır.'],
                ['Zemin düzgünlüğü', 'Parke ve SPC için zemin 2–3 mm toleransla düz olmalıdır; değilse şap gerekir.'],
                ['Kullanım alanı', 'Islak hacimde parke yerine seramik veya SPC seçmek uzun ömür sağlar.'],
            ],
            'faq' => [
                ['En ucuz zemin kaplama hangisi?', 'Malzeme ve işçilik toplamında laminat parke en ekonomik seçenektir; 20 m² bir oda için orta kalitede yaklaşık '.$this->exampleRange(['type' => 'laminat', 'area' => 20, 'quality' => 'orta', 'scope' => 'hazir']).' tutar.'],
                ['Seramik üzerine parke döşenir mi?', 'Seramik düz ve sağlamsa şilte ile laminat veya SPC doğrudan döşenebilir; derz çukurları büyükse ince tesviye önerilir.'],
                ['Mermer zemin m² fiyatı ne kadar?', 'Malzeme, harç ve işçilik dahil orta kalite mermer için '.now()->year.' fiyatlarıyla m² başına yaklaşık '.$this->exampleRange(['type' => 'mermer', 'area' => 30, 'quality' => 'orta', 'scope' => 'hazir'], true).' arasıdır.'],
                ['Hesaba neler dahil?', 'Malzeme, fire, yapıştırıcı (seramik/mermer), işçilik; seçerseniz söküm, şap ve süpürgelik. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/parke-maliyet-hesaplayici', 'Parke maliyeti'], ['/araclar/seramik-maliyet-hesaplayici', 'Seramik maliyeti'], ['/araclar/epoksi-zemin-hesaplayici', 'Epoksi zemin'], ['/araclar/sap-hesaplayici', 'Şap hesaplayıcı']],
        ];
    }

    public function steps(): array
    {
        $types = [];
        foreach ($this->p()['types'] as $k => $t) {
            $types[$k] = ['label' => $t['label'], 'hint' => '≈ %'.round($t['waste'] * 100).' fire'];
        }

        return [
            ['type' => 'cards', 'key' => 'type', 'title' => 'Kaplama türü', 'short' => 'Kaplama türü', 'required' => true, 'cols' => 3, 'options' => $types],
            ['type' => 'numbers', 'title' => 'Alan', 'cols' => 1, 'fields' => [
                ['key' => 'area', 'label' => 'Zemin alanı', 'unit' => 'm²', 'min' => 1, 'max' => 5000, 'step' => 0.5, 'placeholder' => 'Örn. 30', 'required' => true],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Kalite', 'short' => 'Kalite', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Yerli, giriş seviyesi'],
                'orta' => ['label' => 'Orta', 'hint' => 'Bilinen marka'],
                'premium' => ['label' => 'Premium', 'hint' => 'İthal / büyük ebat / masif'],
            ]],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Mevcut zemin', 'short' => 'Mevcut zemin', 'required' => true, 'cols' => 4, 'options' => [
                'hazir' => ['label' => 'Hazır', 'hint' => 'Düz şap'],
                'yumusak' => ['label' => 'Parke / halı var', 'hint' => 'Sökülecek'],
                'sert' => ['label' => 'Seramik / mermer var', 'hint' => 'Kırılacak'],
                'tesviye' => ['label' => 'Bozuk zemin', 'hint' => 'Tesviye şapı'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['supurgelik' => 'Süpürgelik', 'sap' => 'Ek tesviye şapı (söküm sonrası)']],
        ];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $t = $p['types'][$in['type']];
        $a = (float) $in['area'];
        $qf = $this->q($in, $p['quality_factor']);
        $screed = $in['scope'] === 'tesviye' || ($this->has($in, 'extras', 'sap') && in_array($in['scope'], ['yumusak', 'sert'], true));
        $items = [
            'sokum' => ['label' => 'Eski kaplama sökümü', 'mid' => in_array($in['scope'], ['yumusak', 'sert'], true) ? $a * $p['removal_m2'][$in['scope']] : 0, 'color' => '#5B6472'],
            'sap' => ['label' => 'Tesviye şapı', 'mid' => $screed ? $a * $p['screed_m2'] : 0, 'color' => '#2F6F8F'],
            'malzeme' => ['label' => $t['label'].' (%'.round($t['waste'] * 100).' fire dahil)', 'mid' => $a * (1 + $t['waste']) * $t['material'] * $qf, 'color' => '#FFB800'],
            'harc' => ['label' => 'Yapıştırıcı, derz / harç', 'mid' => $a * ($t['adhesive_m2'] ?? 0), 'color' => '#8A6200'],
            'iscilik' => ['label' => 'Döşeme işçiliği', 'mid' => $a * $t['labor'], 'color' => '#101318'],
            'supurgelik' => ['label' => 'Süpürgelik', 'mid' => $this->has($in, 'extras', 'supurgelik') ? 4 * sqrt($a) * $p['skirting_m'] : 0, 'color' => '#C9A55A'],
        ];
        $speed = in_array($in['type'], ['laminat', 'spc'], true) ? 50 : (in_array($in['type'], ['seramik'], true) ? 20 : 12);
        $days = [max(1, (int) ceil($a / ($speed * 1.4))), max(1, (int) ceil($a / $speed))];
        if ($screed) {
            $days = [$days[0] + 2, $days[1] + 4];
        }

        return [
            'items' => $items,
            'per_unit' => $a,
            'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Malzeme miktarı', self::n1($a * (1 + $t['waste'])).' m²']],
        ];
    }

    public function materials(array $in): array
    {
        $t = $this->p()['types'][$in['type']];
        $a = (float) $in['area'];
        $rows = [[$t['label'], self::n1($a * (1 + $t['waste'])), 'm²', 'Alan '.self::n1($a).' m² + %'.round($t['waste'] * 100).' fire']];
        if (! empty($t['adhesive_m2'])) {
            $rows[] = ['Yapıştırıcı (25 kg)', (int) ceil($a * 5 / 25), 'torba', 'Yaklaşık 5 kg/m²'];
            $rows[] = ['Derz (5 kg)', (int) max(1, ceil($a * 0.5 / 5)), 'paket', 'Yaklaşık 0,5 kg/m²'];
        } elseif ($in['type'] !== 'spc') {
            $rows[] = ['Şilte / altlık', (int) ceil($a * 1.05), 'm²', 'Birleşim payı dahil'];
        }
        if ($this->has($in, 'extras', 'supurgelik')) {
            $rows[] = ['Süpürgelik', (int) ceil(4 * sqrt($a) * 1.1 / 2.4), 'boy (2,4 m)', '%10 kesim dahil'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([
            in_array($in['scope'] ?? '', ['yumusak', 'sert'], true) ? ['Söküm ve moloz', .2] : null,
            ($in['scope'] ?? '') === 'tesviye' || $this->has($in, 'extras', 'sap') ? ['Şap ve kuruma', .35] : null,
            ['Döşeme', .45],
            ['Süpürgelik, temizlik, teslim', .15],
        ]));
    }
}

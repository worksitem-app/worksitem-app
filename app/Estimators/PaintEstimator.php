<?php

namespace App\Estimators;

use App\Estimators\Concerns\DbPrices;

class PaintEstimator extends KitEstimator
{
    use DbPrices;

    private const T = 'boya-hesaplayici';

    public function key(): string
    {
        return 'boya';
    }

    public function slug(): string
    {
        return 'boya-hesaplayici';
    }

    public function pricesLabel(): string
    {
        return $this->reviewedLabel();
    }

    private function p(): array
    {
        return (array) config('worksitem_estimators.boya');
    }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Boya hesaplayıcı',
            'h1' => "Ev boyama maliyeti ve boya hesaplama ($y)",
            'lede' => 'Odanın veya evin zemin alanını girin, renk değişimini ve duvarların durumunu seçin; boya, astar, macun ve ustalık kalem kalem hesaplansın. Kaç kova boya gerektiği Pro raporda.',
            'seo_title' => "Boya Hesaplama $y: Ev Boyama Fiyatı, Kaç Kova Boya Gider? | Worksitem",
            'seo_desc' => "$y ev boyama fiyatı: daire veya oda m², renk değişimi, duvar durumu ve boya kalitesine göre boya, astar, macun ve işçilik dahil maliyeti ücretsiz hesaplayın.",
            'service' => 'Boya Badana',
            'wait' => 'Zemin alanını girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Boya markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Kova ve litre hesabı', '$ € £ karşılığı'],
            'method' => [
                'Duvar alanı biliniyorsa o kullanılır; bilinmiyorsa tek odada 4 × √zemin × yükseklik, dairede zemin × 2,7 (2,6 m tavan) alınır.',
                'Boya miktarı = boyanacak alan × kat sayısı ÷ 11 m²/L. Renk değişiminde astar, koyudan açığa geçişte 3. kat eklenir.',
                'Macun miktarı duvar durumuna göre (sağlam / lokal tamir / tam macun) m² başına 0,05–0,8 kg alınır.',
                'Ustalık m² fiyatı 2 kat ve standart hazırlık içindir; 3 kat ve kötü duvarda artırılır. Tavan ayrıca hesaplanır.',
                'Bant, örtü, rulo gibi sarf malzemesi ve %10 beklenmedik gider payı eklenir; aralık −%15 / +%20\'dir.',
            ],
            'factors' => [
                ['Renk değişimi', 'Koyu bir renkten açığa geçiş astar ve 3. kat ister; maliyeti yaklaşık %30 artırır.'],
                ['Duvar durumu', 'Kabaran, çatlak ya da nem lekeli duvarlarda tam macun ve zımpara, işçiliği %50 artırabilir.'],
                ['Boya kalitesi', 'Silinebilir premium boya ekonomik boyanın yaklaşık 2,5 katıdır ama daha iyi örter ve uzun ömürlüdür.'],
                ['Eşyalı ev', 'Mobilyaların taşınması ve korunması ek işçilik ve süre demektir.'],
            ],
            'faq' => [
                ['100 m² ev boyası kaç TL?', 'Aynı renkle, sağlam duvarlarda, duvar + tavan ve orta kalite boya ile '.now()->year.' fiyatlarıyla malzeme ve ustalık dahil yaklaşık '.$this->exampleRange(['target' => 'daire', 'floor' => 100, 'surfaces' => 'duvar_tavan', 'color' => 'ayni', 'state' => 'iyi', 'quality' => 'orta']).' arasıdır. Renk değişimi ve duvar tamiri bu tutarı artırır.'],
                ['1 kova boya kaç m² boyar?', '15 litrelik bir kova tek katta yaklaşık 150–170 m², iki katta 75–85 m² duvar boyar. Emicilik ve renk farkı bu değeri düşürür.'],
                ['Boyadan önce astar şart mı?', 'Renk değişiminde, yeni sıvada ve macunlanmış yüzeylerde astar önerilir; aynı renk yenilemede çoğu zaman gerekmez.'],
                ['Daire boyası kaç günde biter?', 'Eşyasız 100 m² bir daire 3–5 iş gününde, eşyalı ve tamirli bir daire 5–8 iş gününde boyanır.'],
            ],
            'related' => [['/araclar/duvar-rengi-gorsellestirici', 'Duvar rengini fotoğrafta dene'], ['/araclar/alcipan-hesaplayici', 'Alçıpan hesaplayıcı'], ['/araclar/seramik-maliyet-hesaplayici', 'Seramik maliyeti'], ['/maliyet-hesapla', 'Tüm ev tadilat bütçesi']],
        ];
    }

    public function perUnitLabel(): ?string
    {
        return 'zemin m² başına';
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'target', 'title' => 'Neresi boyanacak?', 'short' => 'Boyanacak yer', 'required' => true, 'cols' => 3, 'options' => [
                'oda' => ['label' => 'Tek oda', 'hint' => 'Yatak odası, salon vb.'],
                'daire' => ['label' => 'Daire / ev', 'hint' => 'Tüm odalar, koridor dahil'],
                'ticari' => ['label' => 'Ofis / dükkân', 'hint' => 'Açık alan, az bölme'],
            ]],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 3, 'fields' => [
                ['key' => 'floor', 'label' => 'Zemin alanı', 'unit' => 'm²', 'min' => 4, 'max' => 3000, 'step' => 1, 'placeholder' => 'Örn. 100', 'required' => true],
                ['key' => 'height', 'label' => 'Yükseklik', 'unit' => 'm', 'min' => 2.2, 'max' => 6, 'step' => 0.1, 'placeholder' => '2,6', 'default' => 2.6],
                ['key' => 'wall', 'label' => 'Duvar alanı (biliyorsanız)', 'unit' => 'm²', 'min' => 5, 'max' => 10000, 'step' => 1, 'placeholder' => 'Boş bırakın'],
            ], 'help' => 'Daire için net m² yazın (tapudaki brüt değil). Duvar alanını bilmiyorsanız boş bırakın; zemin alanından tahmin edilir.'],
            ['type' => 'cards', 'key' => 'surfaces', 'title' => 'Hangi yüzeyler?', 'short' => 'Yüzeyler', 'required' => true, 'cols' => 2, 'options' => [
                'duvar' => ['label' => 'Sadece duvarlar', 'hint' => 'Tavan boyanmayacak'],
                'duvar_tavan' => ['label' => 'Duvar + tavan', 'hint' => 'Tavan için ayrı tavan boyası'],
            ]],
            ['type' => 'cards', 'key' => 'color', 'title' => 'Renk değişimi', 'short' => 'Renk değişimi', 'required' => true, 'cols' => 3, 'options' => [
                'ayni' => ['label' => 'Aynı / yakın renk', 'hint' => '2 kat, astarsız'],
                'degisim' => ['label' => 'Renk değişiyor', 'hint' => 'Astar + 2 kat'],
                'koyu_acik' => ['label' => 'Koyudan açığa', 'hint' => 'Astar + 3 kat'],
            ]],
            ['type' => 'cards', 'key' => 'state', 'title' => 'Duvarların durumu', 'short' => 'Duvar durumu', 'required' => true, 'cols' => 3, 'options' => [
                'iyi' => ['label' => 'Sağlam', 'hint' => 'Hafif zımpara yeter'],
                'orta' => ['label' => 'Çatlak, delik var', 'hint' => 'Lokal macun'],
                'kotu' => ['label' => 'Kabarma, nem lekesi', 'hint' => 'Tam macun + zımpara'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Boya kalitesi', 'short' => 'Boya kalitesi', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Plastik iç cephe'],
                'orta' => ['label' => 'Orta', 'hint' => 'Silinebilir, mat'],
                'premium' => ['label' => 'Premium', 'hint' => 'Leke tutmaz, yüksek örtücü'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => [
                'furniture' => 'Eşyalı ev: taşıma ve koruma',
                'door' => 'Kapı ve kasa boyası',
                'radiator' => 'Petek (radyatör) boyası',
                'plaster' => 'Alçı sıva tamiri',
            ]],
        ];
    }

    /** @return array{0: float, 1: float} [duvar alanı, tavan alanı] */
    private function areas(array $in): array
    {
        $floor = (float) $in['floor'];
        $h = (float) ($in['height'] ?? 2.6);
        if (! empty($in['_given_wall'])) {
            $wall = (float) $in['wall'];
        } elseif (($in['target'] ?? '') === 'oda') {
            $wall = max(0, 4 * sqrt($floor) * $h - 3.5);   // kapı + pencere payı
        } else {
            $factor = ($in['target'] ?? '') === 'ticari' ? 1.6 : $this->p()['wall_factor'];
            $wall = $floor * $factor * $h / 2.6;
        }
        $ceiling = ($in['surfaces'] ?? '') === 'duvar_tavan' ? $floor : 0.0;

        return [round($wall, 1), $ceiling];
    }

    private function coats(array $in): int
    {
        return ($in['color'] ?? '') === 'koyu_acik' ? 3 : 2;
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        [$wall, $ceil] = $this->areas($in);
        $coats = $this->coats($in);
        $cov = $p['coverage_m2_per_l'];
        $qf = $this->q($in, $p['quality_factor']);

        $wallPaintL = $wall * $coats / $cov;
        $ceilPaintL = $ceil * 2 / 10;
        $primerL = ($in['color'] ?? '') !== 'ayni' ? ($wall + $ceil) / 8 : 0;
        $puttyKg = ($wall + $ceil) * ['iyi' => 0.05, 'orta' => 0.12, 'kotu' => 0.8][$in['state']];

        $wallPrice = $this->toolPrice(self::T, 'wall_paint', $p['wall_paint_15l']) / 15;
        $ceilPrice = $this->toolPrice(self::T, 'ceiling_paint', $p['ceiling_paint_10l']) / 10;
        $primerPrice = $this->toolPrice(self::T, 'primer', $p['primer_15l']) / 15;
        $puttyPrice = $this->toolPrice(self::T, 'putty', $p['putty_4_5kg']) / 4.5;

        $base = $this->setting('paint_labor_standard_m2', $p['labor_m2']);
        if ($wall + $ceil > $this->setting('paint_labor_large_threshold', 500)) {
            $base = $this->setting('paint_labor_large_base_m2', 175);
        }
        $laborF = ($coats === 3 ? 1.3 : 1.0) * ['iyi' => 1.0, 'orta' => 1.15, 'kotu' => 1.5][$in['state']] * ($this->has($in, 'extras', 'furniture') ? 1.1 : 1.0);
        $doors = ($in['target'] ?? '') === 'oda' ? 1 : max(3, (int) round((float) $in['floor'] / 20));
        $rads = ($in['target'] ?? '') === 'oda' ? 1 : max(2, (int) round((float) $in['floor'] / 18));

        $extra = 0;
        $extra += $this->has($in, 'extras', 'furniture') ? $p['extras']['furniture'] * (($in['target'] ?? '') === 'oda' ? 0.4 : 1) : 0;
        $extra += $this->has($in, 'extras', 'door') ? $doors * $p['extras']['door'] : 0;
        $extra += $this->has($in, 'extras', 'radiator') ? $rads * $p['extras']['radiator'] : 0;

        $items = [
            'macun' => ['label' => 'Macun, zımpara ve hazırlık malzemesi', 'mid' => $puttyKg * $puttyPrice + ($wall + $ceil) * 4, 'color' => '#5B6472'],
            'alci' => ['label' => 'Alçı sıva tamiri', 'mid' => $this->has($in, 'extras', 'plaster') ? $wall * 0.1 * $p['extras']['plaster_m2'] : 0, 'color' => '#2F6F8F'],
            'astar' => ['label' => 'Astar', 'mid' => $primerL * $primerPrice, 'color' => '#7FB3C8'],
            'boya' => ['label' => 'Duvar boyası ('.$coats.' kat)', 'mid' => $wallPaintL * $wallPrice * $qf, 'color' => '#FFB800'],
            'tavan' => ['label' => 'Tavan boyası', 'mid' => $ceilPaintL * $ceilPrice, 'color' => '#C9A55A'],
            'iscilik' => ['label' => 'Ustalık (işçilik)', 'mid' => ($wall + $ceil * 1.1) * $base * $laborF, 'color' => '#101318'],
            'sarf' => ['label' => 'Bant, örtü, rulo, fırça', 'mid' => 600 + ($wall + $ceil) * 6, 'color' => '#8A6200'],
            'ekstra' => ['label' => 'Seçilen ekstralar', 'mid' => $extra, 'color' => '#3F7D5B'],
        ];
        $total = $wall + $ceil;
        $days = [max(1, (int) ceil($total / 120)), max(2, (int) ceil($total / 70))];
        if ($in['state'] === 'kotu') {
            $days = [$days[0] + 1, $days[1] + 2];
        }

        return [
            'items' => $items,
            'per_unit' => (float) $in['floor'],
            'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Boyanacak alan', self::n1($total).' m²']],
            'notes' => empty($in['_given_wall']) ? ['Duvar alanı zemin alanından tahmin edildi ('.self::n1($wall).' m²).'] : [],
        ];
    }

    public function materials(array $in): array
    {
        $p = $this->p();
        [$wall, $ceil] = $this->areas($in);
        $coats = $this->coats($in);
        $wallL = $wall * $coats / $p['coverage_m2_per_l'];
        $rows = [
            ['İç cephe boyası', (int) ceil($wallL / 15), 'kova (15 L)', self::n1($wallL).' L · '.$coats.' kat · 11 m²/L'],
        ];
        if ($ceil > 0) {
            $rows[] = ['Tavan boyası', (int) ceil($ceil * 2 / 10 / 10), 'kova (10 L)', self::n1($ceil * 2 / 10).' L · 2 kat'];
        }
        if (($in['color'] ?? '') !== 'ayni') {
            $rows[] = ['Astar', (int) ceil(($wall + $ceil) / 8 / 15), 'kova (15 L)', 'Yaklaşık 8 m²/L'];
        }
        $putty = ($wall + $ceil) * ['iyi' => 0.05, 'orta' => 0.12, 'kotu' => 0.8][$in['state']];
        $rows[] = ['Duvar macunu (4,5 kg)', (int) max(1, ceil($putty / 4.5)), 'paket', self::n1($putty).' kg'];
        $rows[] = ['Maskeleme bandı (25 m)', (int) max(2, ceil(($wall + $ceil) / 40)), 'rulo', 'Pervaz, priz, süpürgelik'];
        $rows[] = ['Koruma örtüsü (10 m²)', (int) max(2, ceil((float) $in['floor'] / 10)), 'adet', 'Zemin ve eşya koruması'];
        $rows[] = ['Rulo, tava, fırça', 1, 'set', 'Usta getiriyorsa gerekmez'];

        return $rows;
    }

    public function phases(array $in): array
    {
        return [
            ['Eşya koruma ve bantlama', .1],
            ['Macun, zımpara, tamir', ($in['state'] ?? '') === 'kotu' ? .3 : .15],
            ['Astar ve tavan', .2],
            ['Duvar boyası katları', .4],
            ['Rötuş, temizlik, teslim', .1],
        ];
    }

    public function checklist(): array
    {
        return array_merge([
            'Boyanın marka, seri ve renk kodunu teklifte yazdırın; usta boyası mı, sizin boyanız mı netleştirin.',
            'Kaç kat boya ve astar uygulanacağını yazılı alın.',
            'Nem lekesi varsa önce nem kaynağı çözülmeli; yoksa boya kısa sürede kabarır.',
        ], array_slice(parent::checklist(), 2, 4));
    }
}

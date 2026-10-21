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
            'lede' => 'Oda, daire ya da bina dış cephesi: ölçüyü girin, renk değişimini ve duvarların durumunu seçin; boya, astar, macun, ustalık ve gerekiyorsa iskele kalem kalem hesaplansın. Kaç kova boya gerektiği Pro raporda.',
            'seo_title' => "Boya Hesaplama $y: Ev Boyama Fiyatı, Kaç Kova Boya Gider? | Worksitem",
            'seo_desc' => "$y ev boyama fiyatı: daire veya oda m², renk değişimi, duvar durumu ve boya kalitesine göre boya, astar, macun ve işçilik dahil maliyeti ücretsiz hesaplayın.",
            'service' => 'Boya Badana',
            'wait' => 'Zemin alanını girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Boya markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'İç ve dış cephe, iskele dahil', 'Kova ve litre hesabı'],
            'method' => [
                'Duvar alanı biliniyorsa o kullanılır; bilinmiyorsa tek odada 4 × √zemin × yükseklik, dairede zemin × 2,7 (2,6 m tavan) alınır.',
                'Boya miktarı = boyanacak alan × kat sayısı ÷ 11 m²/L. Renk değişiminde astar, koyudan açığa geçişte 3. kat eklenir.',
                'Macun miktarı duvar durumuna göre (sağlam / lokal tamir / tam macun) m² başına 0,05–0,8 kg alınır.',
                'Ustalık m² fiyatı 2 kat ve standart hazırlık içindir; 3 kat ve kötü duvarda artırılır. Tavan ayrıca hesaplanır.',
                'Bant, örtü, rulo gibi sarf malzemesi ve %10 beklenmedik gider payı eklenir; aralık −%15 / +%20\'dir.',
                'Dış cephede alan, bina çevresi × kat × 3 m − %15 pencere payıdır; boya 8 m²/L, astar her zaman uygulanır. İskele brüt cephe üzerinden resmî iskele birim fiyatı (51 m üstü ayrı) ve nakliye ile eklenir.',
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
                ['Dış cephe boyası m² fiyatı ne kadar?', 'Silikonlu boya, astar, ustalık ve iskele dahil 5 katlı bir binada '.now()->year.' fiyatlarıyla cephe m² başına yaklaşık '.$this->exampleRange(['target' => 'dis_cephe', 'perimeter' => 60, 'floors' => 5, 'color' => 'ayni', 'state' => 'orta', 'quality' => 'orta', 'access' => 'iskele'], true).' arasıdır. İskele toplamın önemli bir kısmını oluşturur.'],
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
            ['type' => 'cards', 'key' => 'target', 'title' => 'Neresi boyanacak?', 'short' => 'Boyanacak yer', 'required' => true, 'cols' => 2, 'options' => [
                'oda' => ['label' => 'Tek oda', 'hint' => 'Yatak odası, salon vb.'],
                'daire' => ['label' => 'Daire / ev', 'hint' => 'Tüm odalar, koridor dahil'],
                'ticari' => ['label' => 'Ofis / dükkân', 'hint' => 'Açık alan, az bölme'],
                'dis_cephe' => ['label' => 'Dış cephe', 'hint' => 'Bina cephesi, iskele ile'],
            ]],
            ['type' => 'numbers', 'title' => 'Cephe ölçüleri', 'cols' => 3, 'show' => ['target' => ['dis_cephe']], 'required_any' => 'cephe alanı veya bina çevresi ve kat sayısı', 'fields' => [
                ['key' => 'facade', 'label' => 'Boyanacak cephe alanı (biliyorsanız)', 'unit' => 'm²', 'min' => 10, 'max' => 50000, 'step' => 1, 'placeholder' => 'Örn. 650'],
                ['key' => 'perimeter', 'label' => 'Bina çevresi', 'unit' => 'm', 'min' => 8, 'max' => 2000, 'step' => 1, 'placeholder' => 'Örn. 60'],
                ['key' => 'floors', 'label' => 'Kat sayısı', 'unit' => 'kat', 'min' => 1, 'max' => 40, 'step' => 1, 'placeholder' => 'Örn. 5'],
            ], 'help' => 'Cephe alanını bilmiyorsanız bina çevresini ve kat sayısını yazın; kat yüksekliği 3 m ve pencere payı %15 alınır. Yalnız bir cepheyi boyatacaksanız o cephenin genişliğini çevre olarak yazın.'],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 3, 'hide' => ['target' => ['dis_cephe']], 'fields' => [
                ['key' => 'floor', 'label' => 'Zemin alanı', 'unit' => 'm²', 'min' => 4, 'max' => 3000, 'step' => 1, 'placeholder' => 'Örn. 100', 'required' => true],
                ['key' => 'height', 'label' => 'Yükseklik', 'unit' => 'm', 'min' => 2.2, 'max' => 6, 'step' => 0.1, 'placeholder' => '2,6', 'default' => 2.6],
                ['key' => 'wall', 'label' => 'Duvar alanı (biliyorsanız)', 'unit' => 'm²', 'min' => 5, 'max' => 10000, 'step' => 1, 'placeholder' => 'Boş bırakın'],
            ], 'help' => 'Daire için net m² yazın (tapudaki brüt değil). Duvar alanını bilmiyorsanız boş bırakın; zemin alanından tahmin edilir.'],
            ['type' => 'cards', 'key' => 'surfaces', 'title' => 'Hangi yüzeyler?', 'short' => 'Yüzeyler', 'required' => true, 'cols' => 2, 'hide' => ['target' => ['dis_cephe']], 'options' => [
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
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Boya kalitesi', 'short' => 'Boya kalitesi', 'required' => true, 'cols' => 3, 'hide' => ['target' => ['dis_cephe']], 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Plastik iç cephe'],
                'orta' => ['label' => 'Orta', 'hint' => 'Silinebilir, mat'],
                'premium' => ['label' => 'Premium', 'hint' => 'Leke tutmaz, yüksek örtücü'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Dış cephe boyası', 'short' => 'Dış cephe boyası', 'required' => true, 'cols' => 3, 'show' => ['target' => ['dis_cephe']], 'options' => [
                'ekonomik' => ['label' => 'Akrilik', 'hint' => 'Ekonomik, 5–7 yıl'],
                'orta' => ['label' => 'Silikonlu', 'hint' => 'Su itici, 8–10 yıl'],
                'premium' => ['label' => 'Silikon / elastomerik', 'hint' => 'Kılcal çatlak kapatır'],
            ]],
            ['type' => 'cards', 'key' => 'access', 'title' => 'Cepheye erişim', 'short' => 'Erişim', 'required' => true, 'cols' => 3, 'show' => ['target' => ['dis_cephe']], 'options' => [
                'iskele' => ['label' => 'İskele kurulacak', 'hint' => 'Çok katlı bina'],
                'yok' => ['label' => 'Gerekmiyor', 'hint' => 'Tek kat / mevcut iskele'],
                'sepet' => ['label' => 'Sepetli platform', 'hint' => 'Bedeli teklif ile'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'hide' => ['target' => ['dis_cephe']], 'options' => [
                'furniture' => 'Eşyalı ev: taşıma ve koruma',
                'door' => 'Kapı ve kasa boyası',
                'radiator' => 'Petek (radyatör) boyası',
                'plaster' => 'Alçı sıva tamiri',
            ]],
            ['type' => 'checks', 'key' => 'xextras', 'title' => 'Cephe ekstraları', 'show' => ['target' => ['dis_cephe']], 'options' => [
                'wash' => 'Basınçlı su ile cephe yıkama',
                'sill' => 'Pencere söveleri ve denizlik boyası',
                'mesh' => 'File + sıva tamiri (geniş çatlaklar)',
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

    private function exterior(array $in): bool
    {
        return ($in['target'] ?? '') === 'dis_cephe';
    }

    /** @return array{0: float, 1: float, 2: float} [net boyanacak cephe, brüt (iskele) cephe, yükseklik m] */
    private function facadeAreas(array $in): array
    {
        $f = $this->p()['facade'];
        $h = ! empty($in['_given_floors']) ? (float) $in['floors'] * $f['floor_height'] : 0.0;
        if (! empty($in['_given_facade'])) {
            $net = (float) $in['facade'];

            return [$net, round($net / (1 - $f['window_share'])), $h];
        }
        if (! empty($in['_given_perimeter']) && ! empty($in['_given_floors'])) {
            $gross = (float) $in['perimeter'] * $h;

            return [round($gross * (1 - $f['window_share'])), round($gross), $h];
        }

        return [0.0, 0.0, $h];
    }

    public function missing(array $in): array
    {
        $m = parent::missing($in);
        if ($this->exterior($in) && ! in_array('cephe alanı veya bina çevresi ve kat sayısı', $m, true) && $this->facadeAreas($in)[0] <= 0) {
            $m[] = empty($in['_given_floors']) ? 'kat sayısı' : 'bina çevresi';
        }

        return $m;
    }

    private function computeFacade(array $in): array
    {
        $f = $this->p()['facade'];
        [$net, $gross, $h] = $this->facadeAreas($in);
        $coats = $this->coats($in);
        $qf = $this->q($in, $f['quality_factor']);
        $paintL = $net * $coats / $f['coverage_m2_per_l'];
        $primerL = $net / $f['primer_m2_per_l'];
        $paintPrice = $this->setting('paint_facade_15l', $f['paint_15l']) / 15;
        $primerPrice = $this->setting('paint_facade_primer_15l', $f['primer_15l']) / 15;
        $stateF = ['iyi' => 1.0, 'orta' => 1.1, 'kotu' => 1.25][$in['state']];
        $labor = $net * $this->setting('paint_facade_labor_m2', $f['labor_m2']) * ($coats === 3 ? 1.25 : 1.0) * $stateF;

        $notes = ($in['color'] ?? '') === 'ayni' ? ['Dış cephede aynı renkte de astar uygulanır; astar hesaba katıldı.'] : [];
        $scaffold = 0.0;
        if (($in['access'] ?? '') === 'iskele') {
            $role = $h > 51 ? 'scaffold_51_plus' : 'scaffold_3_51';
            $price = $this->toolPrice(self::T, $role, (float) config('worksitem_estimators.iskele.m2.cephe', 260));
            $scaffold = $gross * $price + (float) config('worksitem_estimators.iskele.transport', 6000);
            $notes[] = 'İskele brüt cephe alanı ('.self::n1($gross).' m²) üzerinden, 1 aylık kira + kurulum + söküm ve nakliye ile hesaplandı.';
        } elseif (($in['access'] ?? '') === 'sepet') {
            $notes[] = 'Sepetli platform bedeli saha koşullarına göre teklifle belirlenir; toplama eklenmedi.';
        }
        if (empty($in['_given_facade'])) {
            $notes[] = 'Cephe alanı çevre × kat × 3 m − %15 pencere payı ile tahmin edildi ('.self::n1($net).' m²).';
        }
        $sillM = $net * 0.3;   // tipik konutta cephe m² başına ~0,3 m söve/denizlik
        $extra = 0.0;
        $extra += $this->has($in, 'xextras', 'wash') ? $net * $f['extras']['wash_m2'] : 0;
        $extra += $this->has($in, 'xextras', 'sill') ? $sillM * $f['extras']['sill_m'] : 0;
        $extra += $this->has($in, 'xextras', 'mesh') ? $net * $f['extras']['mesh_m2'] : 0;

        $items = [
            'tamir' => ['label' => 'Çatlak ve sıva tamiri, hazırlık', 'mid' => $net * $f['repair_m2'][$in['state']], 'color' => '#5B6472'],
            'astar' => ['label' => 'Dış cephe astarı', 'mid' => $primerL * $primerPrice, 'color' => '#7FB3C8'],
            'boya' => ['label' => 'Dış cephe boyası ('.$coats.' kat)', 'mid' => $paintL * $paintPrice * $qf, 'color' => '#FFB800'],
            'iscilik' => ['label' => 'Ustalık (işçilik)', 'mid' => $labor, 'color' => '#101318'],
            'iskele' => ['label' => 'İskele (kira, kurulum, söküm, nakliye)', 'mid' => $scaffold, 'color' => '#2F6F8F'],
            'sarf' => ['label' => 'Bant, örtü, rulo, fırça', 'mid' => 1500 + $net * 5, 'color' => '#8A6200'],
            'ekstra' => ['label' => 'Seçilen ekstralar', 'mid' => $extra, 'color' => '#3F7D5B'],
        ];
        $days = [max(2, (int) ceil($net / 150)), max(4, (int) ceil($net / 80))];
        if (($in['access'] ?? '') === 'iskele') {
            $days = [$days[0] + 2, $days[1] + 3];
        }

        return [
            'items' => $items,
            'per_unit' => $net,
            'per_unit_label' => 'cephe m² başına',
            'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü (hava uygunsa)'], ['Boyanacak cephe', self::n1($net).' m²']],
            'notes' => $notes,
        ];
    }

    protected function compute(array $in): array
    {
        if ($this->exterior($in)) {
            return $this->computeFacade($in);
        }
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

        // Ustalık fiyatı piyasada DAİRE (zemin) m²'si başına verilir (Kainta saha referansı: 250 TL/m²).
        // Boyanan yüzeye çevirmek için: tipik dairede 1 m² zemine (duvar katsayısı + tavan) m² yüzey düşer.
        $perFloor = (float) $p['wall_factor'] + 1.0;
        $floorEq = ($wall + $ceil) / $perFloor;
        $base = $this->setting('paint_labor_standard_m2', $p['labor_m2']);
        if ($floorEq > $this->setting('paint_labor_large_threshold', 500)) {
            $base = $this->setting('paint_labor_large_base_m2', 175);
        }
        $base /= $perFloor;   // → boyanan yüzey m²'si başına
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
        if ($this->exterior($in)) {
            $f = $this->p()['facade'];
            [$net] = $this->facadeAreas($in);
            $l = $net * $this->coats($in) / $f['coverage_m2_per_l'];

            return [
                ['Dış cephe boyası', (int) ceil($l / 15), 'kova (15 L)', self::n1($l).' L · '.$this->coats($in).' kat · '.$f['coverage_m2_per_l'].' m²/L'],
                ['Dış cephe astarı', (int) max(1, ceil($net / $f['primer_m2_per_l'] / 15)), 'kova (15 L)', 'Yaklaşık '.$f['primer_m2_per_l'].' m²/L'],
                ['Dış cephe dolgu / tamir harcı (25 kg)', (int) max(1, ceil($net * ['iyi' => 0.05, 'orta' => 0.2, 'kotu' => 0.6][$in['state']] / 25)), 'torba', 'Çatlak ve sıva tamiri'],
                ['Maskeleme bandı ve naylon', (int) max(2, ceil($net / 60)), 'set', 'Pencere, kapı ve doğrama koruması'],
            ];
        }
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
        if ($this->exterior($in)) {
            return [['İskele kurulumu ve koruma', .15], ['Yıkama, çatlak ve sıva tamiri', ($in['state'] ?? '') === 'kotu' ? .3 : .15], ['Astar', .15], ['Boya katları', .4], ['Rötuş, iskele sökümü, temizlik', .15]];
        }

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

<?php

namespace App\Estimators;

class InsulationEstimator extends KitEstimator
{
    public function key(): string
    {
        return 'mantolama';
    }

    public function slug(): string
    {
        return 'mantolama-maliyeti';
    }

    public function perUnitLabel(): ?string
    {
        return 'cephe m² başına';
    }

    private function p(): array
    {
        return (array) config('worksitem_estimators.mantolama');
    }

    private const BOARDS = ['eps' => 'EPS (beyaz) levha', 'grafit' => 'Karbonlu (grafitli) EPS levha', 'tasyunu' => 'Taşyünü levha'];

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Mantolama maliyeti',
            'h1' => "Mantolama (dış cephe ısı yalıtımı) maliyeti hesaplama ($y)",
            'lede' => 'Cephe alanını girin, levha türünü, kalınlığı ve son katı seçin; yalıtım levhası, yapıştırma ve sıva sistemi, iskele ve işçilik kalem kalem hesaplansın.',
            'seo_title' => "Mantolama Fiyatı $y: m² Dış Cephe Isı Yalıtımı Maliyeti | Worksitem",
            'seo_desc' => "$y mantolama fiyatları: EPS, karbonlu EPS ve taşyünü ile dış cephe ısı yalıtımının m² maliyetini iskele, sıva, file, dübel ve işçilik dahil ücretsiz hesaplayın.",
            'service' => 'Mantolama',
            'wait' => 'Cephe alanını girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Yalıtım markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'İskele dahil seçenek', '$ € £ karşılığı'],
            'method' => [
                'Cephe alanı biliniyorsa kullanılır; bilinmiyorsa bina çevresi × kat yüksekliği × kat sayısından pencere payı (%15) düşülerek bulunur.',
                'Levha fiyatı = cm başına m² fiyatı × kalınlık × alan × %5 fire.',
                'Sistem malzemesi (yapıştırma ve sıva harcı, file, dübel, köşe profili) ve son kat (boya veya dekoratif sıva) m² başına eklenir.',
                'İşçilik ve gerekiyorsa iskele cephe m² başına; denizlik ve eski kaplama sökümü seçilirse eklenir.',
                '%10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Levha türü', 'Taşyünü yanmaz (A1) ve nefes alır; EPS\'nin yaklaşık 2–2,5 katı maliyetlidir.'],
                ['Kalınlık', 'Kalınlık arttıkça levha maliyeti doğrusal artar; İstanbul ve iç bölgelerde 5–8 cm yaygındır.'],
                ['İskele', 'Çok katlı binalarda iskele toplam maliyetin %15–20\'sini oluşturur.'],
                ['Son kat', 'Dekoratif sıva boyadan pahalıdır ama çizilmeye ve renk solmasına daha dayanıklıdır.'],
            ],
            'faq' => [
                ['Mantolama m² fiyatı ne kadar?', 'Karbonlu EPS 5 cm, boya son kat ve iskele dahil '.now()->year.' fiyatlarıyla yaklaşık '.$this->exampleRange(['facade' => 1000, 'board' => 'grafit', 'thickness' => '5', 'finish' => 'boya', 'quality' => 'orta', 'scaffold' => 'var'], true).' / m² arasıdır.'],
                ['Mantolama kaç cm olmalı?', 'TS 825\'e göre iklim bölgesine bağlıdır; 1. bölgede 3–4 cm, İstanbul (2. bölge) 5–6 cm, iç ve doğu bölgelerde 8–10 cm önerilir.'],
                ['Mantolama yakıt tasarrufu sağlar mı?', 'Yalıtımsız bir binada ısıtma giderini %30–50 azaltabilir; geri dönüş süresi genellikle 4–7 yıldır.'],
                ['Kat malikleri kararı gerekir mi?', 'Ortak alan olan dış cephe için kat malikleri kurulunda karar gerekir; ayrıntı için yönetiminize danışın.'],
            ],
            'related' => [['/araclar/iskele-metraji-maliyeti', 'İskele maliyeti'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı'], ['/araclar/enerji-tuketim-hesaplama', 'Enerji tüketimi'], ['/araclar/su-yalitimi-hesaplayici', 'Su yalıtımı']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'numbers', 'title' => 'Cephe', 'cols' => 3, 'required_any' => 'cephe alanı veya bina çevresi ve kat sayısı', 'fields' => [
                ['key' => 'facade', 'label' => 'Cephe alanı (biliyorsanız)', 'unit' => 'm²', 'min' => 10, 'max' => 50000, 'step' => 1, 'placeholder' => 'Örn. 850'],
                ['key' => 'perimeter', 'label' => 'Bina çevresi', 'unit' => 'm', 'min' => 10, 'max' => 2000, 'step' => 1, 'placeholder' => 'Örn. 60'],
                ['key' => 'floors', 'label' => 'Kat sayısı', 'unit' => 'kat', 'min' => 1, 'max' => 40, 'step' => 1, 'placeholder' => 'Örn. 5'],
            ], 'help' => 'Cephe alanını bilmiyorsanız bina çevresi ve kat sayısını yazın; kat yüksekliği 3 m ve pencere payı %15 alınır.'],
            ['type' => 'cards', 'key' => 'board', 'title' => 'Yalıtım levhası', 'short' => 'Levha türü', 'required' => true, 'cols' => 3, 'options' => [
                'eps' => ['label' => 'EPS (beyaz)', 'hint' => 'Ekonomik'],
                'grafit' => ['label' => 'Karbonlu EPS', 'hint' => 'Daha iyi yalıtım'],
                'tasyunu' => ['label' => 'Taşyünü', 'hint' => 'Yanmaz, A1'],
            ]],
            ['type' => 'cards', 'key' => 'thickness', 'title' => 'Kalınlık', 'short' => 'Kalınlık', 'required' => true, 'cols' => 4, 'options' => [
                '4' => ['label' => '4 cm', 'hint' => 'Ilıman bölge'],
                '5' => ['label' => '5 cm', 'hint' => 'İstanbul, Marmara'],
                '6' => ['label' => '6 cm', 'hint' => 'Önerilen'],
                '8' => ['label' => '8 cm', 'hint' => 'Soğuk bölge'],
            ]],
            ['type' => 'cards', 'key' => 'finish', 'title' => 'Son kat', 'short' => 'Son kat', 'required' => true, 'cols' => 2, 'options' => [
                'boya' => ['label' => 'Dış cephe boyası', 'hint' => 'Sıva üzeri 2 kat'],
                'dekoratif' => ['label' => 'Dekoratif sıva', 'hint' => 'Renkli, dokulu'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Sistem kalitesi', 'short' => 'Sistem kalitesi', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Yerel sistem'],
                'orta' => ['label' => 'Orta', 'hint' => 'Marka sistem'],
                'premium' => ['label' => 'Premium', 'hint' => 'Sertifikalı tam sistem'],
            ]],
            ['type' => 'cards', 'key' => 'scaffold', 'title' => 'İskele', 'short' => 'İskele', 'required' => true, 'cols' => 2, 'options' => [
                'var' => ['label' => 'İskele kurulacak', 'hint' => 'Çok katlı bina'],
                'yok' => ['label' => 'Gerekmiyor', 'hint' => 'Tek kat / mevcut iskele'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['denizlik' => 'Pencere denizlikleri', 'sokum' => 'Eski cephe kaplaması sökümü']],
        ];
    }

    private function area(array $in): float
    {
        if (! empty($in['_given_facade'])) {
            return (float) $in['facade'];
        }
        if (! empty($in['_given_perimeter']) && ! empty($in['_given_floors'])) {
            return round((float) $in['perimeter'] * (float) $in['floors'] * 3 * 0.85);
        }

        return 0.0;
    }

    public function missing(array $in): array
    {
        $m = parent::missing($in);
        if (! in_array('cephe alanı veya bina çevresi ve kat sayısı', $m, true) && $this->area($in) <= 0) {
            $m[] = 'kat sayısı';
        }

        return $m;
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $a = $this->area($in);
        $qf = $this->q($in, $p['quality_factor']);
        $cm = (int) $in['thickness'];
        $windows = $a / 6;   // yaklaşık pencere adedi
        $items = [
            'iskele' => ['label' => 'İskele (kira + kurulum)', 'mid' => $in['scaffold'] === 'var' ? $a * $p['scaffold_m2'] : 0, 'color' => '#5B6472'],
            'sokum' => ['label' => 'Eski kaplama sökümü', 'mid' => $this->has($in, 'extras', 'sokum') ? $a * $p['old_removal_m2'] : 0, 'color' => '#7FB3C8'],
            'levha' => ['label' => self::BOARDS[$in['board']].' '.$cm.' cm', 'mid' => $a * 1.05 * $cm * $this->q($in, $p['board_m2'][$in['board']]), 'color' => '#FFB800'],
            'sistem' => ['label' => 'Yapıştırma ve sıva harcı, file, dübel, profil', 'mid' => $a * $p['system_m2'] * $qf, 'color' => '#8A6200'],
            'sonkat' => ['label' => $in['finish'] === 'boya' ? 'Dış cephe boyası' : 'Dekoratif sıva', 'mid' => $a * $p['finish_m2'][$in['finish']] * $qf, 'color' => '#C9A55A'],
            'iscilik' => ['label' => 'Uygulama işçiliği', 'mid' => $a * $p['labor_m2'], 'color' => '#101318'],
            'denizlik' => ['label' => 'Pencere denizlikleri', 'mid' => $this->has($in, 'extras', 'denizlik') ? $windows * 1.3 * $p['sill_m'] : 0, 'color' => '#2F6F8F'],
        ];
        $days = [max(5, (int) ceil($a / 60)), max(8, (int) ceil($a / 35))];

        return [
            'items' => $items,
            'per_unit' => $a,
            'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Cephe alanı', number_format($a, 0, ',', '.').' m²']],
            'notes' => empty($in['_given_facade']) ? ['Cephe alanı çevre × kat × 3 m − %15 pencere payı ile tahmin edildi.'] : [],
        ];
    }

    public function materials(array $in): array
    {
        $a = $this->area($in);
        $cm = (int) $in['thickness'];
        $rows = [
            [self::BOARDS[$in['board']].' '.$cm.' cm', (int) ceil($a * 1.05), 'm²', '%5 fire dahil'],
            ['Yapıştırma harcı (25 kg)', (int) ceil($a * 5 / 25), 'torba', 'Yaklaşık 5 kg/m²'],
            ['Sıva harcı (25 kg)', (int) ceil($a * 4 / 25), 'torba', 'Yaklaşık 4 kg/m², 2 kat'],
            ['Sıva filesi (50 m²)', (int) ceil($a * 1.1 / 50), 'rulo', '%10 bindirme dahil'],
            ['Dingil dübel', (int) ceil($a * 6), 'adet', 'm² başına 6 adet'],
            ['Köşe ve dilatasyon profili', (int) ceil($a / 8), 'mtül', 'Yaklaşık'],
        ];
        $rows[] = $in['finish'] === 'boya'
            ? ['Dış cephe boyası (15 L)', (int) ceil($a * 2 / 10 / 15), 'kova', '2 kat, 10 m²/L']
            : ['Dekoratif sıva (25 kg)', (int) ceil($a * 2.8 / 25), 'kova', 'Yaklaşık 2,8 kg/m²'];

        return $rows;
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([
            ($in['scaffold'] ?? '') === 'var' ? ['İskele kurulumu', .1] : null,
            $this->has($in, 'extras', 'sokum') ? ['Eski kaplama sökümü', .1] : null,
            ['Levha yapıştırma ve dübel', .3],
            ['File ve sıva katları', .25],
            ['Son kat (boya / dekoratif sıva)', .15],
            ['Denizlik, temizlik, iskele sökümü', .1],
        ]));
    }

    public function checklist(): array
    {
        return array_merge([
            'Levha yoğunluğunu (EPS için en az 16 kg/m³) ve TS EN 13163 belgesini isteyin.',
            'Dübel sayısının ve file bindirmesinin (en az 10 cm) uygulamada kontrol edilmesini sağlayın.',
            'Sistem bileşenlerinin (harç, file, levha) aynı sistem onayına sahip olduğunu sorun.',
        ], array_slice(parent::checklist(), 0, 5));
    }

    public function visuals(): array
    {
        return self::openVisuals([
            ['duvar-rengi-gorsellestirici', route('tools.paint_visualizer'), 'Cephe rengini binanızda görün', 'Binanızın fotoğrafında son kat rengini deneyin.'],
        ]);
    }
}

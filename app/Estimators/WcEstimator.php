<?php

namespace App\Estimators;

use App\Services\BathroomEstimator;

/** Misafir WC: banyo motorunun (Fiyat Merkezi fiyatları) WC tipiyle kullanılması */
class WcEstimator extends KitEstimator
{
    public function key(): string
    {
        return 'wc';
    }

    public function slug(): string
    {
        return 'wc-maliyet-hesabi';
    }

    public function perUnitLabel(): ?string
    {
        return 'zemin m² başına';
    }

    private function bath(): BathroomEstimator
    {
        return app(BathroomEstimator::class);
    }

    public function pricesLabel(): string
    {
        return $this->bath()->pricesLabel();
    }

    public function contingencyRate(): float
    {
        return (float) ($this->bath()->config()['contingency_rate'] ?? 0.10);
    }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'WC tadilat maliyeti',
            'h1' => "Misafir WC tadilat maliyeti hesaplama ($y)",
            'lede' => 'Misafir tuvaletinin ölçülerini girin, kapsamı ve kaliteyi seçin; kırım, tesisat, seramik, klozet ve lavabo kalem kalem hesaplansın. Fiyatlar banyo hesaplayıcısıyla aynı fiyat merkezinden gelir.',
            'seo_title' => "WC Tadilat Maliyeti $y: Misafir Tuvaleti Yenileme Fiyatı | Worksitem",
            'seo_desc' => "$y misafir WC tadilat fiyatı: ölçü, kapsam ve kaliteye göre kırım, tesisat, seramik, klozet, lavabo ve gömme rezervuar dahil maliyeti kalem kalem, ücretsiz hesaplayın.",
            'service' => 'WC Tadilatı',
            'wait' => 'WC ölçülerini girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Vitrifiye ve seramik markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Kalem kalem döküm', '$ € £ karşılığı'],
            'method' => [
                'Kaplama alanı = zemin (en × boy) + duvarlar (çevre × yükseklik) − kapı payı.',
                'Komple yenilemede kırım, su ve gider tesisatı ve zemin su yalıtımı eklenir; yüzey yenilemede yalnız vitrifiye sökülür.',
                'Seramik m² fiyatı ve vitrifiye takımı kaliteye göre Fiyat Merkezi\'nden alınır (WC\'de vitrifiye banyonun %70\'i).',
                'Seçilen ekstralar eklenir, %10 beklenmedik gider payı ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Kapsam', 'Tesisatı yerinde bırakan yüzey yenileme, kırımlı komple yenilemeden belirgin ucuzdur.'],
                ['Gömme rezervuar', 'Duvar içi rezervuar şık görünür ama hem ürün hem işçilik maliyetini artırır.'],
                ['Kaplama alanı', 'Küçük WC\'de bile duvarlar tavana kadar kaplanınca alan zeminin 4–5 katına çıkar.'],
                ['Malzeme', 'Premium vitrifiye ve büyük ebat seramik toplamı orta kaliteye göre %60\'a kadar artırabilir.'],
            ],
            'faq' => [
                ['Misafir WC tadilatı kaç TL?', '1,2 × 1,5 m bir misafir WC\'nin orta kalitede komple yenilemesi '.now()->year.' fiyatlarıyla yaklaşık '.$this->exampleRange(['w' => 1.2, 'l' => 1.5, 'scope' => 'komple', 'quality' => 'orta']).' tutar.'],
                ['WC tadilatı kaç gün sürer?', 'Yüzey yenileme 3–5, kırımlı komple yenileme su yalıtımı kuruma süresiyle 6–10 iş günü sürer.'],
                ['Gömme rezervuar mı, normal klozet mi?', 'Gömme rezervuar yer kazandırır ve temizliği kolaydır; ancak duvar içine montaj gerektirdiği için tadilatın kırım aşamasında karar verilmelidir.'],
                ['Hesaba neler dahil?', 'Malzeme, işçilik, kırım ve moloz (komple yenilemede) dahildir. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/banyo-maliyet-hesaplayici', 'Banyo tadilat maliyeti'], ['/araclar/seramik-maliyet-hesaplayici', 'Seramik metraj ve maliyet'], ['/araclar/su-yalitimi-hesaplayici', 'Su yalıtımı maliyeti'], ['/araclar/mutfak-maliyet-hesaplayici', 'Mutfak tadilat maliyeti']],
        ];
    }

    public function planner(): ?array
    {
        return [
            'url' => route('tools.wc_planner'),
            'label' => "WC'mi 2D / 3D gör",
            'hint' => 'Ölçüleriniz plana otomatik aktarılır; ücretsiz',
            'map' => ['w' => 'width', 'l' => 'length', 'h' => 'height', 'scope' => 'scope', 'quality' => 'quality'],
            'checks' => ['extras' => ['ex_dolap', 'ex_rezervuar', 'ex_havlupan']],
        ];
    }

    public function steps(): array
    {
        $c = $this->bath()->config();
        $ex = [];
        foreach ($c['extras'] as $k => $e) {
            $ex[$k] = $e['label'];
        }

        return [
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 3, 'fields' => [
                ['key' => 'w', 'label' => 'En', 'unit' => 'm', 'min' => 0.6, 'max' => 4, 'step' => 0.1, 'placeholder' => 'Örn. 1,2', 'required' => true],
                ['key' => 'l', 'label' => 'Boy', 'unit' => 'm', 'min' => 0.6, 'max' => 4, 'step' => 0.1, 'placeholder' => 'Örn. 1,5', 'required' => true],
                ['key' => 'h', 'label' => 'Yükseklik', 'unit' => 'm', 'min' => 2, 'max' => 4, 'step' => 0.1, 'placeholder' => '2,6', 'default' => 2.6],
            ], 'help' => 'Duvar yüksekliğini bilmiyorsanız boş bırakın; 2,6 m alınır.'],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Ne kadar değişecek?', 'short' => 'Kapsam', 'required' => true, 'cols' => 3, 'options' => collect($c['scopes'])->map(fn ($s) => ['label' => $s['label'], 'hint' => $s['hint']])->all()],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Malzeme kalitesi', 'short' => 'Malzeme kalitesi', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => $c['qualities']['eko']['hint']],
                'orta' => ['label' => 'Orta', 'hint' => $c['qualities']['orta']['hint']],
                'premium' => ['label' => 'Premium', 'hint' => $c['qualities']['premium']['hint']],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => $ex],
        ];
    }

    private function bathInput(array $in): array
    {
        return [
            'type' => 'wc', 'w' => (float) $in['w'], 'l' => (float) $in['l'], 'h' => (float) ($in['h'] ?? 2.6),
            'scope' => $in['scope'], 'quality' => ['ekonomik' => 'eko', 'orta' => 'orta', 'premium' => 'premium'][$in['quality']] ?? 'orta',
            'shower' => '', 'extras' => $in['extras'] ?? [],
        ];
    }

    protected function compute(array $in): array
    {
        $b = $this->bath()->estimate($this->bathInput($in));
        $labels = ['sokum' => 'Söküm ve moloz', 'tesisat' => 'Su ve gider tesisatı', 'yalitim' => 'Zemin su yalıtımı', 'seramik' => 'Seramik (zemin + duvar)', 'vitrifiye' => 'Klozet, lavabo ve batarya', 'ekstra' => 'Seçilen ekstralar'];
        $colors = ['sokum' => '#5B6472', 'tesisat' => '#2F6F8F', 'yalitim' => '#7FB3C8', 'seramik' => '#FFB800', 'vitrifiye' => '#101318', 'ekstra' => '#C9A55A'];
        $items = [];
        foreach ($labels as $k => $label) {
            if (isset($b['items'][$k])) {
                $items[$k] = ['label' => $label, 'mid' => $b['items'][$k]['mid'], 'color' => $colors[$k]];
            }
        }

        return [
            'items' => $items,
            'per_unit' => $b['floor'],
            'days' => $b['days'],
            'meta' => [['Tahmini süre', $b['days'][0].'–'.$b['days'][1].' iş günü'], ['Kaplama alanı', self::n1($b['tile_area']).' m²']],
        ];
    }

    public function materials(array $in): array
    {
        $b = $this->bath()->estimate($this->bathInput($in));
        $tile = $b['tile_area'];
        $rows = [
            ['Seramik (zemin + duvar)', ceil($tile * 1.1), 'm²', 'Kaplama '.self::n1($tile).' m² + %10 fire'],
            ['Seramik yapıştırıcısı (25 kg)', (int) ceil($tile * 5 / 25), 'torba', 'Yaklaşık 5 kg/m²'],
            ['Derz dolgu (5 kg)', (int) max(1, ceil($tile * 0.5 / 5)), 'paket', 'Yaklaşık 0,5 kg/m²'],
            ['Klozet + lavabo + batarya', 1, 'takım', 'Seçilen kalite'],
            ['Silikon', 2, 'tüp', 'Lavabo ve klozet birleşimleri'],
        ];
        if (($in['scope'] ?? '') !== 'yuzey') {
            $rows[] = ['Su yalıtımı (20 kg)', (int) max(1, ceil($b['floor'] * 3 / 20)), 'kova', 'Zemin, yaklaşık 3 kg/m²'];
            $rows[] = ['PPRC / PVC boru ve ek parça', 1, 'set', 'Yerleşime göre'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return ($in['scope'] ?? '') === 'yuzey'
            ? [['Vitrifiye sökümü', .15], ['Seramik', .5], ['Vitrifiye montajı', .25], ['Temizlik ve teslim', .1]]
            : [['Kırım ve moloz', .15], ['Tesisat', .2], ['Su yalıtımı ve kuruma', .15], ['Seramik', .3], ['Vitrifiye montajı', .15], ['Teslim', .05]];
    }

    public function visuals(): array
    {
        return self::openVisuals([
            ['seramik-gorsellestirici', route('tools.tile_visualizer'), 'Seramiği WC\'nizde görün', 'Fotoğraf yükleyin; karoyu zeminde ve duvarda deneyin.'],
        ]);
    }
}

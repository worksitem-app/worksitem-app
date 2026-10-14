<?php

namespace App\Estimators;

use App\Http\Controllers\ConTech\EpoxyFloorCalculatorController;

class EpoxyEstimator extends PozEstimator
{
    public function key(): string { return 'epoksi'; }

    public function slug(): string { return 'epoksi-zemin-hesaplayici'; }

    public function perUnitLabel(): ?string { return 'm² başına'; }

    private function layers(): array { return EpoxyFloorCalculatorController::catalog(); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Epoksi zemin maliyeti',
            'h1' => "Epoksi zemin kaplama maliyeti ($y)",
            'lede' => 'Zemin alanını girin; 2,5 mm self-leveling epoksiye isteğe bağlı tesviye ve UV dayanımlı poliüretan son kat ekleyin. Her katman resmî YFK birim fiyatıyla ayrı kalem olarak hesaplansın.',
            'seo_title' => "Epoksi Zemin Fiyatı $y: m² Self-Leveling Epoksi Kaplama (YFK) | Worksitem",
            'seo_desc' => "$y epoksi zemin kaplama fiyatları: self-leveling epoksi, zemin tesviyesi ve UV dayanımlı poliüretan son katın m² maliyetini resmî YFK birim fiyatıyla, KDV dahil hesaplayın.",
            'service' => 'Epoksi Zemin',
            'wait' => 'Zemin alanını girin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Zemin kaplama markaları',
            'facts' => ['Resmî YFK birim fiyatı', 'Katman katman döküm', '$ € £ karşılığı'],
            'method' => [
                'Her katmanın YFK pozu m² birim fiyatı (malzeme + işçilik, KDV hariç) alınır: tesviye, epoksi, son kat.',
                'Her katman = alan × birim fiyat; toplamın üzerine %20 KDV eklenir.',
                'Resmî fiyatı bulunmayan katmanda fiyat uydurulmaz.',
            ],
            'factors' => [
                ['Zemin hazırlığı', 'Bozuk veya tozuyan beton zeminde tesviye ve astar şarttır.'],
                ['Kalınlık', 'Forklift trafiği olan alanlarda 2,5–3 mm self-leveling, yaya alanlarında ince boya tipi yeterlidir.'],
                ['UV', 'Güneş gören alanlarda epoksi sararır; poliüretan son kat önerilir.'],
                ['Nem', 'Betonda nem yüksekse epoksi kabarır; nem ölçümü yapılmalıdır.'],
            ],
            'faq' => [
                ['Epoksi zemin m² fiyatı ne kadar?', $this->priceAnswer(now()->year.' resmî YFK birim fiyatlarıyla temel epoksi kaplama KDV dahil m² başına yaklaşık :fiyat tutar; self-leveling tesviye ve UV son kat eklenince fiyat artar.', 'Seçilen katmanların resmî YFK birim fiyatları sonuç kartında katman katman görünür; KDV dahil toplam ve m² tutarı birlikte verilir.')],
                ['Epoksi kaç günde kurur?', 'Yaya trafiğine 24–48 saat, tam kimyasal dayanıma 7 gün sonra ulaşır.'],
                ['Konutta epoksi uygulanır mı?', 'Garaj, depo ve modern iç mekânlarda uygulanır; ıslak alanda kaymaz katkı eklenmelidir.'],
                ['Hesaba neler dahil?', 'YFK pozlarının tanımındaki malzeme ve işçilik; KDV ayrıca eklenir.'],
            ],
            'related' => [['/araclar/saha-betonu-hesaplayici', 'Saha betonu'], ['/araclar/sap-hesaplayici', 'Şap'], ['/araclar/zemin-kaplama', 'Zemin kaplama'], ['/araclar/yukseltilmis-doseme-hesaplayici', 'Yükseltilmiş döşeme']],
        ];
    }

    public function steps(): array
    {
        $l = $this->layers();

        return [
            ['type' => 'numbers', 'title' => 'Alan', 'cols' => 1, 'fields' => [
                ['key' => 'area', 'label' => 'Zemin alanı', 'unit' => 'm²', 'min' => 5, 'max' => 200000, 'step' => 1, 'placeholder' => 'Örn. 300', 'required' => true],
            ]],
            ['type' => 'checks', 'key' => 'layers', 'title' => 'Ek katmanlar', 'options' => ['leveling' => $l['leveling']['label'], 'uv_topcoat' => $l['uv_topcoat']['label']]],
        ];
    }

    protected function compute(array $in): array
    {
        $a = (float) $in['area'];
        $items = [];
        $colors = ['leveling' => '#5B6472', 'epoxy' => '#FFB800', 'uv_topcoat' => '#C9A55A'];
        foreach ($this->layers() as $k => $layer) {
            if ($k !== 'epoxy' && ! $this->has($in, 'layers', $k)) {
                continue;
            }
            $p = $this->poz($layer['poz'] ?? null);
            if (! $p) {
                return $this->blocked($layer['label'], $layer['poz'] ?? null);
            }
            $items[$k] = ['label' => $layer['label'].' · YFK '.$layer['poz'], 'mid' => $a * $p['price'], 'color' => $colors[$k] ?? '#101318'];
        }
        $days = [max(2, (int) ceil($a / 200) + 1), max(3, (int) ceil($a / 100) + 2)];

        return $this->withVat($items, ['per_unit' => $a, 'days' => $days, 'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Katman sayısı', count($items).' katman']]]);
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];
        $rows = [['Epoksi astar', (int) ceil($a * 0.3 / 10), 'set (10 kg)', '≈0,3 kg/m²'], ['Self-leveling epoksi (2,5 mm)', number_format($a * 2.5 * 1.6, 0, ',', '.'), 'kg', '≈1,6 kg/m²/mm']];
        if ($this->has($in, 'layers', 'leveling')) {
            $rows[] = ['Self-leveling tesviye harcı (25 kg)', (int) ceil($a * 2 * 1.7 / 25), 'torba', '2 mm, ≈1,7 kg/m²/mm'];
        }
        if ($this->has($in, 'layers', 'uv_topcoat')) {
            $rows[] = ['Poliüretan son kat', number_format($a * 0.15, 0, ',', '.'), 'kg', '≈0,15 kg/m²'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([['Zımpara / freze ve astar', .25], $this->has($in, 'layers', 'leveling') ? ['Tesviye ve kuruma', .2] : null, ['Epoksi döküm', .35], $this->has($in, 'layers', 'uv_topcoat') ? ['Son kat', .1] : null, ['Kür', .1]]));
    }
}

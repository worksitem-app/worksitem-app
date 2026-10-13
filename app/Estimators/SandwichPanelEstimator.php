<?php

namespace App\Estimators;

use App\Http\Controllers\ConTech\SandwichPanelCalculatorController;

class SandwichPanelEstimator extends CatalogPozEstimator
{
    public function key(): string { return 'sandvic'; }

    public function slug(): string { return 'sandvic-panel-hesaplayici'; }

    protected function catalog(): array { return SandwichPanelCalculatorController::catalog(); }

    protected function systemTitle(): string { return 'Panel sistemi'; }

    protected function areaLabel(): string { return 'Kaplanacak alan'; }

    protected function speed(): array { return [250, 120]; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Sandviç panel maliyeti',
            'h1' => "Sandviç panel çatı ve cephe maliyeti ($y)",
            'lede' => 'Çatı veya cephe alanını girin, panel sistemini seçin (poliüretan, PIR, EPS, taşyünü; galvaniz, PVC, TPO yüzey); resmî YFK birim fiyatıyla imalat bedeli, KDV ve panel metrajı hesaplansın.',
            'seo_title' => "Sandviç Panel Fiyatı $y: m² Çatı ve Cephe Paneli Maliyeti (YFK) | Worksitem",
            'seo_desc' => "$y sandviç panel fiyatları: poliüretan, PIR, EPS ve taşyünü dolgulu çatı ve cephe panellerinin m² maliyetini resmî YFK birim fiyatıyla, KDV dahil hesaplayın.",
            'service' => 'Sandviç Panel',
            'wait' => 'Panel sistemini ve alanı seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Panel ve çatı markaları',
            'facts' => ['Resmî YFK birim fiyatı', 'Çatı ve cephe', '$ € £ karşılığı'],
            'method' => [
                'Seçilen panelin YFK pozu m² birim fiyatı (malzeme + montaj, KDV hariç) alınır.',
                'İmalat bedeli = alan × birim fiyat; %20 KDV eklenir.',
                'Resmî fiyatı bulunmayan sistemde fiyat uydurulmaz.',
                'Aralık piyasa farkı için −%10 / +%15 gösterilir.',
            ],
            'factors' => [
                ['Dolgu', 'Taşyünü yanmaz (A1) ve yangın yönetmeliğinde öne çıkar; poliüretan/PIR daha iyi ısı yalıtımı verir.'],
                ['Üst yüz', 'PVC veya TPO membranlı paneller düşük eğimli çatılarda su yalıtımını güçlendirir.'],
                ['Taşıyıcı', 'Aşık aralığı panel kalınlığını ve maliyeti belirler; statik hesap gerekir.'],
                ['Detay', 'Mahya, dere, baca ve parapet detayları için ek kaplama sacı gerekir.'],
            ],
            'faq' => [
                ['Sandviç panel m² fiyatı ne kadar?', 'Seçilen panelin resmî YFK birim fiyatı sonuç kartında görünür; KDV dahil toplam ve m² başına tutar birlikte verilir.'],
                ['Çatı için hangi panel seçilmeli?', 'Konut ve sanayi çatısında 50 mm poliüretan yaygındır; yangın riski yüksek tesislerde taşyünü dolgulu panel önerilir.'],
                ['Panel montajı ne kadar sürer?', 'Hazır taşıyıcı üzerinde günde 120–250 m² panel monte edilebilir.'],
                ['Hesaba neler dahil?', 'YFK pozunun tanımındaki panel ve montaj; KDV ayrıca eklenir. Taşıyıcı çelik ve dere/mahya sacı dahil değildir.'],
            ],
            'related' => [['/araclar/cati-maliyet-hesaplayici', 'Çatı maliyeti'], ['/araclar/yagmur-borusu-hesabi', 'Yağmur borusu'], ['/araclar/mantolama-maliyeti', 'Mantolama'], ['/araclar/iskele-metraji-maliyeti', 'İskele']],
        ];
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];

        return [
            ['Panel', self::n1($a * 1.05), 'm²', '%5 kesim ve bindirme'],
            ['Panel (1 m faydalı genişlik)', (int) ceil($a * 1.05 / 6), 'adet (6 m boy)', 'Boy projeye göre değişir'],
            ['Kendinden delen vida + conta', number_format($a * 5, 0, ',', '.'), 'adet', '≈5 adet/m²'],
            ['Mahya / köşe sacı', (int) ceil(sqrt($a) * 1.2 / 3), 'adet (3 m)', 'Yaklaşık'],
        ];
    }

    public function phases(array $in): array
    {
        return [['Taşıyıcı kontrolü ve aplikasyon', .2], ['Panel montajı', .6], ['Mahya, köşe ve detaylar', .2]];
    }
}

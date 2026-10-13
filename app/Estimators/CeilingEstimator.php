<?php

namespace App\Estimators;

use App\Http\Controllers\ConTech\CeilingCalculatorController;

class CeilingEstimator extends CatalogPozEstimator
{
    public function key(): string { return 'asma_tavan'; }

    public function slug(): string { return 'asma-tavan-hesaplayici'; }

    protected function catalog(): array { return CeilingCalculatorController::catalog(); }

    protected function systemTitle(): string { return 'Tavan sistemi'; }

    protected function areaLabel(): string { return 'Tavan alanı'; }

    protected function speed(): array { return [80, 40]; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Asma tavan maliyeti',
            'h1' => "Asma tavan maliyeti hesaplama ($y)",
            'lede' => 'Tavan alanını girin ve sistemi seçin (60×60 alüminyum, galvaniz, gizli taşıyıcılı, lamel, PVC, baffle, petek); resmî YFK birim fiyatıyla imalat bedeli, KDV ve panel adedi hesaplansın.',
            'seo_title' => "Asma Tavan Fiyatı $y: m² Alüminyum, Galvaniz, Lamel Tavan (YFK) | Worksitem",
            'seo_desc' => "$y asma tavan fiyatları: 60×60 alüminyum ve galvaniz sarkmalı, gizli taşıyıcılı, lamel ve PVC tavan sistemlerinin m² maliyetini resmî YFK birim fiyatıyla hesaplayın.",
            'service' => 'Asma Tavan',
            'wait' => 'Sistemi ve tavan alanını seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Tavan sistemi markaları',
            'facts' => ['Resmî YFK birim fiyatı', 'Panel adedi', '$ € £ karşılığı'],
            'method' => [
                'Seçilen sisteme karşılık gelen YFK pozunun m² birim fiyatı (malzeme + işçilik, KDV hariç) alınır.',
                'İmalat bedeli = tavan alanı × birim fiyat; %20 KDV eklenir. Fire YFK fiyatının içindedir.',
                'Resmî fiyatı olmayan sistemlerde fiyat uydurulmaz.',
                'Aralık piyasa farkı için −%10 / +%15 gösterilir.',
            ],
            'factors' => [
                ['Taşıyıcı', 'Gizli taşıyıcılı sistem estetiktir ama sarkmalı sisteme göre pahalı ve sökümü zordur.'],
                ['Malzeme', 'Alüminyum paneller nemli ortamda galvanize göre daha dayanıklıdır.'],
                ['Tesisat', 'Aydınlatma, sprinkler ve klima menfezleri için panel kesimi ek işçilik gerektirir.'],
                ['Yükseklik', 'Yüksek tavanlarda iskele gerekir; YFK fiyatına dahil olmayabilir.'],
            ],
            'faq' => [
                ['Asma tavan m² fiyatı ne kadar?', 'Seçilen sistemin resmî YFK birim fiyatı sonuç kartında görünür; KDV dahil toplam ve m² başına tutar birlikte verilir.'],
                ['60×60 tavan için kaç panel gerekir?', 'Her panel 0,36 m²\'dir; 100 m² tavan için yaklaşık 278 panel ve %5 fire ile 292 panel gerekir.'],
                ['Asma tavan kaç günde yapılır?', 'Ustalı bir ekip günde 40–80 m² hazır panel tavan kurar.'],
                ['Hesaba neler dahil?', 'YFK pozunun tanımındaki malzeme ve işçilik; KDV ayrıca eklenir. Aydınlatma ve iskele dahil değildir.'],
            ],
            'related' => [['/araclar/alcipan-hesaplayici', 'Alçıpan duvar'], ['/araclar/havalandirma-hesaplayici', 'Havalandırma'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı'], ['/araclar/yukseltilmis-doseme-hesaplayici', 'Yükseltilmiş döşeme']],
        ];
    }

    public function materials(array $in): array
    {
        $sys = $this->system($in);
        $a = (float) $in['area'];
        $rows = [['Tavan alanı', self::n1($a), 'm²', 'Sipariş için %5 fire: '.self::n1($a * 1.05).' m²']];
        if (! empty($sys['panel_area'])) {
            $rows[] = ['Panel', (int) ceil($a * 1.05 / (float) $sys['panel_area']), 'adet', 'Panel '.str_replace('.', ',', (string) $sys['panel_area']).' m², %5 fire'];
        }
        $rows[] = ['Askı / sarkma teli', (int) ceil($a / 1.2), 'adet', '≈1,2 m²\'ye bir askı'];
        $rows[] = ['Kenar (L) profili', (int) ceil(4 * sqrt($a) / 3), 'adet (3 m)', 'Çevre'];

        return $rows;
    }

    public function phases(array $in): array
    {
        return [['Kot alma ve askı', .3], ['Taşıyıcı profiller', .3], ['Panel montajı', .3], ['Temizlik ve teslim', .1]];
    }
}

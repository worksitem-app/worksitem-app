<?php

namespace App\Estimators;

use App\Http\Controllers\ConTech\WaterproofingCalculatorController;

class WaterproofingEstimator extends CatalogPozEstimator
{
    public function key(): string { return 'su_yalitimi'; }

    public function slug(): string { return 'su-yalitimi-hesaplayici'; }

    protected function catalog(): array { return WaterproofingCalculatorController::catalog(); }

    protected function systemTitle(): string { return 'Yalıtım sistemi'; }

    protected function areaLabel(): string { return 'Yalıtılacak alan'; }

    protected function speed(): array { return [120, 50]; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Su yalıtımı maliyeti',
            'h1' => "Su yalıtımı maliyeti hesaplama ($y)",
            'lede' => 'Yalıtılacak alanı girin ve sistemi seçin (çimento esaslı, kristalize, poliürea, PVC/TPO/HDPE membran, bitümlü örtü); resmî YFK birim fiyatıyla imalat bedeli, KDV ve malzeme metrajı hesaplansın.',
            'seo_title' => "Su Yalıtımı Fiyatı $y: m² Teras, Banyo, Bodrum Yalıtımı (YFK) | Worksitem",
            'seo_desc' => "$y su yalıtımı fiyatları: teras, balkon, banyo, bodrum ve perde duvarda çimento esaslı, kristalize, poliürea ve membran sistemlerin m² maliyetini resmî YFK birim fiyatıyla hesaplayın.",
            'service' => 'Su Yalıtımı',
            'wait' => 'Sistemi ve alanı seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Su yalıtımı markaları',
            'facts' => ['Resmî YFK birim fiyatı', 'Teras, banyo, bodrum', '$ € £ karşılığı'],
            'method' => [
                'Seçilen sistemin YFK pozu m² birim fiyatı (malzeme + işçilik, KDV hariç) alınır.',
                'İmalat bedeli = alan × birim fiyat; %20 KDV eklenir.',
                'Resmî fiyatı bulunmayan sistemde fiyat uydurulmaz.',
                'Aralık piyasa farkı için −%10 / +%15 gösterilir.',
            ],
            'factors' => [
                ['Uygulama yeri', 'Teras ve bodrum perdesinde su basıncı ve UV nedeniyle daha güçlü sistem gerekir.'],
                ['Sistem', 'File takviyeli ve 3 katlı sistemler çatlak köprüleme yeteneğini artırır.'],
                ['Detaylar', 'Süzgeç, köşe ve dilatasyon detayları yalıtımın en zayıf noktalarıdır.'],
                ['Koruma', 'Membran üzerine koruma şapı veya kaplama yapılmazsa ömrü kısalır.'],
            ],
            'faq' => [
                ['Su yalıtımı m² fiyatı ne kadar?', 'Seçilen sistemin resmî YFK birim fiyatı sonuç kartında görünür; KDV dahil toplam ve m² başına tutar birlikte verilir.'],
                ['Banyoda hangi su yalıtımı kullanılır?', 'Seramik altında genellikle iki bileşenli çimento esaslı sürme yalıtım, köşelerde file/bant ile uygulanır.'],
                ['Yalıtımdan sonra test gerekir mi?', 'Islak hacim ve teraslarda 24–48 saat göllendirme (su) testi önerilir.'],
                ['Hesaba neler dahil?', 'YFK pozunun tanımındaki malzeme ve işçilik; KDV ayrıca eklenir. Koruma şapı ve kaplama dahil değildir.'],
            ],
            'related' => [['/araclar/banyo-maliyet-hesaplayici', 'Banyo tadilat maliyeti'], ['/araclar/seramik-maliyet-hesaplayici', 'Seramik maliyeti'], ['/araclar/sap-hesaplayici', 'Şap hesaplayıcı'], ['/araclar/mantolama-maliyeti', 'Mantolama']],
        ];
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];
        $k = (string) $in['system'];
        $rows = [['Yalıtım alanı', self::n1($a), 'm²', 'Köşe dönüşleri için +%10: '.self::n1($a * 1.1).' m²']];
        if (str_starts_with($k, 'cement') || str_starts_with($k, 'crystalline')) {
            $rows[] = ['Çimento esaslı yalıtım (20 kg set)', (int) ceil($a * (str_contains($k, '3coat') ? 4 : 3) / 20), 'set', '≈3–4 kg/m²'];
        }
        if (str_contains($k, 'mesh')) {
            $rows[] = ['Yalıtım filesi', (int) ceil($a * 1.1), 'm²', 'Bindirme dahil'];
        }
        if (str_contains($k, 'membrane') || str_contains($k, 'bituminous') || str_contains($k, 'breathable')) {
            $rows[] = ['Membran / örtü', (int) ceil($a * 1.12), 'm²', '%12 bindirme'];
        }
        $rows[] = ['Köşe yalıtım bandı', (int) ceil(4 * sqrt($a) * 1.2), 'm', 'Duvar–zemin birleşimi'];

        return $rows;
    }

    public function phases(array $in): array
    {
        return [['Yüzey hazırlığı ve astar', .25], ['Köşe ve detay uygulaması', .2], ['Yalıtım katları', .4], ['Kür ve su testi', .15]];
    }
}

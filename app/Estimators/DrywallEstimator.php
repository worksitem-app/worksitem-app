<?php

namespace App\Estimators;

use App\Http\Controllers\ConTech\DrywallCalculatorController;

class DrywallEstimator extends PozEstimator
{
    public function key(): string { return 'alcipan'; }

    public function slug(): string { return 'alcipan-hesaplayici'; }

    public function perUnitLabel(): ?string { return 'm² başına'; }

    private const TYPES = [
        'c50' => ['label' => 'Bölme duvar C50', 'hint' => 'Toplam ≈7,5 cm', 'prefix' => 'partition_c50_', 'faces' => 2],
        'c75' => ['label' => 'Bölme duvar C75', 'hint' => 'Toplam ≈10 cm', 'prefix' => 'partition_c75_', 'faces' => 2],
        'c100' => ['label' => 'Bölme duvar C100', 'hint' => 'Toplam ≈12,5 cm', 'prefix' => 'partition_c100_', 'faces' => 2],
        'glue' => ['label' => 'Giydirme (yapıştırma)', 'hint' => 'Duvara yapıştırılan levha', 'prefix' => 'lining_glue_', 'faces' => 1],
        'frame' => ['label' => 'Giydirme (karkaslı)', 'hint' => 'Profil üzerine levha', 'prefix' => 'lining_frame_', 'faces' => 1],
    ];

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Alçıpan maliyeti',
            'h1' => "Alçıpan bölme ve giydirme duvar maliyeti ($y)",
            'lede' => 'Duvar alanını girin, sistemi ve levha türünü seçin; Çevre, Şehircilik ve İklim Değişikliği Bakanlığı (YFK) resmî birim fiyatıyla imalat bedeli, KDV ve levha-profil metrajı hesaplansın.',
            'seo_title' => "Alçıpan Duvar Fiyatı $y: m² Bölme ve Giydirme Maliyeti (YFK) | Worksitem",
            'seo_desc' => "$y alçıpan bölme duvar ve giydirme fiyatı: C50, C75, C100 sistemler, standart, suya ve yangına dayanıklı levha için resmî YFK birim fiyatıyla m² maliyeti ve metraj.",
            'service' => 'Alçıpan Duvar',
            'wait' => 'Sistemi, levhayı ve alanı seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Alçıpan ve profil markaları',
            'facts' => ['Resmî YFK birim fiyatı', 'Levha ve profil metrajı', '$ € £ karşılığı'],
            'method' => [
                'Sistem ve levha seçimine karşılık gelen YFK pozunun m² birim fiyatı (malzeme + işçilik, KDV hariç) alınır.',
                'İmalat bedeli = duvar alanı × birim fiyat; üzerine %20 KDV eklenir.',
                'Resmî fiyat bulunamazsa fiyat uydurulmaz; yalnız metraj verilir.',
                'Aralık, piyasa farkı için −%10 / +%15 olarak gösterilir.',
            ],
            'factors' => [
                ['Profil genişliği', 'C75 ve C100 profiller daha yüksek ve rijit duvar ile daha iyi ses yalıtımı sağlar.'],
                ['Levha türü', 'Islak hacimde suya dayanıklı (yeşil), kaçış yollarında yangına dayanıklı (pembe) levha kullanılır.'],
                ['Yalıtım', 'Bölme içine taşyünü konursa ses yalıtımı belirgin artar (ayrıca fiyatlandırılır).'],
                ['Boya', 'Alçıpan üzeri derz bandı, macun ve boya YFK pozuna dahil değildir.'],
            ],
            'faq' => [
                ['Alçıpan bölme duvar m² fiyatı ne kadar?', 'Fiyat seçilen profil ve levhaya göre resmî YFK birim fiyatından hesaplanır; sistemi seçince m² fiyatı sonuç kartında görünür.'],
                ['Alçıpan duvar ne kadar sürede yapılır?', 'Ustalı bir ekip günde 25–40 m² bölme duvar kurar; derz ve boya ayrıca 2–3 gün sürer.'],
                ['Hangi levhayı seçmeliyim?', 'Kuru alanlarda standart (beyaz), banyo ve mutfakta suya dayanıklı (yeşil), yangın kaçış koridorlarında yangına dayanıklı (pembe) levha.'],
                ['Hesaba neler dahil?', 'YFK pozunun tanımındaki malzeme ve işçilik; KDV ayrıca eklenir. Boya ve yalıtım dahil değildir.'],
            ],
            'related' => [['/araclar/asma-tavan-hesaplayici', 'Asma tavan'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı'], ['/araclar/duvar-maliyet-hesaplayici', 'Duvar maliyeti'], ['/araclar/alci-siva-hesaplama', 'Alçı sıva']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'system', 'title' => 'Sistem', 'short' => 'Sistem', 'required' => true, 'cols' => 3, 'options' => collect(self::TYPES)->map(fn ($t) => ['label' => $t['label'], 'hint' => $t['hint']])->all()],
            ['type' => 'cards', 'key' => 'board', 'title' => 'Levha türü', 'short' => 'Levha türü', 'required' => true, 'cols' => 4, 'options' => [
                'standard' => ['label' => 'Standart', 'hint' => 'Beyaz, kuru alan'],
                'water' => ['label' => 'Suya dayanıklı', 'hint' => 'Yeşil, ıslak hacim'],
                'fire' => ['label' => 'Yangına dayanıklı', 'hint' => 'Pembe'],
                'water_fire' => ['label' => 'Su + yangın', 'hint' => 'Kombine'],
            ]],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 2, 'fields' => [
                ['key' => 'area', 'label' => 'Duvar alanı', 'unit' => 'm²', 'min' => 1, 'max' => 50000, 'step' => 0.5, 'placeholder' => 'Örn. 40', 'required' => true],
                ['key' => 'height', 'label' => 'Duvar yüksekliği', 'unit' => 'm', 'min' => 2, 'max' => 8, 'step' => 0.1, 'placeholder' => '2,8', 'default' => 2.8],
            ], 'help' => 'Kapı ve pencere boşluklarını düşün. Yükseklik yalnız profil metrajında kullanılır.'],
        ];
    }

    private function system(array $in): ?array
    {
        $key = self::TYPES[$in['system']]['prefix'].$in['board'];

        return DrywallCalculatorController::catalog()[$key] ?? null;
    }

    protected function compute(array $in): array
    {
        $sys = $this->system($in);
        $p = $this->poz($sys['poz'] ?? null);
        if (! $sys || ! $p) {
            return $this->blocked($sys['label'] ?? 'Bu sistem', $sys['poz'] ?? null);
        }
        $a = (float) $in['area'];
        $days = [max(1, (int) ceil($a / 40)), max(2, (int) ceil($a / 22))];

        return $this->withVat([
            'imalat' => ['label' => $sys['label'].' · YFK '.$sys['poz'], 'mid' => $a * $p['price'], 'color' => '#FFB800'],
        ], [
            'per_unit' => $a, 'days' => $days,
            'meta' => [['Birim fiyat (KDV hariç)', '₺'.number_format($p['price'], 2, ',', '.').' / '.$p['unit']], ['Tahmini süre', $days[0].'–'.$days[1].' iş günü']],
            'notes' => [$sys['note'] ?? ''],
        ]);
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];
        $h = (float) ($in['height'] ?? 2.8);
        $faces = self::TYPES[$in['system']]['faces'];
        $len = $a / $h;
        $rows = [['Alçı levha (120×250 cm)', (int) ceil($a * $faces / 3 * 1.05), 'adet', $faces === 2 ? 'Her iki yüz, %5 fire' : 'Tek yüz, %5 fire']];
        if ($in['system'] !== 'glue') {
            $rows[] = ['C (dikme) profil', (int) ceil($len / 0.6 + 1) , 'adet ('.number_format($h, 1, ',', '').' m)', '60 cm aks'];
            $rows[] = ['U (taban/tavan) profil', (int) ceil($len * 2 / 3), 'adet (3 m)', 'Alt + üst'];
        } else {
            $rows[] = ['Levha yapıştırma alçısı', (int) ceil($a * 4 / 25), 'torba (25 kg)', '≈4 kg/m²'];
        }
        $rows[] = ['Alçıpan vidası', number_format($a * $faces * 30, 0, ',', '.'), 'adet', '≈30 adet/m² yüz'];
        $rows[] = ['Derz bandı', (int) ceil($a * $faces * 1.5), 'm', 'Derz macunu ile'];

        return $rows;
    }

    public function phases(array $in): array
    {
        return [['Aplikasyon ve U profil', .15], ['Dikme profiller', .25], ['Levha montajı', .4], ['Derz bandı ve macun', .2]];
    }
}

<?php

namespace App\Estimators;

use App\Estimators\Concerns\DbPrices;
use App\Services\PriceService;

/** Bina inşaat maliyeti: ÇŞB yapı sınıfı birim maliyeti × piyasa katsayısı, kalem dağılımı ayarlardan */
class BuildingCostEstimator extends KitEstimator
{
    use DbPrices;

    // Resmî sınıf kodu ve (DB'de yoksa) 2026 yaklaşık birim maliyet (TL/m²)
    // ÇŞB 2026 Yapı Yaklaşık Birim Maliyetleri Tebliği (RG 03.02.2026, 33157): köy evi/bungalov II-C, 5 yıldızlı otel/şehir hastanesi V-D
    protected const TYPES = [
        'mustakil_basit' => ['label' => 'Köy evi / bungalov', 'hint' => 'Basit yapı', 'class' => '2C', 'fallback' => 15100],
        'apartman' => ['label' => 'Apartman / müstakil ev', 'hint' => 'Konut, 3. sınıf', 'class' => '3B', 'fallback' => 21050],
        'rezidans' => ['label' => 'Rezidans / otel / AVM', 'hint' => 'Lüks konut, 4. sınıf', 'class' => '4A', 'fallback' => 26450],
        'kampus' => ['label' => 'Üniversite / stadyum', 'hint' => '5. sınıf', 'class' => '5A', 'fallback' => 42350],
        'hastane' => ['label' => 'Hastane / 5 yıldızlı otel', 'hint' => '5. sınıf ileri', 'class' => '5D', 'fallback' => 53500],
    ];

    public function key(): string { return 'bina'; }

    public function slug(): string { return 'tahmini-bina-maliyeti'; }

    public function perUnitLabel(): ?string { return 'inşaat m² başına'; }

    public function contingencyRate(): float { return 0.05; }

    /** [birim (piyasa), resmî birim, resmî mi] */
    protected function classCost(string $type): array
    {
        $t = static::TYPES[$type];
        $official = null;
        try {
            $r = app(PriceService::class)->buildingClass($t['class'], '2026');
            $official = is_array($r) && ($r['price'] ?? 0) > 0 ? (float) $r['price'] : null;
        } catch (\Throwable) {
        }
        $base = $official ?? (float) $t['fallback'];

        return [$base * $this->setting('building.market_factor', 1.6), $base, $official !== null];
    }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Bina inşaat maliyeti',
            'h1' => "Bina inşaat maliyeti hesaplama ($y)",
            'lede' => 'Toplam inşaat alanını girin, yapı türünü ve kaliteyi seçin; Bakanlık yapı sınıfı birim maliyetinden yola çıkarak kaba inşaat, ince işler, mekanik ve elektrik kalemleriyle yaklaşık bina maliyeti hesaplansın.',
            'seo_title' => "İnşaat Maliyeti $y: m² Bina Yapım Maliyeti Hesaplama | Worksitem",
            'seo_desc' => "$y inşaat maliyeti: yapı sınıfına göre m² bina yapım maliyetini kaba inşaat, ince işler, mekanik ve elektrik dağılımı, proje ve müteahhit payı dahil ücretsiz hesaplayın.",
            'service' => 'Bina İnşaatı',
            'wait' => 'İnşaat alanını girin ve yapı türünü seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Yapı malzemesi markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'ÇŞB yapı sınıfı', '$ € £ karşılığı'],
            'method' => [
                'Resmî yapı sınıfı birim maliyeti (ÇŞB '.$y.' tebliği) piyasa katsayısıyla (yönetim ayarı: '.str_replace('.', ',', (string) $this->setting('building.market_factor', 1.6)).') güncel piyasa seviyesine taşınır.',
                'Toplam = inşaat alanı × birim maliyet × kalite çarpanı; kaba inşaat, ince işler, mekanik ve elektrik payları yönetim ayarlarındaki oranlarla dağıtılır.',
                'Proje-ruhsat ve müteahhit genel gider/kâr payı seçilirse eklenir.',
                '%5 beklenmedik gider ve −%15 / +%20 aralık uygulanır. Arsa bedeli dahil değildir.',
            ],
            'factors' => [
                ['Yapı sınıfı', 'Hastane ve otel gibi yapılarda tesisat yoğunluğu nedeniyle m² maliyeti konutun 2–2,5 katıdır.'],
                ['Kalite', 'Cephe, doğrama ve iç mekân malzemesi lüks seçildiğinde maliyet %25 ve üzeri artar.'],
                ['Bodrum ve zemin', 'Kötü zemin, iksa ve bodrum katları kaba inşaat payını büyütür.'],
                ['Süre ve enflasyon', 'Uzun süren projelerde malzeme fiyat artışı için ayrıca pay ayırın.'],
            ],
            'faq' => [
                ['1 m² inşaat maliyeti ne kadar?', $y.' fiyatlarıyla standart kalite apartman için müteahhit payı hariç m² başına yaklaşık '.$this->exampleRange(['type' => 'apartman', 'area' => 1000, 'quality' => 'orta'], true).' arasıdır.'],
                ['Resmî birim maliyet ile piyasa neden farklı?', 'Bakanlığın yapı sınıfı birim maliyetleri harç ve ücret hesabı içindir; gerçek piyasa maliyeti genellikle daha yüksektir.'],
                ['Kat karşılığı hesabı nasıl yapılır?', 'Toplam yapım maliyeti ve satılabilir alan üzerinden; ayrıntılı analiz için Kat Karşılığı Fizibilite aracını kullanın.'],
                ['Hesaba neler dahil?', 'Kaba, ince, mekanik ve elektrik işleri; seçerseniz proje-ruhsat ve müteahhit payı. Arsa, KDV dışı vergiler ve finansman dahil değildir.'],
            ],
            'related' => [['/araclar/kat-karsiligi-analiz', 'Kat karşılığı analizi'], ['/araclar/statik-proje-bedeli', 'Statik proje bedeli'], ['/araclar/taks-kaks-hesaplayici', 'TAKS / KAKS'], ['/maliyet-hesapla', 'Tadilat bütçesi']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'type', 'title' => 'Yapı türü', 'short' => 'Yapı türü', 'required' => true, 'cols' => 3, 'options' => collect(static::TYPES)->map(fn ($t) => ['label' => $t['label'], 'hint' => $t['hint'].' · '.$t['class']])->all()],
            ['type' => 'numbers', 'title' => 'Alan', 'cols' => 1, 'fields' => [
                ['key' => 'area', 'label' => 'Toplam inşaat alanı (brüt)', 'unit' => 'm²', 'min' => 30, 'max' => 2000000, 'step' => 10, 'placeholder' => 'Örn. 1000', 'required' => true],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Malzeme kalitesi', 'short' => 'Kalite', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => '−%15'],
                'orta' => ['label' => 'Standart', 'hint' => 'Rayiç'],
                'premium' => ['label' => 'Lüks', 'hint' => '+%25'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['projects' => 'Proje, ruhsat ve harçlar (%4)', 'contractor' => 'Müteahhit genel gider ve kâr (%15)', 'landscape' => 'Çevre düzenlemesi (%3)']],
        ];
    }

    protected function compute(array $in): array
    {
        $a = (float) $in['area'];
        [$unit, $official, $isOfficial] = $this->classCost($in['type']);
        $base = $a * $unit * $this->q($in, [0.85, 1.0, 1.25]);
        $share = fn (string $k, float $d) => $this->setting('building.breakdown.'.$k, $d);
        $items = [
            'kaba' => ['label' => 'Kaba inşaat (temel, karkas, duvar, çatı)', 'mid' => $base * $share('rough', .34), 'color' => '#5B6472'],
            'ince' => ['label' => 'İnce işler (sıva, kaplama, doğrama, boya)', 'mid' => $base * $share('finishing', .27), 'color' => '#FFB800'],
            'mekanik' => ['label' => 'Mekanik tesisat (su, ısıtma, havalandırma)', 'mid' => $base * $share('mechanical', .21), 'color' => '#2F6F8F'],
            'elektrik' => ['label' => 'Elektrik tesisatı', 'mid' => $base * $share('electrical', .18), 'color' => '#101318'],
            'proje' => ['label' => 'Proje, ruhsat ve harçlar', 'mid' => $this->has($in, 'extras', 'projects') ? $base * 0.04 : 0, 'color' => '#7FB3C8'],
            'cevre' => ['label' => 'Çevre düzenlemesi', 'mid' => $this->has($in, 'extras', 'landscape') ? $base * 0.03 : 0, 'color' => '#3F7D5B'],
            'muteahhit' => ['label' => 'Müteahhit genel gider ve kâr', 'mid' => $this->has($in, 'extras', 'contractor') ? $base * 0.15 : 0, 'color' => '#8A6200'],
        ];
        $months = max(6, (int) ceil(6 + $a / 800));

        return ['items' => $items, 'per_unit' => $a, 'days' => null,
            'meta' => [['Tahmini süre', $months.'–'.($months + 6).' ay'], ['Resmî sınıf maliyeti', number_format($official, 0, ',', '.').' TL/m² ('.static::TYPES[$in['type']]['class'].')']],
            'notes' => [$isOfficial ? 'Resmî birim maliyet Fiyat Merkezi\'nden okundu.' : 'Resmî birim maliyet veritabanında yok; '.date('Y').' tebliği yaklaşık değeri kullanıldı.']];
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];

        return [
            ['Hazır beton (yaklaşık)', number_format($a * 0.45, 0, ',', '.'), 'm³', '≈0,45 m³/m² (karkas)'],
            ['İnşaat demiri (yaklaşık)', number_format($a * 0.045, 1, ',', '.'), 'ton', '≈45 kg/m²'],
            ['Duvar bloğu (yaklaşık)', number_format($a * 1.1, 0, ',', '.'), 'm²', 'Dış + iç duvar'],
            ['Kalıp (yaklaşık)', number_format($a * 2.7, 0, ',', '.'), 'm²', '≈6 m²/m³ beton'],
        ];
    }

    public function phases(array $in): array
    {
        return [['Proje ve ruhsat', .1], ['Hafriyat ve temel', .1], ['Kaba inşaat', .35], ['Tesisat altyapısı', .15], ['İnce işler', .25], ['İskân ve teslim', .05]];
    }

    public function proReport(array $in): array
    {
        $r = parent::proReport($in);
        $months = max(6, (int) ceil(6 + (float) $in['area'] / 800));
        $r['program'] = [];
        $m = 1;
        foreach ($this->phases($in) as [$name, $share]) {
            $len = max(1, (int) round($months * $share));
            $r['program'][] = ['name' => $name, 'from' => $m, 'to' => $m + $len - 1];
            $m += $len;
        }
        $r['totalDays'] = $m - 1;
        $r['programUnit'] = 'ay';

        return $r;
    }
}

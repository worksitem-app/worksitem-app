<?php

namespace App\Estimators;

/** Statik (betonarme) proje bedeli: inşaat alanı × yapı sınıfı birim maliyeti × hizmet oranı */
class StaticFeeEstimator extends BuildingCostEstimator
{
    public function key(): string { return 'statik_proje'; }

    public function slug(): string { return 'statik-proje-bedeli'; }

    public function perUnitLabel(): ?string { return 'inşaat m² başına'; }

    public function contingencyRate(): float { return 0.0; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Statik proje bedeli',
            'h1' => "Statik proje bedeli hesaplama ($y)",
            'lede' => 'İnşaat alanını ve yapı türünü girin; yapı sınıfı birim maliyeti üzerinden statik proje, zemin etüdü ve tesisat projeleri dahil yaklaşık mühendislik bedeli hesaplansın.',
            'seo_title' => "Statik Proje Bedeli $y: Betonarme Proje Ücreti Hesaplama | Worksitem",
            'seo_desc' => "$y statik proje bedeli: inşaat alanı ve yapı sınıfına göre betonarme proje, performans analizi, zemin etüdü ve tesisat projeleri dahil yaklaşık mühendislik ücretini hesaplayın.",
            'service' => 'Statik Proje',
            'wait' => 'İnşaat alanını girin ve seçimleri yapın; bedel burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Mühendislik ve yazılım firmaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'ÇŞB yapı sınıfı', '$ € £ karşılığı'],
            'method' => [
                'Yapı yaklaşık maliyeti = inşaat alanı × resmî yapı sınıfı birim maliyeti.',
                'Statik proje bedeli bu maliyetin yaklaşık %1,1\'i alınır; mevcut binada performans analizi/güçlendirme projesinde 1,5 katı.',
                'Zemin etüdü, mekanik ve elektrik proje bedelleri seçilirse eklenir.',
                'Aralık −%15 / +%20; meslek odası asgari ücret tarifesi ve büro farkları sonucu değiştirir.',
            ],
            'factors' => [
                ['Yapı sınıfı', 'Karmaşık yapılar (hastane, yüksek bina) daha yüksek birim maliyet ve oranla hesaplanır.'],
                ['Proje türü', 'Mevcut binanın performans analizi karot, röntgen ve modelleme gerektirir.'],
                ['Zemin', 'Zemin etüdü olmadan statik proje onaylanmaz; sondaj sayısı fiyatı belirler.'],
                ['Revizyon', 'Mimari proje sık değişirse statik revizyon ücretleri doğar.'],
            ],
            'faq' => [
                ['1000 m² binanın statik projesi kaç TL?', 'Apartman sınıfında yeni bina için '.$y.' yaklaşık değeri '.$this->exampleRange(['type' => 'apartman', 'area' => 1000, 'kind' => 'yeni']).' civarıdır; meslek odası tarifesi ve büro farkları belirleyicidir.'],
                ['Statik proje ücretini kim belirler?', 'İnşaat Mühendisleri Odası asgari ücret tarifesi yayımlar; bürolar bunun üzerinde fiyat verebilir.'],
                ['Güçlendirme projesi neden pahalı?', 'Mevcut yapıdan karot ve donatı tespiti alınır, bina modellenir ve performans analizi yapılır.'],
                ['Hesaba neler dahil?', 'Statik proje; seçerseniz zemin etüdü, mekanik ve elektrik projeleri. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/tahmini-bina-maliyeti', 'Bina inşaat maliyeti'], ['/araclar/deprem-izolator-maliyeti', 'Deprem izolatörü'], ['/araclar/karot-dayanim', 'Karot dayanımı'], ['/araclar/kolon-tasima-kapasitesi', 'Kolon taşıma kapasitesi']],
        ];
    }

    public function steps(): array
    {
        $s = parent::steps();

        return [
            $s[0], $s[1],
            ['type' => 'cards', 'key' => 'kind', 'title' => 'Proje türü', 'short' => 'Proje türü', 'required' => true, 'cols' => 2, 'options' => [
                'yeni' => ['label' => 'Yeni bina', 'hint' => 'Betonarme proje'],
                'guclendirme' => ['label' => 'Mevcut bina', 'hint' => 'Performans analizi / güçlendirme'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['soil' => 'Zemin etüdü raporu', 'mechanical' => 'Mekanik tesisat projesi', 'electrical' => 'Elektrik projesi']],
        ];
    }

    protected function compute(array $in): array
    {
        $a = (float) $in['area'];
        [, $official] = $this->classCost($in['type']);
        $cost = $a * $official;
        $items = [
            'statik' => ['label' => $in['kind'] === 'yeni' ? 'Statik (betonarme) proje' : 'Performans analizi ve güçlendirme projesi', 'mid' => $cost * 0.011 * ($in['kind'] === 'yeni' ? 1 : 1.5) * 1.2, 'color' => '#FFB800'],
            'zemin' => ['label' => 'Zemin etüdü', 'mid' => $this->has($in, 'extras', 'soil') ? 45000 + $a * 8 : 0, 'color' => '#5B6472'],
            'mekanik' => ['label' => 'Mekanik tesisat projesi', 'mid' => $this->has($in, 'extras', 'mechanical') ? $cost * 0.007 * 1.2 : 0, 'color' => '#2F6F8F'],
            'elektrik' => ['label' => 'Elektrik projesi', 'mid' => $this->has($in, 'extras', 'electrical') ? $cost * 0.006 * 1.2 : 0, 'color' => '#101318'],
        ];
        $days = $in['kind'] === 'yeni' ? [15, 40] : [30, 75];

        return ['items' => $items, 'per_unit' => $a, 'days' => $days,
            'meta' => [['Hazırlık süresi', $days[0].'–'.$days[1].' iş günü'], ['Yapı yaklaşık maliyeti', number_format($cost, 0, ',', '.').' TL']]];
    }

    public function materials(array $in): array
    {
        return [];
    }

    public function phases(array $in): array
    {
        return ($in['kind'] ?? '') === 'yeni'
            ? [['Mimari proje uyumu ve zemin verisi', .2], ['Modelleme ve analiz', .4], ['Çizim ve detaylar', .3], ['Onay ve revizyon', .1]]
            : [['Yerinde tespit (karot, röntgen)', .3], ['Modelleme ve performans analizi', .4], ['Güçlendirme projesi', .2], ['Onay', .1]];
    }

    public function proReport(array $in): array
    {
        return KitEstimator::proReport($in);
    }
}

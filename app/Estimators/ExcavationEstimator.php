<?php

namespace App\Estimators;

class ExcavationEstimator extends KitEstimator
{
    public function key(): string { return 'hafriyat'; }

    public function slug(): string { return 'hafriyat-kazi-hesabi'; }

    public function perUnitLabel(): ?string { return 'm³ başına'; }

    private function p(): array { return (array) config('worksitem_estimators.hafriyat'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Hafriyat maliyeti',
            'h1' => "Hafriyat ve kazı maliyeti hesaplama ($y)",
            'lede' => 'Kazı ölçülerini girin, zemin türünü ve kamyon kapasitesini seçin; kazı, kabarma, kamyon seferi, döküm sahası ücreti ve iksa kalem kalem hesaplansın.',
            'seo_title' => "Hafriyat Fiyatı $y: m³ Kazı, Kamyon Seferi ve Döküm Maliyeti | Worksitem",
            'seo_desc' => "$y hafriyat fiyatları: kazı hacmi, zemin kabarma oranı, kamyon sefer sayısı ve döküm sahası ücreti dahil hafriyat maliyetini ücretsiz hesaplayın.",
            'service' => 'Hafriyat ve Kazı',
            'wait' => 'Kazı ölçülerini girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'İş makinesi ve nakliye firmaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Sefer sayısı', '$ € £ karşılığı'],
            'method' => [
                'Yerinde hacim = en × boy × derinlik. Taşınan hacim zemin türüne göre kabarma oranıyla (%20–50) artar.',
                'Kazı m³ fiyatı zemin türüne göre (normal toprak, sert kil, kaya) uygulanır.',
                'Sefer sayısı = kabarmış hacim ÷ kamyon kapasitesi; sefer başına nakliye ve m³ başına döküm sahası ücreti eklenir.',
                'Derinlik 1,5 m\'yi aşarsa iksa (kazı desteği) önerilir; seçilirse çevre × derinlik m² başına eklenir.',
            ],
            'factors' => [
                ['Zemin', 'Kaya zeminde kırıcı gerekir; kazı m³ maliyeti normal toprağın 2,5 katına çıkar.'],
                ['Döküm sahası', 'Belediyenin izinli döküm alanına uzaklık nakliye maliyetini belirler.'],
                ['İksa', 'Komşu bina veya yol varsa şev yerine iksa gerekir ve maliyet belirgin artar.'],
                ['Yeraltı suyu', 'Su çıkarsa pompaj ve drenaj ek maliyettir.'],
            ],
            'faq' => [
                ['1 m³ hafriyat kaç TL?', 'Normal toprakta kazı, nakliye ve döküm ücreti dahil '.now()->year.' fiyatlarıyla m³ başına yaklaşık '.$this->exampleRange(['soil' => 'normal', 'width' => 15, 'length' => 20, 'depth' => 3, 'truck' => '16'], true).' arasıdır.'],
                ['Toprak ne kadar kabarır?', 'Normal toprak yaklaşık %20, kil %35, kaya %50 kabarır; kamyon hesabı kabarmış hacimle yapılır.'],
                ['Hafriyat için izin gerekir mi?', 'Belediyeden hafriyat toprağı taşıma ve döküm izni (hafriyat kabul belgesi) alınması gerekir.'],
                ['Hesaba neler dahil?', 'Kazı, yükleme, nakliye, döküm sahası ücreti; seçerseniz iksa. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/metrekup-hacim-hesaplama', 'Hacim hesaplama'], ['/araclar/is-makinesi-maliyeti', 'İş makinesi maliyeti'], ['/araclar/beton-hesaplayici', 'Beton hesaplayıcı'], ['/araclar/bahce-duvari-maliyeti', 'Bahçe duvarı']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'numbers', 'title' => 'Kazı ölçüleri', 'cols' => 3, 'fields' => [
                ['key' => 'width', 'label' => 'En', 'unit' => 'm', 'min' => 0.3, 'max' => 500, 'step' => 0.1, 'placeholder' => 'Örn. 15', 'required' => true],
                ['key' => 'length', 'label' => 'Boy', 'unit' => 'm', 'min' => 0.3, 'max' => 500, 'step' => 0.1, 'placeholder' => 'Örn. 20', 'required' => true],
                ['key' => 'depth', 'label' => 'Derinlik', 'unit' => 'm', 'min' => 0.2, 'max' => 30, 'step' => 0.1, 'placeholder' => 'Örn. 3', 'required' => true],
            ]],
            ['type' => 'cards', 'key' => 'soil', 'title' => 'Zemin türü', 'short' => 'Zemin türü', 'required' => true, 'cols' => 3, 'options' => [
                'normal' => ['label' => 'Normal toprak', 'hint' => '%20 kabarma'],
                'sert' => ['label' => 'Kil / sert toprak', 'hint' => '%35 kabarma'],
                'kaya' => ['label' => 'Kaya / parçalı', 'hint' => '%50 kabarma, kırıcı'],
            ]],
            ['type' => 'cards', 'key' => 'truck', 'title' => 'Kamyon kapasitesi', 'short' => 'Kamyon kapasitesi', 'required' => true, 'cols' => 3, 'options' => [
                '10' => ['label' => '10 m³', 'hint' => 'Kırkayak / küçük kamyon'],
                '16' => ['label' => '16 m³', 'hint' => 'Standart damperli'],
                '20' => ['label' => '20 m³', 'hint' => 'Büyük damperli'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['iksa' => 'İksa (kazı desteği)', 'pompa' => 'Su pompajı / drenaj']],
        ];
    }

    private function vol(array $in): array
    {
        $v = (float) $in['width'] * (float) $in['length'] * (float) $in['depth'];
        [$swell] = $this->p()['soils'][$in['soil']];

        return [$v, $v * $swell];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        [$v, $loose] = $this->vol($in);
        [, $dig] = $p['soils'][$in['soil']];
        $trips = (int) ceil($loose / (int) $in['truck']);
        $perim = 2 * ((float) $in['width'] + (float) $in['length']);
        $items = [
            'kazi' => ['label' => 'Kazı ve yükleme', 'mid' => $v * $dig, 'color' => '#FFB800'],
            'nakliye' => ['label' => 'Kamyon nakliyesi ('.$trips.' sefer)', 'mid' => $trips * $p['truck_trip'] * ((int) $in['truck'] / 16), 'color' => '#101318'],
            'dokum' => ['label' => 'Döküm sahası ücreti', 'mid' => $loose * $p['dump_m3'], 'color' => '#5B6472'],
            'iksa' => ['label' => 'İksa', 'mid' => $this->has($in, 'extras', 'iksa') ? $perim * (float) $in['depth'] * $p['shoring_m2'] : 0, 'color' => '#2F6F8F'],
            'pompa' => ['label' => 'Su pompajı / drenaj', 'mid' => $this->has($in, 'extras', 'pompa') ? 15000 + $v * 20 : 0, 'color' => '#7FB3C8'],
        ];
        $days = [max(1, (int) ceil($v / 600)), max(1, (int) ceil($v / 300))];

        return ['items' => $items, 'per_unit' => $v, 'days' => $days,
            'meta' => [['Kazı süresi', $days[0].'–'.$days[1].' iş günü'], ['Hacim (yerinde / kabarmış)', number_format($v, 0, ',', '.').' / '.number_format($loose, 0, ',', '.').' m³'], ['Kamyon seferi', $trips.' sefer']],
            'notes' => (float) $in['depth'] > 1.5 && ! $this->has($in, 'extras', 'iksa') ? ['1,5 m\'den derin kazıda şev veya iksa önerilir.'] : []];
    }

    public function materials(array $in): array
    {
        [$v, $loose] = $this->vol($in);

        return [
            ['Yerinde kazı hacmi', number_format($v, 0, ',', '.'), 'm³', ''],
            ['Taşınacak (kabarmış) hacim', number_format($loose, 0, ',', '.'), 'm³', 'Kabarma dahil'],
            ['Kamyon seferi ('.$in['truck'].' m³)', (int) ceil($loose / (int) $in['truck']), 'sefer', ''],
            ['Ekskavatör çalışma süresi', self::n1(max(1, $v / 60)), 'saat', '≈60 m³/saat (normal toprak)'],
        ];
    }

    public function phases(array $in): array
    {
        return [['İzinler ve aplikasyon', .15], ['Kazı ve yükleme', .7], ['Tesviye ve temizlik', .15]];
    }
}

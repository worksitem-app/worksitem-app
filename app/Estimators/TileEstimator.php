<?php

namespace App\Estimators;

use App\Estimators\Concerns\DbPrices;

class TileEstimator extends KitEstimator
{
    use DbPrices;

    public function key(): string
    {
        return 'seramik';
    }

    public function slug(): string
    {
        return 'seramik-maliyet-hesaplayici';
    }

    public function pricesLabel(): string
    {
        return $this->reviewedLabel();
    }

    private function p(): array
    {
        return (array) config('worksitem_estimators.seramik');
    }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Seramik metraj ve maliyet',
            'h1' => "Seramik döşeme maliyeti ve metraj hesaplama ($y)",
            'lede' => 'Zemin veya duvar alanını girin, karo ebadını ve kalitesini seçin; seramik malzemesi, yapıştırıcı, derz, işçilik ve fire kalem kalem hesaplansın. Kaç kutu, kaç torba gerektiği Pro raporda.',
            'seo_title' => "Seramik Hesaplama $y: m² Maliyet, Kutu ve Yapıştırıcı | Worksitem",
            'seo_desc' => "$y seramik ve porselen döşeme fiyatı: m², karo ebadı (60×60, 60×120), fire, yapıştırıcı ve derz dahil seramik maliyetini kalem kalem, ücretsiz hesaplayın.",
            'service' => 'Seramik Döşeme',
            'wait' => 'Alanı girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Seramik ve yapı kimyasalı markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Fire ve kutu hesabı', '$ € £ karşılığı'],
            'method' => [
                'Kaplanacak alan = seçilen yüzeylere göre zemin m² + duvar m².',
                'Seramik malzemesi: alan × (1 + fire oranı) × kaliteye göre m² fiyatı. Fire oranı ebatla artar (60×60\'ta %10, 120×120\'de %15).',
                'Yapıştırıcı yaklaşık 5 kg/m² (25 kg torba), derz yaklaşık 0,5 kg/m² (5 kg paket) alınır.',
                'İşçilik: m² döşeme fiyatı × ebat katsayısı (büyük ebatta 1,35–1,6). Eski seramik üzerine döşemede %10 eklenir.',
                'Söküm, şap, su yalıtımı, profil ve süpürgelik seçildiyse eklenir; %10 beklenmedik gider payı ile aralık −%15 / +%20 verilir.',
            ],
            'factors' => [
                ['Karo ebadı', 'Büyük ebat (60×120, 120×120) daha fazla fire ve özen ister; işçilik m² fiyatı %35–60 artar.'],
                ['Malzeme', 'Seramikten rektifiyeli porselene geçmek malzeme kalemini 3 katına çıkarabilir.'],
                ['Zemin durumu', 'Eski kaplamanın sökülmesi ve şapla tesviye, toplam maliyete m² başına yaklaşık 750 TL ekler.'],
                ['Islak hacim', 'Banyo ve balkonda seramik altına su yalıtımı yapılmazsa sonradan sızıntı riski doğar.'],
            ],
            'faq' => [
                ['1 m² seramik döşeme kaç TL?', now()->year.' fiyatlarıyla yalnız döşeme işçiliği yaklaşık '.number_format($this->setting('tile_labor_standard_m2', $this->p()['labor_m2']), 0, ',', '.').' TL/m²\'dir; malzeme, yapıştırıcı, derz ve fire eklendiğinde 60×60 mat porselen için toplam m² maliyeti yaklaşık '.$this->exampleRange(['surface' => 'zemin', 'floor' => 20, 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir'], true).' olur.'],
                ['Seramik firesi ne kadar alınmalı?', 'Düz döşemede %10, büyük ebat veya çapraz döşemede %12–15 fire önerilir. Aynı parti (lot) numarasından fazladan 1 kutu almak ileride tamirde işe yarar.'],
                ['1 m² için kaç kg yapıştırıcı gerekir?', 'Karo ebadı ve zemin düzgünlüğüne göre 4–6 kg; 60×60 ve üzeri ebatta çift taraflı sürme nedeniyle üst sınıra yakındır.'],
                ['Eski seramik üzerine seramik döşenir mi?', 'Eski kaplama sağlam, düz ve boşluksuzsa uygun astar ve yapıştırıcıyla döşenebilir; kapı yüksekliği ve gider kotları kontrol edilmelidir.'],
            ],
            'related' => [['/araclar/banyo-maliyet-hesaplayici', 'Banyo tadilat maliyeti'], ['/araclar/mutfak-maliyet-hesaplayici', 'Mutfak tadilat maliyeti'], ['/araclar/seramik-gorsellestirici', 'Seramiği mekânında gör (AI)'], ['/araclar/su-yalitimi-hesaplayici', 'Su yalıtımı maliyeti']],
        ];
    }

    public function perUnitLabel(): ?string
    {
        return 'm² başına';
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'surface', 'title' => 'Nereye döşenecek?', 'short' => 'Yüzey', 'required' => true, 'cols' => 3, 'options' => [
                'zemin' => ['label' => 'Zemin', 'hint' => 'Oda, salon, mutfak zemini'],
                'duvar' => ['label' => 'Duvar', 'hint' => 'Banyo / mutfak duvarı'],
                'zemin_duvar' => ['label' => 'Zemin + duvar', 'hint' => 'Banyo, ıslak hacim'],
            ]],
            ['type' => 'numbers', 'title' => 'Alan', 'cols' => 2, 'required_any' => 'zemin veya duvar alanı', 'fields' => [
                ['key' => 'floor', 'label' => 'Zemin alanı', 'unit' => 'm²', 'min' => 0.5, 'max' => 2000, 'step' => 0.5, 'placeholder' => 'Örn. 20'],
                ['key' => 'wall', 'label' => 'Duvar alanı', 'unit' => 'm²', 'min' => 0.5, 'max' => 2000, 'step' => 0.5, 'placeholder' => 'Örn. 24'],
            ], 'help' => 'Duvar alanı = duvar uzunluğu × yükseklik (kapı ve pencereleri düşün). Seçtiğiniz yüzeye ait alan hesaba katılır.'],
            ['type' => 'cards', 'key' => 'size', 'title' => 'Karo ebadı', 'short' => 'Karo ebadı', 'required' => true, 'cols' => 4, 'options' => [
                '30x60' => ['label' => '30×60', 'hint' => 'Duvar seramiği'],
                '60x60' => ['label' => '60×60', 'hint' => 'En yaygın zemin'],
                '60x120' => ['label' => '60×120', 'hint' => 'Büyük ebat'],
                '120x120' => ['label' => '120×120+', 'hint' => 'Plaka / slab'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Malzeme kalitesi', 'short' => 'Malzeme kalitesi', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Standart seramik'],
                'orta' => ['label' => 'Orta', 'hint' => 'Mat porselen'],
                'premium' => ['label' => 'Premium', 'hint' => 'Rektifiyeli / parlak / ithal'],
            ]],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Mevcut yüzey', 'short' => 'Mevcut yüzey', 'required' => true, 'cols' => 3, 'options' => [
                'hazir' => ['label' => 'Hazır', 'hint' => 'Şap / sıva hazır, sadece döşeme'],
                'sokum' => ['label' => 'Söküm var', 'hint' => 'Eski kaplama kırılıp atılacak'],
                'ustune' => ['label' => 'Üstüne döşeme', 'hint' => 'Eski seramik üzerine'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => [
                'sap' => 'Tesviye şapı (zemin)',
                'yalitim' => 'Su yalıtımı (ıslak hacim)',
                'profil' => 'Köşe profili',
                'supurgelik' => 'Süpürgelik (zemin)',
            ]],
        ];
    }

    private function areas(array $in): array
    {
        $s = $in['surface'] ?? '';
        $floor = in_array($s, ['zemin', 'zemin_duvar'], true) && ! empty($in['_given_floor']) ? (float) $in['floor'] : 0.0;
        $wall = in_array($s, ['duvar', 'zemin_duvar'], true) && ! empty($in['_given_wall']) ? (float) $in['wall'] : 0.0;

        return [$floor, $wall];
    }

    public function missing(array $in): array
    {
        $m = parent::missing($in);
        if (($in['surface'] ?? '') !== '' && ! in_array('zemin veya duvar alanı', $m, true)) {
            [$f, $w] = $this->areas($in);
            if ($f + $w <= 0) {
                $m[] = ['zemin' => 'zemin alanı', 'duvar' => 'duvar alanı', 'zemin_duvar' => 'zemin veya duvar alanı'][$in['surface']];
            }
        }

        return $m;
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        [$floor, $wall] = $this->areas($in);
        $area = $floor + $wall;
        $size = $in['size'];
        $waste = $p['size_waste'][$size];
        $labor = $this->setting('tile_labor_standard_m2', $p['labor_m2']) * $p['size_labor'][$size] * ($in['scope'] === 'ustune' ? 1.1 : 1);
        $adhesive = $this->unitPrice('tile_adhesive_standard_25kg', $p['adhesive_bag']);
        $grout = $this->unitPrice('tile_grout_5kg', $p['grout_pack']);
        $bags = (int) ceil($area * 5 / 25);
        $packs = (int) max(1, ceil($area * 0.5 / 5));
        $perimeter = $floor > 0 ? 4 * sqrt($floor) : 0;

        $items = [
            'sokum' => ['label' => 'Eski kaplama sökümü ve moloz', 'mid' => $in['scope'] === 'sokum' ? $area * $p['demolition_m2'] : 0, 'color' => '#5B6472'],
            'sap' => ['label' => 'Tesviye şapı', 'mid' => $this->has($in, 'extras', 'sap') ? $floor * $p['screed_m2'] : 0, 'color' => '#2F6F8F'],
            'yalitim' => ['label' => 'Su yalıtımı', 'mid' => $this->has($in, 'extras', 'yalitim') ? $area * $p['waterproof_m2'] : 0, 'color' => '#7FB3C8'],
            'seramik' => ['label' => 'Seramik / porselen malzeme (%'.round($waste * 100).' fire dahil)', 'mid' => $area * (1 + $waste) * $this->q($in, $p['tile_m2']), 'color' => '#FFB800'],
            'yapistirici' => ['label' => 'Yapıştırıcı ve derz', 'mid' => $bags * $adhesive + $packs * $grout, 'color' => '#8A6200'],
            'iscilik' => ['label' => 'Döşeme işçiliği', 'mid' => $area * $labor, 'color' => '#101318'],
            'profil' => ['label' => 'Köşe profili', 'mid' => $this->has($in, 'extras', 'profil') ? max(6, $perimeter * 0.5 + $wall * 0.3) * $p['profile_m'] : 0, 'color' => '#C9A55A'],
            'supurgelik' => ['label' => 'Süpürgelik', 'mid' => $this->has($in, 'extras', 'supurgelik') && $floor > 0 ? $perimeter * $p['skirting_m'] : 0, 'color' => '#3F7D5B'],
        ];
        $days = [max(1, (int) ceil($area / 25)), max(2, (int) ceil($area / 15))];
        if ($in['scope'] === 'sokum') {
            $days = [$days[0] + 1, $days[1] + 2];
        }

        return [
            'items' => $items,
            'per_unit' => $area,
            'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Kaplanacak alan', self::n1($area).' m²']],
            'notes' => $this->has($in, 'extras', 'sap') || $in['scope'] !== 'hazir' ? [] : ['Zemin düzgün değilse tesviye şapı gerekebilir.'],
        ];
    }

    public function materials(array $in): array
    {
        $p = $this->p();
        [$floor, $wall] = $this->areas($in);
        $area = $floor + $wall;
        $size = $in['size'];
        $need = $area * (1 + $p['size_waste'][$size]);
        $box = $p['box_m2'][$size];
        $rows = [
            ['Karo ('.$this->steps()[2]['options'][$size]['label'].')', self::n1($need), 'm²', 'Alan '.self::n1($area).' m² + %'.round($p['size_waste'][$size] * 100).' fire'],
            ['Karo kutusu', (int) ceil($need / $box), 'kutu', 'Kutu başına yaklaşık '.self::n1($box).' m² (markaya göre değişir)'],
            ['Seramik yapıştırıcısı (C2TE, 25 kg)', (int) ceil($area * 5 / 25), 'torba', 'Yaklaşık 5 kg/m²'],
            ['Derz dolgu (5 kg)', (int) max(1, ceil($area * 0.5 / 5)), 'paket', 'Yaklaşık 0,5 kg/m²'],
            ['Seviye ayar klipsi', (int) ceil($area * 4), 'adet', 'Büyük ebatta düzgünlük için'],
        ];
        if ($this->has($in, 'extras', 'yalitim')) {
            $rows[] = ['Çimento esaslı su yalıtımı (20 kg)', (int) ceil($area * 3 / 20), 'kova', '2 kat, yaklaşık 3 kg/m²'];
        }
        if ($this->has($in, 'extras', 'sap') && $floor > 0) {
            $rows[] = ['Tesviye şapı (25 kg)', (int) ceil($floor * 1.6 * 3 / 25), 'torba', 'Ortalama 3 mm, 1,6 kg/m²/mm'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([
            ($in['scope'] ?? '') === 'sokum' ? ['Söküm ve moloz', .2] : null,
            $this->has($in, 'extras', 'sap') ? ['Şap ve kuruma', .2] : null,
            $this->has($in, 'extras', 'yalitim') ? ['Su yalıtımı ve test', .15] : null,
            ['Döşeme', .45],
            ['Derz, silikon ve temizlik', .15],
        ]));
    }

    public function checklist(): array
    {
        return array_merge([
            'Bütün kutuların aynı parti (lot) ve ton numarasından olduğunu kontrol edin.',
            'Zemin terazisini döşemeden önce ölçtürün; 3 mm\'den fazla bozukluk varsa tesviye isteyin.',
            'Islak hacimde su yalıtımı ve göllendirme testi yapılmadan seramiğe geçilmesin.',
        ], array_slice(parent::checklist(), 0, 5));
    }
}

<?php

namespace App\Estimators;

class GardenWallEstimator extends KitEstimator
{
    public function key(): string { return 'bahce_duvari'; }

    public function slug(): string { return 'bahce-duvari-maliyeti'; }

    public function perUnitLabel(): ?string { return 'metre başına'; }

    private function p(): array { return (array) config('worksitem_estimators.bahce'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Bahçe duvarı maliyeti',
            'h1' => "Bahçe duvarı maliyeti hesaplama ($y)",
            'lede' => 'Duvarın uzunluğunu ve yüksekliğini girin, taş, betonarme veya blok duvar seçin; temel, duvar gövdesi, sıva, harpuşta ve üstüne korkuluk kalem kalem hesaplansın.',
            'seo_title' => "Bahçe Duvarı Fiyatı $y: Metre Başına Duvar Maliyeti | Worksitem",
            'seo_desc' => "$y bahçe duvarı fiyatları: taş, betonarme ve blok bahçe duvarının metre maliyetini temel, sıva, harpuşta ve korkuluk dahil ücretsiz hesaplayın.",
            'service' => 'Bahçe Duvarı',
            'wait' => 'Duvar ölçülerini girin ve tipini seçin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Yapı malzemesi markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Temel dahil', '$ € £ karşılığı'],
            'method' => [
                'Temel: uzunluk × 0,5 m × 0,6 m betonarme sürekli temel; temel kazısı uzunluk × 0,7 m × 0,7 m (çalışma payı dahil), kazı ve döküm hafriyat aracıyla aynı fiyatlardan.',
                'Taş duvar 50 cm, betonarme duvar 25 cm kalınlıkla m³ fiyatından; blok duvar m² fiyatından hesaplanır (blokta iki yüz sıva eklenir).',
                'Harpuşta, korkuluk/panel çit ve boya seçilirse metre veya m² başına eklenir.',
                '%10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Duvar tipi', 'Betonarme duvar en dayanıklısıdır; taş duvar estetik ama işçiliği yüksektir.'],
                ['Yükseklik', 'İmar yönetmeliği ve komşu sınırına göre izin verilen yükseklik değişir; belediyeye danışın.'],
                ['Zemin', 'Eğimli arazide temel kademelendirilir ve maliyet artar.'],
                ['Korkuluk', 'Alçak duvar üzerine panel çit, tamamı duvardan daha ekonomik olabilir.'],
            ],
            'faq' => [
                ['Bahçe duvarının metresi kaç TL?', '1,5 m yükseklikte betonarme bahçe duvarı temel dahil '.now()->year.' fiyatlarıyla metre başına yaklaşık '.$this->exampleRange(['type' => 'betonarme', 'length' => 40, 'height' => 1.5], true).' arasıdır.'],
                ['Bahçe duvarı için izin gerekir mi?', 'Birçok belediyede bahçe duvarı yüksekliği sınırlıdır (genellikle 0,5–1 m duvar + korkuluk); yapmadan önce imar durumuna bakın.'],
                ['Temel ne kadar derin olmalı?', 'Don derinliğinin altına inilmelidir; ılıman bölgelerde 50–80 cm yaygındır. Yüksek duvarlarda statik hesap gerekir.'],
                ['Hesaba neler dahil?', 'Temel, duvar gövdesi, işçilik ve (blokta) sıva; seçerseniz harpuşta, korkuluk ve boya. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/duvar-maliyet-hesaplayici', 'Duvar maliyeti'], ['/araclar/hafriyat-kazi-hesabi', 'Hafriyat'], ['/araclar/saha-betonu-hesaplayici', 'Saha betonu'], ['/araclar/beton-hesaplayici', 'Beton hesaplayıcı']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'type', 'title' => 'Duvar tipi', 'short' => 'Duvar tipi', 'required' => true, 'cols' => 3, 'options' => [
                'tas' => ['label' => 'Taş duvar', 'hint' => '≈50 cm, harçlı'],
                'betonarme' => ['label' => 'Betonarme', 'hint' => '≈25 cm, demir + kalıp'],
                'blok' => ['label' => 'Blok / briket + sıva', 'hint' => 'En ekonomik'],
            ]],
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 2, 'fields' => [
                ['key' => 'length', 'label' => 'Duvar uzunluğu', 'unit' => 'm', 'min' => 1, 'max' => 5000, 'step' => 0.5, 'placeholder' => 'Örn. 40', 'required' => true],
                ['key' => 'height', 'label' => 'Ortalama yükseklik', 'unit' => 'm', 'min' => 0.3, 'max' => 4, 'step' => 0.1, 'placeholder' => 'Örn. 1,5', 'required' => true],
            ], 'help' => 'Yükseklik zemin üstü duvar yüksekliğidir; temel ayrıca hesaplanır.'],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['coping' => 'Harpuşta (duvar başlığı)', 'fence' => 'Üstüne korkuluk / panel çit', 'paint' => 'Dış cephe boyası (iki yüz)']],
        ];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $L = (float) $in['length'];
        $H = (float) $in['height'];
        $body = match ($in['type']) {
            'tas' => $L * $H * 0.5 * $p['stone_m3'],
            'betonarme' => $L * $H * 0.25 * $p['concrete_m3'],
            default => $L * $H * $p['block_m2'],
        };
        // Temel kazısı (v118): 0,5 m temel + iki yanda 10 cm çalışma payı, 0,6 m + 10 cm tesviye → 0,7 × 0,7 m kesit.
        // Birim fiyat hafriyat aracından: kazı + kabarmış hacmin nakliye ve döküm bedeli
        $h = (array) config('worksitem_estimators.hafriyat');
        [$swell, $digM3] = $h['soils']['normal'];
        $trenchM3 = $digM3 + $swell * ($h['truck_trip'] / 16 + $h['dump_m3']);
        $items = [
            'kazi' => ['label' => 'Temel kazısı ve moloz atımı', 'mid' => $L * 0.7 * 0.7 * $trenchM3, 'color' => '#7FB3C8'],
            'temel' => ['label' => 'Sürekli temel (betonarme)', 'mid' => $L * 0.5 * 0.6 * $p['footing_m3'], 'color' => '#5B6472'],
            'govde' => ['label' => ['tas' => 'Taş duvar gövdesi', 'betonarme' => 'Betonarme duvar (beton, demir, kalıp)', 'blok' => 'Blok duvar örgüsü'][$in['type']], 'mid' => $body, 'color' => '#FFB800'],
            'siva' => ['label' => 'Sıva (iki yüz)', 'mid' => $in['type'] === 'blok' ? $L * $H * 2 * $p['plaster_m2'] : 0, 'color' => '#C9A55A'],
            'harpusta' => ['label' => 'Harpuşta', 'mid' => $this->has($in, 'extras', 'coping') ? $L * $p['coping_m'] : 0, 'color' => '#8A6200'],
            'korkuluk' => ['label' => 'Korkuluk / panel çit', 'mid' => $this->has($in, 'extras', 'fence') ? $L * $p['fence_m'] : 0, 'color' => '#101318'],
            'boya' => ['label' => 'Boya (iki yüz)', 'mid' => $this->has($in, 'extras', 'paint') ? $L * $H * 2 * $p['paint_m2'] : 0, 'color' => '#7FB3C8'],
        ];
        $days = [max(3, (int) ceil($L / 15)), max(5, (int) ceil($L / 7))];

        return ['items' => $items, 'per_unit' => $L, 'days' => $days, 'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Duvar yüzeyi', self::n1($L * $H).' m²']]];
    }

    public function materials(array $in): array
    {
        $L = (float) $in['length'];
        $H = (float) $in['height'];
        $rows = [['Temel kazısı', self::n1($L * 0.7 * 0.7), 'm³', '0,7 × 0,7 m kesit (çalışma payı dahil)'], ['Temel betonu (C25)', self::n1($L * 0.3 * 1.05), 'm³', '0,5 × 0,6 m kesit + %5']];
        if ($in['type'] === 'betonarme') {
            $rows[] = ['Duvar betonu (C25)', self::n1($L * $H * 0.25 * 1.05), 'm³', '25 cm kalınlık'];
            $rows[] = ['İnşaat demiri', number_format($L * ($H * 0.25 + 0.3) * 90, 0, ',', '.'), 'kg', '≈90 kg/m³'];
            $rows[] = ['Kalıp', self::n1($L * $H * 2), 'm²', 'İki yüz'];
        } elseif ($in['type'] === 'tas') {
            $rows[] = ['Moloz / kesme taş', self::n1($L * $H * 0.5 * 1.1), 'm³', '%10 fire'];
            $rows[] = ['Çimento (50 kg)', (int) ceil($L * $H * 0.5 * 0.3 * 250 / 50), 'torba', 'Harç ≈%30'];
        } else {
            $rows[] = ['Briket / blok', (int) ceil($L * $H * 12.5 * 1.05), 'adet', '≈12,5 adet/m²'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return [['Aplikasyon ve temel kazısı', .15], ['Temel betonu', .15], ['Duvar gövdesi', .45], ['Sıva / harpuşta / korkuluk', .2], ['Temizlik', .05]];
    }
}

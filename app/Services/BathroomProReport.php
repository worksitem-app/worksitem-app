<?php

namespace App\Services;

/**
 * Banyo Pro rapor içeriği: 3 kalite karşılaştırması, yaklaşık malzeme listesi,
 * iş programı, ödeme planı önerisi ve usta seçme kontrol listesi.
 * Miktarlar sektörde yaygın sarfiyat oranlarıyla yaklaşık hesaplanır; kesin metraj keşifte yapılır.
 */
class BathroomProReport
{
    public function __construct(private BathroomEstimator $estimator) {}

    /** @return array<string, mixed> */
    public function build(array $input): array
    {
        $cfg = $this->estimator->config();
        $est = $this->estimator->estimate($input);
        $type = $cfg['types'][$input['type']] ?? $cfg['types']['standart'];
        $full = ($input['scope'] ?? 'komple') !== 'yuzey';

        $compare = [];
        foreach ($cfg['qualities'] as $k => $q) {
            $e = $this->estimator->estimate(array_merge($input, ['quality' => $k]));
            $compare[$k] = ['label' => $q['label'], 'hint' => $q['hint'], 'low' => $e['low'], 'mid' => $e['mid'], 'high' => $e['high'], 'selected' => $k === $input['quality']];
        }

        $tile = $est['tile_area'];
        $floor = $est['floor'];
        $perimeter = 2 * ($input['w'] + $input['l']);
        $wpArea = $full ? $floor + ($type['shower'] ? 3.0 : 0) : 0;

        $materials = [
            ['Seramik / porselen karo', ceil($tile * 1.10), 'm²', 'Kaplama alanı '.number_format($tile, 1, ',', '.').' m² + %10 kesim firesi'],
            ['Seramik yapıştırıcısı (C2TE, 25 kg)', (int) ceil($tile * 5 / 25), 'torba', 'Yaklaşık 5 kg/m²'],
            ['Derz dolgu (5 kg)', (int) max(1, ceil($tile * 0.5 / 5)), 'paket', 'Yaklaşık 0,5 kg/m² (2–3 mm derz)'],
            ['Silikon (sıhhi tesisat)', 2, 'tüp', 'Lavabo, duş ve köşe birleşimleri'],
        ];
        if ($full) {
            $materials[] = ['Çimento esaslı su yalıtımı (2 kat, 20 kg)', (int) ceil($wpArea * 3 / 20), 'kova', 'Zemin + duş bölgesi '.number_format($wpArea, 1, ',', '.').' m², yaklaşık 3 kg/m²'];
            $materials[] = ['Su yalıtım köşe bandı', (int) ceil($perimeter + 2), 'm', 'Duvar–zemin birleşimi + duş köşeleri'];
            $materials[] = ['PPRC temiz su borusu ve fittings', (int) ceil(6 + $floor * 1.5), 'm', 'Yaklaşık; yerleşime göre değişir'];
            $materials[] = ['PVC pis su borusu (Ø50/Ø100)', (int) ceil(3 + $floor * 0.8), 'm', 'Yaklaşık; eğim %1–2'];
        }
        $materials[] = ['Klozet + lavabo + bataryalar', 1, 'takım', 'Seçilen kalite: '.($cfg['qualities'][$input['quality']]['label'] ?? '')];
        if ($type['shower'] && isset($cfg['showers'][$input['shower']])) {
            $materials[] = [$cfg['showers'][$input['shower']]['label'], 1, 'adet', 'Ölçü keşifte alınır'];
        }
        foreach ($input['extras'] as $x) {
            $materials[] = [$cfg['extras'][$x]['label'] ?? $x, 1, 'adet/iş', 'Seçilen ekstra'];
        }

        // İş programı: süre aralığının ortası, aşama payları
        [$d0, $d1] = $est['days'];
        $total = (int) round(($d0 + $d1) / 2);
        $phases = $full
            ? [['Söküm, kırım ve moloz', .12], ['Su ve gider tesisatı', .20], ['Su yalıtımı + kuruma ve su testi', .16], ['Seramik (zemin + duvar), derz', .30], ['Vitrifiye, duş ve aksesuar montajı', .14], ['Silikon, temizlik ve teslim', .08]]
            : [['Vitrifiye sökümü ve hazırlık', .15], ['Seramik (zemin + duvar), derz', .50], ['Vitrifiye, duş ve aksesuar montajı', .25], ['Silikon, temizlik ve teslim', .10]];
        $program = [];
        $day = 1;
        foreach ($phases as [$name, $share]) {
            $len = max(1, (int) round($total * $share));
            $program[] = ['name' => $name, 'from' => $day, 'to' => $day + $len - 1];
            $day += $len;
        }

        $mid = $est['mid'];
        $payments = [
            ['Sözleşme ve malzeme siparişi', 30, (int) round($mid * .30, -2)],
            ['Seramik işleri tamamlandığında', 40, (int) round($mid * .40, -2)],
            ['Teslim ve kontrol sonrası', 30, (int) round($mid * .30, -2)],
        ];

        $checklist = [
            'Teklifi yazılı ve kalem kalem alın; malzeme marka ve modeli teklifte yazsın.',
            'Su yalıtımından sonra en az 24–48 saat su göllendirme testi yapılmasını isteyin.',
            'Zemin eğiminin süzgece doğru olduğunu seramik öncesi kontrol edin.',
            'Moloz taşıma, malzeme nakliyesi ve kat çıkışının kime ait olduğunu netleştirin.',
            'İş takvimini ve gecikme durumunu sözleşmeye yazın.',
            'İşçilik garantisinin süresini ve kapsamını yazılı alın.',
            'Ödemeleri yapılan işe göre aşamalı yapın; tamamını peşin vermeyin.',
            'Fatura ve ürün garanti belgelerini teslimde isteyin.',
        ];

        return compact('est', 'compare', 'materials', 'program', 'payments', 'checklist') + ['totalDays' => $day - 1];
    }
}

<?php

namespace App\Estimators;

use App\Estimators\Concerns\DbPrices;

/** Saha betonu (zemin betonu): beton, hasır, pompa, perdah — beton fiyatları admindeki beton bağlantılarından */
class SlabEstimator extends KitEstimator
{
    use DbPrices;

    public function key(): string { return 'saha_betonu'; }

    public function slug(): string { return 'saha-betonu-hesaplayici'; }

    public function perUnitLabel(): ?string { return 'm² başına'; }

    public function pricesLabel(): string { return $this->reviewedLabel(); }

    private function p(): array { return (array) config('worksitem_estimators.saha'); }

    public function contingencyRate(): float { return 0.05; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Saha betonu maliyeti',
            'h1' => "Saha betonu (zemin betonu) maliyeti hesaplama ($y)",
            'lede' => 'Alanı ve beton kalınlığını girin, beton sınıfını, hasırı ve yüzey işlemini seçin; hazır beton, çelik hasır, pompa, perdah ve işçilik kalem kalem hesaplansın.',
            'seo_title' => "Saha Betonu Fiyatı $y: m² Zemin Betonu ve Perdah | Worksitem",
            'seo_desc' => "$y saha betonu fiyatları: bahçe, otopark ve depo zemin betonunun m² maliyetini hazır beton, çelik hasır, pompa ve helikopter perdah dahil ücretsiz hesaplayın.",
            'service' => 'Saha Betonu',
            'wait' => 'Alanı ve kalınlığı girin, seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Hazır beton ve yapı kimyasalı firmaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Mikser ve hasır adedi', '$ € £ karşılığı'],
            'method' => [
                'Beton hacmi = alan × kalınlık × %5 fire; m³ fiyatı seçilen beton sınıfına göre (admin fiyat bağlantısı varsa oradan).',
                'Çelik hasır, yüzey işlemi (helikopter perdah veya çekme) ve beton döküm işçiliği eklenir.',
                'Mobil pompa döküm başına; stabilize dolgu, naylon ve derz kesimi seçilirse eklenir.',
                '%5 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Kalınlık', 'Araç trafiği olan alanlarda en az 15 cm, yaya alanlarında 10 cm önerilir.'],
                ['Hasır', 'Ağır yükte çift sıra hasır çatlakları sınırlar.'],
                ['Perdah', 'Helikopter perdah ve kuvars serpme aşınma direnci ve düzgün yüzey sağlar.'],
                ['Alt yapı', 'Sıkıştırılmış stabilize dolgu olmadan beton oturma ve çatlak yapar.'],
            ],
            'faq' => [
                ['1 m² saha betonu kaç TL?', '15 cm C25, tek sıra hasır, helikopter perdah ve pompa dahil '.now()->year.' fiyatlarıyla m² başına yaklaşık '.$this->exampleRange(['area' => 200, 'thickness' => 15, 'class' => 'c25', 'mesh' => 'tek', 'finish' => 'helikopter', 'pump' => 'evet'], true).' arasıdır.'],
                ['Saha betonu kaç cm olmalı?', 'Bahçe yolu ve yaya alanı için 10 cm, otopark ve araç yolu için 15 cm, depo ve forklift trafiği için 20 cm ve üzeri önerilir.'],
                ['Derz neden kesilir?', 'Beton büzülürken kontrolsüz çatlamasın diye 3–5 m arayla derz kesilir.'],
                ['Hesaba neler dahil?', 'Hazır beton, hasır, işçilik, yüzey işlemi; seçerseniz pompa, dolgu, naylon ve derz. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/beton-hesaplayici', 'Beton hesaplayıcı'], ['/araclar/hafriyat-kazi-hesabi', 'Hafriyat'], ['/araclar/epoksi-zemin-hesaplayici', 'Epoksi zemin'], ['/araclar/demir-metraji-hesaplayici', 'Demir metrajı']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'numbers', 'title' => 'Ölçüler', 'cols' => 2, 'fields' => [
                ['key' => 'area_en', 'label' => 'En', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 4'],
                ['key' => 'area_boy', 'label' => 'Boy', 'unit' => 'm', 'min' => 0.1, 'max' => 2000, 'step' => 0.01, 'placeholder' => 'Örn. 5'],
                ['key' => 'area', 'label' => 'Alan (biliyorsanız)', 'from' => ['area_en', 'area_boy'], 'unit' => 'm²', 'min' => 2, 'max' => 100000, 'step' => 1, 'placeholder' => 'Örn. 200', 'required' => true, 'missing' => 'alan (en × boy ya da m²)'],
                ['key' => 'thickness', 'label' => 'Beton kalınlığı', 'unit' => 'cm', 'min' => 5, 'max' => 50, 'step' => 1, 'placeholder' => 'Örn. 15', 'required' => true],
            ]],
            ['type' => 'cards', 'key' => 'class', 'title' => 'Beton sınıfı', 'short' => 'Beton sınıfı', 'required' => true, 'cols' => 3, 'options' => [
                'c20' => ['label' => 'C20/25', 'hint' => 'Bahçe yolu, yaya'],
                'c25' => ['label' => 'C25/30', 'hint' => 'Otopark, genel'],
                'c30' => ['label' => 'C30/37', 'hint' => 'Ağır yük, depo'],
            ]],
            ['type' => 'cards', 'key' => 'mesh', 'title' => 'Çelik hasır', 'short' => 'Hasır', 'required' => true, 'cols' => 3, 'options' => [
                'yok' => ['label' => 'Hasırsız', 'hint' => 'Önerilmez'],
                'tek' => ['label' => 'Tek sıra', 'hint' => '≈3 kg/m²'],
                'cift' => ['label' => 'Çift sıra', 'hint' => '≈6 kg/m²'],
            ]],
            ['type' => 'cards', 'key' => 'finish', 'title' => 'Yüzey işlemi', 'short' => 'Yüzey işlemi', 'required' => true, 'cols' => 2, 'options' => [
                'helikopter' => ['label' => 'Helikopter perdah + kuvars', 'hint' => 'Düzgün, aşınmaya dayanıklı'],
                'korund' => ['label' => 'Helikopter + korund sertleştirici', 'hint' => 'Forklift, ağır trafik, depo'],
                'cekme' => ['label' => 'Mastarla çekme', 'hint' => 'İşlemsiz, kaba'],
            ]],
            ['type' => 'cards', 'key' => 'pump', 'title' => 'Mobil pompa', 'short' => 'Pompa', 'required' => true, 'cols' => 2, 'options' => [
                'evet' => ['label' => 'Pompa gerekli', 'hint' => 'Mikser yanaşamıyorsa'],
                'hayir' => ['label' => 'Gerekmiyor', 'hint' => 'Mikser oluğu yeterli'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['fill' => 'Stabilize dolgu (15 cm, sıkıştırılmış)', 'nylon' => 'Naylon (beton altı)', 'joint' => 'Derz kesimi', 'curing' => 'Kür kimyasalı (püskürtme)', 'densifier' => 'Sıvı yüzey sertleştirici (toz önleyici)']],
        ];
    }

    private function vol(array $in): float
    {
        return (float) $in['area'] * (float) $in['thickness'] / 100 * 1.05;
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $a = (float) $in['area'];
        $v = $this->vol($in);
        $price = $this->toolPrice('beton-hesaplayici', 'concrete_'.$in['class'], $p['concrete_m3'][$in['class']]);
        $pours = max(1, (int) ceil($v / 120));
        $items = [
            'dolgu' => ['label' => 'Stabilize dolgu (sıkıştırılmış)', 'mid' => $this->has($in, 'extras', 'fill') ? $a * 0.15 * $p['fill_m3'] : 0, 'color' => '#5B6472'],
            'naylon' => ['label' => 'Naylon', 'mid' => $this->has($in, 'extras', 'nylon') ? $a * $p['nylon_m2'] : 0, 'color' => '#7FB3C8'],
            'hasir' => ['label' => 'Çelik hasır', 'mid' => $in['mesh'] === 'yok' ? 0 : $a * $p['mesh_m2'][$in['mesh']], 'color' => '#8A6200'],
            'beton' => ['label' => 'Hazır beton '.strtoupper($in['class']).' (%5 fire)', 'mid' => $v * $price, 'color' => '#FFB800'],
            'pompa' => ['label' => 'Mobil pompa ('.$pours.' döküm)', 'mid' => $in['pump'] === 'evet' ? $pours * $this->toolPrice('beton-hesaplayici', 'pump', $p['pump']) : 0, 'color' => '#2F6F8F'],
            'iscilik' => ['label' => 'Döküm ve vibrasyon işçiliği', 'mid' => $v * $p['labor_m3'], 'color' => '#101318'],
            'perdah' => ['label' => ['helikopter' => 'Helikopter perdah + kuvars', 'korund' => 'Helikopter perdah + korund serpme sertleştirici', 'cekme' => 'Mastarla çekme'][$in['finish']] ?? 'Yüzey işlemi', 'mid' => $a * $p['finish_m2'][$in['finish']], 'color' => '#C9A55A'],
            'derz' => ['label' => 'Derz kesimi', 'mid' => $this->has($in, 'extras', 'joint') ? $a * $p['joint_m2'] : 0, 'color' => '#3F7D5B'],
            'kur' => ['label' => 'Kür kimyasalı', 'mid' => $this->has($in, 'extras', 'curing') ? $a * $p['curing_m2'] : 0, 'color' => '#6FA8A0'],
            'sertlestirici' => ['label' => 'Sıvı yüzey sertleştirici', 'mid' => $this->has($in, 'extras', 'densifier') ? $a * $p['densifier_m2'] : 0, 'color' => '#9C8B5E'],
        ];
        $days = [max(1, (int) ceil($a / 400)), max(2, (int) ceil($a / 200))];

        return ['items' => $items, 'per_unit' => $a, 'days' => $days,
            'meta' => [['Uygulama süresi', $days[0].'–'.$days[1].' iş günü'], ['Beton (fire dahil)', self::n1($v).' m³ · '.(int) ceil($v / 8).' mikser']]];
    }

    public function materials(array $in): array
    {
        $a = (float) $in['area'];
        $v = $this->vol($in);
        $rows = [['Hazır beton '.strtoupper($in['class']), self::n1($v), 'm³', (int) ceil($v / 8).' mikser (8 m³)']];
        if ($in['mesh'] !== 'yok') {
            $rows[] = ['Çelik hasır (Q188, 2,15×5 m)', (int) ceil($a * 1.15 / 10.75) * ($in['mesh'] === 'cift' ? 2 : 1), 'adet', 'Bindirme dahil'];
            $rows[] = ['Hasır sehpası / paspayı', (int) ceil($a * 4), 'adet', '≈4 adet/m²'];
        }
        if ($in['finish'] === 'helikopter') {
            $rows[] = ['Kuvars serpme sertleştirici', number_format($a * 4, 0, ',', '.'), 'kg', '≈4 kg/m²'];
        }
        if ($in['finish'] === 'korund') {
            $rows[] = ['Korund / metalik serpme sertleştirici', number_format($a * 5, 0, ',', '.'), 'kg', '≈5 kg/m², ağır trafik'];
        }
        if ($this->has($in, 'extras', 'curing')) {
            $rows[] = ['Kür kimyasalı', self::n1($a * 0.2), 'L', '≈0,2 L/m², perdahtan hemen sonra'];
        }
        if ($this->has($in, 'extras', 'densifier')) {
            $rows[] = ['Sıvı yüzey sertleştirici (lityum silikat)', self::n1($a * 0.15), 'L', '≈0,15 L/m², beton 28 günlük olunca'];
        }
        if ($this->has($in, 'extras', 'fill')) {
            $rows[] = ['Stabilize malzeme', self::n1($a * 0.15 * 1.25), 'm³', 'Sıkışma payı dahil'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return array_values(array_filter([
            $this->has($in, 'extras', 'fill') ? ['Dolgu ve sıkıştırma', .3] : null,
            ['Kalıp, naylon ve hasır', .25], ['Beton dökümü', .15], ['Perdah', .15], ['Kür ve derz', .15],
        ]));
    }
}

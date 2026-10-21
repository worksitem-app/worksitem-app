<?php

namespace App\Estimators;

use App\Estimators\Concerns\DbPrices;

/**
 * Beton onarım maliyeti: hasarlı beton yüzey/kesit onarımı (tamir harcı, korozyon önleyici, aderans astarı,
 * çatlak enjeksiyonu) ve makine / ankraj / kolon altı grout dolgusu.
 * Fiyatlar config('worksitem_estimators.beton_onarim'); Fiyat Merkezi › pricing_settings ile güncellenir.
 */
class ConcreteRepairEstimator extends KitEstimator
{
    use DbPrices;

    public function key(): string { return 'beton_onarim'; }

    public function slug(): string { return 'beton-onarim-maliyeti'; }

    public function pricesLabel(): string { return $this->reviewedLabel(); }

    public function contingencyRate(): float { return 0.10; }

    public function perUnitLabel(): ?string { return null; }

    private function p(): array { return (array) config('worksitem_estimators.beton_onarim'); }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'Beton onarım maliyeti',
            'h1' => "Beton onarım ve grout maliyeti hesaplama ($y)",
            'lede' => 'Hasarlı beton alanını ve onarım derinliğini girin, donatı durumunu ve harç tipini seçin; tamir harcı, korozyon önleyici, astar ve işçilik kalem kalem hesaplansın. Makine ve ankraj montajında grout dolgusu da aynı araçta.',
            'seo_title' => "Beton Onarım Fiyatı $y: Tamir Harcı, Paspayı ve Grout Maliyeti | Worksitem",
            'seo_desc' => "$y beton onarım fiyatları: dökülen paspayı, paslanan donatı, kolon ve kiriş tamiri için tamir harcı, korozyon önleyici ve işçilik; makine altı ve ankraj grout dolgusunun maliyetini ücretsiz hesaplayın.",
            'service' => 'Beton Onarım',
            'wait' => 'İş türünü seçip ölçüleri girin; maliyet burada anında hesaplanır.',
            'per_unit_label' => null,
            'sponsor_title' => 'Tamir harcı ve grout markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Kaç torba harç gerektiği', '$ € £ karşılığı'],
            'method' => [
                'Onarım harcı = hasarlı alan × ortalama derinlik × harç yoğunluğu (çimento esaslı ≈20 kg/m²/cm, epoksi ≈19 kg/m²/cm) + %10 fire.',
                'Donatı paslıysa temizlik ve korozyon önleyici, her onarımda aderans astarı eklenir; kesit kaybı varsa ilave donatı kalemi gelir.',
                'İşçilik yüzeyin konumuna göre çarpanla artar: tavan altı ve cephe, zemin onarımından zordur.',
                'Grout: plaka ölçüsü × dolgu kalınlığı × adet + %10 yayılma; çimento esaslı ≈2.200 kg/m³, epoksi ≈2.000 kg/m³.',
                '%10 beklenmedik gider ve −%15 / +%20 aralık uygulanır. Fiyatlar KDV dahil yaklaşık değerlerdir.',
            ],
            'factors' => [
                ['Donatı korozyonu', 'Paslanan donatı temizlenmeden kapatılırsa onarım birkaç yılda tekrar dökülür; kesit kaybı varsa statik değerlendirme gerekir.'],
                ['Derinlik', '5 cm\'yi aşan onarımda harç katlar halinde veya akışkan harçla kalıp içine dökülür.'],
                ['Harç tipi', 'Epoksi tamir harcı hızlı sertleşir ve kimyasala dayanır ama kg fiyatı çimento esaslının birkaç katıdır.'],
                ['Erişim', 'Kolon, kiriş ve tavan altında iskele, cephede sepetli platform maliyeti artırır.'],
            ],
            'faq' => [
                ['Beton tamir harcı m² kaç TL?', $this->exampleRange(['mode' => 'onarim', 'area' => 10, 'depth' => 3, 'element' => 'kolon', 'rebar' => 'hafif', 'material' => 'cimento']) !== ''
                    ? '3 cm derinlikte, paslı donatılı 10 m² kolon ve kiriş onarımı '.now()->year.' fiyatlarıyla toplam yaklaşık '.$this->exampleRange(['mode' => 'onarim', 'area' => 10, 'depth' => 3, 'element' => 'kolon', 'rebar' => 'hafif', 'material' => 'cimento']).' tutar; harç, korozyon önleyici, astar ve işçilik dahildir.'
                    : 'Hasarlı alan, derinlik ve donatı durumuna göre değişir; sonuç kartında kalem kalem görünür.'],
                ['Paspayı dökülmesi tehlikeli mi?', 'Paspayı dökülüp donatı görünüyorsa korozyon ilerliyordur. Küçük alanda onarım yeterli olabilir; kolonda yaygın dökülme ve kesit kaybı varsa bir inşaat mühendisine incelettirin.'],
                ['Grout harcı nerede kullanılır?', 'Makine ve kolon ayağı plakalarının altında, ankraj ve filiz deliklerinde, köprü mesnetlerinde ve izolatör plakalarında boşluksuz, rötresiz dolgu için kullanılır.'],
                ['Çimento esaslı mı epoksi mi?', 'Genel onarım ve statik yüklü grout işinde çimento esaslı yeterlidir. Titreşimli makine, kimyasal ortam veya ertesi gün yükleme gereken işlerde epoksi tercih edilir.'],
                ['Hesaba neler dahil?', 'Kırım ve yüzey hazırlığı, tamir harcı, korozyon önleyici, aderans astarı ve işçilik; seçerseniz çatlak enjeksiyonu, koruyucu kaplama, ilave donatı ve iskele. Statik proje ve güçlendirme projesi dahil değildir.'],
            ],
            'related' => [['/araclar/karot-dayanim', 'Karot dayanım hesabı'], ['/araclar/deprem-izolator-maliyeti', 'Deprem izolatörü maliyeti'], ['/araclar/beton-hesaplayici', 'Beton hesaplayıcı'], ['/araclar/su-yalitimi-hesaplayici', 'Su yalıtımı maliyeti']],
        ];
    }

    public function steps(): array
    {
        $onarim = ['mode' => ['onarim']];
        $grout = ['mode' => ['grout']];

        return [
            ['type' => 'cards', 'key' => 'mode', 'title' => 'Ne yapılacak?', 'short' => 'İş türü', 'required' => true, 'cols' => 2, 'options' => [
                'onarim' => ['label' => 'Beton onarımı', 'hint' => 'Dökülen paspayı, paslı donatı, bozulmuş yüzey'],
                'grout' => ['label' => 'Grout dolgusu', 'hint' => 'Makine, kolon ayağı ve ankraj altı'],
            ]],

            // ── Onarım ──
            ['type' => 'numbers', 'title' => 'Hasarlı alan', 'cols' => 2, 'show' => $onarim, 'fields' => [
                ['key' => 'area', 'label' => 'Onarılacak alan', 'unit' => 'm²', 'min' => 0.5, 'max' => 20000, 'step' => 0.5, 'placeholder' => 'Örn. 12', 'required' => true],
                ['key' => 'depth', 'label' => 'Ortalama onarım derinliği', 'unit' => 'cm', 'min' => 0.5, 'max' => 15, 'step' => 0.5, 'placeholder' => 'Örn. 3', 'default' => 3],
            ], 'help' => 'Derinlik: gevşek beton kırıldıktan sonra sağlam betona kadar olan ortalama kalınlık. Paspayı dökülmesinde genelde 2–4 cm.'],
            ['type' => 'cards', 'key' => 'element', 'title' => 'Hasar nerede?', 'short' => 'Eleman', 'required' => true, 'cols' => 3, 'show' => $onarim, 'options' => [
                'zemin' => ['label' => 'Zemin / rampa', 'hint' => 'Otopark, depo, merdiven'],
                'perde' => ['label' => 'Perde / duvar', 'hint' => 'Bodrum perdesi, istinat'],
                'kolon' => ['label' => 'Kolon / kiriş', 'hint' => 'Taşıyıcı eleman'],
                'tavan' => ['label' => 'Döşeme altı', 'hint' => 'Tavandan beton dökülmesi'],
                'cephe' => ['label' => 'Balkon / cephe', 'hint' => 'Konsol, saçak, denizlik'],
            ]],
            ['type' => 'cards', 'key' => 'rebar', 'title' => 'Donatı durumu', 'short' => 'Donatı', 'required' => true, 'cols' => 3, 'show' => $onarim, 'options' => [
                'yok' => ['label' => 'Donatı görünmüyor', 'hint' => 'Yüzey bozulması, segregasyon'],
                'hafif' => ['label' => 'Yüzeyde pas', 'hint' => 'Donatı görünüyor, kesit sağlam'],
                'agir' => ['label' => 'Kesit kaybı', 'hint' => 'Donatı incelmiş, kopmuş'],
            ]],
            ['type' => 'cards', 'key' => 'material', 'title' => 'Tamir harcı', 'short' => 'Harç tipi', 'required' => true, 'cols' => 2, 'show' => $onarim, 'options' => [
                'cimento' => ['label' => 'Çimento esaslı (R4)', 'hint' => 'Yapısal, rötresiz, en yaygın'],
                'epoksi' => ['label' => 'Epoksi esaslı', 'hint' => 'Hızlı, kimyasala dayanıklı, ince kesit'],
            ]],
            ['type' => 'numbers', 'title' => 'Çatlak', 'cols' => 1, 'show' => $onarim, 'fields' => [
                ['key' => 'crack', 'label' => 'Enjeksiyonla doldurulacak çatlak', 'unit' => 'mtül', 'min' => 0, 'max' => 5000, 'step' => 0.5, 'placeholder' => 'Yoksa boş bırakın'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'show' => $onarim, 'options' => [
                'kaplama' => 'Koruyucu kaplama (karbonatlaşma ve su geçirmezlik)',
                'iskele' => 'İskele / platform',
            ]],

            // ── Grout ──
            ['type' => 'numbers', 'title' => 'Plaka ve dolgu', 'cols' => 2, 'show' => $grout, 'fields' => [
                ['key' => 'pieces', 'label' => 'Plaka / makine ayağı sayısı', 'unit' => 'adet', 'min' => 1, 'max' => 5000, 'step' => 1, 'placeholder' => 'Örn. 8', 'required' => true],
                ['key' => 'plate_w', 'label' => 'Plaka eni', 'unit' => 'cm', 'min' => 10, 'max' => 600, 'step' => 1, 'placeholder' => 'Örn. 50', 'required' => true],
                ['key' => 'plate_l', 'label' => 'Plaka boyu', 'unit' => 'cm', 'min' => 10, 'max' => 1200, 'step' => 1, 'placeholder' => 'Örn. 50', 'required' => true],
                ['key' => 'gap', 'label' => 'Dolgu kalınlığı', 'unit' => 'cm', 'min' => 1, 'max' => 15, 'step' => 0.5, 'placeholder' => 'Örn. 4', 'default' => 4],
            ], 'help' => 'Dolgu kalınlığı: plaka altı ile beton yüzeyi arasındaki boşluk. Çimento esaslı grout için genelde 2–8 cm.'],
            ['type' => 'numbers', 'title' => 'Ankraj', 'cols' => 1, 'show' => $grout, 'fields' => [
                ['key' => 'anchors', 'label' => 'Grout ile doldurulacak ankraj deliği', 'unit' => 'adet', 'min' => 0, 'max' => 20000, 'step' => 1, 'placeholder' => 'Yoksa boş bırakın'],
            ]],
            ['type' => 'cards', 'key' => 'gmaterial', 'title' => 'Grout tipi', 'short' => 'Grout tipi', 'required' => true, 'cols' => 2, 'show' => $grout, 'options' => [
                'cimento' => ['label' => 'Çimento esaslı', 'hint' => 'Statik yük, kolon ayağı, köprü mesnedi'],
                'epoksi' => ['label' => 'Epoksi esaslı', 'hint' => 'Titreşimli makine, kimyasal ortam, hızlı yükleme'],
            ]],
        ];
    }

    private function grout(array $in): array
    {
        $p = $this->p();
        $n = (int) round((float) ($in['pieces'] ?? 0));
        $vol = $n * (float) $in['plate_w'] / 100 * (float) $in['plate_l'] / 100 * (float) $in['gap'] / 100 * 1.10;
        $epoxy = ($in['gmaterial'] ?? '') === 'epoksi';
        $kg = $vol * ($epoxy ? 2000 : 2200) + (int) round((float) ($in['anchors'] ?? 0)) * $p['anchor_kg'];

        return [$n, $vol, $kg, $epoxy];
    }

    private function repairKg(array $in): float
    {
        $epoxy = ($in['material'] ?? '') === 'epoksi';

        return (float) $in['area'] * (float) $in['depth'] * ($epoxy ? 19 : 20) * 1.10;
    }

    protected function compute(array $in): array
    {
        $p = $this->p();

        if (($in['mode'] ?? '') === 'grout') {
            [$n, $vol, $kg, $epoxy] = $this->grout($in);
            $anchors = (int) round((float) ($in['anchors'] ?? 0));
            $kgPrice = $epoxy ? $this->setting('repair_grout_epoxy_kg', $p['grout_kg']['epoksi']) : $this->setting('repair_grout_cement_kg', $p['grout_kg']['cimento']);
            $items = [
                'hazirlik' => ['label' => 'Yüzey pürüzlendirme, temizlik ve kalıp', 'mid' => $n * $p['grout_prep_each'], 'color' => '#5B6472'],
                'grout' => ['label' => ($epoxy ? 'Epoksi grout' : 'Çimento esaslı akışkan grout').' ('.number_format($kg, 0, ',', '.').' kg)', 'mid' => $kg * $kgPrice, 'color' => '#FFB800'],
                'iscilik' => ['label' => 'Grout dökümü ve kür işçiliği', 'mid' => $n * $p['grout_labor_each'] * ($epoxy ? 1.3 : 1) + $anchors * $p['anchor_labor_each'], 'color' => '#101318'],
            ];
            $days = [1, max(2, (int) ceil($n / 10) + ($epoxy ? 1 : 3))];

            return ['items' => $items, 'days' => $days, 'meta' => [
                ['Dolgu hacmi', self::n1($vol * 1000).' litre'],
                ['Tahmini süre', $days[0].'–'.$days[1].' iş günü (yükleme için '.($epoxy ? '24 saat' : '3–7 gün').' beklenir)'],
            ], 'notes' => ['Makine montajında grout kalınlığı ve tipi üreticinin montaj talimatına göre seçilmelidir.']];
        }

        $a = (float) $in['area'];
        $d = (float) $in['depth'];
        $epoxy = ($in['material'] ?? '') === 'epoksi';
        $f = $p['element_factor'][$in['element']] ?? 1.0;
        $kg = $this->repairKg($in);
        $kgPrice = $epoxy ? $this->setting('repair_mortar_epoxy_kg', $p['mortar_kg']['epoksi']) : $this->setting('repair_mortar_cement_kg', $p['mortar_kg']['cimento']);
        $rebar = (string) $in['rebar'];
        $crack = (float) ($in['crack'] ?? 0);
        $items = [
            'hazirlik' => ['label' => 'Gevşek beton kırımı ve yüzey hazırlığı', 'mid' => $a * ($p['prep_m2'] + $d * $p['prep_cm']) * $f, 'color' => '#5B6472'],
            'pas' => ['label' => 'Donatı pas temizliği ve korozyon önleyici', 'mid' => $rebar === 'yok' ? 0 : $a * $p['corrosion_m2'][$rebar], 'color' => '#8A6200'],
            'astar' => ['label' => 'Aderans astarı', 'mid' => $a * $p['primer_m2'], 'color' => '#C9A55A'],
            'harc' => ['label' => ($epoxy ? 'Epoksi tamir harcı' : 'Çimento esaslı yapısal tamir harcı (R4)').' ('.number_format($kg, 0, ',', '.').' kg)', 'mid' => $kg * $kgPrice, 'color' => '#FFB800'],
            'iscilik' => ['label' => 'Onarım uygulama işçiliği', 'mid' => $a * ($p['labor_m2'] + $d * $p['labor_cm']) * $f, 'color' => '#101318'],
            'donati' => ['label' => 'İlave donatı ve bindirme', 'mid' => $rebar === 'agir' ? $a * $p['rebar_add_m2'] : 0, 'color' => '#2F6F8F'],
            'enjeksiyon' => ['label' => 'Epoksi çatlak enjeksiyonu ('.self::n1($crack).' mtül)', 'mid' => $crack * $p['injection_m'], 'color' => '#7FB3C8'],
            'kaplama' => ['label' => 'Koruyucu kaplama', 'mid' => $this->has($in, 'extras', 'kaplama') ? $a * $p['coating_m2'] : 0, 'color' => '#3F7D5B'],
            'iskele' => ['label' => 'İskele / platform', 'mid' => $this->has($in, 'extras', 'iskele') ? max($p['scaffold_min'], $a * $p['scaffold_m2']) : 0, 'color' => '#9C8B5E'],
        ];
        $days = [max(1, (int) ceil($a / 15)), max(2, (int) ceil($a / 6) + 1)];

        return ['items' => $items, 'per_unit' => $a, 'per_unit_label' => 'onarım m² başına', 'days' => $days,
            'meta' => [['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Onarım hacmi', self::n1($a * $d * 10).' litre']],
            'notes' => $rebar === 'agir' || $in['element'] === 'kolon'
                ? ['Taşıyıcı elemanda kesit kaybı varsa onarımdan önce bir inşaat mühendisi incelemeli; gerekirse güçlendirme projesi hazırlanır.']
                : []];
    }

    public function materials(array $in): array
    {
        $p = $this->p();
        if (($in['mode'] ?? '') === 'grout') {
            [$n, $vol, $kg, $epoxy] = $this->grout($in);

            return [
                $epoxy
                    ? ['Epoksi grout (3 bileşen, 15 kg set)', (int) max(1, ceil($kg / 15)), 'set', '≈2.000 kg/m³']
                    : ['Akışkan grout harcı (25 kg)', (int) max(1, ceil($kg / 25)), 'torba', '≈2.200 kg/m³, rötresiz'],
                ['Kalıp ve sızdırmazlık', $n, 'plaka', 'Kenarlardan taşmaması için'],
            ];
        }
        $a = (float) $in['area'];
        $epoxy = ($in['material'] ?? '') === 'epoksi';
        $kg = $this->repairKg($in);
        $rows = [
            $epoxy
                ? ['Epoksi tamir harcı (15 kg set)', (int) max(1, ceil($kg / 15)), 'set', '≈19 kg/m²/cm, %10 fire']
                : ['Yapısal tamir harcı R4 (25 kg)', (int) max(1, ceil($kg / 25)), 'torba', '≈20 kg/m²/cm, %10 fire'],
            ['Aderans astarı', self::n1($a * 0.3), 'kg', '≈0,3 kg/m²'],
        ];
        if (($in['rebar'] ?? '') !== 'yok') {
            $rows[] = ['Donatı korozyon önleyici', self::n1($a * (($in['rebar'] ?? '') === 'agir' ? 1.0 : 0.5)), 'kg', 'Pası alınan donatıya 2 kat'];
        }
        if ((float) ($in['crack'] ?? 0) > 0) {
            $rows[] = ['Epoksi enjeksiyon reçinesi', self::n1((float) $in['crack'] * 0.25), 'kg', '≈0,25 kg/mtül, paker dahil'];
        }
        if ($this->has($in, 'extras', 'kaplama')) {
            $rows[] = ['Koruyucu kaplama', self::n1($a * 1.5), 'kg', '2 kat, ≈1,5 kg/m²'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return ($in['mode'] ?? '') === 'grout'
            ? [['Yüzey hazırlığı ve kalıp', .3], ['Grout dökümü', .3], ['Kür ve yükleme beklemesi', .4]]
            : [['Kırım ve yüzey hazırlığı', .3], ['Donatı temizliği ve korozyon önleyici', .2], ['Tamir harcı uygulaması', .35], ['Kür ve kaplama', .15]];
    }

    public function checklist(): array
    {
        return array_merge([
            'Teklifte kullanılacak tamir harcının sınıfını (R3, R4) ve markasını yazdırın.',
            'Paslı donatının parlak metal görünene kadar temizlendiğini onarım öncesinde görün.',
            'Taşıyıcı kolon ve kirişte yaygın hasar varsa önce bir inşaat mühendisine inceletin.',
        ], array_slice(parent::checklist(), 0, 5));
    }
}

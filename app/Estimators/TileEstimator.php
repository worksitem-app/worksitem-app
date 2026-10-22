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
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Zemin ve duvar ayrı ürün', 'Fire ve kutu hesabı'],
            'method' => [
                'Kaplanacak alan = seçilen yüzeylere göre zemin m² + duvar m². Zemin + duvarda iki yüzey için ayrı ebat ve malzeme seçilebilir; her biri kendi fire ve işçilik katsayısıyla hesaplanır.',
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
                ['Banyoda zemin ve duvar için ayrı seramik nasıl hesaplanır?', 'Zemin + duvarı seçip iki alanı ayrı girin; duvar için farklı ebat (ör. 30×60) ve malzeme seçtiğinizde her yüzey kendi firesi, kutu sayısı ve işçiliğiyle ayrı satırda hesaplanır. Örneğin 5 m² zemin 60×60 mat porselen ve 22 m² duvar 30×60 seramik için '.now()->year.' fiyatlarıyla toplam yaklaşık '.$this->exampleRange(['surface' => 'zemin_duvar', 'floor' => 5, 'wall' => 22, 'size' => '60x60', 'quality' => 'orta', 'wsize' => '30x60', 'wquality' => 'ekonomik', 'scope' => 'hazir']).' tutar.'],
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
            ['type' => 'cards', 'key' => 'size', 'title' => 'Karo ebadı', 'short' => 'Karo ebadı', 'required' => true, 'cols' => 4, 'hide' => ['surface' => ['zemin_duvar']], 'options' => [
                '30x60' => ['label' => '30×60', 'hint' => 'Duvar seramiği'],
                '60x60' => ['label' => '60×60', 'hint' => 'En yaygın zemin'],
                '60x120' => ['label' => '60×120', 'hint' => 'Büyük ebat'],
                '120x120' => ['label' => '120×120+', 'hint' => 'Plaka / slab'],
            ]],
            ['type' => 'cards', 'key' => 'size', 'title' => 'Zemin karo ebadı', 'short' => 'Zemin karo ebadı', 'required' => true, 'cols' => 4, 'show' => ['surface' => ['zemin_duvar']], 'options' => [
                '30x60' => ['label' => '30×60', 'hint' => 'Duvar seramiği'],
                '60x60' => ['label' => '60×60', 'hint' => 'En yaygın zemin'],
                '60x120' => ['label' => '60×120', 'hint' => 'Büyük ebat'],
                '120x120' => ['label' => '120×120+', 'hint' => 'Plaka / slab'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Malzeme kalitesi', 'short' => 'Malzeme kalitesi', 'required' => true, 'cols' => 3, 'hide' => ['surface' => ['zemin_duvar']], 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Standart seramik'],
                'orta' => ['label' => 'Orta', 'hint' => 'Mat porselen'],
                'premium' => ['label' => 'Premium', 'hint' => 'Rektifiyeli / parlak / ithal'],
            ]],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Zemin malzemesi', 'short' => 'Zemin malzemesi', 'required' => true, 'cols' => 3, 'show' => ['surface' => ['zemin_duvar']], 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Standart seramik'],
                'orta' => ['label' => 'Orta', 'hint' => 'Mat porselen'],
                'premium' => ['label' => 'Premium', 'hint' => 'Rektifiyeli / parlak / ithal'],
            ]],
            ['type' => 'cards', 'key' => 'wsize', 'title' => 'Duvar karo ebadı (zeminden farklıysa)', 'short' => 'Duvar karo ebadı', 'cols' => 4, 'show' => ['surface' => ['zemin_duvar']], 'options' => [
                '30x60' => ['label' => '30×60', 'hint' => 'Duvar seramiği'],
                '60x60' => ['label' => '60×60', 'hint' => 'En yaygın zemin'],
                '60x120' => ['label' => '60×120', 'hint' => 'Büyük ebat'],
                '120x120' => ['label' => '120×120+', 'hint' => 'Plaka / slab'],
            ]],
            ['type' => 'cards', 'key' => 'wquality', 'title' => 'Duvar malzemesi (zeminden farklıysa)', 'short' => 'Duvar malzemesi', 'cols' => 3, 'show' => ['surface' => ['zemin_duvar']], 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => 'Standart seramik'],
                'orta' => ['label' => 'Orta', 'hint' => 'Mat porselen'],
                'premium' => ['label' => 'Premium', 'hint' => 'Rektifiyeli / parlak / ithal'],
            ]],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Mevcut yüzey', 'short' => 'Mevcut yüzey', 'required' => true, 'cols' => 3, 'options' => [
                'hazir' => ['label' => 'Hazır', 'hint' => 'Şap / sıva hazır, sadece döşeme'],
                'sokum' => ['label' => 'Söküm var', 'hint' => 'Eski kaplama kırılıp atılacak'],
                'ustune' => ['label' => 'Üstüne döşeme', 'hint' => 'Eski seramik üzerine'],
            ]],
            ['type' => 'cards', 'key' => 'grout', 'title' => 'Derz dolgu tipi', 'short' => 'Derz dolgu', 'cols' => 2, 'options' => [
                'cimento' => ['label' => 'Çimento esaslı', 'hint' => 'Standart, kuru alanlar ve banyo duvarı'],
                'epoksi' => ['label' => 'Epoksi esaslı', 'hint' => 'Leke ve su tutmaz; duş zemini, mutfak, havuz'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => [
                'sap' => 'Tesviye şapı (zemin)',
                'yalitim' => 'Su yalıtımı (ıslak hacim)',
                'seviye' => 'Karo seviyeleme sistemi (klips + kama)',
                'profil' => 'Köşe profili',
                'supurgelik' => 'Süpürgelik (zemin)',
                'temizlik' => 'Teslim öncesi temizlik ve bakım',
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

    /** @return array{0:string,1:string,2:string,3:string} [zemin ebadı, duvar ebadı, zemin kalitesi, duvar kalitesi] */
    private function specs(array $in): array
    {
        $fs = (string) $in['size'];
        $fq = (string) $in['quality'];

        return [$fs, ($in['wsize'] ?? '') !== '' ? (string) $in['wsize'] : $fs, $fq, ($in['wquality'] ?? '') !== '' ? (string) $in['wquality'] : $fq];
    }

    /** Zemin ve duvar ayrı ürün mü? (ikisi de alanlı ve ebat ya da kalite farklı) */
    private function split(array $in, float $floor, float $wall): bool
    {
        [$fs, $ws, $fq, $wq] = $this->specs($in);

        return $floor > 0 && $wall > 0 && ($fs !== $ws || $fq !== $wq);
    }

    private function sizeLabel(string $size): string
    {
        return ['30x60' => '30×60', '60x60' => '60×60', '60x120' => '60×120', '120x120' => '120×120+'][$size] ?? $size;
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        [$floor, $wall] = $this->areas($in);
        $area = $floor + $wall;
        [$fs, $ws, $fq, $wq] = $this->specs($in);
        $split = $this->split($in, $floor, $wall);
        $base = $this->setting('tile_labor_standard_m2', $p['labor_m2']) * ($in['scope'] === 'ustune' ? 1.1 : 1);
        $laborTotal = $floor * $base * $p['size_labor'][$fs] + $wall * $base * $p['size_labor'][$ws];
        $mat = fn (float $a, string $sz, string $q) => $a * (1 + $p['size_waste'][$sz]) * $this->q(['quality' => $q], $p['tile_m2']);
        $size = $fs;
        $waste = $p['size_waste'][$size];
        $adhesive = $this->unitPrice('tile_adhesive_standard_25kg', $p['adhesive_bag']);
        $grout = $this->unitPrice('tile_grout_5kg', $p['grout_pack']);
        $bags = (int) ceil($area * 5 / 25);
        $epoxy = ($in['grout'] ?? '') === 'epoksi';
        $packs = (int) max(1, ceil($area * 0.5 / 5));
        $epoxySets = (int) max(1, ceil($area * $p['epoxy_grout_kg_m2'] / 3));
        $groutCost = $epoxy
            ? $epoxySets * $this->setting('tile_epoxy_grout_3kg', $p['epoxy_grout_3kg']) + $area * $this->setting('tile_epoxy_grout_labor_m2', $p['epoxy_grout_labor_m2'])
            : $packs * $grout;
        $clips = (int) ceil($floor * $p['level_clips_m2'][$fs] + $wall * $p['level_clips_m2'][$ws]);
        $perimeter = $floor > 0 ? 4 * sqrt($floor) : 0;

        $items = [
            'sokum' => ['label' => 'Eski kaplama sökümü ve moloz', 'mid' => $in['scope'] === 'sokum' ? $area * $p['demolition_m2'] : 0, 'color' => '#5B6472'],
            'sap' => ['label' => 'Tesviye şapı', 'mid' => $this->has($in, 'extras', 'sap') ? $floor * $p['screed_m2'] : 0, 'color' => '#2F6F8F'],
            'yalitim' => ['label' => 'Su yalıtımı', 'mid' => $this->has($in, 'extras', 'yalitim') ? $area * $p['waterproof_m2'] : 0, 'color' => '#7FB3C8'],
            'seramik' => ['label' => $split ? 'Zemin karosu '.$this->sizeLabel($fs).' (%'.round($p['size_waste'][$fs] * 100).' fire dahil)' : 'Seramik / porselen malzeme (%'.round($waste * 100).' fire dahil)',
                'mid' => $split ? $mat($floor, $fs, $fq) : $mat($area, $fs, $fq), 'color' => '#FFB800'],
            'seramik_duvar' => ['label' => 'Duvar karosu '.$this->sizeLabel($ws).' (%'.round($p['size_waste'][$ws] * 100).' fire dahil)', 'mid' => $split ? $mat($wall, $ws, $wq) : 0, 'color' => '#E0A100'],
            'yapistirici' => ['label' => $epoxy ? 'Yapıştırıcı' : 'Yapıştırıcı ve derz', 'mid' => $bags * $adhesive + ($epoxy ? 0 : $groutCost), 'color' => '#8A6200'],
            'derz_epoksi' => ['label' => 'Epoksi derz dolgu ve uygulama', 'mid' => $epoxy ? $groutCost : 0, 'color' => '#6E4F00'],
            'seviye' => ['label' => 'Karo seviyeleme sistemi ('.number_format($clips, 0, ',', '.').' klips)', 'mid' => $this->has($in, 'extras', 'seviye') ? $clips * $this->setting('tile_level_clip', $p['level_clip']) : 0, 'color' => '#9C8B5E'],
            'iscilik' => ['label' => 'Döşeme işçiliği', 'mid' => $laborTotal, 'color' => '#101318'],
            'profil' => ['label' => 'Köşe profili', 'mid' => $this->has($in, 'extras', 'profil') ? max(6, $perimeter * 0.5 + $wall * 0.3) * $p['profile_m'] : 0, 'color' => '#C9A55A'],
            'supurgelik' => ['label' => 'Süpürgelik', 'mid' => $this->has($in, 'extras', 'supurgelik') && $floor > 0 ? $perimeter * $p['skirting_m'] : 0, 'color' => '#3F7D5B'],
            'temizlik' => ['label' => 'Teslim öncesi temizlik ve bakım', 'mid' => $this->has($in, 'extras', 'temizlik') ? $area * $this->setting('tile_cleaning_m2', $p['cleaning_m2']) : 0, 'color' => '#6FA8A0'],
        ];
        $days = [max(1, (int) ceil($area / 25)), max(2, (int) ceil($area / 15))];
        if ($in['scope'] === 'sokum') {
            $days = [$days[0] + 1, $days[1] + 2];
        }
        $days[1] = max($days[1], $days[0] + 1);

        return [
            'items' => $items,
            'per_unit' => $area,
            'days' => $days,
            'meta' => array_merge([['Tahmini süre', $days[0].'–'.$days[1].' iş günü'], ['Kaplanacak alan', self::n1($area).' m²']],
                $split ? [['Zemin / duvar', self::n1($floor).' m² · '.self::n1($wall).' m²']] : []),
            'notes' => $this->has($in, 'extras', 'sap') || $in['scope'] !== 'hazir' ? [] : ['Zemin düzgün değilse tesviye şapı gerekebilir.'],
        ];
    }

    public function materials(array $in): array
    {
        $p = $this->p();
        [$floor, $wall] = $this->areas($in);
        $area = $floor + $wall;
        [$fs, $ws] = $this->specs($in);
        $tile = function (string $name, float $a, string $sz) use ($p) {
            $need = $a * (1 + $p['size_waste'][$sz]);

            return [
                [$name.' ('.$this->sizeLabel($sz).')', self::n1($need), 'm²', 'Alan '.self::n1($a).' m² + %'.round($p['size_waste'][$sz] * 100).' fire'],
                [$name.' kutusu', (int) ceil($need / $p['box_m2'][$sz]), 'kutu', 'Kutu başına yaklaşık '.self::n1($p['box_m2'][$sz]).' m² (markaya göre değişir)'],
            ];
        };
        $rows = $this->split($in, $floor, $wall)
            ? array_merge($tile('Zemin karosu', $floor, $fs), $tile('Duvar karosu', $wall, $ws))
            : $tile('Karo', $area, $fs);
        $rows = array_merge($rows, [
            ['Seramik yapıştırıcısı (C2TE, 25 kg)', (int) ceil($area * 5 / 25), 'torba', 'Yaklaşık 5 kg/m²'],
            ($in['grout'] ?? '') === 'epoksi'
                ? ['Epoksi derz dolgu (3 kg set)', (int) max(1, ceil($area * $p['epoxy_grout_kg_m2'] / 3)), 'set', 'Yaklaşık '.self::n1($p['epoxy_grout_kg_m2']).' kg/m²']
                : ['Derz dolgu (5 kg)', (int) max(1, ceil($area * 0.5 / 5)), 'paket', 'Yaklaşık 0,5 kg/m²'],
            ['Seviye ayar klipsi', (int) ceil($floor * $p['level_clips_m2'][$fs] + $wall * $p['level_clips_m2'][$ws]), 'adet', 'Ebada göre m² başına '.$p['level_clips_m2'][$fs].' klips; kamalar tekrar kullanılır'],
        ]);
        if ($this->has($in, 'extras', 'temizlik')) {
            $rows[] = ['Seramik temizleyici (inşaat sonrası)', (int) max(1, ceil($area / 40)), 'L', 'Yaklaşık 1 L / 40 m², seyreltilerek'];
        }
        if ($this->has($in, 'extras', 'yalitim')) {
            $rows[] = ['Çimento esaslı su yalıtımı (20 kg)', (int) ceil($area * 3 / 20), 'kova', '2 kat, yaklaşık 3 kg/m²'];
        }
        if ($this->has($in, 'extras', 'sap') && $floor > 0) {
            $rows[] = ['Tesviye şapı (25 kg)', (int) ceil($floor * 1.6 * 3 / 25), 'torba', 'Ortalama 3 mm, 1,6 kg/m²/mm'];
        }

        return $rows;
    }

    public function expertNotes(array $in): array
    {
        [$floor, $wall] = $this->areas($in);
        [$fs, $ws, $fq, $wq] = $this->specs($in);
        $sizes = array_unique(array_filter([$floor > 0 ? $fs : null, $wall > 0 ? $ws : null]));
        $big = (bool) array_intersect($sizes, ['60x60', '60x120', '120x120']);
        $porc = in_array($fq, ['orta', 'premium'], true) || in_array($wq, ['orta', 'premium'], true);
        $notes = [];

        $notes[] = ['Yapıştırıcı sınıfı', $big || $porc
            ? 'Porselen ve 60 cm üzeri karoda en az C2TE, büyük ebatta tercihen C2TE S1 (esnek) yapıştırıcı kullanılır. Karonun arkasına da ince kat sürülür (çift taraflı uygulama); altında boşluk kalan karo zamanla çatlar ya da kopar.'
            : 'Standart seramikte C2TE sınıfı yapıştırıcı yeterlidir. Taraklı mala ile sürülür, karo yerine bastırılarak oturtulur; açık bekleme süresi geçen yapıştırıcının üzerine karo konmaz.'];
        if (in_array('30x60', $sizes, true) || in_array('60x120', $sizes, true)) {
            $notes[] = ['Dikdörtgen karo', 'Uzun kenarı 60 cm ve üzeri dikdörtgen karoda şaşırtma en çok 1/3 kaydırılır. Yarım kaydırmada karonun doğal eğriliği yüzünden kenarlarda kot farkı oluşur.'];
        }
        if ($big || $this->has($in, 'extras', 'seviye')) {
            $notes[] = ['Seviyeleme', 'Klips ve kama sistemi, büyük ebatta karo kenarlarının aynı hizada kalmasını sağlar. Klipsler derz açıklığını da sabitler; kamalar bir sonraki işte tekrar kullanılır.'];
        }
        $notes[] = ($in['grout'] ?? '') === 'epoksi'
            ? ['Epoksi derz', 'Epoksi derz küçük partiler hâlinde karıştırılır; çalışma süresi sınırlıdır. Taşan kısım sertleşmeden su ve sünger ile alınır, yoksa yüzeyde parlak film kalır. Duş zemini ve mutfak tezgâh arkası için en dayanıklı seçimdir.']
            : ['Derz', 'Derz dolgu, yapıştırıcı kuruduktan sonra (genellikle 24 saat) yapılır. Rektifiyeli karoda 2 mm, standart karoda 3 mm derz aralığı bırakılır; ıslak hacim zemininde epoksi derz daha uzun ömürlüdür.'];
        if ($this->has($in, 'extras', 'yalitim')) {
            $notes[] = ['Su yalıtımı', 'Çimento esaslı yalıtım iki kat ve çapraz yönde sürülür; duvar-zemin köşelerine ve gider çevresine yalıtım bandı konur. Seramikten önce 24–48 saat göllendirme testi yapılır.'];
        }
        if ($this->has($in, 'extras', 'sap')) {
            $notes[] = ['Tesviye', 'Tesviye şapının üzerine döşeme için üreticinin bekleme süresi beklenir. Zemindeki 2 m mastarda 3 mm üzeri bozukluk, yapıştırıcıyla kapatılmaz.'];
        }
        if (($in['scope'] ?? '') === 'ustune') {
            $notes[] = ['Eski seramik üstüne', 'Eski karo sağlam ve boşluksuz olmalıdır; tıklatıldığında boş ses veren karo sökülür. Yüzey yağdan arındırılır ve uygun astar sürülür.'];
        }

        return $notes;
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

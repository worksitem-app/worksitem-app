<?php

namespace App\Estimators;

/**
 * Hesap motoru (banyo tarzı): her araç = adım şeması + sunucu tarafı hesap.
 * Sayfa, ölçü/seçim değiştikçe /araclar/{slug}/hesapla uç noktasını çağırır;
 * aynı hesap Pro rapor PDF'inde de kullanılır (tek kaynak).
 *
 * Adım tipleri:
 *  - cards:   ['type'=>'cards','key'=>..,'title'=>..,'options'=>[k=>['label'=>..,'hint'=>..]],'required'=>bool,'cols'=>2|3|4]
 *  - numbers: ['type'=>'numbers','title'=>..,'fields'=>[['key','label','unit','min','max','step','placeholder','required','default']],'help'=>..]
 *  - checks:  ['type'=>'checks','key'=>..,'title'=>..,'options'=>[k=>label]]
 *
 * Koşullu adım: 'show' => ['target' => ['dis_cephe']] → yalnız o seçimde görünür ve hesaba girer;
 * 'hide' => ['target' => ['dis_cephe']] → o seçimde gizlenir (seçim yapılmamışken görünür).
 * Aynı anahtar iki koşullu adımda kullanılabilir (ör. iç/dış cephe için ayrı "kalite" seçenekleri).
 */
abstract class KitEstimator
{
    /** Kısa anahtar (lead kaynağı: {key}_pro_rapor) */
    abstract public function key(): string;

    abstract public function slug(): string;

    /**
     * Sayfa metinleri: title, h1, lede, crumb, seo_title, seo_desc, service (teklif formu),
     * wait (ölçü beklenirken metin), per_unit_label, faq [[q,a]], factors [[t,d]], related [[href,label]], method [..]
     */
    abstract public function page(): array;

    abstract public function steps(): array;

    /**
     * @return array{items: array<string, array{label:string, mid:float}>, per_unit?: float, meta?: array<int, array{0:string,1:string}>, days?: array{0:int,1:int}, notes?: list<string>}
     */
    abstract protected function compute(array $in): array;

    /** Kalem renkleri (sıra ile) */
    protected array $palette = ['#5B6472', '#2F6F8F', '#7FB3C8', '#FFB800', '#101318', '#8A6200', '#C9A55A', '#3F7D5B', '#B4532A', '#D5D9DF'];

    /** "m² başına" gibi birim fiyat etiketi (page() içinde çağırmayın: page() örnek hesap yapabilir) */
    public function perUnitLabel(): ?string
    {
        return null;
    }

    public function band(): array
    {
        return ['low' => 0.85, 'high' => 1.20];
    }

    public function contingencyRate(): float
    {
        return 0.10;
    }

    public function pricesLabel(): string
    {
        return \App\Support\PriceDate::for($this->slug())->locale('tr')->translatedFormat('j F Y');
    }

    /** Sponsor kategorileri (config/worksitem_sponsors.php ile aynı) */
    /**
     * 2D / 3D planlayıcı bağlantısı (yoksa null).
     * ['url' => ..., 'label' => ..., 'hint' => ..., 'map' => [kit alanı => planlayıcı parametresi], 'checks' => [kit onay kutusu grubu => [değerler]]]
     */
    public function planner(): ?array
    {
        return null;
    }

    /**
     * Sonuç kutusunda görselleştirme yönlendirmeleri: [['url', 'label', 'hint'], ...]
     * Yalnız yayında olan araçlar gösterilir.
     */
    public function visuals(): array
    {
        return [];
    }

    /** Görsel aracı yayında mı (araç listesinde yoksa her zaman açık) */
    protected static function toolOpen(string $slug): bool
    {
        $st = (array) config('worksitem_tools.tool_status', []);
        if (! array_key_exists($slug, (array) config('worksitem_tools.tools', [])) || ! empty($st['staging_test_mode'])) {
            return true;
        }

        return (($st['overrides'][$slug] ?? $st['default'] ?? 'review') === 'active');
    }

    /** @param list<array{0:string,1:string,2:string,3:string}> $list [araç slug, adres, başlık, açıklama] */
    protected static function openVisuals(array $list): array
    {
        return array_values(array_map(fn ($v) => ['url' => $v[1], 'label' => $v[2], 'hint' => $v[3]], array_filter($list, fn ($v) => self::toolOpen($v[0]))));
    }

    public function sponsorTitle(): string
    {
        return 'Projeniz için markalar';
    }

    // ───────────────────────── Girdi ─────────────────────────

    public function normalize(array $raw): array
    {
        $in = [];
        foreach ($this->steps() as $step) {
            switch ($step['type']) {
                case 'cards':
                    $v = (string) ($raw[$step['key']] ?? '');
                    $in[$step['key']] = isset($step['options'][$v]) ? $v : '';
                    break;
                case 'numbers':
                    foreach ($step['fields'] as $f) {
                        $s = str_replace(',', '.', trim((string) ($raw[$f['key']] ?? '')));
                        $n = is_numeric($s) ? (float) $s : null;
                        if ($n !== null) {
                            $n = min((float) $f['max'], max((float) $f['min'], $n));
                        }
                        $in[$f['key']] = $n ?? ($f['default'] ?? null);
                        $in['_given_'.$f['key']] = $n !== null;
                    }
                    break;
                case 'checks':
                    $vals = $raw[$step['key']] ?? [];
                    $vals = is_array($vals) ? $vals : explode(',', (string) $vals);
                    // tekil ?ex_x=1 biçimi de kabul edilir
                    foreach (array_keys($step['options']) as $k) {
                        if (($raw[$k] ?? null) === '1' || ($raw[$k] ?? null) === 1) {
                            $vals[] = $k;
                        }
                    }
                    $in[$step['key']] = array_values(array_intersect(array_keys($step['options']), array_map('strval', $vals)));
                    break;
            }
        }

        // Ölçüden alan: 'from' => [en alanı, boy alanı] — alan girilmemişse en × boy kullanılır
        foreach ($this->steps() as $step) {
            if ($step['type'] !== 'numbers') {
                continue;
            }
            foreach ($step['fields'] as $f) {
                [$a, $b] = array_pad((array) ($f['from'] ?? []), 2, null);
                if ($a && $b && empty($in['_given_'.$f['key']]) && ! empty($in['_given_'.$a]) && ! empty($in['_given_'.$b])) {
                    $in[$f['key']] = min((float) $f['max'], max((float) $f['min'], round((float) $in[$a] * (float) $in[$b], 2)));
                    $in['_given_'.$f['key']] = true;
                }
            }
        }

        // Görünmeyen koşullu adımların değerleri hesaba girmez (aynı anahtar görünen bir adımda yoksa)
        $visibleKeys = [];
        foreach ($this->steps() as $step) {
            if ($this->visible($step, $in)) {
                foreach ($this->stepKeys($step) as $k) {
                    $visibleKeys[$k] = true;
                }
            }
        }
        foreach ($this->steps() as $step) {
            if ($this->visible($step, $in)) {
                continue;
            }
            foreach ($this->stepKeys($step) as $k) {
                if (isset($visibleKeys[$k])) {
                    continue;
                }
                $in[$k] = match ($step['type']) { 'checks' => [], 'cards' => '', default => null };
                if ($step['type'] === 'numbers') {
                    $in['_given_'.$k] = false;
                }
            }
        }

        return $in;
    }

    /** Adım görünür mü? ('show' => [anahtar => [değerler]]) */
    public function visible(array $step, array $in): bool
    {
        foreach ((array) ($step['show'] ?? []) as $k => $vals) {
            if (! in_array((string) ($in[$k] ?? ''), array_map('strval', (array) $vals), true)) {
                return false;
            }
        }
        foreach ((array) ($step['hide'] ?? []) as $k => $vals) {
            if (in_array((string) ($in[$k] ?? ''), array_map('strval', (array) $vals), true)) {
                return false;
            }
        }

        return true;
    }

    /** @return list<string> */
    private function stepKeys(array $step): array
    {
        return $step['type'] === 'numbers' ? array_column($step['fields'], 'key') : [$step['key']];
    }

    /** Eksik zorunlu alanların kullanıcıya gösterilecek adları */
    public function missing(array $in): array
    {
        $miss = [];
        $n = 0;
        foreach ($this->steps() as $step) {
            if (! $this->visible($step, $in)) {
                continue;
            }
            $n++;
            if ($step['type'] === 'cards' && ($step['required'] ?? false) && ($in[$step['key']] ?? '') === '') {
                $miss[] = mb_strtolower($step['short'] ?? $step['title']).' ('.$n.'. adım)';
            }
            if ($step['type'] === 'numbers') {
                $any = $step['required_any'] ?? null;
                if ($any) {
                    $ok = collect($step['fields'])->contains(fn ($f) => ! empty($in['_given_'.$f['key']]) && ($in[$f['key']] ?? 0) > 0);
                    if (! $ok) {
                        $miss[] = $any;
                    }
                }
                foreach ($step['fields'] as $f) {
                    if (($f['required'] ?? false) && empty($in['_given_'.$f['key']])) {
                        $miss[] = $f['missing'] ?? mb_strtolower($f['label']);
                    }
                }
            }
        }

        return $miss;
    }

    // ───────────────────────── Hesap ─────────────────────────

    public function estimate(array $in): array
    {
        $missing = $this->missing($in);
        if ($missing) {
            return ['ready' => false, 'missing' => $missing];
        }

        $c = $this->compute($in);
        if (! empty($c['blocked'])) {
            // Hesap yapılamıyor (ör. resmî birim fiyat bulunamadı): fiyat uydurulmaz
            return ['ready' => false, 'missing' => [], 'blocked' => (string) $c['blocked'], 'meta' => $c['meta'] ?? []];
        }
        $band = $this->band();
        $items = [];
        $sub = 0.0;
        $i = 0;
        foreach ($c['items'] as $k => $it) {
            $mid = round(max(0, (float) $it['mid']) / 100) * 100;
            if ($mid <= 0) {
                continue;
            }
            $sub += $mid;
            $items[$k] = ['label' => $it['label'], 'mid' => $mid, 'color' => $it['color'] ?? $this->palette[$i % count($this->palette)]];
            $i++;
        }
        $rate = $c['contingency'] ?? $this->contingencyRate();
        if ($rate > 0 && $sub > 0) {
            $items['beklenmedik'] = ['label' => 'Beklenmedik gider payı (%'.round($rate * 100).')', 'mid' => round($sub * $rate / 100) * 100, 'color' => '#D5D9DF'];
        }
        $mid = array_sum(array_column($items, 'mid'));
        foreach ($items as &$it) {
            $it['low'] = round($it['mid'] * $band['low'] / 100) * 100;
            $it['high'] = round($it['mid'] * $band['high'] / 100) * 100;
        }
        unset($it);

        return [
            'ready' => true,
            'items' => $items,
            'low' => round($mid * $band['low'] / 1000) * 1000,
            'mid' => round($mid / 1000) * 1000,
            'high' => round($mid * $band['high'] / 1000) * 1000,
            'per_unit' => isset($c['per_unit']) && $c['per_unit'] > 0 ? round($mid / $c['per_unit'] / 10) * 10 : null,
            'per_unit_label' => $c['per_unit_label'] ?? $this->perUnitLabel(),
            'meta' => $c['meta'] ?? [],
            'days' => $c['days'] ?? null,
            'notes' => $c['notes'] ?? [],
            'summary' => $this->summary($in),
        ];
    }

    /** Teklif formuna ve rapora yazılan kısa özet */
    public function summary(array $in): string
    {
        $parts = [];
        foreach ($this->steps() as $step) {
            if (! $this->visible($step, $in)) {
                continue;
            }
            if ($step['type'] === 'cards' && ($in[$step['key']] ?? '') !== '') {
                $parts[] = $step['options'][$in[$step['key']]]['label'];
            }
            if ($step['type'] === 'numbers') {
                foreach ($step['fields'] as $f) {
                    if (! empty($in['_given_'.$f['key']])) {
                        $parts[] = $f['label'].' '.rtrim(rtrim(number_format((float) $in[$f['key']], 2, ',', ''), '0'), ',').' '.$f['unit'];
                    }
                }
            }
        }

        return mb_substr(implode(' | ', $parts), 0, 180);
    }

    /** Seçilen girdilerin etiketli listesi (rapor) */
    public function inputRows(array $in): array
    {
        $rows = [];
        foreach ($this->steps() as $step) {
            if (! $this->visible($step, $in)) {
                continue;
            }
            if ($step['type'] === 'cards') {
                $v = $in[$step['key']] ?? '';
                $rows[] = [$step['title'], $v !== '' ? $step['options'][$v]['label'] : 'Seçilmedi'];
            } elseif ($step['type'] === 'numbers') {
                foreach ($step['fields'] as $f) {
                    $given = ! empty($in['_given_'.$f['key']]);
                    $rows[] = [$f['label'], $given ? number_format((float) $in[$f['key']], 1, ',', '.').' '.$f['unit'] : (isset($f['default']) ? 'Varsayılan '.number_format((float) $f['default'], 1, ',', '.').' '.$f['unit'] : '—')];
                }
            } elseif ($step['type'] === 'checks') {
                $labels = array_map(fn ($k) => $step['options'][$k], $in[$step['key']] ?? []);
                $rows[] = [$step['title'], $labels ? implode(', ', $labels) : 'Seçilmedi'];
            }
        }

        return $rows;
    }

    // ───────────────────────── Pro rapor ─────────────────────────

    /** Kalite adımı varsa her seviyede tahmin */
    public function compare(array $in): array
    {
        $step = collect($this->steps())->first(fn ($s) => ($s['key'] ?? null) === 'quality' && $this->visible($s, $in));
        if (! $step) {
            return [];
        }
        $out = [];
        foreach ($step['options'] as $k => $o) {
            $e = $this->estimate(array_merge($in, ['quality' => $k]));
            if ($e['ready']) {
                $out[$k] = ['label' => $o['label'], 'hint' => $o['hint'] ?? '', 'low' => $e['low'], 'mid' => $e['mid'], 'high' => $e['high'], 'selected' => $k === ($in['quality'] ?? '')];
            }
        }

        return $out;
    }

    /** Yaklaşık malzeme listesi: [[ad, miktar, birim, not]] */
    public function materials(array $in): array
    {
        return [];
    }

    /** İş aşamaları: [[ad, pay]] */
    public function phases(array $in): array
    {
        return [['Hazırlık ve söküm', .15], ['Uygulama', .70], ['Temizlik ve teslim', .15]];
    }

    public function checklist(): array
    {
        return [
            'Teklifi yazılı ve kalem kalem alın; malzeme marka ve modeli teklifte yazsın.',
            'İşçilik ile malzemenin ayrı mı, birlikte mi fiyatlandığını netleştirin.',
            'Moloz taşıma, nakliye ve kat çıkışının kime ait olduğunu sözleşmeye yazın.',
            'İş takvimini ve gecikme durumunu sözleşmeye yazın.',
            'İşçilik garantisinin süresini ve kapsamını yazılı alın.',
            'Ödemeleri yapılan işe göre aşamalı yapın; tamamını peşin vermeyin.',
            'Fatura ve ürün garanti belgelerini teslimde isteyin.',
        ];
    }

    public function proReport(array $in): array
    {
        $est = $this->estimate($in);
        $days = $est['days'] ?? null;
        $total = $days ? (int) round(($days[0] + $days[1]) / 2) : 0;
        $program = [];
        if ($total > 0) {
            $day = 1;
            foreach ($this->phases($in) as [$name, $share]) {
                $len = max(1, (int) round($total * $share));
                $program[] = ['name' => $name, 'from' => $day, 'to' => $day + $len - 1];
                $day += $len;
            }
            $total = $day - 1;
        }
        $mid = $est['mid'];
        $payments = $mid >= 30000
            ? [['Sözleşme ve malzeme siparişi', 30, (int) round($mid * .30, -2)], ['İşin yarısı tamamlandığında', 40, (int) round($mid * .40, -2)], ['Teslim ve kontrol sonrası', 30, (int) round($mid * .30, -2)]]
            : [['İş başlangıcı', 50, (int) round($mid * .50, -2)], ['Teslim ve kontrol sonrası', 50, (int) round($mid * .50, -2)]];

        return [
            'est' => $est,
            'compare' => $this->compare($in),
            'materials' => $this->materials($in),
            'program' => $program,
            'totalDays' => $total,
            'payments' => $payments,
            'checklist' => $this->checklist(),
            'inputs' => $this->inputRows($in),
            'expert' => $this->expertNotes($in),
        ];
    }

    /**
     * Pro rapor "Uzman notları": seçimlere göre uygulama ipuçları (başlık, metin). Araç tanımlamazsa bölüm çıkmaz.
     *
     * @return list<array{0:string,1:string}>
     */
    public function expertNotes(array $in): array
    {
        return [];
    }

    // ───────────────────────── Yardımcılar ─────────────────────────

    /** Kalite indeksine göre değer: [ekonomik, orta, premium] */
    protected function q(array $in, array $triple): float
    {
        $i = ['ekonomik' => 0, 'orta' => 1, 'premium' => 2][$in['quality'] ?? 'orta'] ?? 1;

        return (float) $triple[$i];
    }

    protected function has(array $in, string $group, string $k): bool
    {
        return in_array($k, $in[$group] ?? [], true);
    }

    /** SSS / açıklama metinleri için örnek hesap aralığı: "₺70 – ₺100 bin" */
    protected function exampleRange(array $raw, bool $perUnit = false): string
    {
        try {
            $e = $this->estimate($this->normalize($raw));
        } catch (\Throwable) {
            return '';
        }
        if (! ($e['ready'] ?? false)) {
            return '';
        }
        if ($perUnit && $e['per_unit']) {
            $b = $this->band();
            $fmt = fn ($v) => number_format(round($v / 50) * 50, 0, ',', '.');

            return $fmt($e['per_unit'] * $b['low']).'–'.$fmt($e['per_unit'] * $b['high']).' TL';
        }
        $k = fn ($v) => number_format($v / 1000, 0, ',', '.');

        return $k($e['low']).'–'.$k($e['high']).' bin TL';
    }

    protected static function n1(float $v): string
    {
        return number_format($v, 1, ',', '.');
    }
}

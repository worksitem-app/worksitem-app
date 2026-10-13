<?php

namespace App\Services;

/**
 * Banyo tadilat maliyeti v2 (yaklaşık aralık).
 *
 * Sayfadaki JavaScript (resources/views/tools/banyo-calculator.blade.php
 * içindeki estimate()) bu sınıfla AYNI formülü uygular. Birini
 * değiştirirseniz diğerini de değiştirin; BathroomEstimatorTest korur.
 */
class BathroomEstimator
{
    public function __construct(private array $cfg = [])
    {
        $this->cfg = $cfg ?: app(BathroomPriceBook::class)->apply(config('worksitem_bathroom'));
    }

    public function config(): array
    {
        return $this->cfg;
    }

    /** "Ekim 2026" — admin panelindeki en yeni fiyat tarihi, yoksa config */
    public function pricesLabel(): string
    {
        if (! empty($this->cfg['prices_date'])) {
            return \Illuminate\Support\Carbon::parse($this->cfg['prices_date'])->locale('tr')->translatedFormat('j F Y');
        }

        return $this->cfg['prices_reviewed'];
    }

    /**
     * @param  array{type?:string,w?:float,l?:float,h?:float,scope?:string,quality?:string,shower?:string,extras?:array<string>}  $in
     */
    public function estimate(array $in): array
    {
        $c = $this->cfg;
        $type = $c['types'][$in['type'] ?? 'standart'] ?? $c['types']['standart'];
        $scopeKey = isset($c['scopes'][$in['scope'] ?? '']) ? $in['scope'] : 'komple';
        $qKey = isset($c['qualities'][$in['quality'] ?? '']) ? $in['quality'] : 'orta';
        $q = $c['qualities'][$qKey]['i'];
        $u = $c['unit'];

        $w = max(0.5, min(10, (float) ($in['w'] ?? $type['w'])));
        $l = max(0.5, min(10, (float) ($in['l'] ?? $type['l'])));
        $h = max(2.0, min(4, (float) ($in['h'] ?? $type['h'])));

        $floor = $w * $l;
        $wall = max(0, 2 * ($w + $l) * $h - $c['door_area']);
        $tileArea = $floor + $wall;
        $full = $scopeKey !== 'yuzey';

        $items = [];
        $items['sokum'] = $full ? $tileArea * $u['demolition_m2'] : $u['surface_removal'];

        $plumb = 0;
        if ($full) {
            $plumb = $u['plumbing_base'] + $floor * $u['plumbing_m2'];
            if ($scopeKey === 'tasima') {
                $plumb = $plumb * $u['relocation_factor'] + $u['relocation_fixed'];
            }
        }
        $items['tesisat'] = $plumb;
        $items['yalitim'] = $full ? $floor * $u['waterproof_m2'] + ($type['shower'] ? $u['waterproof_shower_zone'] : 0) : 0;
        $items['seramik'] = $tileArea * $u['tile_m2'][$q];
        $items['vitrifiye'] = $u['fixtures'][$q] * $type['fixture_factor'];

        // Duş isteğe bağlı: seçilmediyse (veya tanımsızsa) hesaba katılmaz
        $showerKey = (string) ($in['shower'] ?? '');
        $items['dus'] = $type['shower'] && isset($c['showers'][$showerKey]) ? $c['showers'][$showerKey]['price'][$q] : 0;

        $extras = 0;
        foreach ($in['extras'] ?? [] as $key) {
            $e = $c['extras'][$key] ?? null;
            if (! $e) {
                continue;
            }
            if (isset($e['price'])) {
                $extras += $e['price'][$q];
            } else {
                $extras += ($e['per_m2'] ?? 0) * $floor + ($e['fixed'] ?? 0);
            }
        }
        $items['ekstra'] = $extras;

        $items = array_map(fn ($v) => $this->round($v, 100), $items);
        $subtotal = array_sum($items);
        $items['beklenmedik'] = $this->round($subtotal * $c['contingency_rate'], 100);
        $mid = $subtotal + $items['beklenmedik'];

        $band = $c['band'];
        $lines = [];
        foreach ($items as $k => $v) {
            $lines[$k] = [
                'low' => $this->round($v * $band['low'], 100),
                'mid' => $v,
                'high' => $this->round($v * $band['high'], 100),
            ];
        }

        return [
            'floor' => round($floor, 2),
            'tile_area' => round($tileArea, 2),
            'items' => $lines,
            'low' => $this->round($mid * $band['low'], 1000),
            'mid' => $this->round($mid, 1000),
            'high' => $this->round($mid * $band['high'], 1000),
            'per_m2' => $floor > 0 ? $this->round($mid / $floor, 100) : 0,
            'days' => $c['scopes'][$scopeKey]['days'],
        ];
    }

    /** SEO tablosu: zemin alanına göre standart banyo, komple yenileme */
    public function sizeTable(): array
    {
        $rows = [];
        foreach ($this->cfg['seo_sizes'] as $m2) {
            $side = sqrt($m2);
            $row = ['m2' => $m2];
            foreach (array_keys($this->cfg['qualities']) as $qk) {
                $r = $this->estimate([
                    'type' => 'standart', 'w' => $side, 'l' => $side, 'h' => 2.6,
                    'scope' => 'komple', 'quality' => $qk, 'shower' => 'kabin', 'extras' => ['ex_dolap'],
                ]);
                $row[$qk] = ['low' => $r['low'], 'high' => $r['high']];
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private function round(float $v, int $step): int
    {
        return (int) (round($v / $step) * $step);
    }
}

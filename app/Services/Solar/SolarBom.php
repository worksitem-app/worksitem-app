<?php

namespace App\Services\Solar;

use App\Estimators\Concerns\DbPrices;

/**
 * v90 GES malzeme listesi (keşif öncesi tahmini).
 *
 * Girdi: yüzey grupları (çatı yüzeyleri ve cepheler; her birinde panel sayısı, yön, eğim), panel tipi,
 * kullanım (konut/fabrika/arazi), çatı tipi, kat sayısı, faz tercihi, batarya, arazi çevresi.
 * Çıktı: inverter seçimi (güç, faz, MPPT), yüzey başına dizi (string) düzeni, kalem kalem malzeme ve işçilik,
 * uyarılar ve toplam. Fiyatlar admin › Birim Fiyatlar (kategori "ges", onaylı) → yoksa config('worksitem_solar.bom').
 */
class SolarBom
{
    use DbPrices;

    private array $cfg;

    public function __construct()
    {
        $this->cfg = (array) config('worksitem_solar.bom');
    }

    public function price(string $key): float
    {
        return $this->unitPrice($key, (float) ($this->cfg['prices'][$key] ?? 0));
    }

    /**
     * @param  list<array{panels:int, angle:float, azimuth:float, kind?:string, label?:string, area?:float}>  $groups
     * @param  array{panel:string, use:string, roof:string, floors:int, phase:string, battery:bool, battery_kwh?:float, site?:array}  $o
     */
    public function build(array $groups, array $o): array
    {
        $panelKey = (string) ($o['panel'] ?? '550');
        $panel = (array) (config('worksitem_solar.panels.'.$panelKey) ?: config('worksitem_solar.panels.550'));
        $elec = (array) ($this->cfg['panel_elec'][$panelKey] ?? $this->cfg['panel_elec']['550']);
        $w = (int) $panel['w'];
        $use = (string) ($o['use'] ?? 'konut');
        $land = $use === 'arazi';
        $floors = max(1, (int) ($o['floors'] ?? 1));

        $groups = array_values(array_filter(array_map(fn ($g) => [
            'panels' => max(0, (int) ($g['panels'] ?? 0)),
            'angle' => (float) ($g['angle'] ?? 0),
            'azimuth' => (float) ($g['azimuth'] ?? 180),
            'kind' => ($g['kind'] ?? 'roof') === 'facade' ? 'facade' : 'roof',
            'label' => (string) ($g['label'] ?? ''),
            'area' => (float) ($g['area'] ?? 0),
        ], $groups), fn ($g) => $g['panels'] > 0));

        $nRoof = array_sum(array_map(fn ($g) => $g['kind'] === 'roof' ? $g['panels'] : 0, $groups));
        $nFacade = array_sum(array_map(fn ($g) => $g['kind'] === 'facade' ? $g['panels'] : 0, $groups));
        $n = $nRoof + $nFacade;
        $dcKw = $n * $w / 1000;
        if ($n === 0) {
            return ['ok' => false, 'message' => 'Panel yok.'];
        }

        // ── İnverter ──
        $inv = $this->chooseInverter($dcKw, (string) ($o['phase'] ?? 'auto'), $use);
        $fam = (array) $this->cfg['inverters'][$inv['phase']];
        $notes = $inv['notes'];

        // ── Diziler (string): her yüzey kendi MPPT girişinde ──
        $vocCold = $elec['voc'] * (float) $this->cfg['cold_voc_factor'];
        $vmpHot = $elec['vmp'] * (float) $this->cfg['hot_vmp_factor'];
        $maxLen = (int) floor($fam['vmax'] / $vocCold);
        $minLen = (int) ceil($fam['mppt_min'] / $vmpHot);
        $strings = [];
        $mpptNeed = 0;
        foreach ($groups as $i => $g) {
            $k = (int) ceil($g['panels'] / $maxLen);
            $len = $g['panels'] / $k;
            $lens = [];
            for ($j = 0; $j < $k; $j++) {
                $lens[] = (int) floor($g['panels'] / $k) + ($j < $g['panels'] % $k ? 1 : 0);
            }
            $short = min($lens) < $minLen;
            $mpptNeed += (int) ceil($k / max(1, (int) $fam['strings_per_mppt']));
            $strings[] = ['group' => $i, 'label' => $g['label'], 'kind' => $g['kind'], 'count' => $k, 'lengths' => $lens, 'short' => $short,
                'vmp' => round(min($lens) * $elec['vmp']), 'voc_cold' => round(max($lens) * $vocCold)];
            if ($short) {
                $notes[] = ($g['label'] ?: 'Bir yüzey').': '.$g['panels'].' panel tek dizi için az (en az '.$minLen.'). Keşifte komşu yüzeyle birleştirme ya da panel optimizer/mikro inverter değerlendirilir.';
            }
        }
        $mpptHave = $inv['count'] * $inv['mppt'];
        if ($mpptNeed > $mpptHave) {
            $notes[] = 'Farklı yönlerdeki yüzeyler için '.$mpptNeed.' MPPT girişi gerekiyor, seçilen inverterde '.$mpptHave.' var; bazı yüzeyler aynı girişe bağlanır ya da ikinci inverter eklenir (keşifte netleşir).';
        }
        $nStrings = array_sum(array_column($strings, 'count'));

        // ── Kablo uzunlukları ──
        $homerun = $land ? 30 + sqrt(max(0, (float) ($o['site']['area'] ?? 0))) / 2 : $floors * 3 + 15;
        $dcM = (int) ceil($nStrings * 2 * $homerun + $n * 1.0);
        $acM = $land ? 50 : ($use === 'fabrika' ? 30 : 15);
        $acA = $inv['phase'] === 'mono' ? $inv['kw'] * 1000 / 230 : $inv['kw'] * 1000 / (sqrt(3) * 400);
        [$acSec, $acKey] = $acA <= 25 ? ['6', 'ges_ac_cable_6'] : ($acA <= 40 ? ['10', 'ges_ac_cable_10'] : ($acA <= 63 ? ['16', 'ges_ac_cable_16'] : ($acA <= 100 ? ['35', 'ges_ac_cable_35'] : ['70', 'ges_ac_cable_70'])));
        $acCores = $inv['phase'] === 'mono' ? '3' : '5';

        // ── Kalemler ──
        $L = [];
        $add = function (string $group, string $name, string $spec, float $qty, string $unit, float $unitPrice) use (&$L) {
            if ($qty <= 0 || $unitPrice <= 0) {
                return;
            }
            $L[] = ['group' => $group, 'name' => $name, 'spec' => $spec, 'qty' => round($qty, 2), 'unit' => $unit, 'unit_price' => round($unitPrice, 2), 'total' => round($qty * $unitPrice, -1)];
        };

        $add('Panel', 'Güneş paneli', $w.' W monokristal ('.str_replace('.', ',', (string) $panel['l']).' × '.str_replace('.', ',', (string) $panel['s']).' m)', $n, 'adet', $w * $this->price('ges_panel_w'));

        $hybrid = ! empty($o['battery']);
        $invPrice = $this->price($inv['key']) * ($hybrid ? (float) $this->cfg['hybrid_factor'] : 1);
        $add('İnverter', $hybrid ? 'Hibrit inverter' : 'Şebeke bağlantılı inverter', $inv['kw'].' kW '.($inv['phase'] === 'mono' ? 'monofaze' : 'trifaze').', '.$inv['mppt'].' MPPT', $inv['count'], 'adet', $invPrice);

        $roof = (string) ($o['roof'] ?? 'besik');
        $mountKey = $land ? 'ges_mount_arazi' : ($roof === 'trapez' ? 'ges_mount_trapez' : ($roof === 'duz' ? 'ges_mount_beton' : 'ges_mount_kiremit'));
        $mountName = ['ges_mount_arazi' => 'arazi, çakma kazık', 'ges_mount_trapez' => 'trapez / sandviç panel, mini ray', 'ges_mount_beton' => 'düz çatı, eğimli sehpa', 'ges_mount_kiremit' => 'kiremit çatı, kanca + alüminyum ray'][$mountKey];
        $add('Konstrüksiyon', 'Taşıyıcı sistem', $mountName, $nRoof, 'panel', $this->price($mountKey));
        $add('Konstrüksiyon', 'Cephe taşıyıcı sistemi', 'dikey duvar ankrajlı alüminyum ray', $nFacade, 'panel', $this->price('ges_mount_cephe'));

        $add('Kablo ve koruma', 'Solar DC kablo', '6 mm², çift yalıtımlı ('.$nStrings.' dizi)', $dcM, 'm', $this->price('ges_dc_cable_m'));
        $add('Kablo ve koruma', 'MC4 konnektör', 'dizi uçları + yedek', $nStrings * 2 + 2, 'çift', $this->price('ges_mc4_pair'));
        $add('Kablo ve koruma', 'DC koruma', 'sigorta, DC parafudr, DC ayırıcı', max($mpptNeed, 1), 'MPPT', $this->price('ges_dc_box_mppt'));
        $acBox = $inv['phase'] === 'mono' ? 'ges_ac_box_mono' : ($inv['kw'] > 30 ? 'ges_ac_box_tri_large' : 'ges_ac_box_tri');
        $add('Kablo ve koruma', 'AC koruma panosu', 'şalter, kaçak akım, AC parafudr', $inv['count'], 'adet', $this->price($acBox));
        $add('Kablo ve koruma', 'AC kablo', 'NYY '.$acCores.'×'.$acSec.' mm² (inverter → sayaç)', $acM * $inv['count'], 'm', $this->price($acKey));
        $add('Kablo ve koruma', 'Topraklama ve eş potansiyel', 'paratoner bağlantısı hariç', 1, 'takım', $this->price('ges_grounding_base') + $dcKw * $this->price('ges_grounding_kwp'));

        if ($hybrid) {
            $kwh = $this->batteryKwh($dcKw, (float) ($o['monthly_kwh'] ?? 0), $use);
            $add('Batarya', 'Lityum batarya', 'LiFePO4, '.str_replace('.', ',', (string) $kwh).' kWh', $kwh, 'kWh', $this->price('ges_battery_kwh'));
            $add('Batarya', 'Otomatik güç kontrolü ve enerji kesme sistemi', 'depolamalı sistem mevzuat gereği', 1, 'takım', (float) (config('worksitem_solar.storage_control')[$use === 'arazi' ? 'fabrika' : $use] ?? 35000));
        }

        $labor = $this->price('ges_labor_panel');
        $add('İşçilik', 'Montaj işçiliği', $land ? 'arazi' : ($mountKey === 'ges_mount_kiremit' ? 'kiremit çatı' : 'çatı'), $nRoof, 'panel', $labor * ($mountKey === 'ges_mount_kiremit' ? (float) $this->cfg['kiremit_labor_factor'] : 1));
        $add('İşçilik', 'Cephe montaj işçiliği', 'yüksekte çalışma', $nFacade, 'panel', $labor * (float) $this->cfg['facade_labor_factor']);
        if (! $land && $floors >= 3 && $nRoof > 0) {
            $add('İşçilik', 'Çatıya erişim', 'iskele ya da vinç ('.$floors.' kat)', 1, 'iş', $this->price('ges_access_fixed'));
        }
        $facadeArea = array_sum(array_map(fn ($g) => $g['kind'] === 'facade' ? $g['area'] : 0, $groups));
        if ($facadeArea > 0) {
            $add('İşçilik', 'Cephe iskelesi', 'kurulum, söküm, kira', $facadeArea, 'm²', $this->price('ges_facade_scaffold_m2'));
        }

        $add('Proje ve bağlantı', 'Uzaktan izleme', 'Wi-Fi veri kaydedici', $inv['count'], 'adet', $this->price('ges_monitor'));
        if (! $land) {
            $add('Proje ve bağlantı', 'Çift yönlü sayaç ve bağlantı', 'dağıtım şirketi', 1, 'adet', $this->price('ges_meter'));
        }
        $add('Proje ve bağlantı', 'Proje, başvuru ve kabul', $land ? 'lisanssız üretim, arazi' : ($use === 'fabrika' ? 'işyeri' : 'mesken'), 1, 'iş',
            $land ? (float) config('worksitem_solar.land.project', 250000) : $this->price($use === 'fabrika' ? 'ges_project_fabrika' : 'ges_project_konut'));

        if ($land) {
            $site = (array) ($o['site'] ?? []);
            $LC = (array) config('worksitem_solar.land');
            $add('Saha', 'Saha hazırlığı ve tesviye', '', (float) ($site['area'] ?? 0), 'm²', (float) ($LC['prep_m2'] ?? 40));
            $add('Saha', 'Çevre çiti ve giriş kapısı', '', (float) ($site['perimeter'] ?? 0), 'm', (float) ($LC['fence_m'] ?? 1500));
            $add('Saha', 'Kamera, aydınlatma ve saha topraklaması', '', 1, 'takım', max(60000, $dcKw * (float) ($LC['site_kwp'] ?? 400)));
        }

        $sub = array_sum(array_column($L, 'total'));
        $pct = (float) $this->setting('ges_contingency_pct', (float) $this->cfg['contingency_pct']);
        $cont = round($sub * $pct / 100, -2);
        $total = round($sub + $cont, -3);

        $byGroup = [];
        foreach ($L as $l) {
            $byGroup[$l['group']] = ($byGroup[$l['group']] ?? 0) + $l['total'];
        }
        $byGroup['Beklenmeyen giderler (%'.rtrim(rtrim(number_format($pct, 1, ',', ''), '0'), ',').')'] = $cont;

        return [
            'ok' => true,
            'system' => [
                'panels' => $n, 'panels_roof' => $nRoof, 'panels_facade' => $nFacade, 'panel_w' => $w,
                'dc_kwp' => round($dcKw, 2), 'inverter' => $inv, 'dc_ac' => round($dcKw / ($inv['kw'] * $inv['count']), 2),
                'strings' => $strings, 'string_limits' => ['min' => $minLen, 'max' => $maxLen], 'mppt_need' => $mpptNeed, 'mppt_have' => $mpptHave,
                'cable' => ['dc_m' => $dcM, 'ac_m' => $acM * $inv['count'], 'ac' => $acCores.'×'.$acSec.' mm²'],
            ],
            'lines' => $L,
            'groups' => array_map(fn ($v) => round($v, -2), $byGroup),
            'subtotal' => round($sub, -2), 'contingency' => $cont, 'total' => $total,
            'low' => round($total * 0.9, -3), 'high' => round($total * 1.15, -3),
            'notes' => $notes,
            'vat' => 'KDV dahil',
        ];
    }

    /**
     * Batarya: günlük tüketimin yarısı (akşam/gece payı) ya da tüketim bilinmiyorsa kWp × oran;
     * en çok kWp × oran, konutta en az 1 modül; 5,12 kWh'lik modüllere yukarı yuvarlanır.
     */
    public function batteryKwh(float $dcKw, float $monthlyKwh, string $use): float
    {
        $module = 5.12;
        $max = $dcKw * (float) config('worksitem_solar.battery_ratio', 2);
        $want = $monthlyKwh > 0 ? $monthlyKwh / 30 * 0.5 : ($use === 'konut' ? min($max, 10.24) : $max);
        $want = max($module, min($max, $want));

        return round(ceil($want / $module - 1e-9) * $module, 2);
    }

    /** En küçük standart güç ≥ DC ÷ oran; 100 kW üstünde eşit güçte birden fazla inverter */
    public function chooseInverter(float $dcKw, string $phase, string $use): array
    {
        $ratio = (float) $this->cfg['dc_ac_ratio'];
        $need = $dcKw / $ratio;
        $notes = [];
        $p = $phase === 'mono' ? 'mono' : ($phase === 'tri' ? 'tri' : ($use === 'konut' && $need <= (float) $this->cfg['mono_max_kw'] ? 'mono' : 'tri'));
        if ($p === 'mono' && $need > (float) $this->cfg['mono_max_kw']) {
            $p = 'tri';
            $notes[] = 'Sistem '.str_replace('.', ',', (string) round($need, 1)).' kW inverter gerektiriyor; '.(int) $this->cfg['mono_max_kw'].' kW üstü için trifaze bağlantı gerekir (aboneliğiniz monofazeyse dağıtım şirketinden faz değişikliği istenir).';
        }
        $fam = (array) $this->cfg['inverters'][$p];
        $sizes = $fam['sizes'];
        $count = 1;
        if ($need > max($sizes)) {
            $count = (int) ceil($need / max($sizes));
            $need /= $count;
        }
        $kw = max($sizes);
        foreach ($sizes as $s) {
            if ($s >= $need - 1e-9) {
                $kw = $s;
                break;
            }
        }

        return ['kw' => $kw, 'count' => $count, 'phase' => $p, 'mppt' => (int) ($fam['mppt'][(string) $kw] ?? 2),
            'key' => 'ges_inv_'.$p.'_'.$kw, 'notes' => $notes];
    }
}

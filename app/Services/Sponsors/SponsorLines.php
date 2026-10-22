<?php

namespace App\Services\Sponsors;

use App\Models\Sponsor;
use Illuminate\Support\Facades\Schema;

/**
 * Kalem sponsorluğu: hesap sonucundaki kalemlere, o araç ve kategorideki yayındaki sponsoru eşler.
 * Sonuç fiyatlarını değiştirmez; yalnız "Sponsorlu · Marka" etiketi için ad ve yönlendirme adresi döner.
 */
class SponsorLines
{
    /** @return array<string, string> kalem → kategori */
    public static function categories(string $tool, array $itemKeys, array $in): array
    {
        $map = (array) config("worksitem_sponsors.item_map.$tool", []);
        $out = [];
        foreach ($itemKeys as $k) {
            $rule = $map[$k] ?? null;
            if (is_string($rule) && $rule !== '') {
                $out[$k] = $rule;
            } elseif (is_array($rule)) {
                $val = (string) ($in[$rule['field'] ?? ''] ?? '');
                $cat = (string) ($rule['default'] ?? '');
                foreach ((array) ($rule['map'] ?? []) as $prefix => $c) {
                    if ($val !== '' && str_starts_with($val, (string) $prefix)) {
                        $cat = (string) $c;
                        break;
                    }
                }
                if ($cat !== '') {
                    $out[$k] = $cat;
                }
            }
        }

        return $out;
    }

    /** @return array<string, array{brand:string, url:string, id:int}> */
    public static function for(string $tool, array $itemKeys, array $in): array
    {
        $cats = self::categories($tool, $itemKeys, $in);
        if (! $cats) {
            return [];
        }
        try {
            if (! Schema::hasTable('sponsors')) {
                return [];
            }
            $rows = Sponsor::applyExclusive(Sponsor::query()->live()->where('tool_slug', $tool)->whereIn('category', array_values(array_unique($cats)))
                ->orderBy('priority')->orderBy('id')->get())->groupBy('category');
        } catch (\Throwable) {
            return [];
        }
        $out = [];
        foreach ($cats as $item => $cat) {
            $s = $rows->get($cat)?->first();
            if ($s) {
                $out[$item] = ['brand' => (string) $s->brand, 'url' => route('sponsor.go', $s), 'id' => (int) $s->id];
            }
        }

        return $out;
    }

    /**
     * Pro rapor malzeme listesi: satır sırası → o kategorideki yayındaki sponsor (her kategori bir kez).
     * Miktar ve tutar değişmez; rapor yalnız "Sponsorlu eşdeğer" notu ekler.
     *
     * @param  list<array>  $materials  [ad, miktar, birim, not]
     * @return array<int, Sponsor>
     */
    public static function forMaterials(string $tool, array $materials): array
    {
        $toolCats = array_keys((array) config("worksitem_sponsors.tools.$tool.categories", []));
        $map = (array) config('worksitem_sponsors.material_map', []);
        $want = [];
        foreach ($materials as $i => $row) {
            $name = mb_strtolower((string) ($row[0] ?? ''), 'UTF-8');
            foreach ($map as $needle => $cat) {
                if (in_array($cat, $toolCats, true) && str_contains($name, mb_strtolower((string) $needle, 'UTF-8'))) {
                    if (! in_array($cat, $want, true)) {
                        $want[$i] = $cat;
                    }
                    break;
                }
            }
        }
        if (! $want) {
            return [];
        }
        try {
            if (! Schema::hasTable('sponsors')) {
                return [];
            }
            $rows = Sponsor::applyExclusive(Sponsor::query()->live()->where('tool_slug', $tool)->whereIn('category', array_values($want))
                ->orderBy('priority')->orderBy('id')->get())->groupBy('category');
        } catch (\Throwable) {
            return [];
        }
        $out = [];
        foreach ($want as $i => $cat) {
            if ($s = $rows->get($cat)?->first()) {
                $out[$i] = $s;
            }
        }

        return $out;
    }
}

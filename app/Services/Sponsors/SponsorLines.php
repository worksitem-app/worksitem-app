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
            $rows = Sponsor::query()->live()->where('tool_slug', $tool)->whereIn('category', array_values(array_unique($cats)))
                ->orderBy('priority')->orderBy('id')->get()->groupBy('category');
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
}

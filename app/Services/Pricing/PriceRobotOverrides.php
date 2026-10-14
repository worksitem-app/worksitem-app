<?php

namespace App\Services\Pricing;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fiyat robotunun yayındaki değerlerini (value) config'in üzerine yazar. Onay bekleyen öneri
 * (proposed_value) yayına girmez; o sırada önceki yayındaki değer kullanılmaya devam eder.
 * Hesap motorları fiyatı config('worksitem_estimators...') üzerinden okuduğu için
 * araçlarda kod değişikliği gerekmez.
 */
class PriceRobotOverrides
{
    public const CACHE_KEY = 'price-robot:overrides';

    /** @return array<string, mixed> path => value */
    public static function map(): array
    {
        try {
            $cached = Cache::get(self::CACHE_KEY);
            if (is_array($cached)) {
                return $cached;
            }
            if (! Schema::hasTable('price_robot_items')) {
                return [];
            }
            $map = [];
            foreach (DB::table('price_robot_items')->whereIn('status', ['live', 'needs_review'])->whereNotNull('value')->get(['path', 'value']) as $r) {
                $v = json_decode((string) $r->value, true);
                if (self::valid($v)) {
                    $map[$r->path] = $v;
                }
            }
            Cache::put(self::CACHE_KEY, $map, 600);

            return $map;
        } catch (\Throwable) {
            return [];
        }
    }

    public static function apply(): void
    {
        foreach (self::map() as $path => $value) {
            if (str_starts_with($path, 'worksitem_estimators.') || str_starts_with($path, 'worksitem_bathroom.')) {
                config([$path => $value]);
            }
        }
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function valid(mixed $v): bool
    {
        if (is_array($v)) {
            return $v !== [] && collect($v)->every(fn ($x) => is_numeric($x) && (float) $x > 0);
        }

        return is_numeric($v) && (float) $v > 0;
    }
}

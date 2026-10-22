<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Araç fiyatlarının son güncellenme tarihi (ekran, PDF ve Pro rapor için tek kaynak) */
class PriceDate
{
    public static function for(?string $tool = null): \Illuminate\Support\Carbon
    {
        $dates = [(string) config('worksitem_estimators.prices_reviewed', now()->toDateString())];
        try {
            if ($tool && Schema::hasTable('price_robot_items')) {
                $dates[] = (string) DB::table('price_robot_items')->where('tool_slug', $tool)->whereIn('status', ['live', 'needs_review'])->max('researched_at');
            }
            // Bu araca bağlı merkezi fiyat kayıtları (admin › Fiyat Merkezi)
            if ($tool && Schema::hasTable('tool_price_bindings') && Schema::hasTable('unit_prices')) {
                $dates[] = (string) DB::table('tool_price_bindings as b')->join('unit_prices as u', 'u.key', '=', 'b.price_key')
                    ->where('b.tool_slug', $tool)->max('u.updated_at');
            }
        } catch (\Throwable) {
        }
        $best = collect($dates)->filter()->map(fn ($d) => \Illuminate\Support\Carbon::parse($d))->filter(fn ($d) => $d->lte(now()->addDay()))->max();

        return $best ?? now();
    }

    /** Rapor notu: bu araçta fiyat robotunun izlediği kalem sayısı ve yenileme sıklığı (yalnız doğru olanı söyler) */
    public static function note(?string $tool = null): string
    {
        $n = $tool ? count(array_filter((array) config('worksitem_price_robot.items', []), fn ($i) => ($i['tool'] ?? '') === $tool)) : 0;
        $d = (int) config('worksitem_price_robot.refresh_days', 7);

        return $n > 0 && config('worksitem_price_robot.enabled', true)
            ? "Bu araçtaki $n kalem fiyat robotuyla en geç $d günde bir yeniden araştırılır; diğer fiyatlar uzman kontrolündedir."
            : 'Fiyatlar uzman kontrolünde güncellenir.';
    }
}

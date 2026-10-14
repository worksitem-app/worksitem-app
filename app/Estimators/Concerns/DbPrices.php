<?php

namespace App\Estimators\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Admin'de tutulan fiyat kayıtlarını (varsa) okur; yoksa config varsayılanı döner */
trait DbPrices
{
    private static array $settingsCache = [];

    protected function setting(string $key, float $fallback): float
    {
        try {
            if (! array_key_exists($key, self::$settingsCache)) {
                self::$settingsCache[$key] = Schema::hasTable('pricing_settings')
                    ? DB::table('pricing_settings')->where('key', $key)->value('value')
                    : null;
            }
            $v = self::$settingsCache[$key];

            return is_numeric($v) && (float) $v > 0 ? (float) $v : $fallback;
        } catch (\Throwable) {
            return $fallback;
        }
    }

    /** unit_prices (onaylı, aktif) — KDV hariç ise %20 eklenir */
    protected function unitPrice(string $key, float $fallback): float
    {
        try {
            if (! Schema::hasTable('unit_prices')) {
                return $fallback;
            }
            $row = DB::table('unit_prices')->where('key', $key)->where('is_active', 1)->where('review_status', 'approved')->first();
            if (! $row || ! is_numeric($row->price ?? null) || (float) $row->price <= 0) {
                return $fallback;
            }
            $vatIncluded = (bool) ($row->vat_included ?? false);

            return (float) $row->price * ($vatIncluded ? 1 : 1.2);
        } catch (\Throwable) {
            return $fallback;
        }
    }

    /** tool_price_bindings rolü (ToolPriceService) — KDV hariç ise %20 eklenir */
    protected function toolPrice(string $tool, string $role, float $fallback): float
    {
        try {
            $r = app(\App\Services\ToolPriceService::class)->forTool($tool)->get($role);
            $r = $r ? (array) $r : null;
            if (! $r || ! is_numeric($r['price'] ?? null) || (float) $r['price'] <= 0) {
                return $fallback;
            }

            return (float) $r['price'] * (! empty($r['vat_included']) ? 1 : 1.2);
        } catch (\Throwable) {
            return $fallback;
        }
    }

    /**
     * Fiyatların son güncellenme tarihi: kod tarihi, fiyat robotunun bu araç için son araştırması
     * ve admin fiyat kayıtlarından (unit_prices / pricing_settings) en yenisi.
     */
    protected function reviewedLabel(): string
    {
        return \App\Support\PriceDate::for(method_exists($this, 'slug') ? $this->slug() : null)->locale('tr')->translatedFormat('j F Y');
    }

}

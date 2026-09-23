<?php

namespace App\Services\Leads;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PricingEngine
{
    /**
     * Fiyat snapshot'ı — 1 saat cache
     */
    public function getPricingSnapshot(): array
    {
        return Cache::remember('pricing_engine_snapshot', 3600, function () {
            // 3B ve 4A sınıf fiyatları
            $classPrices = DB::table('building_classes as c')
                ->join('building_class_costs as p', 'p.building_class_id', '=', 'c.id')
                ->whereIn('c.code', ['3B', '4A'])
                ->where('c.is_active', true)
                ->where('p.is_active', true)
                ->orderByDesc('p.period')
                ->select('c.code', 'p.unit_price', 'p.period', 'p.updated_at')
                ->get()
                ->unique('code')
                ->keyBy('code');

            $class3b = $classPrices->get('3B');
            $class4a = $classPrices->get('4A');

            // Tadilat fiyatları
            $renovation = DB::table('unit_prices')
                ->where('key', 'tadilat_standart_m2')
                ->where('price_pool', 'market')
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->value('price');

            $renovationPremium = DB::table('unit_prices')
                ->where('key', 'tadilat_komple_m2')
                ->where('price_pool', 'market')
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->value('price');

            // Fallback'ler
            $class3bPrice = $class3b->unit_price ?? 21050;
            $class4aPrice = $class4a->unit_price ?? 26450;
            $renovationPrice = $renovation ?: 7500;
            $renovationPremiumPrice = $renovationPremium ?: 12000;

            // Version — en güncel tarihten
            $latestDate = collect([
                $class3b->updated_at ?? null,
                $class4a->updated_at ?? null,
            ])->filter()->max() ?? now();

            $version = 'worksitem-' . date('Y-m', strtotime($latestDate));

            return [
                'bina_sinif_3b_m2' => (float) $class3bPrice,
                'bina_sinif_4a_m2' => (float) $class4aPrice,
                'tadilat_standart_m2' => (float) $renovationPrice,
                'tadilat_premium_m2' => (float) $renovationPremiumPrice,
                'version' => $version,
                'cached_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Fiyat versiyonu
     */
    public function getCurrentVersion(): string
    {
        return $this->getPricingSnapshot()['version'];
    }

    /**
     * Manuel m² hesabı — Zero-Trust (frontend'in değeri geçersiz)
     */
    public function calculateManualM2(
        float $area,
        string $intent,      // insaat | tadilat | kentsel_donusum
        string $quality,     // ekonomik | standart | premium | luks
        string $city = 'other'
    ): array {
        $pricing = $this->getPricingSnapshot();

        // Base fiyat
        if ($intent === 'tadilat') {
            $base = $pricing['tadilat_standart_m2'];
        } elseif ($intent === 'kentsel_donusum') {
            $base = $pricing['bina_sinif_4a_m2'];
        } else {
            $base = $pricing['bina_sinif_3b_m2'];
        }

        // Kalite çarpanı
        $qualityM = [
            'ekonomik' => 0.85,
            'standart' => 1.00,
            'premium' => 1.28,
            'luks' => 1.60,
        ][$quality] ?? 1.0;

        // Şehir çarpanı
        $cityM = [
            'istanbul' => 1.15,
            'i̇stanbul' => 1.15,
            'ankara' => 1.00,
            'izmir' => 1.08,
            'antalya' => 1.08,
            'other' => 0.95,
        ][strtolower($city)] ?? 0.95;

        // Toplam
        $total = $area * $base * $qualityM * $cityM;

        return [
            'area' => $area,
            'intent' => $intent,
            'quality' => $quality,
            'city' => $city,
            'base_price' => round($base, 2),
            'quality_multiplier' => $qualityM,
            'city_multiplier' => $cityM,
            'total' => round($total, 2),
            'price_version' => $pricing['version'],
            'calculated_at' => now()->toIso8601String(),
        ];
    }
}

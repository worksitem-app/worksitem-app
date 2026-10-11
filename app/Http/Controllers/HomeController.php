<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Public homepage allowlist:
     * Yalnızca homepage legacy tadilat hesabının gerçekten kullandığı piyasa fiyatları
     * tarayıcıya gönderilir. Resmî yapı sınıfı fiyatları burada ASLA yayınlanmaz.
     * Sıfırdan inşaat hesabı BuildingEstimateService üzerinden yapılır.
     */
    private const PUBLIC_PRICE_KEYS = ['tadilat_standart_m2'];

    public function __invoke(): View
    {
        try {
            $homePrices = DB::table('unit_prices')
                ->where('price_pool', 'market')
                ->where('is_active', true)
                ->where('review_status', 'approved')
                ->whereIn('key', self::PUBLIC_PRICE_KEYS)
                ->pluck('price', 'key')
                ->map(fn ($v) => (float) $v)
                ->toArray();
        } catch (\Throwable) {
            $homePrices = [];
        }

        // Fiyat sürümü: fiyatlardan biri değiştiğinde otomatik değişir.
        ksort($homePrices);
        $priceVersion = 'market-'.substr(
            hash('sha256', json_encode($homePrices, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            0,
            16
        );

        return view('index', compact('homePrices', 'priceVersion'));
    }
}

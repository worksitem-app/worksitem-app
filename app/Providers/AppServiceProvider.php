<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\UnitPrice;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Merkezi fiyatları tüm 'tools.*' view'larına otomatik enjekte ediyoruz.
        View::composer('tools.*', function ($view) {
            $pricing = config('pricing') ?? []; // Eski fallback
            
            if (Schema::hasTable('unit_prices')) {
                try {
                    $prices = UnitPrice::where('price_pool', 'market')->where('is_active', true)->get();
                    foreach ($prices as $p) {
                        $subKey = str_replace($p->category . '_', '', $p->key);
                        // DB'den gelen güncel fiyatla eskisini eziyoruz
                        $pricing[$p->category][$subKey] = $p->price;
                        $pricing[$p->category][$p->key] = $p->price; // Emniyet kilidi
                    }
                } catch (\Exception $e) {
                    // Veritabanı hazır değilse yoksay
                }
            }
            $view->with('pricing', $pricing);
        });
        \Illuminate\Support\Facades\URL::forceScheme('https');

        //
    }
}

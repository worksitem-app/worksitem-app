<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\CostEngine;

class ViewServiceProvider extends ServiceProvider
{
    public function register() { }

    public function boot()
    {
        // Tüm hesaplayıcılara ve ana maliyet-hesapla sayfasına güncel ÇŞB fiyatlarını ve Çarpanı gönderiyoruz
        View::composer('*', function ($view) {
            $prices = CostEngine::getGlobalPrices();
            $view->with('SYSTEM_PRICES', json_encode($prices));
            $view->with('KAINTA_MULTIPLIER', 1.40); // %40 Kainta Ticari Çarpanı (Kâr + Genel Gider)
        });
    }
}

<?php

namespace App\Providers;

use App\Services\CostEngine;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * CSB_PRICES / KAINTA_MULTIPLIER kullanan legacy hesaplayıcı view'ları.
     *
     * ÖNCE: View::composer('*') → her view + her Blade partial + Filament bileşenleri için
     * çalışıyor ve TÜM market fiyat listesini her sayfanın HTML kaynağına basıyordu.
     *
     * ŞİMDİ: yalnızca bu view'lar, ve yalnızca view dosyasında adı geçen fiyat anahtarları.
     */
    private const PRICED_VIEWS = [
        'tools.ac-calculator', 'tools.bathroom-calculator', 'tools.building-weight',
        'tools.cable-calculator', 'tools.cement-calculator', 'tools.column-capacity',
        'tools.column-starter-bar', 'tools.core-strength', 'tools.cutting-optimization',
        'tools.domed-tank', 'tools.elevator-calculator', 'tools.energy-calculator',
        'tools.flat-for-land-calculator', 'tools.garden-wall-calculator', 'tools.generator-calculator',
        'tools.installment-vs-cash', 'tools.insulation-calculator', 'tools.isolator-calculator',
        'tools.kitchen-calculator', 'tools.labor-calculator', 'tools.logistics-cost',
        'tools.maliyet-hesapla', 'tools.map-scale-calculator', 'tools.material-weight',
        'tools.mortgage-calculator', 'tools.plumbing-calculator', 'tools.pump-power',
        'tools.pvc-calculator', 'tools.radiator-calculator', 'tools.rainpipe-calculator',
        'tools.rebar-equivalent-calculator', 'tools.rent-value', 'tools.roof-calculator',
        'tools.room-planner', 'tools.salary-calculator', 'tools.scaffolding-calculator',
        'tools.sgk-labor', 'tools.shelter-regulation', 'tools.simple-beam',
        'tools.solar-calculator', 'tools.stair-calculator', 'tools.stairs-calculator',
        'tools.static-project-fee', 'tools.structural-dynamics', 'tools.unit-price-calculator',
        'tools.ventilation-duct', 'tools.zoning-calculator',
    ];

    /** @var array<string, array<string, mixed>> view path => filtrelenmiş fiyatlar (istek içi) */
    private static array $memo = [];

    public function register(): void
    {
    }

    public function boot(): void
    {
        View::composer(self::PRICED_VIEWS, function ($view) {
            $path = $view->getPath();

            if (! isset(self::$memo[$path])) {
                $source = is_file($path) ? (string) file_get_contents($path) : '';

                self::$memo[$path] = array_filter(
                    CostEngine::getGlobalPrices(),
                    fn ($price, $key) => $source !== '' && str_contains($source, (string) $key),
                    ARRAY_FILTER_USE_BOTH
                );
            }

            $view->with('SYSTEM_PRICES', json_encode((object) self::$memo[$path], JSON_UNESCAPED_UNICODE));
            $view->with('KAINTA_MULTIPLIER', (float) config('pricing.kainta_multiplier', 1.40));
        });
    }
}

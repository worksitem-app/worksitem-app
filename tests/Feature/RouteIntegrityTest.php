<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Tests\TestCase;

/**
 * Parametresiz TÜM public GET rotalarını gezer; hiçbiri 500 dönmemeli.
 *
 * Neyi yakalar: eksik view (ör. eski /araclar/beta-hesaplayici), eksik controller
 * metodu (ör. eski projects.pdf → downloadPdf yoktu), view içinde tanımsız değişken,
 * bozuk Blade. 60+ araç sayfası için tek tek test yazmaya gerek bırakmaz.
 */
class RouteIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_parametresiz_public_get_rotalari_500_donmez(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $checked = 0;
        $failures = [];

        /** @var Route $route */
        foreach (app('router')->getRoutes() as $route) {
            $uri = $route->uri();

            if (! in_array('GET', $route->methods(), true)
                || str_contains($uri, '{')
                || str_starts_with($uri, 'admin')
                || str_starts_with($uri, 'livewire')
                || str_starts_with($uri, 'filament')
                || str_starts_with($uri, 'storage')
                || str_starts_with($uri, '_')
            ) {
                continue;
            }

            $status = $this->get('/'.ltrim($uri, '/'))->status();
            $checked++;

            if ($status >= 500) {
                $failures[] = "{$status}  /{$uri}";
            }
        }

        $this->assertGreaterThan(50, $checked, 'Beklenenden az rota tarandı.');
        $this->assertSame([], $failures, "500 dönen rotalar:\n".implode("\n", $failures));
    }

    public function test_routelarda_tanimli_her_controller_metodu_mevcut(): void
    {
        $missing = [];

        foreach (app('router')->getRoutes() as $route) {
            $action = $route->getActionName();

            if (! str_contains($action, '@') || ! str_starts_with($action, 'App\\')) {
                continue;
            }

            [$class, $method] = explode('@', $action);

            if (! method_exists($class, $method)) {
                $missing[] = $action.'  → /'.$route->uri();
            }
        }

        $this->assertSame([], $missing, "Eksik controller metotları:\n".implode("\n", $missing));
    }

    public function test_view_larda_kullanilan_tum_route_isimleri_tanimli(): void
    {
        $names = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));

        foreach ($files as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            preg_match_all("/route\\(\\s*['\"]([a-zA-Z0-9_.\\-]+)['\"]/", file_get_contents($file->getPathname()), $m);
            foreach ($m[1] as $name) {
                $names[$name][] = str_replace(resource_path('views').'/', '', $file->getPathname());
            }
        }

        $undefined = array_filter(
            $names,
            fn ($_, $name) => ! app('router')->has($name),
            ARRAY_FILTER_USE_BOTH
        );

        $this->assertSame([], array_keys($undefined), 'Tanımsız route isimleri: '.json_encode($undefined, JSON_UNESCAPED_UNICODE));
    }
}

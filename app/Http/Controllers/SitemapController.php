<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index()
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $now = now()->toAtomString();

        // Öncelikli sayfalar
        $priorityPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/araclar', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/araclar/otomatik-kesif-fiyatlandirma', 'priority' => '0.95', 'changefreq' => 'weekly'],
            ['loc' => '/maliyet-hesapla', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/araclar/beton-hesaplayici', 'priority' => '0.8'],
            ['loc' => '/araclar/dijital-metraj', 'priority' => '0.8'],
            ['loc' => '/araclar/boya-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/seramik-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/alcipan-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/sap-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/beton-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/banyo-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/demir-metraji-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/pvc-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/merdiven-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/insaat-maliyeti-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/imarlı-hesaplayici', 'priority' => '0.7'],
            ['loc' => '/araclar/harita-olcek-hesaplayici', 'priority' => '0.6'],
            ['loc' => '/araclar/kat-karsiligi-hesaplayici', 'priority' => '0.6'],
            ['loc' => '/ai-tasarim/ic-mekan', 'priority' => '0.8'],
            ['loc' => '/kvkk-aydinlatma', 'priority' => '0.3'],
        ];

        // Otomatik: tüm isimli GET route'ları
        $excludePrefixes = ['admin', 'filament', 'api', 'sanctum', 'debugbar', 'livewire', '_ignition', 'horizon', 'telescope'];

        $autoPages = [];
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (!$name) continue;
            if (!in_array('GET', $route->methods())) continue;

            $uri = $route->uri();

            $excluded = false;
            foreach ($excludePrefixes as $prefix) {
                if (str_starts_with($uri, $prefix) || str_starts_with($name, $prefix)) {
                    $excluded = true;
                    break;
                }
            }
            if ($excluded) continue;
            if (str_contains($uri, '{')) continue;

            $autoPages[] = [
                'loc' => '/' . ltrim($uri, '/'),
                'priority' => '0.5',
                'changefreq' => 'monthly',
            ];
        }

        // Birleştir
        $allPages = [];
        $seen = [];
        foreach (array_merge($priorityPages, $autoPages) as $page) {
            $loc = $page['loc'];
            if (isset($seen[$loc])) continue;
            $seen[$loc] = true;
            $allPages[] = $page;
        }

        // XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($allPages as $page) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($baseUrl . $page['loc'], ENT_XML1) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $now . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $page['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}

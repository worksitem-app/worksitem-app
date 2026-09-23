<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $now = now()->toAtomString();

        $urls = [];

        $urls[] = [
            'loc' => $baseUrl . '/',
            'lastmod' => $now,
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ];

        $urls[] = [
            'loc' => $baseUrl . '/araclar',
            'lastmod' => $now,
            'changefreq' => 'weekly',
            'priority' => '0.9',
        ];

        $routes = collect(Route::getRoutes())->filter(function ($route) {
            $uri = $route->uri();

            if (str_starts_with($uri, 'admin')) return false;
            if (str_starts_with($uri, 'api')) return false;
            if (str_starts_with($uri, '_')) return false;
            if (str_contains($uri, '{')) return false;
            if (!in_array('GET', $route->methods())) return false;

            return true;
        });

        foreach ($routes as $route) {
            $uri = $route->uri();
            if ($uri === '/') continue;

            $urls[] = [
                'loc' => $baseUrl . '/' . ltrim($uri, '/'),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}

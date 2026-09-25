<?php

namespace App\Http\Controllers;

use App\Models\TenderPoz;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = 'https://worksitem.com';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Statik sayfalar
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/araclar', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/araclar/otomatik-kesif-fiyatlandirma', 'priority' => '0.95', 'changefreq' => 'weekly'],
            ['loc' => '/maliyet-hesapla', 'priority' => '0.95', 'changefreq' => 'weekly'],
            ['loc' => '/pozlar', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/araclar/beton-hesaplayici', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/araclar/dijital-metraj', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        $today = date('c');

        foreach ($staticPages as $page) {
            $loc = $baseUrl . $page['loc'];
            $priority = $page['priority'] ?? '0.5';
            $changefreq = $page['changefreq'] ?? 'monthly';

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>{$today}</lastmod>\n";
            $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
            $xml .= "    <priority>{$priority}</priority>\n";
            $xml .= "  </url>\n";
        }

        // Poz sayfaları (43.290 URL)
        TenderPoz::query()
            ->where('is_active', true)
            ->select('poz_no', 'updated_at')
            ->orderBy('poz_no')
            ->chunk(1000, function ($pozs) use (&$xml, $baseUrl) {
                foreach ($pozs as $poz) {
                    $loc = $baseUrl . '/poz/' . $poz->poz_no;
                    $lastmod = $poz->updated_at ? $poz->updated_at->toAtomString() : date('c');

                    $xml .= "  <url>\n";
                    $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
                    $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                    $xml .= "    <changefreq>monthly</changefreq>\n";
                    $xml .= "    <priority>0.6</priority>\n";
                    $xml .= "  </url>\n";
                }
            });

        $xml .= "</urlset>";

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}

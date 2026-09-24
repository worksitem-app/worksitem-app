<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = 'https://worksitem.com';

        // Statik sayfalar
        $pages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/araclar', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/araclar/otomatik-kesif-fiyatlandirma', 'priority' => '0.95', 'changefreq' => 'weekly'],
            ['loc' => '/maliyet-hesapla', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/araclar/beton-hesaplayici', 'priority' => '0.8'],
            ['loc' => '/araclar/dijital-metraj', 'priority' => '0.8'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($pages as $page) {
            $loc = $baseUrl . $page['loc'];
            $priority = $page['priority'] ?? '0.5';
            $changefreq = $page['changefreq'] ?? 'monthly';
            $lastmod = date('c');

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
            $xml .= "    <priority>{$priority}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>";

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}

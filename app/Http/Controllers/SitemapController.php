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

        /*
         * Ana / statik sayfalar.
         *
         * Statik sayfalarda her sitemap isteğinde "bugün" lastmod
         * üretmiyoruz. Yanlış güncellik sinyali vermemek için lastmod
         * yalnız gerçek veri tarihi bulunan poz kayıtlarında yayınlanır.
         */
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/araclar', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/maliyet-hesapla', 'priority' => '0.95', 'changefreq' => 'weekly'],
            ['loc' => '/pozlar', 'priority' => '0.9', 'changefreq' => 'daily'],

            ['loc' => '/firma-uyeligi', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/legal/kvkk-aydinlatma', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/legal/acik-riza', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/legal/cerez-politikasi', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/legal/gizlilik-politikasi', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/legal/kvkk-basvuru', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/legal/kullanim-kosullari', 'priority' => '0.4', 'changefreq' => 'monthly'],
        ];

        /*
         * Araç sitemap source-of-truth:
         * config/worksitem_tools.php -> tool_status
         *
         * Yalnız production kullanıma açık "active" araçlar
         * sitemap'e girer. review / soon / inactive araçlar girmez.
         */
        $toolConfig = config('worksitem_tools', []);

        $catalog = $toolConfig['tools'] ?? [];
        $statusConfig = $toolConfig['tool_status'] ?? [];

        $defaultStatus = $statusConfig['default'] ?? 'review';
        $overrides = $statusConfig['overrides'] ?? [];

        ksort($catalog);

        foreach ($catalog as $slug => $meta) {
            /*
             * Bu slug katalog ekranında da bilinçli olarak gizleniyor.
             */
            if ($slug === 'havalandirma-kanali') {
                continue;
            }

            $status = $overrides[$slug] ?? $defaultStatus;

            if ($status !== 'active') {
                continue;
            }

            $staticPages[] = [
                'loc' => '/araclar/' . $slug,
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ];
        }

        // Firma rehberi (yalnız açıkken ve onaylı firmalar)
        if (config('worksitem_firms.directory_enabled')) {
            try {
                $staticPages[] = ['loc' => '/firmalar', 'priority' => '0.7', 'changefreq' => 'weekly'];
                foreach (\App\Models\Firm::query()->approved()->orderBy('id')->pluck('slug') as $fs) {
                    $staticPages[] = ['loc' => '/firmalar/'.$fs, 'priority' => '0.5', 'changefreq' => 'monthly'];
                }
            } catch (\Throwable) {
            }
        }

        foreach ($staticPages as $page) {
            $loc = $baseUrl . $page['loc'];
            $priority = $page['priority'] ?? '0.5';
            $changefreq = $page['changefreq'] ?? 'monthly';

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
            $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
            $xml .= "    <priority>{$priority}</priority>\n";
            $xml .= "  </url>\n";
        }

        /*
         * Aktif poz kayıtları.
         *
         * lastmod yalnız gerçek updated_at varsa yayınlanır.
         */
        TenderPoz::query()
            ->where('is_active', true)
            // İçeriği zenginleştirilmemiş (yalnızca poz no + fiyat) sayfalar dizine girmez; bkz. config/worksitem_seo.php
            ->when(config('worksitem_seo.poz_index_only_enriched', true), fn ($q) => $q->where('seo_enriched', true))
            ->select('poz_no', 'updated_at')
            ->orderBy('poz_no')
            ->chunk(1000, function ($pozs) use (&$xml, $baseUrl) {
                foreach ($pozs as $poz) {
                    $loc = \App\Support\PozUrl::absolute($poz->poz_no);

                    $xml .= "  <url>\n";
                    $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";

                    if ($poz->updated_at) {
                        $lastmod = $poz->updated_at->toAtomString();
                        $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                    }

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

<?php

namespace App\Http\Middleware;

use App\Support\ToolSeo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Eski şablonlu araç sayfalarını yeni standarda getirir (sayfaya dokunmadan):
 *  - arama başlığı ve açıklaması (config/worksitem_tool_seo.php; yoksa "| WORKSİTEM" eki düzeltilir)
 *  - WebApplication yapılandırılmış verisi yoksa eklenir
 *  - "Markalar ve üreticiler için" sponsorluk bandı yoksa alt bilginin hemen üstüne eklenir
 *  - "Nasıl çalışır? / Neden Worksitem?" bandı (tüm araçlar) markalar bandının üstüne eklenir
 * Yeni şablonlar (kit, banyo, planlayıcı) bunları zaten içerdiği için atlanır.
 */
class ToolPageEnhancer
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200 || $request->boolean('embed')
            || ! preg_match('#^araclar/([a-z0-9-]+)$#', $request->path(), $m)
            || ! str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }
        $slug = $m[1];
        $html = $response->getContent();
        if (! is_string($html) || stripos($html, '</head>') === false) {
            return $response;
        }
        $tool = (array) config("worksitem_tools.tools.$slug", []);
        if (! $tool) {
            return $response;
        }

        // 1) Başlık ve açıklama
        $seo = (array) config("worksitem_tool_seo.$slug", []);
        $year = (string) now()->year;
        if (preg_match('#<title[^>]*>(.*?)</title>#is', $html, $tm)) {
            $old = trim(html_entity_decode(strip_tags($tm[1])));
            $new = ! empty($seo['title']) ? ToolSeo::title(str_replace(':yil', $year, $seo['title'])) : ToolSeo::title($old);
            if ($new !== $old) {
                $html = str_replace($tm[0], '<title>'.e($new).'</title>', $html);
                $html = preg_replace('#(<meta\s+property=["\']og:title["\']\s+content=["\'])[^"\']*#i', '${1}'.e($new), $html, 1);
            }
        }
        // Hesap motoru araçlarında açıklama, güncel örnek fiyatla otomatik yazılır (yalnız config'te elle açıklama yoksa)
        if (empty($seo['desc']) && in_array($slug, \App\Estimators\KitRegistry::slugs(), true)) {
            try {
                $seo['desc'] = ToolSeo::priceSnippet((array) (\App\Estimators\KitRegistry::get($slug)->page()['faq'] ?? [])) ?? '';
            } catch (\Throwable) {
            }
        }
        if (! empty($seo['desc'])) {
            $desc = e(ToolSeo::desc(str_replace(':yil', $year, $seo['desc'])));
            if (preg_match('#<meta\s+name=["\']description["\'][^>]*>#i', $html)) {
                $html = preg_replace('#(<meta\s+name=["\']description["\']\s+content=["\'])[^"\']*#i', '${1}'.$desc, $html, 1);
                $html = preg_replace('#(<meta\s+property=["\']og:description["\']\s+content=["\'])[^"\']*#i', '${1}'.$desc, $html, 1);
            } else {
                $html = preg_replace('#</head>#i', '<meta name="description" content="'.$desc.'">'."\n</head>", $html, 1);
            }
        }

        // 2) Yapılandırılmış veri
        if (! str_contains($html, '"WebApplication"')) {
            $ld = json_encode([
                '@context' => 'https://schema.org', '@type' => 'WebApplication',
                'name' => $tool['title'] ?? $slug, 'url' => 'https://worksitem.com/araclar/'.$slug,
                'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web', 'inLanguage' => 'tr-TR',
                'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'TRY'],
                'publisher' => ['@type' => 'Organization', 'name' => 'Worksitem', 'url' => 'https://worksitem.com'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
            $html = preg_replace('#</head>#i', '<script type="application/ld+json">'.$ld.'</script>'."\n</head>", $html, 1);
        }

        // 3) Markalar için bandı
        if (! str_contains($html, 'class="ws-bc"')) {
            try {
                $band = '<div class="ws-bc-wrap" style="max-width:1180px;margin:0 auto;padding:0 20px 8px">'
                    .\Illuminate\Support\Facades\Blade::render('<x-sponsor.brand-cta :tool="$tool" />', ['tool' => $slug]).'</div>';
                $pos = strripos($html, '<footer');
                $pos = $pos !== false ? $pos : strripos($html, '</body>');
                if ($pos !== false) {
                    $html = substr($html, 0, $pos).$band."\n".substr($html, $pos);
                }
            } catch (\Throwable) {
                // bant eklenemezse sayfa olduğu gibi kalır
            }
        }

        // 3b) "Nasıl çalışır? / Neden Worksitem?" bandı: markalar bandının hemen üstüne
        if (! str_contains($html, 'class="ws-how"')) {
            try {
                $how = \Illuminate\Support\Facades\Blade::render('<x-tools.how :tool="$tool" />', ['tool' => $slug]);
                if (($p = strpos($html, '<div class="ws-bc-wrap"')) !== false) {
                    $html = substr($html, 0, $p).'<div class="ws-how-wrap" style="max-width:1180px;margin:0 auto;padding:0 20px 8px">'.$how.'</div>'."\n".substr($html, $p);
                } elseif (($p = strpos($html, '<section class="ws-bc"')) !== false) {
                    $html = substr($html, 0, $p).$how."\n".substr($html, $p);
                }
            } catch (\Throwable) {
                // bant eklenemezse sayfa olduğu gibi kalır
            }
        }

        // 4) Yazdır / PDF: her araçta aynı Worksitem logolu başlık ve alt bilgi (yalnız çıktıda görünür)
        if (! str_contains($html, 'ws-print-head') && preg_match('#<body[^>]*>#i', $html, $bm, PREG_OFFSET_CAPTURE)) {
            $title = e($tool['title'] ?? $slug);
            $priceDate = \App\Support\PriceDate::for($slug)->locale('tr')->translatedFormat('j F Y');
            $today = now('Europe/Istanbul')->locale('tr')->translatedFormat('j F Y H:i');
            $head = '<div class="ws-print-head" aria-hidden="true"><img src="/brand/worksitem-logo.png" alt="Worksitem" height="30">'
                .'<div><b>'.$title.'</b><span>Rapor tarihi: '.e($today).' · Fiyat tarihi: '.e($priceDate).'</span></div></div>'
                .'<div class="ws-print-foot" aria-hidden="true">worksitem.com/araclar/'.e($slug).' · Worksitem · Yaklaşık hesaptır, teklif değildir.</div>'
                .'<style>.ws-print-head,.ws-print-foot{display:none}@media print{'
                .'.ws-print-head{display:flex!important;align-items:center;gap:14px;padding:0 0 10px;margin:0 0 14px;border-bottom:3px solid #FFB800;font-family:Inter,Arial,sans-serif;color:#101318}'
                .'.ws-print-head img{height:30px;width:auto}.ws-print-head b{display:block;font-size:15px}.ws-print-head span{display:block;font-size:11px;color:#5B6472}'
                .'.ws-print-foot{display:block!important;position:fixed;bottom:0;left:0;right:0;font:10px Inter,Arial,sans-serif;color:#8b93a0;text-align:center;padding-top:4px;border-top:1px solid #E3E6EA;background:#fff}'
                .'.ws-bc-wrap,.ws-bc,.ws-sp{display:none!important}}</style>';
            $at = $bm[0][1] + strlen($bm[0][0]);
            $html = substr($html, 0, $at).$head.substr($html, $at);
        }

        $response->setContent($html);

        return $response;
    }
}

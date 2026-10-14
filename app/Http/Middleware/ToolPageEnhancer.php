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
        if (! empty($seo['desc'])) {
            $desc = e(ToolSeo::desc(str_replace(':yil', $year, $seo['desc'])));
            if (preg_match('#<meta\s+name=["\']description["\'][^>]*>#i', $html)) {
                $html = preg_replace('#(<meta\s+name=["\']description["\']\s+content=["\'])[^"\']*#i', '${1}'.$desc, $html, 1);
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

        $response->setContent($html);

        return $response;
    }
}

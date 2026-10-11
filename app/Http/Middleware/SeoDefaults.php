<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public HTML sayfalarında eksik kalan SEO/paylaşım etiketlerini tamamlar:
 *  - <link rel="canonical"> yoksa: sorgu dizgisiz mevcut URL
 *  - og:* / twitter:* yoksa: <title>, meta description ve canonical'dan üretilir
 *
 * Sayfada zaten varsa hiçbir şeye dokunmaz (sayfaya özel etiketler önceliklidir).
 * 2026-10 taramasında 91 sayfanın 85'inde Open Graph, 9'unda canonical yoktu.
 */
class SeoDefaults
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200
            || $request->is('admin', 'admin/*', 'livewire/*', 'up')
            || ! str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || stripos($html, '</head>') === false) {
            return $response;
        }

        $tags = [];

        if (preg_match('#<link[^>]+rel=["\']canonical["\'][^>]*href=["\']([^"\']+)#i', $html, $m)) {
            $canonical = html_entity_decode($m[1]);
        } else {
            $base = rtrim((string) config('app.url'), '/');
            $canonical = $request->path() === '/' ? $base.'/' : $base.'/'.$request->path();
            $tags[]= '<link rel="canonical" href="'.e($canonical).'">';
        }

        if (! preg_match('#property=["\']og:title#i', $html)) {
            $title = preg_match('#<title[^>]*>(.*?)</title>#is', $html, $m) ? trim(html_entity_decode(strip_tags($m[1]))) : config('app.name');
            $desc = preg_match('#<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)#i', $html, $m) ? html_entity_decode($m[1]) : '';
            $image = rtrim(config('app.url'), '/').'/brand/og-image.png';

            $tags[] = '<meta property="og:type" content="website">';
            $tags[] = '<meta property="og:locale" content="tr_TR">';
            $tags[] = '<meta property="og:site_name" content="WORKSİTEM">';
            $tags[] = '<meta property="og:title" content="'.e($title).'">';
            if ($desc !== '') {
                $tags[] = '<meta property="og:description" content="'.e($desc).'">';
            }
            $tags[] = '<meta property="og:url" content="'.e($canonical).'">';
            $tags[] = '<meta property="og:image" content="'.e($image).'">';
            $tags[] = '<meta name="twitter:card" content="summary_large_image">';
        }

        if ($tags === []) {
            return $response;
        }

        $pos = stripos($html, '</head>');
        $response->setContent(substr($html, 0, $pos).implode("\n", $tags)."\n".substr($html, $pos));

        return $response;
    }
}

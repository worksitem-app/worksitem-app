<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tüm public HTML sayfalarına marka katmanını ekler:
 *  - Inter font bağlantısı (yoksa)
 *  - /css/ws-brand.css (</head> öncesine, sayfa stillerinden SONRA → tipografi her yerde aynı)
 *
 * 100+ bağımsız Blade sayfası tek bir layout'tan türemediği için ortak katman buradan verilir.
 */
class BrandShell
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() !== 200
            || $request->is('admin', 'admin/*', 'livewire/*', 'up')
            || ! str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || stripos($html, '</head>') === false || str_contains($html, '/css/ws-brand.css')) {
            return $response;
        }

        $tags = '';
        if (! str_contains($html, 'family=Inter')) {
            $tags .= '<link rel="preconnect" href="https://fonts.googleapis.com">'
                .'<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
                .'<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">';
        }
        $tags .= '<link rel="stylesheet" href="/css/ws-brand.css?v=1">';

        $pos = stripos($html, '</head>');
        $response->setContent(substr($html, 0, $pos).$tags."\n".substr($html, $pos));

        return $response;
    }
}

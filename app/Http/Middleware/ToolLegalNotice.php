<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tüm hesaplama aracı sayfalarına (config('worksitem_legal.paths')) hukuki uyarıyı ekler:
 * site footer'ının hemen üstüne, footer yoksa </body> öncesine.
 * 100+ bağımsız Blade sayfası olduğu için tek noktadan verilir.
 */
class ToolLegalNotice
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET')
            || $response->getStatusCode() !== 200
            || ! $request->is(...(array) config('worksitem_legal.paths', []))
            || $request->is(...(array) config('worksitem_legal.except', ['__none__']))
            || ! str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || str_contains($html, 'id="ws-legal-notice"')) {
            return $response;
        }

        $pos = stripos($html, '<footer class="wk-footer');
        if ($pos === false) {
            $pos = strripos($html, '</body>');
        }
        if ($pos === false) {
            return $response;
        }

        $block = view('components.tools.legal-notice')->render();
        $response->setContent(substr($html, 0, $pos).$block.substr($html, $pos));

        return $response;
    }
}

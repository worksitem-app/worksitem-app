<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yapısal / teknik hesaplayıcılara belirgin sorumluluk uyarısı ekler.
 * Liste: config('worksitem_tools.engineering_tools')
 */
class EngineeringToolNotice
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return $response;
        }

        $segments = explode('/', trim($request->path(), '/'));
        if (($segments[0] ?? '') !== 'araclar' || ! isset($segments[1])
            || ! in_array($segments[1], (array) config('worksitem_tools.engineering_tools', []), true)) {
            return $response;
        }

        if (! str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || str_contains($html, 'id="ws-eng-notice"')
            || ! preg_match('/<body[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE)) {
            return $response;
        }

        $notice = '<div id="ws-eng-notice" role="note" style="background:#fef3c7;color:#78350f;border-bottom:1px solid #f59e0b;'
            .'padding:10px 16px;font:14px/1.5 system-ui,-apple-system,\'Segoe UI\',sans-serif;text-align:center">'
            .'<strong>Önemli:</strong> Bu araç yalnızca ön kontrol ve eğitim amaçlıdır. Sonuçlar, yetkili bir inşaat mühendisinin '
            .'hazırladığı statik proje veya hesap raporunun <strong>yerine geçmez</strong>; uygulama ya da taşıyıcı sisteme müdahale için kullanılmamalıdır. '
            .'<a href="/legal/kullanim-kosullari" style="color:#78350f;text-decoration:underline">Kullanım Koşulları</a></div>';

        // Site header'ının hemen altına yerleştir; header yoksa <body> başına.
        $pos = $m[0][1] + strlen($m[0][0]);
        $headerStart = strpos($html, 'class="wks-header"');
        if ($headerStart !== false) {
            $headerEnd = stripos($html, '</header>', $headerStart);
            if ($headerEnd !== false) {
                $pos = $headerEnd + strlen('</header>');
            }
        }
        $response->setContent(substr($html, 0, $pos).$notice.substr($html, $pos));

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public HTML yanıtlarına çerez onay bandını ve onaya bağlı GA4 yükleyicisini ekler.
 *
 * KVKK: Analitik/pazarlama etiketleri kullanıcı onay vermeden YÜKLENMEZ.
 * (Eskiden gtag.js her sayfaya koşulsuz basılıyordu; Çerez Politikası ile çelişiyordu.)
 */
class InjectGa4Tag
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $contentType = (string) $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html')) {
            return $response;
        }

        if ($request->is('admin', 'admin/*', 'livewire/*')) {
            return $response;
        }

        $gaId = (string) config('services.ga4.measurement_id');
        if (! preg_match('/^G-[A-Z0-9]{4,20}$/', $gaId)) {
            $gaId = '';
        }

        $adsEnabled = filled(config('lead_conversion.google_ads.conversion_id'));

        // Hiçbir izleyici yapılandırılmamışsa onay istenecek bir şey yok.
        if ($gaId === '' && ! $adsEnabled) {
            return $response;
        }

        $content = $response->getContent();
        if (! is_string($content) || $content === '' || str_contains($content, 'id="ws-cookie-banner"')) {
            return $response;
        }

        $snippet = view('partials.cookie-consent', ['gaId' => $gaId])->render();

        if (stripos($content, '</body>') !== false) {
            $pos = strripos($content, '</body>');
            $content = substr($content, 0, $pos).$snippet."\n".substr($content, $pos);
        } else {
            $content .= $snippet;
        }

        $response->setContent($content);

        return $response;
    }
}

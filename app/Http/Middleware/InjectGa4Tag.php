<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectGa4Tag
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        error_log("[GA4-MW] Request: " . $request->path() . " | Content-Type: " . $response->headers->get("Content-Type", "none"));

        // Sadece HTML yanıtlarına enjekte et
        $contentType = $response->headers->get('Content-Type', '');
        if (strpos($contentType, 'text/html') === false) {
            return $response;
        }

        $ga4Id = config('services.ga4.measurement_id');
        if (empty($ga4Id)) {
            return $response;
        }

        $content = $response->getContent();
        if (empty($content) || stripos($content, 'googletagmanager.com/gtag') !== false) {
            return $response; // Zaten tag varsa ekleme
        }

        $ga4Tag = <<<HTML
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$ga4Id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$ga4Id}', {
    'send_page_view': true,
    'anonymize_ip': true
  });
</script>
<!-- End Google Analytics 4 -->
HTML;

        // </head> tag'inin hemen öncesine ekle
        if (stripos($content, '</head>') !== false) {
            $content = preg_replace('/<\/head>/i', $ga4Tag . "\n</head>", $content, 1);
        } elseif (stripos($content, '<body') !== false) {
            $content = preg_replace('/<body/i', $ga4Tag . "\n<body", $content, 1);
        }

        $response->setContent($content);
        return $response;
    }
}

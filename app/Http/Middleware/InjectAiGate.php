<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AI özelliği çağıran sayfalara telefonla giriş / kredi penceresini ekler.
 * Pencere, AI uç noktalarından gelen 401/402 (ai_gate) yanıtlarını yakalar;
 * girişten sonra isteği kullanıcı bir şey yapmadan yeniden gönderir.
 */
class InjectAiGate
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('worksitem_ai_credits.enabled') || ! $request->isMethod('GET')
            || $request->is('admin', 'admin/*', 'livewire/*')
            || ! str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (! is_string($content) || $content === '' || str_contains($content, 'id="wsag-config"')) {
            return $response;
        }

        $hit = false;
        foreach ((array) config('worksitem_ai_credits.endpoints') as $ep) {
            if (str_contains($content, $ep)) {
                $hit = true;
                break;
            }
        }
        if (! $hit) {
            return $response;
        }

        $snippet = view('partials.ai-gate')->render();
        $pos = strripos($content, '</body>');
        $content = $pos !== false ? substr($content, 0, $pos).$snippet."\n".substr($content, $pos) : $content.$snippet;
        $response->setContent($content);

        return $response;
    }
}

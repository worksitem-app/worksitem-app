<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Basit uygulama-seviyesi filtre. Asıl koruma: Eloquent/Query Builder binding,
 * Blade escaping ve (önerilen) Cloudflare/Nginx WAF katmanıdır.
 *
 * ÖNCE: ham request body'nin tamamı regex'ten geçiyordu → PDF/görsel upload'larında
 * binary içerik yanlış-pozitif 403 üretiyor, büyük body'lerde CPU harcıyordu.
 * ŞİMDİ: URI + query her zaman; body yalnızca küçük, dosyasız isteklerde ve
 * sadece string input değerleri üzerinden taranır.
 */
class BlockSuspiciousRequests
{
    private const MAX_BODY_BYTES = 65536;

    protected array $uriPatterns = [
        '/(\.\.\/|\.\.\\\\)/',
        '/(\/\.env|\/\.git|wp-admin|wp-login|phpmyadmin|xmlrpc\.php)/i',
        '/(union\s+(all\s+)?select|information_schema)/i',
        '/(<script|javascript:|onerror\s*=)/i',
    ];

    protected array $bodyPatterns = [
        '/(<script|javascript:|onerror\s*=)/i',
        '/(union\s+(all\s+)?select|information_schema)/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $uri = rawurldecode($request->getRequestUri());

        foreach ($this->uriPatterns as $pattern) {
            if (preg_match($pattern, $uri)) {
                abort(403, 'Şüpheli istek engellendi.');
            }
        }

        if ($this->shouldScanBody($request)) {
            $values = [];
            $input = $request->post();
            array_walk_recursive($input, function ($v) use (&$values) {
                if (is_string($v)) {
                    $values[] = $v;
                }
            });
            $body = implode(' ', $values);

            foreach ($this->bodyPatterns as $pattern) {
                if (preg_match($pattern, $body)) {
                    abort(403, 'Şüpheli istek engellendi.');
                }
            }
        }

        return $next($request);
    }

    private function shouldScanBody(Request $request): bool
    {
        if ($request->isMethodSafe() || count($request->allFiles()) > 0) {
            return false;
        }

        // Admin (Filament/Livewire) zengin metin içerebilir; zaten auth arkasında.
        if ($request->is('admin', 'admin/*', 'livewire/*')) {
            return false;
        }

        return (int) $request->header('Content-Length', 0) <= self::MAX_BODY_BYTES;
    }
}

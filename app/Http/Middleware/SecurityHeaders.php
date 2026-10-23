<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // İçerik Güvenlik Politikası (v106). CSP_MODE=report → yalnızca tarayıcı konsoluna uyarı (varsayılan),
        // enforce → engeller, off → gönderilmez. Admin/Livewire hariç (Filament kendi betiklerini kullanır).
        $mode = (string) config('app.csp_mode', 'report');
        if ($mode !== 'off' && ! $request->is('admin', 'admin/*', 'livewire/*')) {
            $response->headers->set(
                $mode === 'enforce' ? 'Content-Security-Policy' : 'Content-Security-Policy-Report-Only',
                self::csp()
            );
        }

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    public static function csp(): string
    {
        $cdn = 'https://cdn.jsdelivr.net https://cdnjs.cloudflare.com';
        $google = 'https://www.googletagmanager.com https://*.google-analytics.com https://*.googleadservices.com https://googleads.g.doubleclick.net https://www.google.com';
        $iyzico = 'https://*.iyzipay.com';

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' $cdn $google $iyzico",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com $cdn",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' $cdn $google $iyzico",
            "frame-src 'self' $iyzico https://www.googletagmanager.com",
            "worker-src 'self' blob: https://cdnjs.cloudflare.com",
            "form-action 'self' $iyzico",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "object-src 'none'",
        ]);
    }
}

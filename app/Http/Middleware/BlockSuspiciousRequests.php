<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockSuspiciousRequests
{
    protected array $patterns = [
        '/(\.\.\/|\.\.\\\\)/',
        '/(<script|javascript:|onerror=)/i',
        '/(union.*select|select.*from.*information_schema)/i',
        '/(\.env|\.git|wp-admin|phpmyadmin)/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $target = $request->fullUrl() . ' ' . $request->getContent();

        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $target)) {
                abort(403, 'Şüpheli istek engellendi.');
            }
        }

        return $next($request);
    }
}

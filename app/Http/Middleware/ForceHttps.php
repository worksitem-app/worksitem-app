<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Canlıda tek adres: https://worksitem.com
 *  - http → https (301)
 *  - www.worksitem.com → worksitem.com (301; aynı sitenin iki kopyası arama motorunda bölünmesin)
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('production')) {
            return $next($request);
        }

        $canonicalHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        $host = strtolower($request->getHost());

        if ($canonicalHost !== '' && $host === 'www.'.$canonicalHost) {
            return redirect()->away('https://'.$canonicalHost.$request->getRequestUri(), 301);
        }

        if (! $request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}

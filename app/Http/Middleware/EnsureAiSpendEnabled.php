<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AI harcama kilidi: AI_SPEND_ENABLED=false iken dış AI API'sine giden tüm
 * public uçlar 503 döner (sunucuda elle eklenen route-closure kilidinin
 * düzgün hâli — kilit açıldığında asıl controller'lar çalışır).
 */
class EnsureAiSpendEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('services.ai_spend_enabled')) {
            $message = 'Yapay zekâ özellikleri geçici olarak kapalı. Lütfen daha sonra tekrar deneyin.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message, 'error' => $message], 503)
                : response($message, 503);
        }

        return $next($request);
    }
}

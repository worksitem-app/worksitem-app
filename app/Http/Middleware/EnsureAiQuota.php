<?php

namespace App\Http\Middleware;

use App\Services\AI\AiCredits;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AI kullanım hakkı: telefonla giriş + günde N ücretsiz + kredi.
 * Giriş yoksa 401, hak bitmişse 402 döner (ai_gate alanıyla; sayfadaki pencere bunu yakalar).
 * Başarısız AI yanıtında (2xx değilse) ayrılan hak iade edilir.
 */
class EnsureAiQuota
{
    public function __construct(private AiCredits $credits) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('worksitem_ai_credits.enabled') || $request->user()?->is_admin) {
            return $next($request);
        }

        $feature = (string) ($request->route()?->getName() ?? $request->path());
        $label = ((array) config('worksitem_ai_credits.features'))[$feature] ?? 'Yapay zekâ özelliği';
        $wallet = $this->credits->current($request);

        if (! $wallet) {
            return $this->gate($request, 401, 'login',
                $label.' için telefonunuzla giriş yapın. Her gün '.config('worksitem_ai_credits.free_per_day').' kullanım ücretsizdir.');
        }

        $cost = self::cost($request, $feature);
        $reservation = $this->credits->reserve($wallet, $feature, $cost);
        if (! $reservation['ok']) {
            return $this->gate($request, 402, 'credits', $cost > 1
                ? 'Bu seçenek '.$cost.' kredi gerektirir; günlük ücretsiz hak bu seçenekte kullanılmaz. Kredi yükleyin ya da standart görseli deneyin.'
                : 'Bugünkü ücretsiz hakkınızı kullandınız. Devam etmek için kredi yükleyin ya da yarın tekrar deneyin.');
        }

        $request->attributes->set('ai_charge', ['wallet_id' => $wallet->id, 'reservation' => $reservation]);

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            $this->credits->refund($wallet, $reservation, $feature);
            throw $e;
        }

        if ($response->getStatusCode() >= 300) {
            $this->credits->refund($wallet, $reservation, $feature);
        }

        $s = $this->credits->status($wallet);
        $response->headers->set('X-AI-Quota', json_encode(['free_left' => $s['free_left'], 'credits' => $s['credits'], 'used' => $reservation['kind']]));

        return $response;
    }

    /** İstek başına kredi: config('worksitem_ai_credits.costs') — ör. quality=hd → 2 kredi */
    public static function cost(Request $request, string $feature): int
    {
        $rule = ((array) config('worksitem_ai_credits.costs'))[$feature] ?? null;
        if (! is_array($rule)) {
            return 1;
        }

        return max(1, (int) (($rule['map'] ?? [])[(string) $request->input($rule['param'] ?? '')] ?? 1));
    }

    private function gate(Request $request, int $status, string $reason, string $message): Response
    {
        $payload = [
            'success' => false,
            'ai_gate' => $reason,
            'message' => $message,
            'error' => $message,
        ] + $this->credits->status($this->credits->current($request));

        return $request->expectsJson() || $request->ajax() || $request->isMethod('POST')
            ? response()->json($payload, $status)
            : redirect('/')->with('error', $message);
    }
}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware - her istekte calisir
        $middleware->append(\App\Http\Middleware\StagingTeamAccess::class);
        $middleware->append(\App\Http\Middleware\ToolQaGate::class);
        $middleware->append(\App\Http\Middleware\EngineeringToolNotice::class);
        $middleware->append(\App\Http\Middleware\ToolLegalNotice::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\ForceHttps::class);
        $middleware->append(\App\Http\Middleware\BlockSuspiciousRequests::class);
        $middleware->append(\App\Http\Middleware\InjectGa4Tag::class);
        $middleware->append(\App\Http\Middleware\SeoDefaults::class);
        $middleware->append(\App\Http\Middleware\BrandShell::class);
        // En içte: araç sayfası başlık/şema/marka bandı (SeoDefaults og etiketlerini yeni başlıktan üretir)
        $middleware->append(\App\Http\Middleware\ToolPageEnhancer::class);

        // Route middleware alias'lari
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'api.ratelimit' => \App\Http\Middleware\ApiRateLimit::class,
            'ai.spend' => \App\Http\Middleware\EnsureAiSpendEnabled::class,
            'ai.quota' => \App\Http\Middleware\EnsureAiQuota::class,
        ]);

        // AI kilidi throttle'dan ÖNCE çalışsın: kilit kapalıyken gelen istekler AI kotasını tüketmesin
        $middleware->prependToPriorityList(
            before: \Illuminate\Routing\Middleware\ThrottleRequests::class,
            prepend: \App\Http\Middleware\EnsureAiSpendEnabled::class,
        );
        // AI kullanım hakkı da throttle'dan önce: giriş/kredi penceresi IP kotasını tüketmesin
        $middleware->appendToPriorityList(
            after: \App\Http\Middleware\EnsureAiSpendEnabled::class,
            append: \App\Http\Middleware\EnsureAiQuota::class,
        );
        $middleware->append(\App\Http\Middleware\InjectAiGate::class);

        // Giris yapmamis kullanicilari Filament login sayfasina yonlendir
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('admin/*')
                ? route('filament.admin.auth.login')
                : route('login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Süresi dolan form anahtarı: JSON isteklerinde İngilizce "CSRF token mismatch." yerine anlaşılır Türkçe mesaj
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419 && $request->expectsJson()) {
                return response()->json([
                    'message' => 'Sayfa uzun süre açık kaldı. Sayfayı yenileyip tekrar deneyin.',
                ], 419);
            }
        });

        // Veritabanı bağlantı/limit hatası (eşzamanlı yükte MySQL 1040/1203 vb.): 500 yerine 503 + Retry-After.
        // Yalnız yanıt değişir; rapor()/log her zamanki gibi yazılır.
        $isDbConnectionError = static function (\Throwable $e): bool {
            for ($x = $e, $depth = 0; $x !== null && $depth < 5; $x = $x->getPrevious(), $depth++) {
                if (! $x instanceof \PDOException) {
                    continue;
                }
                $msg = (string) $x->getMessage();
                $codes = [(string) $x->getCode()];
                if (is_array($x->errorInfo ?? null)) {
                    $codes[] = (string) ($x->errorInfo[0] ?? '');
                    $codes[] = (string) ($x->errorInfo[1] ?? '');
                }
                if (preg_match('/SQLSTATE\[(\w+)\](?:\s*\[(\d+)\]|: [^:]+: (\d+))?/', $msg, $m)) {
                    $codes[] = $m[1];
                    $codes[] = $m[2] ?? '';
                    $codes[] = $m[3] ?? '';
                }
                foreach ($codes as $c) {
                    // 1040 Too many connections, 1203/1226 kullanıcı bağlantı sınırı, 2002/2006/2013 sunucuya ulaşılamadı / bağlantı koptu
                    if (in_array($c, ['1040', '1203', '1226', '2002', '2006', '2013'], true) || str_starts_with($c, '08')) {
                        return true;
                    }
                }
                if (stripos($msg, 'max_user_connections') !== false || stripos($msg, 'Too many connections') !== false) {
                    return true;
                }
            }

            return false;
        };

        $exceptions->render(function (\PDOException $e, Request $request) use ($isDbConnectionError) {
            if (! $isDbConnectionError($e)) {
                return null;
            }
            $headers = ['Retry-After' => '30'];
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Sunucu şu an yoğun. Lütfen birkaç saniye sonra tekrar deneyin.',
                ], 503, $headers);
            }

            return response()->view('errors.503', ['busy' => true], 503, $headers);
        });

        // QueryException'i 422 olarak render et (Integer overflow icin) — yalnız girdi kaynaklı (bağlantı dışı) hatalar
        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) use ($isDbConnectionError) {
            if ($request->expectsJson() && ! $isDbConnectionError($e)) {
                return response()->json([
                    'message' => 'Girilen değerler işlenemedi. Lütfen kontrol edin.',
                ], 422);
            }
        });
    })->create();

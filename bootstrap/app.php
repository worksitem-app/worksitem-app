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

        // QueryException'i 422 olarak render et (Integer overflow icin)
        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Girilen değerler işlenemedi. Lütfen kontrol edin.',
                ], 422);
            }
        });
    })->create();

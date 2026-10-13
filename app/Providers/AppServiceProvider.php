<?php

namespace App\Providers;

use App\Models\UnitPrice;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const TOOLS_PRICING_CACHE_KEY = 'tools_pricing_matrix_v1';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Admin'den yönetilen araç yayın durumları config varsayılanlarının üzerine yazılır
        // (ToolQaGate, sitemap, araç listesi aynı kaynağı okur)
        if (! $this->app->runningUnitTests()) {
            \App\Models\ToolStatus::applyToConfig();
        }

        // Yalnızca production'da: local/test ortamında https zorlamak
        // url() / redirect / test assertion'larını bozuyordu.
        if ($this->app->environment('production', 'staging')) {
            URL::forceScheme('https');
        }

        // Cloudflare / load balancer arkasında gerçek istemci IP'si ve https bilgisi.
        // Ayarlanmazsa: tüm ziyaretçiler proxy IP'sini paylaşır (rate limit herkesi birden kilitler)
        // ve ForceHttps yönlendirme döngüsüne girebilir. Örn: TRUSTED_PROXIES=* veya "173.245.48.0/20,103.21.244.0/22"
        $trusted = trim((string) config('app.trusted_proxies', ''));
        if ($trusted !== '') {
            \Illuminate\Http\Middleware\TrustProxies::at(
                $trusted === '*' ? '*' : array_map('trim', explode(',', $trusted))
            );
        }

        $this->configureRateLimiters();

        // Merkezi fiyatlar 'tools.*' view'larına enjekte edilir.
        // ÖNCE: her render'da Schema::hasTable + tüm unit_prices sorgusu.
        // ŞİMDİ: 1 saat cache, admin fiyat güncellemesinde CostEngine::clearCache() ile temizlenir.
        View::composer('tools.*', function ($view) {
            $view->with('pricing', self::toolsPricing());
        });
    }

    public static function toolsPricing(): array
    {
        $pricing = config('pricing') ?? [];

        try {
            $rows = Cache::remember(self::TOOLS_PRICING_CACHE_KEY, 3600, fn () => UnitPrice::query()
                ->where('price_pool', 'market')
                ->where('is_active', true)
                ->get(['category', 'key', 'price'])
                ->toArray());
        } catch (\Throwable) {
            return $pricing; // DB hazır değilse config fallback
        }

        foreach ($rows as $p) {
            $subKey = str_replace($p['category'].'_', '', $p['key']);
            $pricing[$p['category']][$subKey] = $p['price'];
            $pricing[$p['category']][$p['key']] = $p['price'];
        }

        return $pricing;
    }

    /**
     * Pahalı / dış API'ye giden public endpoint'ler için isimli limitler.
     * routes/web.php içinde ->middleware('throttle:ai') şeklinde kullanılır.
     */
    private function configureRateLimiters(): void
    {
        // Dış AI API'si (Replicate / Gemini / OpenAI) tetikleyen istekler
        RateLimiter::for('ai', fn (Request $r) => [
            Limit::perMinute(5)->by('ai-m:'.$r->ip()),
            Limit::perDay(30)->by('ai-d:'.$r->ip()),
            // Global günlük tavan: çok sayıda IP'den gelen kötüye kullanımda
            // Replicate/Gemini faturasının sınırsız büyümesini engeller.
            Limit::perDay(max(1, (int) config('services.ai.global_daily_limit', 300)))
                ->by('ai-global')
                ->response(fn () => response()->json([
                    'message' => 'Yoğunluk nedeniyle AI servisi bugün için geçici olarak kapalı. Lütfen yarın tekrar deneyin.',
                ], 429)),
        ]);

        // AI durum sorgulama (polling) — sık ama sınırsız değil
        // Canlı hesap (ölçü değiştikçe): yazarken çok istek gelir, cömert ama sınırlı
        RateLimiter::for('kit-calc', fn (Request $r) => Limit::perMinute(240)->by('kit:'.$r->ip()));
        RateLimiter::for('ai-poll', fn (Request $r) => Limit::perMinute(90)->by($r->ip()));

        // DB'ye kayıt açan public form endpoint'leri (proje, hesaplama)
        RateLimiter::for('public-write', fn (Request $r) => Limit::perMinute(20)->by($r->ip()));

        // Arama / JSON okuma
        RateLimiter::for('public-read', fn (Request $r) => Limit::perMinute(60)->by($r->ip()));
    }
}

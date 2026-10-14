<?php

namespace App\Console\Commands;

use App\Services\ToolPriceService;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Günlük fiyat araştırma sisteminin durumu (yalnızca okur, hiçbir şeyi değiştirmez).
 *   php artisan prices:status
 */
class PriceStatus extends Command
{
    protected $signature = 'prices:status {--preset=ilk-dalga : Kapsamı gösterilecek araç listesi}';

    protected $description = 'Fiyat araştırma zamanlaması, son gözlemler ve araç başına veritabanı fiyat kapsamı';

    public function handle(Schedule $schedule, ToolPriceService $prices): int
    {
        $this->line('<options=bold>1) Zamanlama</>');
        $beat = Preflight::lastBeat();
        $this->line('  Zamanlayıcı kalp atışı: '.($beat ? (time() - $beat).' sn önce' : 'YOK (cron çalışmıyor)'));
        $research = collect($schedule->events())->map(fn ($e) => (string) ($e->command ?? $e->description ?? ''))->filter(fn ($c) => str_contains($c, 'prices:'));
        foreach (['prices:research-paint-web', 'prices:research-tile-web', 'prices:research-paint', 'prices:research-materials', 'prices:daily-material-report'] as $cmd) {
            $on = $research->contains(fn ($c) => str_contains($c, $cmd.' ') || str_ends_with(trim($c, "'\" "), $cmd));
            $this->line(sprintf('  %-30s %s', $cmd, $on ? 'zamanlanmış' : 'ZAMANLANMAMIŞ'));
        }
        $this->line('  Gemini anahtarı: '.(filled(config('services.gemini.api_key')) ? 'var' : 'YOK')
            .' · AI_SPEND_ENABLED: '.(config('services.ai_spend_enabled') ? 'açık' : 'kapalı'));

        $this->newLine();
        $this->line('<options=bold>2) Piyasa gözlemleri (material_price_observations)</>');
        if (Schema::hasTable('material_price_observations')) {
            $q = DB::table('material_price_observations');
            $dateCol = Schema::hasColumn('material_price_observations', 'observed_at') ? 'observed_at' : 'created_at';
            $this->line('  Toplam: '.(clone $q)->count().' · son gözlem: '.((clone $q)->max($dateCol) ?? '-')
                .' · son 7 gün: '.(clone $q)->where($dateCol, '>=', now()->subDays(7))->count()
                .' · son 30 gün: '.(clone $q)->where($dateCol, '>=', now()->subDays(30))->count());
            if (Schema::hasColumn('material_price_observations', 'verification_status')) {
                $by = (clone $q)->select('verification_status', DB::raw('count(*) as n'))->groupBy('verification_status')->pluck('n', 'verification_status');
                $this->line('  Durum: '.$by->map(fn ($n, $k) => ($k ?: 'boş').'='.$n)->implode(' · '));
            }
            $keys = (clone $q)->where($dateCol, '>=', now()->subDays(30))->distinct()->count('material_key');
            $this->line('  Son 30 günde fiyatı araştırılan farklı malzeme: '.$keys);
        } else {
            $this->line('  Tablo yok');
        }

        $this->newLine();
        $this->line('<options=bold>3) Araç başına fiyat kaynağı</> (veritabanı = fiyat merkezi; yoksa koddaki sabit varsayılan kullanılır)');
        $slugs = (array) config('worksitem_golive.presets.'.$this->option('preset'), []);
        $hasBind = Schema::hasTable('tool_price_bindings');
        $rows = [];
        foreach ($slugs as $slug) {
            $n = $hasBind ? DB::table('tool_price_bindings')->where('tool_slug', $slug)->count() : 0;
            $resolved = 0;
            $oldest = null;
            if ($n) {
                foreach ($prices->forTool($slug) as $r) {
                    $r = (array) $r;
                    if (is_numeric($r['price'] ?? null) && (float) $r['price'] > 0) {
                        $resolved++;
                        $d = $r['updated_at'] ?? $r['observed_at'] ?? $r['source_date'] ?? null;
                        if ($d && (! $oldest || $d < $oldest)) {
                            $oldest = (string) $d;
                        }
                    }
                }
            }
            $kind = $n === 0 ? 'SABİT (kod)' : ($resolved < $n ? 'KISMİ' : 'veritabanı');
            $class = config('worksitem_estimators.tools.'.$slug);
            if ($class && is_subclass_of($class, \App\Estimators\PozEstimator::class)) {
                $kind = 'resmî YFK (ÇŞB)';
            } elseif (in_array($slug, ['banyo-maliyet-hesaplayici', 'banyo-planlayici', 'wc-maliyet-hesabi', 'wc-planlayici'], true) && Schema::hasTable('unit_prices')) {
                $b = DB::table('unit_prices')->where('category', \App\Services\BathroomPriceBook::CATEGORY)->where('is_active', true)->where('review_status', 'approved');
                $n = (clone $b)->count();
                $resolved = $n;
                $oldest = (clone $b)->min('source_date');
                $kind = $n ? 'fiyat merkezi (banyo)' : 'SABİT (kod)';
            }
            $rows[] = [$slug, $n, $resolved, $oldest ? substr((string) $oldest, 0, 10) : '-', $kind];
        }
        $this->table(['Araç', 'Bağlı kalem', 'Fiyatı bulunan', 'En eski fiyat', 'Kaynak'], $rows);

        return self::SUCCESS;
    }
}

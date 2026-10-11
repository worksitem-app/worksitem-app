<?php

use App\Console\Commands\Preflight;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Zamanlanmış görevler
|--------------------------------------------------------------------------
|
| Sunucuda TEK bir cron satırı yeterlidir (Hostinger → Gelişmiş → Cron Jobs):
|
|   * * * * * cd /home/USER/worksitem && php artisan schedule:run >> /dev/null 2>&1
|
| Eski scripts/daily-market-mail.sh cron satırı KALDIRILMALI; aksi halde
| raporlar iki kez gönderilir.
|
*/

// Zamanlayıcının çalıştığını app:preflight doğrulayabilsin diye kalp atışı
Schedule::call(fn () => Cache::put(Preflight::HEARTBEAT_KEY, now(), now()->addHour()))
    ->name('scheduler-heartbeat')
    ->everyMinute();

// Günlük CEO raporu
Schedule::command('reports:daily-ceo')
    ->dailyAt('08:15')
    ->timezone('Europe/Istanbul')
    ->withoutOverlapping()
    ->onOneServer();

// Son 24 saatteki malzeme fiyat değişimleri raporu
Schedule::command('prices:daily-material-report')
    ->dailyAt('08:30')
    ->timezone('Europe/Istanbul')
    ->withoutOverlapping()
    ->onOneServer();

// Poz sayfaları için AI SEO zenginleştirme (canlıda elle eklenmişti).
// AI_SPEND_ENABLED=false iken komut kendini durdurur; harcama yapmaz.
Schedule::command('poz:enrich-seo --limit=100')
    ->dailyAt('02:00')
    ->timezone('Europe/Istanbul')
    ->withoutOverlapping()
    ->onOneServer();

// KVKK saklama süresi: kullanıcıların yüklediği oda fotoğrafları 30 gün sonra silinir
Schedule::command('app:prune-ai-inputs --days=30')
    ->dailyAt('03:30')
    ->timezone('Europe/Istanbul')
    ->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| WORKSITEM Daily Paint Market Research
|--------------------------------------------------------------------------
| Önce piyasa araştırılır.
| Büyük fiyat değişimleri otomatik uygulanmaz;
| Kainta mühendis kontrolüne gönderilir.
*/

// prices:research-paint-web disabled: external Gemini quota dependency.


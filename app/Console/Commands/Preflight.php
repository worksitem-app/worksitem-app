<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Canlıya çıkış kontrol listesi. Deploy sonrası çalıştırın:
 *
 *   php artisan app:preflight
 *
 * FAIL varsa çıkış kodu 1 döner (deploy betiği durur). WARN bilgi amaçlıdır.
 */
class Preflight extends Command
{
    public const HEARTBEAT_KEY = 'scheduler:heartbeat';

    protected $signature = 'app:preflight';

    protected $description = 'Canlı ortam yapılandırmasını denetler (debug, https, mail, DB, migration, cron...)';

    private int $fails = 0;

    private int $warns = 0;

    public function handle(): int
    {
        $this->line('<options=bold>Worksitem canlı ortam kontrolü</>');

        // ── Uygulama ───────────────────────────────────────────
        $this->check(app()->environment('production'), 'APP_ENV=production', 'APP_ENV şu an: '.app()->environment());
        $this->check(! config('app.debug'), 'APP_DEBUG=false', 'APP_DEBUG açık: hata sayfaları kaynak kodu ve .env değerlerini sızdırır');
        $this->check(filled(config('app.key')), 'APP_KEY tanımlı', 'APP_KEY boş: php artisan key:generate');
        $this->check(str_starts_with((string) config('app.url'), 'https://'), 'APP_URL https', 'APP_URL https ile başlamalı (sitemap, canonical, mail linkleri)');
        $this->check((bool) config('session.secure'), 'SESSION_SECURE_COOKIE=true', 'SESSION_SECURE_COOKIE=true olmalı (oturum çerezi yalnız https)');
        $this->warnUnless(config('logging.channels.daily.level') !== 'debug', 'LOG_LEVEL debug değil', 'LOG_LEVEL=debug: loglar hızla büyür ve kişisel veri içerebilir');

        // ── Veritabanı ─────────────────────────────────────────
        $dbOk = false;
        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (\Throwable $e) {
            // mesaj bilinçli olarak kısaltılıyor (şifre/host sızmasın)
        }
        $this->check($dbOk, 'Veritabanı bağlantısı', 'Veritabanına bağlanılamıyor (DB_* değerlerini kontrol edin)');

        if ($dbOk) {
            $pending = $this->pendingMigrations();
            $this->check($pending === [], 'Bekleyen migration yok', count($pending).' bekleyen migration: php artisan migrate --force');

            if (Schema::hasTable('users')) {
                $this->check(User::where('is_admin', true)->exists(), 'En az bir admin var', 'Admin yok: php artisan app:make-admin you@worksitem.com');
                $this->check(! User::where('email', 'test@example.com')->exists(), 'Test kullanıcısı yok', 'test@example.com kullanıcısı var (şifresi "password"): silin');
            }
        }

        // ── Dosya sistemi ──────────────────────────────────────
        $this->check(is_writable(storage_path()) && is_writable(base_path('bootstrap/cache')), 'storage/ ve bootstrap/cache yazılabilir', 'storage/ veya bootstrap/cache yazılamıyor');
        $this->check(file_exists(public_path('storage')), 'public/storage linki var', 'php artisan storage:link çalıştırın (AI görselleri 404 olur)');

        // ── E-posta / bildirim ─────────────────────────────────
        $mailer = (string) config('mail.default');
        $this->check(! in_array($mailer, ['log', 'array'], true), "MAIL_MAILER=$mailer", "MAIL_MAILER=$mailer: lead bildirimleri gönderilmez");
        $this->check(filled(config('worksitem_leads.notification_email')), 'Lead bildirim adresi tanımlı', 'LEAD_NOTIFICATION_EMAIL boş: yeni lead e-postası gitmez');

        // ── Dış servisler ──────────────────────────────────────
        $this->warnUnless(filled(config('services.gemini.api_key')), 'GEMINI_API_KEY tanımlı', 'GEMINI_API_KEY boş: AI keşif/fiyat özellikleri çalışmaz');
        $this->warnUnless(filled(config('services.replicate.token')), 'REPLICATE_API_TOKEN tanımlı', 'REPLICATE_API_TOKEN boş: AI iç mekan tasarımı yedek görsel döner');
        $ga = (string) config('services.ga4.measurement_id');
        $this->warnUnless($ga === '' || preg_match('/^G-[A-Z0-9]{4,20}$/', $ga), 'GA4 ID biçimi', "GA4_MEASUREMENT_ID geçersiz: $ga");

        // ── Zamanlayıcı (cron) ─────────────────────────────────
        $beat = (int) Cache::get(self::HEARTBEAT_KEY, 0);
        $this->warnUnless($beat > 0 && (time() - $beat) <= 300, 'Zamanlayıcı (cron) çalışıyor',
            'Son 5 dk zamanlayıcı kalp atışı yok. Cron: * * * * * cd '.base_path().' && php artisan schedule:run >> /dev/null 2>&1');

        $this->warnUnless(app(\App\Services\FxRates::class)->current() !== null, 'Döviz kuru önbellekte (TCMB)',
            'Döviz kuru yok: php artisan fx:update (banyo aracında $/€/£ satırı gizli kalır)');

        // ── Performans ─────────────────────────────────────────
        $missing = collect(['mersis' => 'COMPANY_MERSIS', 'tax_office' => 'COMPANY_TAX_OFFICE', 'tax_no' => 'COMPANY_TAX_NO', 'kep' => 'COMPANY_KEP'])
            ->filter(fn ($env, $key) => blank(config("worksitem_company.$key")))->values()->implode(', ');
        $this->warnUnless($missing === '', 'Şirket kimlik bilgileri (sözleşmeler)', "Keşif sözleşmesinde eksik: $missing (.env)");

        $this->warnUnless(app()->configurationIsCached(), 'config cache', 'php artisan config:cache çalıştırın');
        $this->warnUnless(app()->routesAreCached(), 'route cache', 'php artisan route:cache çalıştırın');

        $this->newLine();
        $summary = "Sonuç: {$this->fails} FAIL, {$this->warns} WARN";
        $this->fails > 0 ? $this->error($summary) : $this->info($summary);

        return $this->fails > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function check(bool $ok, string $label, string $failMessage): void
    {
        if ($ok) {
            $this->line("  <fg=green>✔</> $label");

            return;
        }
        $this->fails++;
        $this->line("  <fg=red>✘ FAIL</> $failMessage");
    }

    private function warnUnless(bool $ok, string $label, string $warnMessage): void
    {
        if ($ok) {
            $this->line("  <fg=green>✔</> $label");

            return;
        }
        $this->warns++;
        $this->line("  <fg=yellow>! WARN</> $warnMessage");
    }

    private function pendingMigrations(): array
    {
        $migrator = app('migrator');
        if (! $migrator->repositoryExists()) {
            return ['migrations tablosu yok'];
        }

        $files = array_keys($migrator->getMigrationFiles([database_path('migrations')]));
        $ran = $migrator->getRepository()->getRan();

        return array_values(array_diff($files, $ran));
    }
}

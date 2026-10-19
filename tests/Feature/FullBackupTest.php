<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

/** v78: spatie/laravel-backup — her gece 03:00 tam yedek, son 7 gün */
class FullBackupTest extends TestCase
{
    public function test_paket_kurulu_ve_ayarlar_yedi_gun(): void
    {
        $this->assertTrue(class_exists(\Spatie\Backup\BackupServiceProvider::class), 'composer install çalıştırılmalı');

        $this->assertSame(['backups'], config('backup.backup.destination.disks'));
        $this->assertSame(storage_path('app/spatie-backups'), config('filesystems.disks.backups.root'));
        $this->assertSame(7, config('backup.cleanup.default_strategy.keep_all_backups_for_days'));
        foreach (['keep_daily_backups_for_days', 'keep_weekly_backups_for_weeks', 'keep_monthly_backups_for_months', 'keep_yearly_backups_for_years'] as $k) {
            $this->assertSame(0, config("backup.cleanup.default_strategy.$k"), $k);
        }

        $exclude = config('backup.backup.source.files.exclude');
        foreach (['vendor', 'node_modules', '.git'] as $d) {
            $this->assertContains(base_path($d), $exclude);
        }
        // Yedek kendi klasörünü ve eski dökümleri tekrar yedeklemesin
        $this->assertContains(storage_path('app/spatie-backups'), $exclude);
        $this->assertContains(storage_path('app/backups'), $exclude);
    }

    public function test_her_gece_0300_istanbul_ve_temizlik_zamanlandi(): void
    {
        $events = collect(app(Schedule::class)->events());

        $run = $events->first(fn ($e) => str_contains((string) $e->command, 'backup:run'));
        $this->assertNotNull($run, 'backup:run zamanlanmamış');
        $this->assertSame('0 3 * * *', $run->expression);
        $this->assertSame('Europe/Istanbul', (string) $run->timezone);

        $this->assertNotNull($events->first(fn ($e) => str_contains((string) $e->command, 'backup:monitor')));
    }

    public function test_komutlar_kayitli(): void
    {
        $this->artisan('list', ['namespace' => 'backup'])
            ->expectsOutputToContain('backup:run')
            ->expectsOutputToContain('backup:clean')
            ->assertSuccessful();
    }
}

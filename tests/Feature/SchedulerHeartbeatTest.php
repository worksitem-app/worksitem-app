<?php

namespace Tests\Feature;

use App\Console\Commands\Preflight;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Canlıda database cache store nesne unserialize etmez (Laravel 13 serializable_classes);
 * kalp atışı düz unix zaman damgası olmalı.
 */
class SchedulerHeartbeatTest extends TestCase
{
    public function test_kalp_atisi_skaler_saklanir_ve_preflight_cokmeden_okur(): void
    {
        Cache::forget(Preflight::HEARTBEAT_KEY);
        $this->travelTo(now()->startOfHour()->addMinutes(10));   // kalp atışı 5 dakikada bir (v84)
        $this->artisan('schedule:run')->assertSuccessful();

        $this->assertIsInt(Cache::get(Preflight::HEARTBEAT_KEY));

        $this->artisan('app:preflight')
            ->expectsOutputToContain('Zamanlayıcı (cron) çalışıyor');
    }
}

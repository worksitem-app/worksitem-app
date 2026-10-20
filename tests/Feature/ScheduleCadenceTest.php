<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

/**
 * v84: paylaşımlı hostingte cron 5 dakikada bir (*\/5) çalışır.
 * Dakikası 5'in katı olmayan ya da her dakika çalışan bir görev hiç tetiklenmez veya
 * sunucuyu yorar; bu test yeni görev eklenirken bunu yakalar.
 */
class ScheduleCadenceTest extends TestCase
{
    public function test_tum_gorevler_bes_dakikalik_crona_uygun(): void
    {
        $bad = [];
        foreach (app(Schedule::class)->events() as $e) {
            $minute = explode(' ', $e->expression)[0];
            $ok = $minute === '*/5' || $minute === '*/10' || $minute === '*/15' || $minute === '*/30'
                || collect(explode(',', $minute))->every(fn ($m) => ctype_digit($m) && (int) $m % 5 === 0);
            if (! $ok) {
                $bad[] = $e->expression.'  '.($e->description ?: $e->command);
            }
        }
        $this->assertSame([], $bad, "5 dakikalık crona uymayan görevler:\n".implode("\n", $bad));
    }
}

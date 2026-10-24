<?php

namespace Tests\Feature;

use App\Services\KaintaCostEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v111: AI açılırken sürpriz fatura korumaları */
class AiLimitV111Test extends TestCase
{
    use RefreshDatabase;

    public function test_aylik_toplam_tavan_gunluk_tavandan_bagimsiz_isler(): void
    {
        config(['services.ai.global_daily_limit' => 100, 'services.ai.global_monthly_limit' => 1,
            'services.ai_spend_enabled' => true, 'worksitem_ai_credits.enabled' => false]);

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.1'])
            ->postJson('/ai-tasarim/ic-mekan/start', [])->assertStatus(422);   // hak tüketildi

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.2'])
            ->postJson('/ai-tasarim/ic-mekan/start', [])
            ->assertStatus(429)->assertJsonFragment(['message' => 'Yapay zekâ servisinin bu ayki kullanım sınırı doldu. Hesaplayıcılar ücretsiz çalışmaya devam ediyor.']);
    }

    public function test_varsayilan_tavanlar_dusuk(): void
    {
        $src = file_get_contents(config_path('services.php'));
        $this->assertStringContainsString("env('AI_GLOBAL_DAILY_LIMIT', 40)", $src);
        $this->assertStringContainsString("env('AI_GLOBAL_MONTHLY_LIMIT', 600)", $src);
        $this->assertStringContainsString("env('AI_COST_ADVICE_ENABLED', false)", $src);
        $this->assertSame(1, (int) config('worksitem_ai_credits.free_per_day'));
    }

    public function test_maliyet_hesaplayici_ai_aciksa_bile_kota_disi_cagri_yapmaz(): void
    {
        Http::fake();
        config(['services.ai_spend_enabled' => true, 'services.ai.cost_advice' => false, 'services.gemini.api_key' => 'test']);

        $r = app(KaintaCostEngine::class)->calculate('komple', 100, 'standart', 'Kadıköy');

        Http::assertNothingSent();
        $this->assertArrayHasKey('ai_tavsiye', $r);
    }

    public function test_ai_check_tavanlari_gosterir(): void
    {
        config(['services.gemini.api_key' => '']);
        $this->artisan('ai:check')
            ->expectsOutputToContain('30 günlük toplam tavan')
            ->expectsOutputToContain('Maliyet hesaplayıcı AI tavsiyesi: kapalı')
            ->assertExitCode(1);
    }
}

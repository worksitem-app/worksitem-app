<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Middleware\TrustProxies;
use Tests\TestCase;

/**
 * Canlıya çıkış öncesi sertleştirmeler (2. tur CTO denetimi).
 */
class GoLiveHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        TrustProxies::flushState();
        parent::tearDown();
    }

    public function test_ai_global_gunluk_tavan_farkli_ip_lerden_de_asilamaz(): void
    {
        config(['services.ai.global_daily_limit' => 1, 'services.ai_spend_enabled' => true]);

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->postJson('/ai-tasarim/ic-mekan/start', [])
            ->assertStatus(422); // validasyon, ama hak tüketildi

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])
            ->postJson('/ai-tasarim/ic-mekan/start', [])
            ->assertStatus(429);
    }

    public function test_ai_harcama_kilidi_kapaliyken_ai_uclari_503_doner_ve_hak_tuketmez(): void
    {
        config(['services.ai_spend_enabled' => false, 'services.ai.global_daily_limit' => 1]);

        foreach (['/ai-tasarim/ic-mekan/start', '/maliyet-hesapla/guest-render', '/api/seramik-gorsellestirici/render'] as $url) {
            $this->postJson($url, [])->assertStatus(503);
        }

        // Kilit açılınca global hak hâlâ duruyor (kapalıyken tüketilmedi)
        config(['services.ai_spend_enabled' => true]);
        $this->postJson('/ai-tasarim/ic-mekan/start', [])->assertStatus(422);
    }

    public function test_seeder_production_da_bilinen_sifreli_kullanici_acmaz(): void
    {
        $this->app['env'] = 'production';
        try {
            $this->app->make(\Database\Seeders\DatabaseSeeder::class)->run();
        } finally {
            $this->app['env'] = 'testing';
        }

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_make_admin_komutu_admin_olusturur_ve_yukseltir(): void
    {
        $this->artisan('app:make-admin', ['email' => 'ceo@worksitem.com', '--name' => 'CEO', '--password' => 'Uzun-Guclu-Sifre-2026'])
            ->assertSuccessful();

        $user = User::where('email', 'ceo@worksitem.com')->firstOrFail();
        $this->assertTrue($user->isAdmin());

        $this->artisan('app:make-admin', ['email' => 'ceo@worksitem.com', '--password' => 'kisa'])
            ->assertFailed();
    }

    public function test_preflight_testing_ortaminda_production_hatalarini_raporlar(): void
    {
        config([
            'app.debug' => true,
            'app.url' => 'http://worksitem.com',
        ]);

        $this->artisan('app:preflight')
            ->expectsOutputToContain('APP_DEBUG')
            ->assertFailed();
    }
}

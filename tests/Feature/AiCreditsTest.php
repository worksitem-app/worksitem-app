<?php

namespace Tests\Feature;

use App\Models\AiCreditTransaction;
use App\Models\AiWallet;
use App\Models\User;
use App\Services\AI\AiCredits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiCreditsTest extends TestCase
{
    use RefreshDatabase;

    private const START = '/ai-tasarim/ic-mekan/start';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem',
            'services.ai_spend_enabled' => true,
            'worksitem_ai_credits.enabled' => true,
            'worksitem_ai_credits.free_per_day' => 1,
        ]);
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
    }

    private function login(string $phone = '05327654321'): void
    {
        $this->postJson(route('ai_account.code'), ['phone' => $phone])->assertOk()->assertJson(['ok' => true]);
        preg_match('/(\d{6})/', Http::recorded()->last()[0]['messages'][0]['msg'], $m);
        $this->postJson(route('ai_account.login'), ['phone' => $phone, 'code' => $m[1], 'kvkk' => '1'])
            ->assertOk()->assertJson(['ok' => true, 'logged_in' => true, 'free_left' => 1, 'credits' => 0]);
    }

    public function test_girissiz_ai_istegi_401_ve_giris_penceresi_ister(): void
    {
        $this->postJson(self::START, [])
            ->assertStatus(401)
            ->assertJson(['ai_gate' => 'login', 'logged_in' => false]);
        $this->assertSame(0, AiWallet::query()->count());
    }

    public function test_yanlis_kod_ve_kvkk_onaysiz_giris_yapilamaz(): void
    {
        $this->postJson(route('ai_account.code'), ['phone' => '05327654321'])->assertOk();
        $this->postJson(route('ai_account.login'), ['phone' => '05327654321', 'code' => '000000', 'kvkk' => '1'])->assertStatus(422);
        $this->postJson(route('ai_account.login'), ['phone' => '05327654321', 'code' => '123456'])->assertStatus(422)->assertJsonValidationErrors('kvkk');
        $this->getJson(route('ai_account.status'))->assertJson(['logged_in' => false]);
    }

    public function test_gunde_bir_ucretsiz_sonra_kredi_sonra_402(): void
    {
        $this->login();
        $w = AiWallet::query()->firstOrFail();
        $this->assertSame('5327654321', $w->phone);

        // 1. kullanım ücretsiz (doğrulama hatası olsa da istek controller'a ulaşır; 422 → hak iade edilir)
        $this->postJson(self::START, [])->assertStatus(422);
        $this->assertSame(1, $w->fresh()->freeLeft(), 'başarısız istekte hak iade edilir');

        // Başarılı kullanımı doğrudan servis üzerinden simüle et
        $credits = app(AiCredits::class);
        $this->assertSame('free', $credits->reserve($w, 'test')['kind']);
        $this->assertSame(0, $w->fresh()->freeLeft());

        // Hak bitti → 402
        $this->postJson(self::START, [])->assertStatus(402)->assertJson(['ai_gate' => 'credits', 'free_left' => 0, 'credits' => 0]);

        // Admin 2 kredi yükler → kredi kullanılır
        $credits->grant($w, 2, '2 kredi test');
        $this->assertSame('use', $credits->reserve($w->fresh(), 'test')['kind']);
        $this->assertSame(1, (int) $w->fresh()->credits);

        // Başarısız istekte kredi iade edilir ve yanıt başlığı kalan hakkı söyler
        $r = $this->postJson(self::START, []);
        $r->assertStatus(422);
        $this->assertSame(1, (int) $w->fresh()->credits);
        $this->assertStringContainsString('"credits":1', (string) $r->headers->get('X-AI-Quota'));

        $this->assertTrue(AiCreditTransaction::query()->where('kind', 'refund')->exists());
        $this->assertTrue(AiCreditTransaction::query()->where('kind', 'grant')->where('delta', 2)->exists());
    }

    public function test_ertesi_gun_ucretsiz_hak_yenilenir(): void
    {
        $this->login();
        $w = AiWallet::query()->firstOrFail();
        app(AiCredits::class)->reserve($w, 'test');
        $this->assertSame(0, $w->fresh()->freeLeft());

        $this->travel(1)->days();
        $this->assertSame(1, $w->fresh()->freeLeft());
    }

    public function test_engelli_kullanici_ve_cikis(): void
    {
        $this->login();
        AiWallet::query()->update(['blocked' => true]);
        $this->postJson(self::START, [])->assertStatus(401);

        AiWallet::query()->update(['blocked' => false]);
        $this->postJson(route('ai_account.logout'))->assertOk();
        $this->getJson(route('ai_account.status'))->assertJson(['logged_in' => false]);
    }

    public function test_admin_kota_disinda_ve_kapaliyken_kota_uygulanmaz(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->postJson(self::START, [])->assertStatus(422);

        auth()->logout();
        config(['worksitem_ai_credits.enabled' => false]);
        $this->postJson(self::START, [])->assertStatus(422);
    }

    public function test_ai_sayfalarina_giris_penceresi_eklenir_digerlerine_eklenmez(): void
    {
        $this->get('/ai-tasarim/ic-mekan')->assertOk()->assertSee('id="wsag-config"', false)->assertSee('ws-ai-gate.js', false);
        $this->get('/maliyet-hesapla')->assertOk()->assertSee('id="wsag-config"', false);
        $this->get('/hakkimizda')->assertOk()->assertDontSee('id="wsag-config"', false);
    }

    public function test_admin_ai_kredileri_listesi(): void
    {
        $this->login();
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin/ai-wallets')->assertOk()->assertSee('05327654321');
    }

    public function test_seramik_gorsellestirici_yeni_hak_sistemini_gosterir(): void
    {
        $this->get('/araclar/seramik-gorsellestirici')->assertOk()
            ->assertSee('AI hakkı:')
            ->assertSee('Günde 1 ücretsiz AI')
            ->assertDontSee('Render Kredisi:');
    }

    public function test_duvar_rengi_gorsellestirici_tum_renklerle_acilir(): void
    {
        $this->get('/araclar/duvar-rengi-gorsellestirici')->assertOk()
            ->assertSee("let currentBrand = 'Tüm';", false)
            ->assertSee('.catalog-actions[hidden]', false);
    }
}

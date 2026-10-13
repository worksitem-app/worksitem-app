<?php

namespace Tests\Feature;

use App\Models\AiWallet;
use App\Models\Lead;
use App\Services\AI\AiCredits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlannerExtrasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'worksitem_tools.tool_status.overrides.banyo-planlayici' => 'active',
            'worksitem_tools.tool_status.overrides.wc-planlayici' => 'active',
            'services.ai_spend_enabled' => true,
            'services.gemini.api_key' => 'test-key',
        ]);
    }

    private function body(string $quality): array
    {
        $png = base64_encode((string) file_get_contents(public_path('images/tile-visualizer/carrara-beyaz.png')));

        return ['mode' => 'plan', 'scene' => 'data:image/png;base64,'.$png, 'floor' => 'traverten-gri', 'wall' => 'carrara-beyaz', 'quality' => $quality];
    }

    private function fakeGemini(): void
    {
        $png = base64_encode((string) file_get_contents(public_path('images/tile-visualizer/carrara-beyaz.png')));
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['inlineData' => ['mimeType' => 'image/png', 'data' => $png]]]]]]])]);
    }

    private function wallet(int $credits): AiWallet
    {
        $w = AiWallet::query()->create(['phone' => '905551112233', 'credits' => $credits, 'verified_at' => now()]);
        $this->withSession([AiCredits::SESSION => ['id' => $w->id, 'until' => time() + 3600]]);

        return $w;
    }

    public function test_hd_gorsel_iki_kredi_ister_ucretsiz_hak_kullanilmaz(): void
    {
        $this->fakeGemini();
        $w = $this->wallet(1);

        $this->postJson('/araclar/banyo-planlayici/render', $this->body('hd'))->assertStatus(402)->assertJsonPath('ai_gate', 'credits');
        $this->assertSame(1, $w->fresh()->credits);
        $this->assertSame(0, (int) $w->fresh()->free_used);   // ücretsiz hak HD'ye harcanmadı
        Http::assertNothingSent();

        $w->update(['credits' => 3]);
        $this->postJson('/araclar/banyo-planlayici/render', $this->body('hd'))->assertOk()->assertJsonPath('hd', true)->assertJsonPath('preview_only', false);
        $this->assertSame(1, $w->fresh()->credits);
        Http::assertSent(fn ($r) => data_get($r->data(), 'generationConfig.imageConfig.imageSize') === '2K');
    }

    public function test_standart_gorsel_once_ucretsiz_onizleme_hd_basarisizsa_iki_kredi_iade(): void
    {
        $png = base64_encode((string) file_get_contents(public_path('images/tile-visualizer/carrara-beyaz.png')));
        Http::fake(['generativelanguage.googleapis.com/*' => Http::sequence()
            ->push(['candidates' => [['content' => ['parts' => [['inlineData' => ['mimeType' => 'image/png', 'data' => $png]]]]]]])
            ->push(['error' => 'x'], 500)]);
        $w = $this->wallet(0);
        $this->postJson('/araclar/banyo-planlayici/render', $this->body('std'))->assertOk()->assertJsonPath('preview_only', true)->assertJsonPath('hd', false);
        Http::assertSent(fn ($r) => data_get($r->data(), 'generationConfig.imageConfig') === null);

        $w->update(['credits' => 2]);
        $this->postJson('/araclar/banyo-planlayici/render', $this->body('hd'))->assertStatus(502);
        $this->assertSame(2, $w->fresh()->credits);   // başarısız HD: 2 kredi geri
    }

    public function test_wc_planlayici_kucuk_oda_dussuz_ve_wc_hesabina_baglanir(): void
    {
        $html = $this->get('/araclar/wc-planlayici')->assertOk()->assertSee('2D / 3D WC planlayıcı')->getContent();
        preg_match('#id="plCfg">(.*?)</script>#s', $html, $m);
        $c = json_decode($m[1], true);
        $this->assertSame(110, $c['initial']['w']);
        $this->assertSame(160, $c['initial']['l']);
        $this->assertNull($c['initial']['shower']);
        $this->assertSame('wc', $c['variant']);
        $this->assertStringContainsString('/araclar/wc-maliyet-hesabi', $c['back']);
        $this->assertStringContainsString('canonical" href="https://worksitem.com/araclar/wc-planlayici"', $html);

        $c2 = $this->get('/araclar/wc-planlayici?width=1.2&length=1.9');
        preg_match('#id="plCfg">(.*?)</script>#s', $c2->getContent(), $m2);
        $this->assertSame(120, json_decode($m2[1], true)['initial']['w']);
    }

    public function test_wc_hesabinda_planlayici_dugmesi_var_digerlerinde_yok(): void
    {
        config(['worksitem_tools.tool_status.overrides.wc-maliyet-hesabi' => 'active', 'worksitem_tools.tool_status.overrides.boya-hesaplayici' => 'active']);
        $html = $this->get('/araclar/wc-maliyet-hesabi?w=1.2&l=1.6')->assertOk()->getContent();
        $this->assertStringContainsString('id="planBtn" href="'.route('tools.wc_planner').'"', $html);
        $this->assertStringContainsString('"planner":{"map":{"w":"width"', $html);
        $this->assertStringNotContainsString('id="planBtn"', $this->get('/araclar/boya-hesaplayici')->assertOk()->getContent());
    }

    public function test_talep_kisa_numara_alir_ve_sms_te_kullanilir(): void
    {
        $lead = Lead::query()->create(['name' => 'Ali Veli', 'phone' => '05551112233']);
        $this->assertMatchesRegularExpression('/^WS-[ACDEFGHJKLMNPRTUVWXY3479]{6}$/', $lead->ref_code);
        $this->assertSame($lead->ref_code, $lead->displayNo());
        $this->assertNotSame($lead->ref_code, Lead::query()->create(['name' => 'B', 'phone' => '05551112234'])->ref_code);

        config(['worksitem_sms.lead_received.enabled' => true, 'services.netgsm.enabled' => true, 'services.netgsm.usercode' => 'u', 'services.netgsm.password' => 'p', 'services.netgsm.header' => 'H']);
        Http::fake(['*' => Http::response('00 123', 200)]);
        app(\App\Services\Sms\CustomerSms::class)->leadReceived($lead);
        Http::assertSent(fn ($r) => str_contains(urldecode($r->body().$r->url()), $lead->ref_code) || str_contains(json_encode($r->data(), JSON_UNESCAPED_UNICODE), $lead->ref_code));
    }

    public function test_kvkk_metninde_planlayici_fotografi_var(): void
    {
        $this->get('/legal/kvkk-aydinlatma')->assertOk()->assertSee('planlayıcıda gerçekçi görsel için yüklediğiniz banyo fotoğrafı', false);
    }
}

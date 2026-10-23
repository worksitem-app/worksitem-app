<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Services\Sms\CustomerSms;
use App\Services\Sms\NetgsmSms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v106: QA raporu sonrası sertleştirmeler (SMS tavanları, CSP, Türkçe doğrulama, oturum çerezi) */
class QaHardeningV106Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem']);
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
    }

    private function lead(string $phone): Lead
    {
        return Lead::query()->create(['name' => 'Ali Kaya', 'phone' => $phone, 'contact_consent' => true, 'status' => 'yeni']);
    }

    public function test_ayni_numaraya_gunde_en_fazla_iki_talep_sms_i_gider(): void
    {
        $sms = app(CustomerSms::class);
        $this->assertTrue($sms->leadReceived($this->lead('05321112233')));
        $this->assertTrue($sms->leadReceived($this->lead('05321112233')));
        $this->assertFalse($sms->leadReceived($this->lead('05321112233')));
        Http::assertSentCount(2);
    }

    public function test_ayni_ipden_ve_site_genelinde_gunluk_tavan(): void
    {
        config(['worksitem_sms.lead_received.max_per_ip_day' => 3]);
        $sms = app(CustomerSms::class);
        foreach (['05320000001', '05320000002', '05320000003', '05320000004'] as $p) {
            $sms->leadReceived($this->lead($p));
        }
        Http::assertSentCount(3);

        config(['worksitem_sms.lead_received.max_per_ip_day' => 100, 'worksitem_sms.lead_received.global_day' => 4]);
        $this->assertTrue($sms->leadReceived($this->lead('05320000005')));
        $this->assertFalse($sms->leadReceived($this->lead('05320000006')));
    }

    public function test_tum_smsler_icin_gunluk_son_tavan(): void
    {
        config(['worksitem_sms.hard_cap_day' => 2]);
        $sms = app(NetgsmSms::class);
        $this->assertTrue($sms->send('05321112233', 'a')['ok']);
        $this->assertTrue($sms->send('05321112234', 'b')['ok']);
        $r = $sms->send('05321112235', 'c');
        $this->assertFalse($r['ok']);
        $this->assertSame('Günlük SMS tavanı doldu', $r['message']);
    }

    public function test_csp_varsayilan_rapor_modunda_admin_haric(): void
    {
        $res = $this->get('/legal/kvkk-aydinlatma');
        $this->assertStringContainsString("object-src 'none'", (string) $res->headers->get('Content-Security-Policy-Report-Only'));
        $this->assertNull($res->headers->get('Content-Security-Policy'));

        config(['app.csp_mode' => 'enforce']);
        $this->assertNotNull($this->get('/legal/kvkk-aydinlatma')->headers->get('Content-Security-Policy'));

        config(['app.csp_mode' => 'off']);
        $this->assertNull($this->get('/legal/kvkk-aydinlatma')->headers->get('Content-Security-Policy-Report-Only'));
    }

    public function test_dogrulama_mesajlari_turkce(): void
    {
        $this->postJson('/lead-kaydet', [
            'name' => 'Ayşe Demir', 'phone' => '05327654321', 'contact_consent' => true, 'estimated_cost' => -5,
        ])->assertStatus(422)->assertJsonPath('errors.estimated_cost.0', 'tahmini tutar en az 0 olmalıdır.');
    }

    public function test_tool_event_metadata_sinirli(): void
    {
        $this->postJson('/api/tool-events', [
            'tool_slug' => 'boya-hesaplayici', 'event_type' => 'sponsor_click',
            'metadata' => array_fill(0, 25, 'x'),
        ])->assertStatus(422);
    }
}

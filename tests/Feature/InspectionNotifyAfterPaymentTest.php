<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v109: kartla keşif siparişinde SMS ve e-postalar ödeme onaylandıktan sonra gider */
class InspectionNotifyAfterPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'mail.default' => 'array',
            'worksitem_leads.notification_email' => 'ekip@example.com',
            'services.iyzico.api_key' => 'sandbox-test-key',
            'services.iyzico.secret_key' => 'sandbox-test-secret',
            'services.iyzico.base_url' => 'https://sandbox-api.iyzipay.com',
            'worksitem_inspection.iyzico_enabled' => true,
            'services.netgsm.usercode' => '8501234567',
            'services.netgsm.password' => 'x',
            'services.netgsm.header' => 'KAINTA',
            'services.netgsm.team_phone' => '05329998877',
        ]);
    }

    /** @return list<string> */
    private function mails(): array
    {
        return collect(app('mailer')->getSymfonyTransport()->messages())
            ->map(fn ($m) => implode(',', array_map(fn ($a) => $a->getAddress(), $m->getOriginalMessage()->getTo())).' | '.$m->getOriginalMessage()->getSubject())
            ->values()->all();
    }

    private function lead(): Lead
    {
        $this->post('/lead-kaydet', ['name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'city' => 'İstanbul', 'contact_consent' => '1']);

        return Lead::query()->latest('id')->firstOrFail();
    }

    private function kesifSms(): int
    {
        return Http::recorded(fn (HttpRequest $r) => str_contains($r->url(), 'netgsm') && (str_contains((string) ($r['messages'][0]['msg'] ?? ''), 'eşif') || str_contains((string) ($r['messages'][0]['msg'] ?? ''), 'KEŞİF')))->count();
    }

    public function test_kart_odemesi_tamamlanmadan_bildirim_gitmez_odeyince_gider(): void
    {
        Http::fake([
            '*/checkoutform/initialize/*' => Http::response(['status' => 'success', 'token' => 'tok-1', 'paymentPageUrl' => 'https://sandbox-cpp.iyzipay.com?token=tok-1']),
            '*/checkoutform/auth/ecom/detail' => fn () => Http::response([
                'status' => 'success', 'paymentStatus' => 'SUCCESS', 'paidPrice' => 3000.0,
                'basketId' => Lead::query()->latest('id')->value('public_id'), 'paymentId' => '77',
            ]),
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
        ]);
        $this->lead();
        $before = count($this->mails());

        $this->post(route('kesif.accept'), ['odeme' => 'kart', 'fatura' => 'bireysel', 'kesif_onay' => '1', 'email' => 'ayse@example.com'])
            ->assertRedirect('https://sandbox-cpp.iyzipay.com?token=tok-1');
        $this->assertSame($before, count($this->mails()), 'Ödeme sayfasına giderken e-posta gitmemeli');
        $this->assertSame(0, $this->kesifSms(), 'Ödeme sayfasına giderken keşif SMS\'i gitmemeli');

        $this->flushSession();
        $this->post(route('kesif.iyzico.callback'), ['token' => 'tok-1']);

        $after = array_slice($this->mails(), $before);
        $this->assertCount(2, $after);
        $this->assertTrue(collect($after)->contains(fn ($m) => str_starts_with($m, 'ekip@example.com | WORKSITEM | KEŞİF ÖDENDİ')));
        $this->assertTrue(collect($after)->contains(fn ($m) => str_starts_with($m, 'ayse@example.com | Ödemeniz alındı')));
        $this->assertSame(2, $this->kesifSms());

        // Aynı token ikinci kez gelirse bildirim tekrarlanmaz
        $this->post(route('kesif.iyzico.callback'), ['token' => 'tok-1']);
        $this->assertCount(2, array_slice($this->mails(), $before));
    }

    public function test_odeme_yarida_kalirsa_musteriye_hic_bildirim_gitmez(): void
    {
        Http::fake([
            '*/checkoutform/initialize/*' => Http::response(['status' => 'success', 'token' => 'tok-2', 'paymentPageUrl' => 'https://x.test']),
            '*/checkoutform/auth/ecom/detail' => Http::response(['status' => 'success', 'paymentStatus' => 'FAILURE']),
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
        ]);
        $this->lead();
        $before = count($this->mails());
        $this->post(route('kesif.accept'), ['odeme' => 'kart', 'fatura' => 'bireysel', 'kesif_onay' => '1', 'email' => 'ayse@example.com']);
        $this->flushSession();
        $this->post(route('kesif.iyzico.callback'), ['token' => 'tok-2']);

        $this->assertSame($before, count($this->mails()));
        $this->assertSame(0, $this->kesifSms());
    }

    public function test_iyzico_acilamazsa_yalniz_ekibe_haber_verilir(): void
    {
        Http::fake([
            '*/checkoutform/initialize/*' => Http::response(['status' => 'failure', 'errorMessage' => 'hata']),
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
        ]);
        $this->lead();
        $before = count($this->mails());
        $this->post(route('kesif.accept'), ['odeme' => 'kart', 'fatura' => 'bireysel', 'kesif_onay' => '1', 'email' => 'ayse@example.com'])
            ->assertRedirect(route('kesif.payment'));

        $after = array_slice($this->mails(), $before);
        $this->assertCount(1, $after);
        $this->assertStringContainsString('KEŞİF ÖDEMESİ AÇILAMADI', $after[0]);
        $this->assertSame(0, $this->kesifSms());
    }

    public function test_havalede_siparis_aninda_iban_sms_ve_e_postalar_gider(): void
    {
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
        $this->lead();
        $before = count($this->mails());
        $this->post(route('kesif.accept'), ['odeme' => 'havale', 'fatura' => 'bireysel', 'kesif_onay' => '1', 'email' => 'ayse@example.com'])
            ->assertRedirect(route('kesif.payment'));

        $this->assertCount(2, array_slice($this->mails(), $before));
        $this->assertSame(1, Http::recorded(fn (HttpRequest $r) => str_contains($r->url(), 'netgsm') && ($r['messages'][0]['no'] ?? '') === '5327654321' && str_contains($r['messages'][0]['msg'], 'Keşif siparişiniz alındı'))->count());
    }
}

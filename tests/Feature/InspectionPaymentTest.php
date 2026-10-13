<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Services\Sms\NetgsmSms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InspectionPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config([
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

    private function order(): Lead
    {
        $this->post('/lead-kaydet', ['name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'city' => 'İstanbul', 'contact_consent' => '1']);

        return Lead::query()->latest('id')->firstOrFail();
    }

    public function test_kart_siparisi_iyzicoya_imzali_istekle_gider_ve_odeme_dogrulanir(): void
    {
        Http::fake([
            'sandbox-api.iyzipay.com/payment/iyzipos/checkoutform/initialize/*' => Http::response(['status' => 'success', 'token' => 'tok-123', 'paymentPageUrl' => 'https://sandbox-cpp.iyzipay.com?token=tok-123']),
            'sandbox-api.iyzipay.com/payment/iyzipos/checkoutform/auth/ecom/detail' => fn (HttpRequest $r) => Http::response([
                'status' => 'success', 'paymentStatus' => 'SUCCESS', 'paidPrice' => 3000.0, 'price' => 3000.0,
                'basketId' => Lead::query()->latest('id')->value('public_id'), 'paymentId' => '999',
            ]),
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
        ]);

        $lead = $this->order();
        $this->post(route('kesif.accept'), ['odeme' => 'kart', 'fatura' => 'bireysel', 'kesif_onay' => '1', 'email' => 'ayse@example.com'])
            ->assertRedirect('https://sandbox-cpp.iyzipay.com?token=tok-123');

        Http::assertSent(function (HttpRequest $r) use ($lead) {
            if (! str_contains($r->url(), 'checkoutform/initialize')) {
                return false;
            }
            $b = json_decode($r->body(), true);
            $auth = base64_decode(substr($r->header('Authorization')[0], 8));

            return str_starts_with($r->header('Authorization')[0], 'IYZWSv2 ')
                && str_contains($auth, 'apiKey:sandbox-test-key&randomKey:')
                && $b['price'] === '3000.0' && $b['paidPrice'] === '3000.0'
                && $b['basketId'] === $lead->public_id
                && $b['buyer']['email'] === 'ayse@example.com'
                && $b['buyer']['gsmNumber'] === '+905327654321'
                && $b['basketItems'][0]['itemType'] === 'VIRTUAL';
        });
        // Sipariş SMS'i müşteriye ve ekibe
        Http::assertSent(fn (HttpRequest $r) => str_contains($r->url(), 'netgsm') && $r['messages'][0]['no'] === '5327654321');
        Http::assertSent(fn (HttpRequest $r) => str_contains($r->url(), 'netgsm') && $r['messages'][0]['no'] === '5329998877');

        // iyzico dönüşü: oturumsuz, CSRF'siz POST
        $this->flushSession();
        $res = $this->post(route('kesif.iyzico.callback'), ['token' => 'tok-123']);
        $res->assertRedirect();
        $this->assertStringContainsString('/kesif/odeme/sonuc/'.$lead->public_id, $res->headers->get('Location'));

        $lead->refresh();
        $this->assertSame('odendi', $lead->inspection_status);
        $this->assertNotNull($lead->inspection_paid_at);
        $this->assertSame('999', $lead->inspection_data['iyzico']['payment_id']);

        $this->get($res->headers->get('Location'))->assertRedirect(route('kesif.payment'));
        $this->get(route('kesif.payment'))->assertOk()->assertSee('Keşif ödemeniz alındı')->assertSee("wsTrack('purchase'", false);
    }

    public function test_tutar_tutmazsa_odendi_sayilmaz(): void
    {
        Http::fake([
            '*/checkoutform/initialize/*' => Http::response(['status' => 'success', 'token' => 'tok-9', 'paymentPageUrl' => 'https://x.test']),
            '*/checkoutform/auth/ecom/detail' => Http::response(['status' => 'success', 'paymentStatus' => 'SUCCESS', 'paidPrice' => 1.0, 'basketId' => 'baska']),
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
        ]);
        $lead = $this->order();
        $this->post(route('kesif.accept'), ['odeme' => 'kart', 'fatura' => 'bireysel', 'kesif_onay' => '1', 'email' => 'a@b.co']);
        $this->post(route('kesif.iyzico.callback'), ['token' => 'tok-9']);

        $this->assertSame('kabul', $lead->fresh()->inspection_status);
    }

    public function test_iyzico_hatasinda_whatsapp_yedegi_ve_hata_mesaji(): void
    {
        Http::fake([
            '*/checkoutform/initialize/*' => Http::response(['status' => 'failure', 'errorCode' => '1001', 'errorMessage' => 'api bilgileri bulunamadı']),
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
        ]);
        $this->order();
        $this->post(route('kesif.accept'), ['odeme' => 'kart', 'fatura' => 'bireysel', 'kesif_onay' => '1', 'email' => 'a@b.co'])
            ->assertRedirect(route('kesif.payment'));
        $this->get(route('kesif.payment'))->assertSee('api bilgileri bulunamadı')->assertSee('Kartla güvenli öde');
    }

    public function test_imzasiz_sonuc_adresi_reddedilir(): void
    {
        $this->get('/kesif/odeme/sonuc/01ABC?ok=1')->assertForbidden();
    }

    public function test_netgsm_numara_bicimi_ve_hata_kodu(): void
    {
        $this->assertSame('5327654321', NetgsmSms::normalize('0532 765 43 21'));
        $this->assertSame('5327654321', NetgsmSms::normalize('+90 532 765 43 21'));
        $this->assertNull(NetgsmSms::normalize('0216 123 45 67'));

        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '30'], 406)]);
        $r = app(NetgsmSms::class)->send('05327654321', 'deneme');
        $this->assertFalse($r['ok']);
        $this->assertStringContainsString('API erişimi', $r['message']);
    }

    public function test_netgsm_rest_reddederse_eski_apiyle_gonderilir(): void
    {
        Http::fake([
            'api.netgsm.com.tr/sms/rest/v2/send' => Http::response(['code' => '30'], 406),
            'api.netgsm.com.tr/sms/send/get' => Http::response('00 123456789'),
        ]);
        $r = app(NetgsmSms::class)->send('05327654321', 'deneme');
        $this->assertTrue($r['ok']);
        $this->assertSame('123456789', $r['jobid']);
        Http::assertSent(fn (HttpRequest $q) => str_contains($q->url(), 'send/get') && $q['gsmno'] === '905327654321' && $q['msgheader'] === 'KAINTA');
    }

    public function test_entegrasyon_komutu_sifre_yazmaz(): void
    {
        $this->artisan('app:integrations')
            ->expectsOutputToContain('dolu (16 karakter)')
            ->doesntExpectOutputToContain('sandbox-test-key')
            ->assertSuccessful();
    }
}

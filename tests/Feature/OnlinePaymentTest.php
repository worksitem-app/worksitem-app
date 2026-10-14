<?php

namespace Tests\Feature;

use App\Models\AiWallet;
use App\Models\Order;
use App\Services\AI\AiCredits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OnlinePaymentTest extends TestCase
{
    use RefreshDatabase;

    private const IYZ = 'https://sandbox-api.iyzipay.com';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'worksitem_payments.online_enabled' => true,
            'services.iyzico.api_key' => 'k', 'services.iyzico.secret_key' => 's', 'services.iyzico.base_url' => self::IYZ,
            'services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem',
        ]);
    }

    private function fakeIyzico(string $token, ?callable $detail = null): void
    {
        Http::fake([
            self::IYZ.'/payment/iyzipos/checkoutform/initialize/auth/ecom' => Http::response(['status' => 'success', 'token' => $token, 'paymentPageUrl' => 'https://sandbox-cpp.iyzipay.com/?token='.$token]),
            self::IYZ.'/payment/iyzipos/checkoutform/auth/ecom/detail' => fn () => Http::response($detail ? $detail() : []),
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
        ]);
    }

    private function paidDetail(Order $o): array
    {
        return ['status' => 'success', 'paymentStatus' => 'SUCCESS', 'basketId' => $o->public_id, 'paidPrice' => $o->amount, 'paymentId' => 'P1'];
    }

    public function test_kapaliyken_kredi_satin_alma_409(): void
    {
        config(['worksitem_payments.online_enabled' => false]);
        $this->postJson(route('odeme.kredi'), ['package' => 0])->assertStatus(409);
    }

    public function test_girissiz_kredi_401_girisli_iyzico_adresi_ve_kredi_bir_kez_yuklenir(): void
    {
        $this->fakeIyzico('TOK1', fn () => $this->paidDetail(Order::query()->firstOrFail()));
        $this->postJson(route('odeme.kredi'), ['package' => 1])->assertStatus(401);

        $w = AiWallet::query()->create(['phone' => '5327654321', 'name' => 'Ayşe Demir', 'verified_at' => now()]);
        $this->withSession([AiCredits::SESSION => ['id' => $w->id, 'until' => now()->addDay()->timestamp]])
            ->postJson(route('odeme.kredi'), ['package' => 1])
            ->assertOk()->assertJson(['ok' => true, 'url' => 'https://sandbox-cpp.iyzipay.com/?token=TOK1']);

        $o = Order::query()->firstOrFail();
        $this->assertSame('credits', $o->kind);
        $this->assertSame(30, (int) $o->credits);
        $this->assertEqualsWithDelta(249, $o->amount, 0.01);

        $r = $this->post(route('odeme.iyzico.callback'), ['token' => 'TOK1']);
        $r->assertRedirect();
        $this->post(route('odeme.iyzico.callback'), ['token' => 'TOK1'])->assertRedirect();   // tekrar: ikinci yükleme yok

        $this->assertSame('paid', $o->fresh()->status);
        $this->assertSame(30, (int) $w->fresh()->credits);
        $this->get($r->headers->get('Location'))->assertOk()->assertSee('Ödemeniz alındı');
    }

    public function test_tutar_tutmazsa_kredi_yuklenmez(): void
    {
        $this->fakeIyzico('TOK2', fn () => ['paidPrice' => 1] + $this->paidDetail(Order::query()->firstOrFail()));
        $w = AiWallet::query()->create(['phone' => '5327654321', 'verified_at' => now()]);
        $this->withSession([AiCredits::SESSION => ['id' => $w->id, 'until' => now()->addDay()->timestamp]])
            ->postJson(route('odeme.kredi'), ['package' => 0])->assertOk();
        $this->post(route('odeme.iyzico.callback'), ['token' => 'TOK2'])->assertRedirect();

        $this->assertSame('failed', Order::query()->firstOrFail()->status);
        $this->assertSame(0, (int) $w->fresh()->credits);
    }

    public function test_ucretli_pro_rapor_once_odeme_sonra_pdf(): void
    {
        Mail::fake();
        config(['worksitem_pro_report.mode' => 'paid', 'worksitem_pro_report.price' => 149]);
        $this->fakeIyzico('TOK3', fn () => $this->paidDetail(Order::query()->firstOrFail()));
        $slug = 'boya-hesaplayici';
        $q = ['target' => 'daire', 'floor' => '100', 'surfaces' => 'duvar_tavan', 'color' => 'ayni', 'state' => 'iyi', 'quality' => 'orta'];

        $this->get('/araclar/'.$slug.'/pro-rapor?'.http_build_query($q))->assertOk()->assertSee('178,80 TL');
        $this->postJson('/araclar/'.$slug.'/pro-rapor/kod', ['phone' => '05327654321'])->assertOk();
        $sms = collect(Http::recorded())->last(fn ($p) => str_contains($p[0]->url(), 'netgsm'));
        preg_match('/(\d{6})/', $sms[0]['messages'][0]['msg'], $m);

        $this->post('/araclar/'.$slug.'/pro-rapor', $q + ['name' => 'Ayşe Demir', 'phone' => '05327654321', 'code' => $m[1], 'kvkk' => '1'])
            ->assertRedirect('https://sandbox-cpp.iyzipay.com/?token=TOK3');
        $o = Order::query()->firstOrFail();
        $this->assertSame('pro_report', $o->kind);
        $this->assertEqualsWithDelta(178.8, $o->amount, 0.01);

        $back = $this->post(route('odeme.iyzico.callback'), ['token' => 'TOK3']);
        $this->get($back->headers->get('Location'))->assertRedirect($o->return_url);

        $pdf = $this->post('/araclar/'.$slug.'/pro-rapor', $q);
        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $pdf->headers->get('Content-Type'));
    }
}

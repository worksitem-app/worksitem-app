<?php

namespace Tests\Feature;

use App\Models\Firm;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class FirmMembershipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config(['services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem']);
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
    }

    private function code(string $phone = '05327654321'): string
    {
        $this->postJson(route('firms.code'), ['phone' => $phone])->assertOk()->assertJson(['ok' => true]);
        preg_match('/(\d{6})/', Http::recorded()->last()[0]['messages'][0]['msg'], $m);

        return $m[1];
    }

    private function form(array $over = []): array
    {
        return $over + [
            'name' => 'Demir Yapı Tadilat', 'type' => 'tadilat', 'services' => ['banyo', 'seramik'], 'cities' => ['İstanbul', 'Kocaeli'],
            'district' => 'Kadıköy', 'contact_name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'email' => 'info@example.com',
            'tax_no' => '1234567890', 'description' => 'Banyo ve seramik işleri.', 'terms' => '1', 'kvkk' => '1',
        ];
    }

    public function test_kayit_sayfasi_ve_aractan_gelen_hizmet_secili(): void
    {
        $this->get('/firma-uyeligi?arac=banyo-maliyet-hesaplayici')->assertOk()
            ->assertSee('Ücretsiz kaydolun')->assertSee('value="banyo" checked', false)->assertSee('Firma Üyelik Koşulları');
        $this->get('/legal/firma-uyelik-kosullari')->assertOk()->assertSee('Öne çıkan üyelik');
    }

    public function test_sms_dogrulamali_kayit_onay_bekler_rehberde_gorunmez(): void
    {
        $this->post(route('firms.register'), $this->form(['code' => '000000']))->assertSessionHasErrors('code');
        $this->post(route('firms.register'), $this->form(['code' => $this->code(), 'terms' => null]))->assertSessionHasErrors('terms');

        $this->post(route('firms.register'), $this->form(['code' => $this->code()]))->assertRedirect(route('firms.page'))->assertSessionHas('firm_ok');
        $f = Firm::query()->firstOrFail();
        $this->assertSame('pending', $f->status);
        $this->assertSame('5327654321', $f->phone);
        $this->assertSame('demir-yapi-tadilat', $f->slug);

        // aynı numarayla ikinci başvuru açılmaz
        $this->post(route('firms.register'), $this->form(['code' => $this->code()]))->assertSessionHas('firm_ok', 'exists');
        $this->assertSame(1, Firm::query()->count());

        config(['worksitem_firms.directory_enabled' => true]);
        $this->get('/firmalar')->assertOk()->assertDontSee('Demir Yapı Tadilat');
        $this->get('/firmalar/demir-yapi-tadilat')->assertNotFound();
    }

    public function test_rehber_kapaliyken_404_acikken_onayli_firma_listelenir_numara_gizli(): void
    {
        $f = Firm::query()->create($this->form(['phone' => '5327654321', 'status' => 'approved', 'approved_at' => now()]));
        $this->get('/firmalar')->assertNotFound();
        $this->get('/firmalar/'.$f->slug)->assertNotFound();
        $this->assertStringNotContainsString('/firmalar/', $this->get('/sitemap.xml')->getContent());

        config(['worksitem_firms.directory_enabled' => true]);
        $this->get('/firmalar?hizmet=banyo&il=İstanbul')->assertOk()->assertSee('Demir Yapı Tadilat')->assertSee('İstanbul Banyo tadilatı Firmaları');
        $this->get('/firmalar?il=Ankara')->assertOk()->assertDontSee('Demir Yapı Tadilat');
        $this->get('/firmalar/'.$f->slug)->assertOk()->assertSee('Demir Yapı Tadilat')->assertSee('0532 *** ** 21')
            ->assertDontSee('5327654321')->assertDontSee('1234567890')->assertSee('HomeAndConstructionBusiness');
        $this->get(route('firms.contact', [$f, 'whatsapp']))->assertRedirect('https://wa.me/905327654321?text='.rawurlencode('Merhaba, Worksitem firma rehberinden ulaşıyorum.'));
        $this->assertSame(1, (int) $f->fresh()->views);
        $this->assertSame(1, (int) $f->fresh()->contact_clicks);
        $this->assertStringContainsString('/firmalar/'.$f->slug, $this->get('/sitemap.xml')->getContent());
    }

    public function test_one_cikan_uyelik_odemesi_bir_yil_pro_yapar(): void
    {
        $base = 'https://sandbox-api.iyzipay.com';
        config(['worksitem_payments.online_enabled' => true, 'services.iyzico.api_key' => 'k', 'services.iyzico.secret_key' => 's', 'services.iyzico.base_url' => $base]);
        Http::fake([
            $base.'/payment/iyzipos/checkoutform/initialize/auth/ecom' => Http::response(['status' => 'success', 'token' => 'FT', 'paymentPageUrl' => 'https://pay.test/FT']),
            $base.'/payment/iyzipos/checkoutform/auth/ecom/detail' => fn () => Http::response(['status' => 'success', 'paymentStatus' => 'SUCCESS',
                'basketId' => Order::query()->first()->public_id, 'paidPrice' => Order::query()->first()->amount, 'paymentId' => 'P']),
        ]);
        $f = Firm::query()->create($this->form(['phone' => '5327654321', 'status' => 'approved']));

        $this->get(route('firms.pro', ['firm' => $f->slug]))->assertForbidden();   // imzasız bağlantı
        $this->get(URL::temporarySignedRoute('firms.pro', now()->addDay(), ['firm' => $f->slug]))->assertRedirect('https://pay.test/FT');
        $o = Order::query()->firstOrFail();
        $this->assertSame('firm_pro', $o->kind);
        $this->assertEqualsWithDelta(2880, $o->amount, 0.01);

        $this->post(route('odeme.iyzico.callback'), ['token' => 'FT'])->assertRedirect();
        $f->refresh();
        $this->assertTrue($f->isPro());
        $this->assertTrue($f->plan_until->isAfter(now()->addDays(360)));
    }

    public function test_arac_sayfasinda_firma_uyeligi_baglantisi(): void
    {
        $this->get('/araclar/boya-hesaplayici')->assertOk()->assertSee('Ücretsiz üye olun')->assertSee('/firma-uyeligi?arac=boya-hesaplayici', false);
    }

    public function test_admin_onaylar_ve_sms_gider(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $f = Firm::query()->create($this->form(['phone' => '5327654321']));
        $o = Order::query()->create(['kind' => 'credits', 'title' => 'x', 'amount' => 1]);
        $this->actingAs($admin)->get('/admin/firms')->assertOk()->assertSee('Demir Yapı Tadilat');
        $this->actingAs($admin)->get('/admin/firms/'.$f->slug.'/edit')->assertOk();
        $this->actingAs($admin)->get('/admin/orders')->assertOk()->assertSee($o->public_id);

        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Resources\FirmResource\Pages\ListFirms::class)
            ->callTableAction('approve', $f);
        $this->assertSame('approved', $f->fresh()->status);
        $this->assertStringContainsString('onaylandı', Http::recorded()->last()[0]['messages'][0]['msg']);
    }
}

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
        \Illuminate\Support\Facades\Cache::flush();   // SMS gönderim sınırı (testte çok kod isteniyor)
        $this->postJson(route('firms.code'), ['phone' => $phone])->assertOk()->assertJson(['ok' => true]);
        preg_match('/(\d{6})/', Http::recorded()->last()[0]['messages'][0]['msg'], $m);

        return $m[1];
    }

    /** Kayıt formu (POST) */
    private function form(array $over = []): array
    {
        return $over + [
            'name' => 'Demir Yapı Tadilat', 'type' => 'tadilat', 'legal_type' => 'sirket', 'legal_name' => 'Demir Yapı Tadilat İnşaat Ltd. Şti.',
            'tax_no' => '1234567890', 'tax_office' => 'Kadıköy', 'efatura' => 'unknown', 'invoice_city' => 'İstanbul', 'invoice_district' => 'Kadıköy',
            'invoice_address' => 'Caferağa Mah. Moda Cad. No: 10', 'tax_doc' => \Illuminate\Http\UploadedFile::fake()->create('levha.pdf', 100, 'application/pdf'),
            'trades' => ['ince-insaat', 'zemin-kaplama'], 'subservices' => ['Banyo Tadilatı ve Dekorasyonu', 'Parke Döşeme'],
            'cities' => ['İstanbul', 'Kocaeli'], 'districts' => 'Kadıköy, Ataşehir',
            'contact_name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'email' => 'info@example.com',
            'description' => 'Banyo ve seramik işleri.', 'ref' => 'SAHA01', 'terms' => '1', 'declare' => '1', 'kvkk' => '1',
        ];
    }

    /** Doğrudan veritabanı kaydı */
    private function firm(array $over = []): Firm
    {
        return Firm::query()->create($over + [
            'name' => 'Demir Yapı Tadilat', 'type' => 'tadilat', 'legal_type' => 'sirket', 'legal_name' => 'Demir Yapı Tadilat İnşaat Ltd. Şti.',
            'tax_no' => '1234567890', 'tax_office' => 'Kadıköy', 'efatura' => 'yes', 'invoice_city' => 'İstanbul', 'invoice_district' => 'Kadıköy',
            'invoice_address' => 'Caferağa Mah. Moda Cad. No: 10', 'trades' => ['ince-insaat'], 'cities' => ['İstanbul'],
            'contact_name' => 'Ayşe Demir', 'phone' => '5327654321', 'email' => 'info@example.com', 'description' => 'Banyo ve seramik işleri.',
        ]);
    }

    public function test_kayit_sayfasi_ve_aractan_gelen_hizmet_secili(): void
    {
        $this->get('/firma-uyeligi?arac=banyo-maliyet-hesaplayici')->assertOk()
            ->assertSee('data-ab="firm_signup_cta:', false)->assertSee('value="ince-insaat" checked', false)->assertSee('Firma Üyelik ve Aracılık Hizmet Sözleşmesi')
            ->assertSee('Banyo Tadilatı ve Dekorasyonu')->assertSee('Vergi levhası');
        $this->get('/firma-uyeligi?ref=AHMET')->assertSee('value="AHMET"', false);
        $this->get('/legal/firma-uyelik-kosullari')->assertOk()->assertSee('Eşleşme bedeli')->assertSee('ari tutmayı')->assertSee('Kurucu firma programı')
            ->assertSee('künyede yer alan hizmet sağlayıcı')->assertSee('platformu devralan');
        $this->get('/firma-uyeligi')->assertSee('komisyon yok');
        $this->get('/legal/teklif-alma-kosullari')->assertOk()->assertSee('yalnızca o Firma ile paylaşılır');
    }

    public function test_sms_dogrulamali_kayit_onay_bekler_rehberde_gorunmez(): void
    {
        $this->post(route('firms.register'), $this->form(['code' => '000000']))->assertSessionHasErrors('code');
        $this->post(route('firms.register'), $this->form(['code' => $this->code(), 'terms' => null]))->assertSessionHasErrors('terms');

        $this->post(route('firms.register'), $this->form(['code' => $this->code(), 'tax_no' => '1234567891']))->assertSessionHasErrors('tax_no');
        $this->post(route('firms.register'), $this->form(['code' => $this->code(), 'tax_no' => '10000000146']))->assertSessionHasErrors('tax_no'); // şirket için TCKN olmaz
        $this->post(route('firms.register'), $this->form(['code' => $this->code(), 'trades' => ['ince-insaat'], 'subservices' => ['Banyo Tadilatı ve Dekorasyonu', 'Parke Döşeme']]))
            ->assertRedirect(route('market.panel.info'))->assertSessionHas('firm_new');
        $f = Firm::query()->firstOrFail();
        $this->assertSame('pending', $f->status);
        $this->assertSame(['Banyo Tadilatı ve Dekorasyonu'], $f->subservices);   // seçilmeyen iş kolunun alt hizmeti alınmaz
        $this->assertSame(['Kadıköy', 'Ataşehir'], $f->districts);
        $this->assertSame('SAHA01', $f->referrer);

        // Kayıt sonrası kısa sorular (isteğe bağlı): iş koluna göre fiyat soruları, cevaplar müşteriye gösterilmez
        $this->get(route('market.panel.info'))->assertOk()->assertSee('Başvurunuz alındı')->assertSee('En küçük kaç TL')
            ->assertSee('Alçıpan bölme duvar')->assertDontSee('50 kg çimento');
        $this->post(route('market.panel.info.save'), ['a' => ['min_job' => 'abc']])->assertSessionHasErrors();
        $this->post(route('market.panel.info.save'), ['a' => ['min_job' => '20000', 'team' => '2-5', 'p_alcipan_bolme' => '1450', 'p_banyo' => '']])
            ->assertRedirect(route('firms.page'))->assertSessionHas('firm_ok');
        $f->refresh();
        $this->assertSame(20000, $f->min_job);
        $this->assertSame(['min_job' => 20000, 'team' => '2-5', 'p_alcipan_bolme' => 1450], $f->answers);
        $this->get(route('market.panel.info'))->assertRedirect(route('market.panel.login'));   // oturum tek seferlik
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists($f->tax_doc_path));
        \Illuminate\Support\Facades\Storage::disk('local')->delete($f->tax_doc_path);
        $this->assertSame('5327654321', $f->phone);
        $this->assertSame('demir-yapi-tadilat', $f->slug);

        // aynı numara veya vergi no ile ikinci başvuru açılmaz
        $this->post(route('firms.register'), $this->form(['code' => $this->code()]))->assertSessionHas('firm_ok', 'exists');
        $this->post(route('firms.register'), $this->form(['code' => $this->code('05321112233'), 'phone' => '05321112233']))->assertSessionHas('firm_ok', 'exists');
        $this->assertSame(1, Firm::query()->count());

        config(['worksitem_firms.directory_enabled' => true]);
        $this->get('/firmalar')->assertOk()->assertDontSee('Demir Yapı Tadilat');
        $this->get('/firmalar/demir-yapi-tadilat')->assertNotFound();
    }

    public function test_rehber_kapaliyken_404_acikken_onayli_firma_listelenir_numara_gizli(): void
    {
        $f = $this->firm(['status' => 'approved', 'approved_at' => now()]);
        $this->get('/firmalar')->assertNotFound();
        $this->get('/firmalar/'.$f->slug)->assertNotFound();
        $this->assertStringNotContainsString('/firmalar/', $this->get('/sitemap.xml')->getContent());

        config(['worksitem_firms.directory_enabled' => true]);
        $this->get('/firmalar?hizmet=ince-insaat&il=İstanbul')->assertOk()->assertSee('Demir Yapı Tadilat')->assertSee('İstanbul İnce İnşaat İşleri Firmaları');
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
        $f = $this->firm(['status' => 'approved']);

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
        $f = $this->firm(['efatura' => 'unknown']);
        $o = Order::query()->create(['kind' => 'credits', 'title' => 'x', 'amount' => 1]);
        $this->actingAs($admin)->get('/admin/firms')->assertOk()->assertSee('Demir Yapı Tadilat');
        $this->actingAs($admin)->get('/admin/firms/'.$f->slug.'/edit')->assertOk();
        $this->actingAs($admin)->get('/admin/orders')->assertOk()->assertSee($o->public_id);

        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Resources\FirmResource\Pages\ListFirms::class)
            ->callTableAction('approve', $f, ['efatura' => 'no', 'checked' => true])->assertHasNoTableActionErrors();
        $this->assertSame('approved', $f->fresh()->status);
        $this->assertSame('no', $f->fresh()->efatura);
        $this->assertNotNull($f->fresh()->tax_verified_at);
        $this->assertStringContainsString('onaylandı', Http::recorded()->last()[0]['messages'][0]['msg']);
    }
}

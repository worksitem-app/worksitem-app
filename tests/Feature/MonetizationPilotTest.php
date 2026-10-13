<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MonetizationPilotTest extends TestCase
{
    use RefreshDatabase;

    private const CALC = ['width' => 2, 'length' => 2.5, 'bathType' => 'standart', 'scope' => 'komple', 'quality' => 'orta', 'shower' => 'kabin'];

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem']);
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
        config(['worksitem_tools.tool_status.overrides.banyo-maliyet-hesaplayici' => 'active']);
    }

    private function sponsor(array $over = []): Sponsor
    {
        return Sponsor::query()->create(array_merge([
            'brand' => 'Örnek Seramik', 'headline' => 'Banyo karolarında yeni sezon', 'body' => 'Kaymaz zemin karoları.',
            'cta_label' => 'İncele', 'target_url' => 'https://ornek-seramik.com.tr/banyo', 'tool_slug' => 'banyo-maliyet-hesaplayici',
            'category' => 'seramik', 'priority' => 10, 'active' => true,
        ], $over));
    }

    private function sentCode(): string
    {
        $msg = Http::recorded()->last()[0]['messages'][0]['msg'];
        preg_match('/(\d{6})/', $msg, $m);

        return $m[1];
    }

    public function test_sponsor_yokken_banyo_sayfasinda_markalar_icin_karti_ve_pro_rapor_butonu(): void
    {
        $this->get('/araclar/banyo-maliyet-hesaplayici')->assertOk()
            ->assertSee('id="proBtn"', false)
            ->assertSee(route('tools.banyo_pro_report'), false)
            ->assertSee('/markalar-icin', false);
    }

    public function test_canli_sponsor_gosterilir_gosterim_sayilir_tiklama_utm_ile_yonlenir(): void
    {
        $s = $this->sponsor();
        $this->sponsor(['brand' => 'Pasif Marka', 'active' => false]);
        $this->sponsor(['brand' => 'Süresi Bitmiş', 'ends_on' => now()->subDays(2)->toDateString()]);

        $this->get('/araclar/banyo-maliyet-hesaplayici')->assertOk()
            ->assertSee('Örnek Seramik')
            ->assertSee('Sponsorlu')
            ->assertSee('sponsored nofollow', false)
            ->assertDontSee('Pasif Marka')
            ->assertDontSee('Süresi Bitmiş');
        $this->assertSame(1, (int) $s->fresh()->impressions);

        $r = $this->get(route('sponsor.go', $s));
        $r->assertRedirect();
        $loc = (string) $r->headers->get('Location');
        $this->assertStringStartsWith('https://ornek-seramik.com.tr/banyo?', $loc);
        $this->assertStringContainsString('utm_source=worksitem', $loc);
        $this->assertStringContainsString('utm_campaign=banyo-maliyet-hesaplayici', $loc);
        $this->assertSame(1, (int) $s->fresh()->clicks);

        $off = $this->sponsor(['brand' => 'Kapalı', 'active' => false]);
        $this->get(route('sponsor.go', $off))->assertRedirect('/araclar');
        $this->assertSame(0, (int) $off->fresh()->clicks);
    }

    public function test_markalar_icin_basvurusu_lead_olur(): void
    {
        Mail::fake();
        $this->get('/markalar-icin')->assertOk()->assertSee('Sponsorlu');

        $this->post('/markalar-icin', [
            'company' => 'Örnek Yapı A.Ş.', 'name' => 'Mehmet Kaya', 'phone' => '0532 111 22 33',
            'email' => 'mehmet@ornekyapi.com', 'interest' => 'Banyo', 'contact_consent' => '1',
        ])->assertRedirect();

        $lead = Lead::query()->where('source', 'sponsor_basvurusu')->first();
        $this->assertNotNull($lead);
        $this->assertSame('Mehmet Kaya', $lead->name);
    }

    public function test_pro_rapor_olcusuz_acilmaz_olculu_sayfa_gosterilir(): void
    {
        $this->get(route('tools.banyo_pro_report'))->assertRedirect('/araclar/banyo-maliyet-hesaplayici');

        $this->get(route('tools.banyo_pro_report', self::CALC))->assertOk()
            ->assertSee('Banyonuz için detaylı Pro rapor')
            ->assertSee('Kod gönder')
            ->assertSee('name="width" value="2"', false)
            ->assertSee('noindex', false);
    }

    public function test_pro_rapor_kodsuz_ya_da_yanlis_kodla_inmez(): void
    {
        $this->post(route('tools.banyo_pro_report_download'), self::CALC + ['name' => 'Ayşe Demir', 'phone' => '05327654321', 'code' => '123456', 'kvkk' => '1'])
            ->assertSessionHasErrors('code');

        $this->post(route('tools.banyo_pro_report_download'), self::CALC + ['name' => 'Ayşe Demir', 'phone' => '05327654321', 'code' => '123456'])
            ->assertSessionHasErrors('kvkk');

        $this->assertSame(0, Lead::query()->count());
    }

    public function test_pro_rapor_sms_dogrulamasiyla_pdf_iner_ve_aranmak_istemeyen_lead_olarak_kaydedilir(): void
    {
        Mail::fake();
        $this->sponsor();

        $this->postJson(route('tools.banyo_pro_report_code'), ['phone' => '0532 765 43 21'])->assertOk()->assertJson(['ok' => true]);
        $code = $this->sentCode();

        $r = $this->post(route('tools.banyo_pro_report_download'), self::CALC + ['name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'code' => $code, 'kvkk' => '1']);
        $r->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $r->headers->get('Content-Type'));

        $lead = Lead::query()->where('source', 'banyo_pro_rapor')->firstOrFail();
        $this->assertFalse((bool) $lead->contact_consent);
        $this->assertStringContainsString('HAYIR', (string) $lead->notes);

        // Doğrulanmış oturumda kod istemeden tekrar iner, ikinci lead açılmaz
        $this->get(route('tools.banyo_pro_report', self::CALC))->assertSee('Telefonunuz doğrulandı');
        $this->post(route('tools.banyo_pro_report_download'), self::CALC)->assertOk();
        $this->assertSame(1, Lead::query()->where('source', 'banyo_pro_rapor')->count());
    }

    public function test_pro_rapor_arama_izni_verilirse_isaretlenir(): void
    {
        Mail::fake();
        $this->postJson(route('tools.banyo_pro_report_code'), ['phone' => '05327654321'])->assertOk();
        $this->post(route('tools.banyo_pro_report_download'), self::CALC + ['name' => 'Ayşe Demir', 'phone' => '05327654321', 'code' => $this->sentCode(), 'kvkk' => '1', 'call_ok' => '1'])->assertOk();

        $this->assertTrue((bool) Lead::query()->where('source', 'banyo_pro_rapor')->firstOrFail()->contact_consent);
    }

    public function test_hatali_telefona_kod_gonderilmez(): void
    {
        $this->postJson(route('tools.banyo_pro_report_code'), ['phone' => '12'])->assertStatus(422);
    }

    public function test_admin_sponsor_listesi_acilir(): void
    {
        $this->sponsor();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/sponsors')->assertOk()->assertSee('Örnek Seramik');
        $this->actingAs($admin)->get('/admin/sponsors/create')->assertOk();
    }
}

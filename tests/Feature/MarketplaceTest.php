<?php

namespace Tests\Feature;

use App\Models\Firm;
use App\Models\JobOffer;
use App\Models\JobRequest;
use App\Models\Order;
use App\Services\Market\Marketplace;
use App\Support\TaxId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    private const IYZ = 'https://sandbox-api.iyzipay.com';

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config([
            'services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem',
            'worksitem_market.enabled' => true, 'worksitem_market.free_until' => '', 'worksitem_payments.online_enabled' => true, 'services.iyzico.api_key' => 'k', 'services.iyzico.secret_key' => 's', 'services.iyzico.base_url' => self::IYZ,
        ]);
        Http::fake([
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
            self::IYZ.'/payment/iyzipos/checkoutform/initialize/auth/ecom' => Http::response(['status' => 'success', 'token' => 'MT', 'paymentPageUrl' => 'https://pay.test/MT']),
            self::IYZ.'/payment/iyzipos/checkoutform/auth/ecom/detail' => fn () => Http::response(['status' => 'success', 'paymentStatus' => 'SUCCESS',
                'basketId' => Order::query()->latest('id')->first()->public_id, 'paidPrice' => Order::query()->latest('id')->first()->amount, 'paymentId' => 'P1']),
        ]);
    }

    private function firm(string $name, string $phone, array $over = []): Firm
    {
        return Firm::query()->create($over + [
            'name' => $name, 'type' => 'tadilat', 'legal_type' => 'sirket', 'legal_name' => $name.' Ltd. Şti.', 'tax_no' => '1234567890', 'tax_office' => 'Kadıköy',
            'efatura' => 'yes', 'invoice_city' => 'İstanbul', 'invoice_district' => 'Kadıköy', 'invoice_address' => 'Moda Cad. No: 1',
            'trades' => ['ince-insaat'], 'cities' => ['İstanbul'], 'contact_name' => 'Ali Veli', 'phone' => $phone, 'email' => strtolower(str_replace(' ', '', $name)).'@example.com',
            'status' => 'approved', 'approved_at' => now(), 'tax_verified_at' => now(),
        ]);
    }

    private function sms(): array
    {
        return collect(Http::recorded())->filter(fn ($p) => str_contains($p[0]->url(), 'netgsm'))
            ->map(fn ($p) => [$p[0]['messages'][0]['no'] ?? ($p[0]['messages'][0]['gsmno'] ?? ''), $p[0]['messages'][0]['msg'] ?? ''])->values()->all();
    }

    private function lastCode(): string
    {
        preg_match('/(\d{6})/', collect($this->sms())->last()[1], $m);

        return $m[1];
    }

    private function panelLogin(Firm $f): void
    {
        Cache::flush();
        $this->postJson(route('market.panel.code'), ['phone' => '0'.$f->phone])->assertOk();
        $this->post(route('market.panel.verify'), ['phone' => '0'.$f->phone, 'code' => $this->lastCode()])->assertRedirect();
    }

    public function test_vergi_numarasi_kontrol_hanesi(): void
    {
        $this->assertTrue(TaxId::valid('1234567890'));
        $this->assertFalse(TaxId::valid('1234567891'));
        $this->assertTrue(TaxId::valid('10000000146'));
        $this->assertFalse(TaxId::valid('10000000147'));
        $this->assertFalse(TaxId::valid('01234567890'));
    }

    public function test_eslesme_bedeli_ve_iletisim_gizleme(): void
    {
        $this->assertSame(250.0, Marketplace::fee(3000));
        $this->assertSame(5000.0, Marketplace::fee(100000));
        $this->assertSame(7500.0, Marketplace::fee(400000));
        $this->assertStringNotContainsString('532', Marketplace::mask('Beni 0532 765 43 21 numarasından arayın, mail a@b.com, www.firma.com'));
        $this->assertStringContainsString('Banyo 5 m2', Marketplace::mask('Banyo 5 m2, 2 lavabo'));
    }

    public function test_tam_akis_talep_teklif_kabul_odeme_eslesme(): void
    {
        $a = $this->firm('Alfa Yapı', '5321110001', ['districts' => ['Kadıköy', 'Üsküdar']]);
        $b = $this->firm('Beta Tadilat', '5321110002');
        $c = $this->firm('Gama Ankara', '5321110003', ['cities' => ['Ankara']]);            // il uymuyor
        $d = $this->firm('Delta Ataşehir', '5321110004', ['districts' => ['Ataşehir']]);   // ilçe uymuyor
        $e = $this->firm('Epsilon Elektrik', '5321110005', ['trades' => ['elektrik']]);     // iş kolu uymuyor
        $p = $this->firm('Pasif Usta', '5321110006', ['status' => 'pending']);             // onaysız

        // Araçtan gelen bağlantı: tutar sunucuda yeniden hesaplanır
        $q = ['target' => 'daire', 'floor' => '100', 'surfaces' => 'duvar_tavan', 'color' => 'ayni', 'state' => 'iyi', 'quality' => 'orta'];
        $this->get('/teklif-al?arac=boya-hesaplayici&'.http_build_query($q).'&min=1&max=2')->assertOk()
            ->assertSee('₺49.000 – ₺69.000')->assertSee('value="ince-insaat" selected', false);

        Cache::flush();
        $this->postJson(route('market.request.code'), ['phone' => '05327654321'])->assertOk();
        $res = $this->post(route('market.request.store'), [
            'tool' => 'boya-hesaplayici', 'trade' => 'ince-insaat', 'title' => 'Daire boyası, 100 m²', 'est_low' => 49000, 'est_high' => 69000,
            'city' => 'İstanbul', 'district' => 'kadıköy', 'description' => 'Duvar ve tavan. Beni 0532 765 43 21 den arayın.', 'start_pref' => '1ay',
            'name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'code' => $this->lastCode(), 'terms' => '1', 'kvkk' => '1',
        ]);
        $job = JobRequest::query()->firstOrFail();
        $res->assertRedirect($job->customerUrl());
        $this->assertSame(2, $job->invited_count);
        $this->assertEqualsCanonicalizing([$a->id, $b->id], $job->invites()->pluck('firm_id')->all());
        $this->assertTrue(collect($this->sms())->contains(fn ($s) => str_contains($s[1], 'kadıköy / İstanbul bölgesinde yeni iş')));

        // Kullanıcı sayfası: gizli anahtar olmadan açılmaz
        $this->flushSession();
        $this->get(route('market.request.show', ['job' => $job->public_id]))->assertNotFound();
        $this->get($job->customerUrl())->assertOk()->assertSee('Henüz teklif yok');

        // Firma paneli: giriş yoksa yönlendirir; onaysız firma kod alamaz
        $this->get(route('market.panel.job', $job))->assertRedirect(route('market.panel.login'));
        $before = count($this->sms());
        $this->postJson(route('market.panel.code'), ['phone' => '0'.$p->phone])->assertOk();
        $this->assertCount($before, $this->sms());

        // Firma A teklif verir: müşteri adı/telefonu görünmez, açıklamadaki numara gizlenir
        $this->panelLogin($a);
        $this->get(route('market.panel.job', $job))->assertOk()->assertSee('Daire boyası')->assertSee('Ayşe · kadıköy')
            ->assertDontSee('5327654321')->assertDontSee('765 43 21')->assertSee('iletişim bilgisi gizlendi');
        $this->post(route('market.panel.offer', $job), ['amount' => 60000, 'duration_days' => 5, 'includes_material' => 1, 'note' => 'Garanti 2 yıl. Tel 0555 111 22 33', 'rules' => '1'])
            ->assertRedirect(route('market.panel.job', $job));
        $offerA = JobOffer::query()->where('firm_id', $a->id)->firstOrFail();
        $this->assertStringNotContainsString('0555', $offerA->note);
        $this->assertTrue(collect($this->sms())->contains(fn ($s) => str_contains($s[1], 'talebinize yeni teklif geldi (Alfa Yapı)')));

        // Firma C davetli değil: işi göremez
        $this->panelLogin($c);
        $this->get(route('market.panel.job', $job))->assertNotFound();

        // Firma B teklif verir
        $this->panelLogin($b);
        $this->post(route('market.panel.offer', $job), ['amount' => 55000, 'includes_material' => 1, 'rules' => '1'])->assertRedirect();
        $offerB = JobOffer::query()->where('firm_id', $b->id)->firstOrFail();

        // Kullanıcı B'yi kabul eder (paylaşım onayı zorunlu)
        $this->flushSession();
        $this->get($job->customerUrl())->assertOk()->assertSee('Alfa Yapı')->assertSee('Beta Tadilat')->assertSee('₺55.000')->assertDontSee('5321110002');
        $acc = route('market.request.accept', ['job' => $job->public_id, 'offer' => $offerB->public_id]);
        $this->post($acc, ['t' => $job->token])->assertSessionHasErrors('share');
        $this->post($acc, ['t' => $job->token, 'share' => '1'])->assertRedirect($job->customerUrl());
        $offerB->refresh();
        $this->assertSame('accepted', $offerB->status);
        $this->assertSame(2750.0, $offerB->fee_net);
        // ikinci kabul engellenir
        $this->post(route('market.request.accept', ['job' => $job->public_id, 'offer' => $offerA->public_id]), ['t' => $job->token, 'share' => '1'])->assertSessionHasErrors('offer');

        // B öder → eşleşme, iletişim açılır, A seçilmedi olur
        $this->panelLogin($b);
        $this->get(route('market.panel.job', $job))->assertSee('Müşteri teklifinizi kabul etti')->assertDontSee('5327654321');
        $this->post(route('market.panel.pay', $job))->assertRedirect('https://pay.test/MT');
        $order = Order::query()->where('kind', 'match_fee')->firstOrFail();
        $this->assertEqualsWithDelta(3300, $order->amount, 0.01);
        $this->assertSame($b->id, $order->firm_id);
        $this->get($this->post(route('odeme.iyzico.callback'), ['token' => 'MT'])->headers->get('Location'))->assertRedirect(route('market.panel.job', $job));

        $this->assertSame('matched', $offerB->fresh()->status);
        $this->assertSame('not_selected', $offerA->fresh()->status);
        $this->assertSame('matched', $job->fresh()->status);
        $this->assertSame(1, (int) $b->fresh()->matches_count);
        $this->get(route('market.panel.job', $job))->assertSee('Eşleşme tamam')->assertSee('05327654321');
        $sms = collect($this->sms());
        $this->assertTrue($sms->contains(fn ($s) => str_contains($s[1], 'Beta Tadilat ile eşleştiniz') && str_contains($s[1], '05321110002')));
        $this->assertTrue($sms->contains(fn ($s) => str_contains($s[1], 'Müşteri: Ayşe Demir, 05327654321')));
        $this->flushSession();
        $this->get($job->customerUrl())->assertSee('Beta Tadilat ile eşleştiniz')->assertSee('05321110002');

        // Fatura: e-Fatura mükellefi firma, KDV ayrımı
        $buyer = $order->fresh()->buyer();
        $this->assertSame('e-Fatura', $buyer['type']);
        $this->assertSame('Beta Tadilat Ltd. Şti.', $buyer['title']);
        $this->assertEqualsWithDelta(2750, $order->fresh()->net(), 0.01);
    }

    public function test_odenmeyen_kabul_48_saatte_duser_ve_firma_bulunamazsa_admine_gider(): void
    {
        $a = $this->firm('Alfa Yapı', '5321110001');
        $job = JobRequest::query()->create(['trade' => 'ince-insaat', 'title' => 'Banyo', 'city' => 'İstanbul', 'district' => 'Kadıköy', 'name' => 'Ayşe Demir', 'phone' => '5327654321']);
        $m = app(Marketplace::class);
        $this->assertSame(1, $m->invite($job));
        $o = $m->submitOffer($a, $job, ['amount' => 20000]);
        $m->accept($job, $o);
        $this->travel(49)->hours();
        $this->artisan('market:tick')->assertSuccessful();
        $this->assertSame('expired', $o->fresh()->status);
        $this->assertSame('open', $job->fresh()->status);

        $none = JobRequest::query()->create(['trade' => 'asansor', 'title' => 'Asansör', 'city' => 'Van', 'district' => 'Merkez', 'name' => 'Ali Kaya', 'phone' => '5320000000']);
        $this->assertSame(0, $m->invite($none));
        $this->travel(15)->days();
        $this->artisan('market:tick')->assertSuccessful();
        $this->assertSame('expired', $none->fresh()->status);
    }

    public function test_arac_sayfasinda_iki_secenek_ve_admin_ekranlari(): void
    {
        $this->get('/araclar/boya-hesaplayici')->assertOk()->assertSee('Bu işi kime yaptırmak istersiniz?')->assertSee('/teklif-al?arac=boya-hesaplayici', false);
        $this->get('/araclar/banyo-maliyet-hesaplayici')->assertOk()->assertSee('Kayıtlı firmalardan teklif alayım');
        $this->get('/araclar/kolon-filiz-boyu')->assertDontSee('Bu işi kime yaptırmak istersiniz?');
        config(['worksitem_market.enabled' => false]);
        $this->get('/araclar/boya-hesaplayici')->assertDontSee('Bu işi kime yaptırmak istersiniz?');
        $this->get('/teklif-al')->assertNotFound();

        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $f = $this->firm('Alfa Yapı', '5321110001');
        JobRequest::query()->create(['trade' => 'ince-insaat', 'title' => 'Banyo yenileme', 'city' => 'İstanbul', 'district' => 'Kadıköy', 'name' => 'Ayşe Demir', 'phone' => '5327654321']);
        Order::query()->create(['kind' => 'match_fee', 'firm_id' => $f->id, 'title' => 'Eşleşme', 'amount' => 360, 'status' => 'paid', 'paid_at' => now()]);
        $this->actingAs($admin)->get('/admin/job-requests')->assertOk()->assertSee('Banyo yenileme');
        $this->actingAs($admin)->get('/admin/orders')->assertOk()->assertSee('KESİLECEK')->assertSee('Alfa Yapı Ltd. Şti.');
        $csv = \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Resources\OrderResource\Pages\ListOrders::class)->callTableAction('export');
        $csv->assertFileDownloaded('worksitem-kesilecek-faturalar-'.now()->format('Y-m-d').'.csv');
    }

    public function test_kurucu_firma_programi_ucretsiz_onay_ile_eslesir(): void
    {
        config(['worksitem_market.free_until' => now('Europe/Istanbul')->addMonth()->toDateString()]);
        $a = $this->firm('Alfa Yapı', '5321110001');
        $job = JobRequest::query()->create(['trade' => 'ince-insaat', 'title' => 'Banyo', 'est_low' => 90000, 'est_high' => 120000, 'city' => 'İstanbul', 'district' => 'Kadıköy', 'name' => 'Ayşe Demir', 'phone' => '5327654321']);
        $m = app(Marketplace::class);
        $m->invite($job);
        $o = $m->submitOffer($a, $job, ['amount' => 100000]);
        $m->accept($job, $o->fresh());
        $this->assertSame(0.0, (float) $o->fresh()->fee_net);
        $this->assertTrue(collect($this->sms())->contains(fn ($s) => str_contains($s[1], 'eşleşmeyi onaylayın (kurucu firma programı: ücretsiz)')));

        $this->panelLogin($a);
        $this->get(route('market.panel.job', $job))->assertSee('Eşleşmeyi onayla')->assertDontSee('Kartla öde')->assertDontSee('5327654321');
        $this->post(route('market.panel.pay', $job))->assertSessionHasErrors('pay');   // ödenecek bedel yok
        $this->post(route('market.panel.confirm', $job))->assertRedirect(route('market.panel.job', $job));
        $this->assertSame('matched', $o->fresh()->status);
        $this->assertSame(0, Order::query()->count());
        $this->get(route('market.panel.job', $job))->assertSee('05327654321');

        // Tarih geçince ücret uygulanır
        config(['worksitem_market.free_until' => now('Europe/Istanbul')->subDay()->toDateString()]);
        $this->assertFalse(Marketplace::isFree());
    }

    public function test_en_kucuk_is_tutarinin_altindaki_isler_firmaya_gitmez_ve_saha_fiyatlari(): void
    {
        $big = $this->firm('Büyük Taahhüt', '5321110001', ['min_job' => 200000, 'answers' => ['min_job' => 200000, 'p_alcipan_bolme' => 1400]]);
        $small = $this->firm('Küçük Usta', '5321110002', ['answers' => ['p_alcipan_bolme' => 1500]]);
        $this->firm('Üçüncü', '5321110003', ['answers' => ['p_alcipan_bolme' => 5000]]);
        $job = JobRequest::query()->create(['trade' => 'ince-insaat', 'title' => 'Boya', 'est_low' => 40000, 'est_high' => 60000, 'city' => 'İstanbul', 'district' => 'Kadıköy', 'name' => 'Ayşe Demir', 'phone' => '5327654321']);
        app(Marketplace::class)->invite($job);
        $ids = $job->invites()->pluck('firm_id')->all();
        $this->assertNotContains($big->id, $ids);
        $this->assertContains($small->id, $ids);

        $row = \App\Services\Market\FirmQuestions::stats()->firstWhere('key', 'p_alcipan_bolme');
        $this->assertSame(3, $row['n']);
        $this->assertSame(1500, $row['median']);
        $this->assertSame(['Üçüncü: 5.000'], $row['outliers']);

        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/saha-fiyatlari')->assertOk()->assertSee('Alçıpan bölme duvar')->assertSee('Üçüncü: 5.000');
    }

    public function test_tedarikci_sorulari(): void
    {
        $f = $this->firm('Yapı Market', '5321110009', ['type' => 'tedarikci']);
        $this->panelLogin($f);
        $this->get(route('market.panel.info'))->assertOk()->assertSee('Hangi ürün gruplarını')->assertSee('50 kg çimento')->assertDontSee('En küçük kaç TL');
        $this->post(route('market.panel.info.save'), ['a' => ['groups' => ['cimento', 'boya'], 'delivery' => 'ucretli', 'p_cimento' => '320', 'brands' => 'Akçansa, Filli Boya']])
            ->assertRedirect(route('market.panel'));
        $this->assertSame(320, $f->fresh()->answers['p_cimento']);
        $this->get(route('market.panel'))->assertSee('Hizmet bilgilerini güncelle');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Support\Pilot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v95 pilot dönem: firma tarafı kapalı, keşif yalnız İstanbul'da, hizmeti Kainta İnşaat verir */
class PilotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_pilot.enabled' => true, 'worksitem_pilot.firm_signup' => false, 'worksitem_market.enabled' => true, 'worksitem_firms.directory_enabled' => true]);
    }

    public function test_varsayilan_ayar_pilot_acik(): void
    {
        $cfg = require config_path('worksitem_pilot.php');
        $this->assertStringContainsString("env('WORKSITEM_PILOT', true)", file_get_contents(config_path('worksitem_pilot.php')));
        $this->assertSame(['İstanbul'], $cfg['cities']);
    }

    public function test_bolge_tespiti_turkce_ve_buyuk_kucuk_harf_duyarsiz(): void
    {
        $this->assertTrue(Pilot::inArea('İstanbul', 'Kadıköy'));
        $this->assertTrue(Pilot::inArea('ISTANBUL'));
        $this->assertTrue(Pilot::inArea(null, 'Ümraniye, istanbul'));
        $this->assertFalse(Pilot::inArea('İzmir', 'Karşıyaka'));
        $this->assertNull(Pilot::inArea(null, ''));

        config(['worksitem_pilot.enabled' => false]);
        $this->assertTrue(Pilot::inArea('İzmir'));
    }

    public function test_firma_uyeligi_kapali_ama_sayfa_bilgi_verir(): void
    {
        $this->get('/firma-uyeligi')->assertOk()
            ->assertSee('Firma üyeliği şu an kapalı.')
            ->assertSee('noindex, follow', false)
            ->assertDontSee('name="tax_no"', false);
        $this->postJson('/firma-uyeligi/kod', ['phone' => '05321234567'])->assertStatus(403);
        $this->post('/firma-uyeligi', ['name' => 'X'])->assertRedirect(route('firms.page'));
        $this->get('/firmalar')->assertNotFound();
        $this->get('/teklif-al')->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/firma-uyeligi', false);

        config(['worksitem_pilot.firm_signup' => true]);
        $this->get('/firma-uyeligi')->assertOk()->assertDontSee('Firma üyeliği şu an kapalı.');
    }

    public function test_sitede_firma_cagrilari_gizli_kainta_ve_istanbul_gorunur(): void
    {
        $home = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('Bölgemdeki işleri al', $home);
        $this->assertStringNotContainsString('href="/firma-uyeligi"', $home);
        $this->assertStringContainsString('Hesapla, keşif iste', $home);   // v110: etiket hedefle eşleşir
        $this->assertStringContainsString('İstanbul&#039;da keşif ve uygulama', $home);

        $tool = $this->get('/araclar/boya-hesaplayici')->assertOk()->getContent();
        $this->assertStringNotContainsString('Ücretsiz üye olun', $tool);
        $this->assertStringNotContainsString('Bu işler size gelsin', $tool);

        $this->get('/legal/kullanim-kosullari')->assertOk()->assertSee('Pilot Dönem')->assertSee('İstanbul');
        $this->get('/legal/firma-uyelik-kosullari')->assertOk()->assertSee('Pilot dönem notu');
    }

    public function test_istanbul_disi_talepten_kesif_odemesi_alinmaz(): void
    {
        $lead = Lead::query()->create(['name' => 'Ali Kaya', 'phone' => '05321112233', 'status' => 'yeni', 'city' => 'İzmir', 'district' => 'Karşıyaka']);
        $this->withSession(['kesif_lead' => $lead->public_id])->get('/maliyet-hesapla/basarili')->assertOk()
            ->assertSee('Hizmet bölgemiz şu an İstanbul')
            ->assertDontSee('Keşif bedelini kabul ediyorum');

        $this->withSession(['kesif_lead' => $lead->public_id])
            ->post('/kesif/kabul', ['odeme' => 'havale', 'fatura' => 'bireysel', 'email' => 'a@b.co', 'kesif_onay' => '1'])
            ->assertRedirect(route('lead.thanks'));
        $this->assertNull($lead->refresh()->inspection_status);

        $ist = Lead::query()->create(['name' => 'Ayşe Yılmaz', 'phone' => '05321112234', 'status' => 'yeni', 'city' => 'İstanbul', 'district' => 'Kadıköy']);
        $this->withSession(['kesif_lead' => $ist->public_id])->get('/maliyet-hesapla/basarili')->assertOk()
            ->assertSee('Keşif bedelini kabul ediyorum');
    }
}

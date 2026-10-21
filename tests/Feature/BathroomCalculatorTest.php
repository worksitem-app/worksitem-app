<?php

namespace Tests\Feature;

use App\Services\BathroomEstimator;
use App\Services\FxRates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BathroomCalculatorTest extends TestCase
{
    use RefreshDatabase;

    // Kur dosyası diskte kalır (storage/app/fx-tcmb.json): test sırası değişince "kur yokken" testleri bozuluyordu
    protected function setUp(): void
    {
        parent::setUp();
        @unlink(storage_path('app/fx-tcmb.json'));
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('app/fx-tcmb.json'));
        parent::tearDown();
    }

    private function est(array $o = []): array
    {
        return app(BathroomEstimator::class)->estimate(array_merge([
            'type' => 'standart', 'w' => 1.8, 'l' => 2.4, 'h' => 2.6,
            'scope' => 'komple', 'quality' => 'orta', 'shower' => 'kabin', 'extras' => ['ex_dolap'],
        ], $o));
    }

    public function test_varsayilan_banyo_elle_hesapla_ayni(): void
    {
        $r = $this->est();
        // zemin 4,32; duvar 2*(4,2)*2,6-1,6=20,24; kaplama 24,56
        $this->assertSame(24.56, $r['tile_area']);
        $this->assertSame(8600, $r['items']['sokum']['mid']);      // 24,56*350
        $this->assertSame(22500, $r['items']['tesisat']['mid']);   // 16000+4,32*1500
        $this->assertSame(10000, $r['items']['yalitim']['mid']);   // 4,32*1200+4800
        $this->assertSame(23300, $r['items']['seramik']['mid']);   // 24,56*950
        $this->assertSame(11300, $r['items']['beklenmedik']['mid']);
        $this->assertSame([106000, 125000, 150000], [$r['low'], $r['mid'], $r['high']]);
    }

    public function test_kalite_ve_kapsam_sirasi_mantikli(): void
    {
        $this->assertLessThan($this->est(['quality' => 'orta'])['mid'], $this->est(['quality' => 'eko'])['mid']);
        $this->assertLessThan($this->est(['quality' => 'premium'])['mid'], $this->est(['quality' => 'orta'])['mid']);
        $this->assertLessThan($this->est(['scope' => 'komple'])['mid'], $this->est(['scope' => 'yuzey'])['mid']);
        $this->assertLessThan($this->est(['scope' => 'tasima'])['mid'], $this->est(['scope' => 'komple'])['mid']);
        $r = $this->est();
        $this->assertTrue($r['low'] < $r['mid'] && $r['mid'] < $r['high']);
    }

    public function test_wc_de_dus_yok_ve_gecersiz_girdi_sinirlanir(): void
    {
        $this->assertSame(0, $this->est(['type' => 'wc'])['items']['dus']['mid']);
        $r = $this->est(['w' => -5, 'l' => 999, 'scope' => 'xx', 'quality' => 'yy', 'extras' => ['yok']]);
        $this->assertSame(5.0, $r['floor']); // 0,5 x 10
        $this->assertGreaterThan(0, $r['mid']);
    }

    public function test_sayfa_aralik_tablo_sss_ve_kvkk_icerir(): void
    {
        $html = $this->get('/araclar/banyo-maliyet-hesaplayici')->assertOk()->getContent();
        $this->assertStringContainsString('Ölçüler bekleniyor', $html); // ölçü otomatik gelmez
        $this->assertStringContainsString('₺104.000', $html); // 4 m² örnek özet (SEO)
        $this->assertDoesNotMatchRegularExpression('/id="width"[^>]*value=/', $html);
        $this->assertStringContainsString('Boyuta göre banyo tadilat maliyeti', $html);
        $this->assertStringContainsString('"FAQPage"', $html);
        $this->assertStringContainsString('"BreadcrumbList"', $html);
        $this->assertStringContainsString('contact_consent', $html);
        $this->assertMatchesRegularExpression('/id="bFx"\s+hidden/', $html); // kur yokken döviz satırı gizli
    }

    public function test_tcmb_kuru_ayristirilir_ve_sayfada_kullanilir(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><Tarih_Date Tarih="09.10.2026" Date="10/09/2026">'
            .'<Currency CurrencyCode="USD"><Unit>1</Unit><ForexSelling>41.80</ForexSelling></Currency>'
            .'<Currency CurrencyCode="EUR"><Unit>1</Unit><ForexSelling>48.20</ForexSelling></Currency>'
            .'<Currency CurrencyCode="GBP"><Unit>1</Unit><ForexSelling>55.10</ForexSelling></Currency>'
            .'<Currency CurrencyCode="JPY"><Unit>100</Unit><ForexSelling>27.5</ForexSelling></Currency></Tarih_Date>';
        Http::fake(['*' => Http::response($xml)]);

        $this->artisan('fx:update')->assertSuccessful();
        $fx = app(FxRates::class)->current();
        $this->assertSame('09.10.2026', $fx['date']);
        $this->assertSame(41.8, $fx['rates']['USD']);

        $this->get('/araclar/banyo-maliyet-hesaplayici')->assertOk()->assertSee('TCMB 09.10.2026');
    }

    public function test_tcmb_ulasilamazsa_eski_kur_korunur(): void
    {
        Cache::put(FxRates::CACHE_KEY, ['date' => '08.10.2026', 'rates' => ['USD' => 41, 'EUR' => 48, 'GBP' => 55]]);
        Http::fake(['*' => Http::response('', 500)]);
        $this->artisan('fx:update')->assertFailed();
        $this->assertSame('08.10.2026', app(FxRates::class)->current()['date']);
    }

    public function test_admin_panelindeki_onayli_fiyat_hesabi_degistirir(): void
    {
        $before = $this->est()['items']['seramik']['mid'];
        $this->assertDatabaseHas('unit_prices', ['key' => 'banyo_seramik_m2_orta', 'price' => 950]);

        \App\Models\UnitPrice::where('key', 'banyo_seramik_m2_orta')->first()->update(['price' => 1900]);
        $this->assertSame(46700, $this->est()['items']['seramik']['mid']); // 24,56 m² × 1900

        // Onayı kaldırılan fiyat kullanılmaz, config değerine dönülür
        \App\Models\UnitPrice::where('key', 'banyo_seramik_m2_orta')->first()->update(['review_status' => 'needs_engineer_review']);
        $this->assertSame($before, $this->est()['items']['seramik']['mid']);
    }

    public function test_tum_banyo_fiyatlari_admin_paneline_eklenmis(): void
    {
        $book = app(\App\Services\BathroomPriceBook::class);
        $keys = array_keys($book->map(config('worksitem_bathroom')));
        $this->assertSame(count($keys), \App\Models\UnitPrice::where('category', 'banyo_tadilati')->count());
    }

    public function test_teklif_hatasi_sayfada_gorunur(): void
    {
        $this->from('/araclar/banyo-maliyet-hesaplayici')
            ->post(route('lead.store'), ['name' => 'Test', 'phone' => '123', 'contact_consent' => '1'])
            ->assertRedirect('/araclar/banyo-maliyet-hesaplayici');
        $this->get('/araclar/banyo-maliyet-hesaplayici')->assertSee('Teklif talebiniz gönderilemedi');
    }

    public function test_banyo_teklifi_kaydedilir_ve_aralik_saklanir(): void
    {
        $this->post(route('lead.store'), [
            'name' => 'Ayşe Kaya', 'phone' => '05321234567', 'location' => 'İzmir',
            'calculated_service' => 'Banyo Tadilatı', 'calculated_area' => 'Standart banyo 4,3m2',
            'estimated_min' => 106000, 'estimated_max' => 150000, 'estimated_cost' => 125000,
            'budget_confirmed' => 1, 'contact_consent' => '1',
        ])->assertRedirect(route('lead.thanks'));

        $this->assertDatabaseCount('leads', 1);
    }

    public function test_uygunsuz_dil_ve_turkce_telefon_mesaji(): void
    {
        $this->from('/araclar/banyo-maliyet-hesaplayici')
            ->post(route('lead.store'), ['name' => 'Test', 'phone' => '0532', 'contact_consent' => '1'])
            ->assertSessionHasErrors(['phone' => 'Telefon numaranızı 05XX XXX XX XX biçiminde, 11 haneli yazın.']);

        $this->from('/araclar/banyo-maliyet-hesaplayici')
            ->post(route('lead.store'), ['name' => 'Ali Veli', 'phone' => '05321234567', 'notes' => 'siktir git', 'contact_consent' => '1'])
            ->assertSessionHasErrors(['notes']);
        $this->assertDatabaseCount('leads', 0);

        // masum kelimeler engellenmez
        $this->assertFalse(\App\Support\LeadTextGuard::isOffensive('Sikke koleksiyonu odası, eşyaları götürmek gerek, malzeme seçimi'));
        $this->assertTrue(\App\Support\LeadTextGuard::isOffensive('AMK'));
    }

    public function test_pdf_rapor_sunucuda_hesaplanir_ve_indirilir(): void
    {
        $res = $this->get('/araclar/banyo-maliyet-hesaplayici/rapor.pdf?bathType=standart&width=2&length=2.5&scope=komple&quality=orta&shower=kabin&ex_dolap=1');
        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());

        // ölçü yoksa araca geri döner
        $this->get('/araclar/banyo-maliyet-hesaplayici/rapor.pdf')->assertRedirect('/araclar/banyo-maliyet-hesaplayici');
    }

    public function test_admin_paneli_acilir_ve_widgetlar_calisir(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        \App\Models\Lead::create(['name' => 'Ayşe Yılmaz', 'phone' => '05321234567', 'phone_normalized' => '905321234567', 'status' => 'yeni', 'estimated_min' => 100000, 'estimated_max' => 140000, 'contact_consent' => true]);
        $this->actingAs($admin)->get('/admin')->assertOk();
        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Widgets\LeadKpiOverview::class)->assertSee('Bugün gelen teklif talebi');
        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Widgets\LatestLeads::class)->assertSee('Ayşe Yılmaz')->assertSee('Yeni talep');
        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Widgets\ToolFunnel::class)->assertOk();
        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Widgets\LeadTrendChart::class)->assertOk();
        $this->actingAs($admin)->get('/admin/leads')->assertOk();
    }
}

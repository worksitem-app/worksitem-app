<?php

namespace Tests\Feature;

use App\Estimators\KitRegistry;
use App\Estimators\PozEstimator;
use App\Models\PriceRobotItem;
use App\Services\Pricing\PriceRobot;
use App\Services\Pricing\PriceRobotOverrides;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PriceRobotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.api_key' => 'test-key']);
        Cache::flush();
        PozEstimator::flushCache();
    }

    private function gemini(array $prices, bool $grounded = true): array
    {
        $sources = array_map(fn ($p, $i) => ['name' => 'Satıcı '.$i, 'url' => 'https://ornek'.$i.'.com.tr/urun', 'price_tl' => $p], $prices, array_keys($prices));
        $steps = [];
        if ($grounded) {
            $steps[] = ['type' => 'google_search_call', 'arguments' => ['queries' => ['test fiyatı']]];
            $steps[] = ['type' => 'google_search_result', 'call_id' => 'x', 'result' => []];
        }
        $steps[] = ['type' => 'model_output', 'content' => [['type' => 'text', 'text' => "```json\n".json_encode(['sources' => $sources, 'note' => 'test'])."\n```",
            'annotations' => [['type' => 'url_citation', 'url' => 'https://ornek0.com.tr', 'title' => 'ornek0']]]]];

        return ['status' => 'completed', 'steps' => $steps];
    }

    private function item(string $path): array
    {
        return app(PriceRobot::class)->catalog()[$path];
    }

    public function test_web_fiyati_ortanca_ile_yayina_girer_ve_hesabi_degistirir(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($this->gemini([600, 640, 700]))]);
        $path = 'worksitem_estimators.parke.material_m2.laminat';   // [380, 550, 850]
        $r = app(PriceRobot::class)->run($this->item($path));

        $this->assertSame('live', $r['status']);
        $this->assertSame([442.0, 640.0, 989.0], array_map('floatval', $r['value']));   // orta = ortanca 640, diğerleri oranla
        Http::assertSent(fn ($req) => str_ends_with($req->url(), '/v1beta/interactions') && ($req['tools'][0]['type'] ?? '') === 'google_search');

        PriceRobotOverrides::flush();
        PriceRobotOverrides::apply();
        $this->assertEquals(640, config($path)[1]);
    }

    public function test_tek_kaynak_veya_ani_sicrama_onaya_duser_yayindaki_deger_korunur(): void
    {
        $path = 'worksitem_estimators.mantolama.labor_m2';   // 380
        Http::fake(['generativelanguage.googleapis.com/*' => Http::sequence()
            ->push($this->gemini([400, 420]))
            ->push($this->gemini([2000, 2100, 2200]))
            ->push($this->gemini([450]))]);
        $robot = app(PriceRobot::class);

        $this->assertSame('live', $robot->run($this->item($path))['status']);
        $jump = $robot->run($this->item($path));
        $this->assertSame('needs_review', $jump['status']);
        $one = $robot->run($this->item($path));
        $this->assertSame('needs_review', $one['status']);

        $row = PriceRobotItem::query()->where('path', $path)->first();
        $this->assertEquals(410, $row->value);           // yayındaki değer değişmedi
        $this->assertEquals(450, $row->proposed_value);  // son öneri onay bekliyor
        PriceRobotOverrides::flush();
        $this->assertEquals(410, PriceRobotOverrides::map()[$path]);

        $robot->approve($row);
        PriceRobotOverrides::flush();
        $this->assertEquals(450, PriceRobotOverrides::map()[$path]);
    }

    public function test_google_aramasi_yapilmadiysa_fiyat_kullanilmaz(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($this->gemini([500, 520, 540], grounded: false))]);
        $r = app(PriceRobot::class)->run($this->item('worksitem_estimators.mantolama.labor_m2'));
        $this->assertSame('error', $r['status']);
    }

    public function test_once_poz_yoksa_web_fiyati_x140_kdv_haric_yazilir(): void
    {
        $robot = app(PriceRobot::class);
        $key = KitRegistry::get('asma-tavan-hesaplayici')->webKey('alcipan');
        $this->assertArrayHasKey('poz.'.$key, $robot->catalog());

        // Resmî fiyatı olan sistem araştırılmaz (önce poz)
        $official = collect($robot->catalog())->first(fn ($i) => ($i['kind'] ?? '') === 'poz' && $i['official']);
        DB::table('rayic_prices')->insert(['source' => 'YFK', 'period' => '2026-08', 'poz_no' => $official['poz'], 'description' => 'T', 'unit' => 'm²', 'price' => 800, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        $this->assertNotContains($official['path'], array_column($robot->due(500), 'path'));

        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($this->gemini([1100, 1200, 1300]))]);
        $r = $robot->run($robot->catalog()['poz.'.$key]);
        $this->assertSame('live', $r['status']);
        $this->assertEquals(round(1200 * 1.40 / 1.2, 2), $r['value']);   // 1400 TL KDV hariç

        $e = KitRegistry::get('asma-tavan-hesaplayici');
        $est = $e->estimate($e->normalize(['system' => 'alcipan', 'area' => 100]));
        $this->assertTrue($est['ready']);
        $this->assertStringContainsString('piyasa fiyatı', collect($est['items'])->pluck('label')->implode(' '));
    }

    public function test_ayni_ay_ikinci_arastirma_kaydi_gunceller_cift_kayit_hatasi_vermez(): void
    {
        $robot = app(PriceRobot::class);
        $key = KitRegistry::get('su-yalitimi-hesaplayici')->webKey('pu_liquid');
        $it = $robot->catalog()['poz.'.$key];

        Http::fake(['generativelanguage.googleapis.com/*' => Http::sequence()
            ->push($this->gemini([1000, 1050, 1100]))
            ->push($this->gemini([1100, 1150, 1200]))]);
        $this->assertSame('live', $robot->run($it)['status']);
        $this->assertSame('live', $robot->run($it)['status']);   // v93 canlıda: Duplicate entry rayic_prices_source_period_poz_unique

        $rows = DB::table('rayic_prices')->where('poz_no', $key)->where('source', 'WEB')->get();
        $this->assertCount(1, $rows);
        $this->assertEquals(round(1150 * 1.40 / 1.2, 2), (float) $rows[0]->price);
        $this->assertTrue((bool) $rows[0]->is_active);
    }

    public function test_komut_ve_zamanlama(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($this->gemini([600, 640, 700]))]);
        $this->artisan('prices:robot', ['--limit' => 2])->expectsOutputToContain('Bitti: ')->assertSuccessful();
        $this->assertSame(2, PriceRobotItem::query()->count());
        $this->artisan('prices:robot', ['--list' => true])->assertSuccessful();
        $this->artisan('schedule:list')->expectsOutputToContain('prices:robot')->assertSuccessful();
    }

    public function test_admin_fiyat_robotu_ekrani_kalemleri_listeler(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/price-robot-items')->assertOk()->assertSee('Fiyat Robotu')->assertSee('Şimdi 5 kalem araştır');
        $this->assertGreaterThan(30, PriceRobotItem::query()->count());
    }

    public function test_turkce_fiyat_yazimi_ve_baglantisiz_kaynak_arama_sonucundan_tamamlanir(): void
    {
        $body = ['steps' => [
            ['type' => 'google_search_call', 'arguments' => ['queries' => ['mutfak dolabı metre fiyatı']]],
            ['type' => 'model_output', 'content' => [['type' => 'text', 'text' => 'Sonuç: {"sources":[{"name":"Koçtaş","price_tl":"12.500 TL"},{"name":"Bauhaus","url":"","price_tl":"13.000,00"},{"name":"X","price_tl":"bilinmiyor"}],"note":""}',
                'annotations' => [['type' => 'url_citation', 'url' => 'https://a.example/1', 'title' => 'a.example'], ['type' => 'url_citation', 'url' => 'https://b.example/2', 'title' => 'b.example']]]]],
        ]];
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($body)]);
        $found = app(PriceRobot::class)->search(['q' => 'mutfak dolabı', 'unit' => 'mtül']);
        $this->assertSame([12500.0, 13000.0], array_column($found['sources'], 'price_tl'));
        $this->assertSame('https://a.example/1', $found['sources'][0]['url']);
    }

    public function test_banyo_kalemi_fiyat_defterine_yazar_ve_fiyat_tarihi_guncellenir(): void
    {
        $book = app(\App\Services\BathroomPriceBook::class);
        DB::table('unit_prices')->where('category', 'banyo_tadilati')->update(['source_date' => '2026-10-12']);   // migration ile gelen satırlar
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($this->gemini([24000, 26000, 25000]))]);
        $r = app(PriceRobot::class)->run($this->item('worksitem_bathroom.unit.fixtures'));
        $this->assertSame('live', $r['status']);
        $this->assertEquals(25000, DB::table('unit_prices')->where('key', 'banyo_vitrifiye_orta')->value('price'));
        $this->assertSame(now()->toDateString(), (string) DB::table('unit_prices')->where('key', 'banyo_vitrifiye_eko')->value('source_date'));
        $this->assertSame(now()->toDateString(), $book->overrides()['latest']);
    }

    public function test_arac_fiyat_tarihi_robot_arastirmasini_gosterir_ve_yazdir_basligi_var(): void
    {
        config(['worksitem_estimators.prices_reviewed' => '2026-10-01', 'worksitem_tools.tool_status.default' => 'active']);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($this->gemini([600, 640, 700]))]);
        app(PriceRobot::class)->run($this->item('worksitem_estimators.parke.material_m2.laminat'));
        $label = now()->locale('tr')->translatedFormat('j F Y');
        $this->assertSame($label, KitRegistry::get('parke-maliyet-hesaplayici')->pricesLabel());

        $html = $this->get('/araclar/parke-maliyet-hesaplayici')->assertOk()->getContent();
        $this->assertStringContainsString('class="ws-print-head"', $html);
        $this->assertStringContainsString('/brand/worksitem-logo.png', $html);
        $this->assertStringContainsString('Fiyat tarihi: '.$label, $html);
        $this->assertSame(1, substr_count($html, 'class="ws-print-head"'));
    }

    public function test_piyasa_karsilastirmasi_sapmayi_isaretler(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($this->gemini([20000, 25000, 30000]))]);
        $this->artisan('prices:compare', ['--tool' => 'boya-hesaplayici'])
            ->expectsOutputToContain('YÜKSEK')->assertSuccessful();   // bizim örnek ~49–69 bin, piyasa ortanca 25 bin
        $this->artisan('schedule:list')->expectsOutputToContain('prices:compare')->assertSuccessful();
    }
}

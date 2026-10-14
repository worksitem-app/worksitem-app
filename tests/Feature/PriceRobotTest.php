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

        return ['candidates' => [[
            'content' => ['parts' => [['text' => "```json\n".json_encode(['sources' => $sources, 'note' => 'test'])."\n```"]]],
            'groundingMetadata' => ['groundingChunks' => $grounded ? [['web' => ['uri' => 'https://ornek0.com.tr']]] : []],
        ]]];
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
        Http::assertSent(fn ($req) => str_contains($req->url(), ':generateContent') && isset($req['tools'][0]['google_search']));

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
}

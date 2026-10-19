<?php

namespace Tests\Feature;

use App\Services\Solar\SolarPlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SolarPlannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['worksitem_tools.tool_status.overrides' => ['ges-planlayici' => 'active', 'gunes-paneli-hesaplama' => 'active']]);
    }

    private function pvgis(float $perMonth = 120): array
    {
        return ['outputs' => ['monthly' => ['fixed' => array_map(fn ($m) => ['month' => $m, 'E_m' => $perMonth], range(1, 12))], 'totals' => ['fixed' => ['E_y' => $perMonth * 12]]]];
    }

    public function test_sayfa_acilir_ve_ges_aracindan_baglanti_var(): void
    {
        $this->get('/araclar/ges-planlayici')->assertOk()
            ->assertSee('GES planlayıcı', false)->assertSee('ges-geometry.js', false)->assertSee('2D uydu')->assertSee('3D model');
        $this->get('/araclar/gunes-paneli-hesaplama')->assertOk()->assertSee('/araclar/ges-planlayici', false);
    }

    public function test_alan_turf_grafik_chartjs_three_guncel(): void
    {
        $html = $this->get('/araclar/ges-planlayici')->assertOk()->getContent();
        $this->assertStringContainsString('@turf/area@', $html);
        $this->assertStringContainsString('@turf/length@', $html);
        $this->assertStringContainsString('chart.js@4', $html);
        $this->assertStringContainsString('three@0.186', $html);
        $this->assertStringNotContainsString('three@0.160', $html);
        $this->assertStringContainsString('areaM2(S.proj.pts)', $html);
        $this->assertStringContainsString('id="gpChartBox"', $html);
    }

    public function test_adres_arama_turkiye_ile_sinirli_ve_onbellekli(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['display_name' => 'Moda, Kadıköy, İstanbul', 'lat' => '40.98', 'lon' => '29.03']])]);
        $this->getJson('/araclar/ges-planlayici/adres?q=Moda Kadıköy')->assertOk()->assertJsonPath('results.0.lat', 40.98);
        $this->getJson('/araclar/ges-planlayici/adres?q=moda kadıköy')->assertOk();
        Http::assertSentCount(1);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'countrycodes=tr') && str_contains($r->header('User-Agent')[0] ?? '', 'Worksitem'));
    }

    public function test_bina_hatti_noktayi_iceren_yapi(): void
    {
        $ring = [['lat' => 40.0, 'lon' => 29.0], ['lat' => 40.0, 'lon' => 29.0003], ['lat' => 40.0002, 'lon' => 29.0003], ['lat' => 40.0002, 'lon' => 29.0], ['lat' => 40.0, 'lon' => 29.0]];
        Http::fake(['overpass.kumi.systems/*' => Http::response('', 504), 'overpass.private.coffee/*' => Http::response(['elements' => [['type' => 'way', 'tags' => ['building' => 'industrial', 'building:levels' => '2'], 'geometry' => $ring]]])]);
        $this->getJson('/araclar/ges-planlayici/bina?lat=40.0001&lon=29.0001')->assertOk()
            ->assertJsonPath('found', true)->assertJsonPath('levels', 2)->assertJsonPath('type', 'industrial')->assertJsonCount(4, 'ring');
        $this->getJson('/araclar/ges-planlayici/bina?lat=51.5&lon=0.1')->assertStatus(422);   // Türkiye dışı
    }

    public function test_uretim_pvgis_yuz_bazinda_ve_maliyet(): void
    {
        Http::fake(['re.jrc.ec.europa.eu/*' => Http::response($this->pvgis(120))]);
        $res = $this->postJson('/araclar/ges-planlayici/hesapla', [
            'lat' => 40.98, 'lon' => 29.03, 'use' => 'konut', 'flat' => false,
            'faces' => [['kwp' => 5.5, 'angle' => 30, 'azimuth' => 180], ['kwp' => 2.2, 'angle' => 30, 'azimuth' => 90]],
            'monthly_kwh' => 600, 'price' => 3.5,
        ])->assertOk()->assertJsonPath('ok', true)->assertJsonPath('kwp', 7.7)->assertJsonPath('yield.source', 'PVGIS');
        $this->assertEquals(round(7.7 * 120 * 12), $res->json('yield.yearly'));
        $this->assertCount(12, $res->json('yield.monthly'));
        $this->assertGreaterThan(0, $res->json('eco.total'));
        $this->assertNotNull($res->json('eco.payback'));
        // azimut → PVGIS aspect: güney 0, doğu -90
        Http::assertSent(fn ($r) => str_contains($r->url(), 'aspect=0') && str_contains($r->url(), 'angle=30') && str_contains($r->url(), 'mountingplace=building'));
        Http::assertSent(fn ($r) => str_contains($r->url(), 'aspect=-90'));
    }

    public function test_pvgis_yoksa_yaklasik_deger_ve_depolama_kalemi(): void
    {
        Http::fake(['re.jrc.ec.europa.eu/*' => Http::response('down', 529)]);
        $res = $this->postJson('/araclar/ges-planlayici/hesapla', [
            'lat' => 38.4, 'lon' => 27.1, 'use' => 'fabrika', 'flat' => true,
            'faces' => [['kwp' => 220, 'angle' => 15, 'azimuth' => 180]], 'monthly_kwh' => 40000, 'price' => 4.2, 'battery' => true,
        ])->assertOk()->assertJsonPath('yield.source', 'yaklaşık');
        $items = array_keys($res->json('eco.items'));
        $this->assertContains('Otomatik güç kontrolü ve enerji kesme sistemi', $items);
        $this->assertTrue(collect($items)->contains(fn ($k) => str_starts_with($k, 'Batarya')));
        $this->assertGreaterThan(1000, $res->json('specific'));
    }

    public function test_arazi_ges_yatirim_kalemleri_ve_serbest_montaj(): void
    {
        Http::fake(['re.jrc.ec.europa.eu/*' => Http::response($this->pvgis(130))]);
        $res = $this->postJson('/araclar/ges-planlayici/hesapla', [
            'lat' => 37.9, 'lon' => 32.5, 'use' => 'arazi', 'flat' => true, 'price' => 3.5,
            'faces' => [['kwp' => 1000, 'angle' => 29, 'azimuth' => 180]], 'site' => ['area' => 20000, 'perimeter' => 600],
        ])->assertOk()->assertJsonPath('ok', true);
        $items = array_keys($res->json('eco.items'));
        $this->assertTrue(collect($items)->contains(fn ($k) => str_starts_with($k, 'Çevre çiti')));
        $this->assertTrue(collect($items)->contains(fn ($k) => str_starts_with($k, 'Saha hazırlığı')));
        $this->assertNotNull($res->json('eco.payback'));                 // tüketim girilmese de üretim değeriyle
        Http::assertSent(fn ($r) => str_contains($r->url(), 'mountingplace=free'));
        $html = $this->get('/araclar/ges-planlayici')->getContent();
        $this->assertStringContainsString('value="arazi"', $html);
    }

    public function test_panel_yoksa_anlasilir_hata(): void
    {
        $this->postJson('/araclar/ges-planlayici/hesapla', ['lat' => 40, 'lon' => 29, 'use' => 'konut', 'faces' => [['kwp' => 0, 'angle' => 30, 'azimuth' => 180]]])
            ->assertStatus(422)->assertJsonPath('ok', false);
    }

    public function test_buyuk_sistemde_kwp_maliyeti_duser(): void
    {
        $s = app(SolarPlanService::class);
        $small = $s->economics(10, 'konut', false, 14000, 0, 3.5);
        $big = $s->economics(600, 'fabrika', false, 840000, 0, 3.5);
        $this->assertLessThan($small['total'] / 10, $big['total'] / 600);
    }
}

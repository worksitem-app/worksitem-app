<?php

namespace Tests\Feature;

use App\Services\Solar\SolarBom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v90 GES malzeme listesi — değerler elle hesaplandı (yorumlarda) */
class SolarBomTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['worksitem_tools.tool_status.overrides' => ['ges-planlayici' => 'active']]);
    }

    private function bom(): SolarBom
    {
        return app(SolarBom::class);
    }

    private function line(array $r, string $name): array
    {
        foreach ($r['lines'] as $l) {
            if ($l['name'] === $name) {
                return $l;
            }
        }
        $this->fail("Kalem yok: $name");
    }

    public function test_konut_kiremit_iki_yuzey_kalem_kalem(): void
    {
        $r = $this->bom()->build(
            [['panels' => 12, 'angle' => 30, 'azimuth' => 180, 'label' => 'Güney'], ['panels' => 6, 'angle' => 30, 'azimuth' => 90, 'label' => 'Doğu']],
            ['panel' => '550', 'use' => 'konut', 'roof' => 'besik', 'floors' => 2, 'phase' => 'auto', 'battery' => false]
        );
        $s = $r['system'];
        $this->assertSame(18, $s['panels']);
        $this->assertEquals(9.9, $s['dc_kwp']);                         // 18 × 550 W
        // inverter: 9,9 ÷ 1,2 = 8,25 kW → 8'den büyük ilk standart = 10 kW; 5 kW üstü → trifaze
        $this->assertSame(['kw' => 10, 'count' => 1, 'phase' => 'tri'], array_intersect_key($s['inverter'], array_flip(['kw', 'count', 'phase'])));
        // dizi sınırları (trifaze 200–1000 V): en çok ⌊1000 ÷ (49,6 × 1,13)⌋ = 17, en az ⌈200 ÷ (41,7 × 0,85)⌉ = 6
        $this->assertSame(['min' => 6, 'max' => 17], $s['string_limits']);
        $this->assertSame([12], $s['strings'][0]['lengths']);
        $this->assertSame([6], $s['strings'][1]['lengths']);
        $this->assertSame(2, $s['mppt_need']);

        $this->assertEquals(103950, $this->line($r, 'Güneş paneli')['total']);           // 18 × 550 × 10,5
        $this->assertEquals(47000, $this->line($r, 'Şebeke bağlantılı inverter')['total']);
        $this->assertEquals(34200, $this->line($r, 'Taşıyıcı sistem')['total']);         // 18 × 1.900
        $this->assertEquals(102, $this->line($r, 'Solar DC kablo')['qty']);              // 2 dizi × 2 × (2 kat × 3 + 15) + 18
        $this->assertEquals(9000, $this->line($r, 'DC koruma')['total']);                // 2 MPPT × 4.500
        $this->assertEquals(5490, $this->line($r, 'Topraklama ve eş potansiyel')['total']); // 4.000 + 9,9 × 150
        $this->assertEquals(34270, $this->line($r, 'Montaj işçiliği')['total']);         // 18 × 1.700 × 1,12
        $this->assertSame('NYY 5×6 mm² (inverter → sayaç)', $this->line($r, 'AC kablo')['spec']);   // 10 kW ÷ (√3 × 400) ≈ 14,4 A
        $this->assertEquals(273700, $r['subtotal']);
        $this->assertEquals(13700, $r['contingency']);                                   // %5
        $this->assertEquals(287000, $r['total']);
        $this->assertEquals(array_sum(array_column($r['lines'], 'total')), array_sum(array_map(fn ($l) => $l['total'], $r['lines'])));
    }

    public function test_kucuk_konut_monofaze_ve_faz_uyarisi(): void
    {
        $r = $this->bom()->build([['panels' => 8, 'angle' => 30, 'azimuth' => 180]], ['panel' => '550', 'use' => 'konut', 'roof' => 'kirma', 'floors' => 1, 'phase' => 'auto', 'battery' => false]);
        $this->assertSame('mono', $r['system']['inverter']['phase']);   // 4,4 ÷ 1,2 = 3,7 → 5 kW monofaze
        $this->assertSame(5, $r['system']['inverter']['kw']);
        $this->assertSame('NYY 3×6 mm² (inverter → sayaç)', $this->line($r, 'AC kablo')['spec']);

        $big = $this->bom()->build([['panels' => 20, 'angle' => 30, 'azimuth' => 180]], ['panel' => '550', 'use' => 'konut', 'roof' => 'besik', 'floors' => 1, 'phase' => 'mono', 'battery' => false]);
        $this->assertSame('tri', $big['system']['inverter']['phase']);
        $this->assertStringContainsString('trifaze bağlantı gerekir', implode(' ', $big['notes']));
    }

    public function test_cephe_iskele_erisim_ve_batarya(): void
    {
        $r = $this->bom()->build(
            [['panels' => 16, 'angle' => 30, 'azimuth' => 180], ['panels' => 10, 'angle' => 90, 'azimuth' => 180, 'kind' => 'facade', 'label' => 'Güney cephe', 'area' => 30]],
            ['panel' => '550', 'use' => 'konut', 'roof' => 'besik', 'floors' => 4, 'phase' => 'auto', 'battery' => true, 'monthly_kwh' => 600]
        );
        $this->assertSame(10, $r['system']['panels_facade']);
        $this->assertEquals(35000, $this->line($r, 'Cephe taşıyıcı sistemi')['total']);      // 10 × 3.500
        $this->assertEquals(25500, $this->line($r, 'Cephe montaj işçiliği')['total']);       // 10 × 1.700 × 1,5
        $this->assertEquals(5400, $this->line($r, 'Cephe iskelesi')['total']);               // 30 m² × 180
        $this->assertEquals(15000, $this->line($r, 'Çatıya erişim')['total']);               // 4 kat ≥ 3
        // batarya: 600 kWh/ay ÷ 30 × 0,5 = 10 kWh → 5,12'lik modüllerle 10,24 kWh
        $this->assertEquals(10.24, $this->line($r, 'Lityum batarya')['qty']);
        $this->assertSame('Hibrit inverter', collect($r['lines'])->firstWhere('group', 'İnverter')['name']);
        $this->assertEquals(80000, $this->line($r, 'Hibrit inverter')['unit_price']);          // 12 kW trifaze 50.000 × 1,6
    }

    public function test_az_panelli_yuzey_ve_buyuk_sistem_coklu_inverter(): void
    {
        $r = $this->bom()->build([['panels' => 14, 'angle' => 30, 'azimuth' => 180], ['panels' => 3, 'angle' => 30, 'azimuth' => 270, 'label' => 'Batı']], ['panel' => '550', 'use' => 'konut', 'roof' => 'besik', 'floors' => 2, 'phase' => 'auto', 'battery' => false]);
        $this->assertTrue($r['system']['strings'][1]['short']);
        $this->assertStringContainsString('Batı: 3 panel tek dizi için az', implode(' ', $r['notes']));

        $f = $this->bom()->build([['panels' => 400, 'angle' => 8, 'azimuth' => 170]], ['panel' => '610', 'use' => 'fabrika', 'roof' => 'trapez', 'floors' => 1, 'phase' => 'auto', 'battery' => false]);
        // 244 kWp ÷ 1,2 = 203 kW → 3 × 67,8 → 80 kW
        $this->assertSame(['kw' => 80, 'count' => 3], array_intersect_key($f['system']['inverter'], array_flip(['kw', 'count'])));
        $this->assertSame(16, $f['system']['string_limits']['max']);                     // ⌊1000 ÷ (53,9 × 1,13)⌋
        $this->assertSame(400, array_sum(array_merge(...array_column($f['system']['strings'], 'lengths'))));
    }

    public function test_onayli_admin_fiyati_kullanilir_onaysiz_kullanilmaz(): void
    {
        $this->assertDatabaseHas('unit_prices', ['key' => 'ges_panel_w', 'category' => 'ges', 'review_status' => 'needs_review']);
        $this->assertSame(10.5, $this->bom()->price('ges_panel_w'));

        DB::table('unit_prices')->where('key', 'ges_panel_w')->update(['price' => 12, 'review_status' => 'approved']);
        $this->assertSame(12.0, (new SolarBom)->price('ges_panel_w'));
        $this->assertSame(count(config('worksitem_solar.bom.prices')), DB::table('unit_prices')->where('category', 'ges')->count());
        $this->assertSame('10 kW trifaze şebeke bağlantılı inverter', DB::table('unit_prices')->where('key', 'ges_inv_tri_10')->value('name'));
    }

    public function test_sayfada_cephe_faz_ve_malzeme_tablosu(): void
    {
        $html = $this->get('/araclar/ges-planlayici')->assertOk()->getContent();
        foreach (['id="gpFacadeOn"', 'class="gp-frow" data-row="1"', 'class="gp-frow" data-row="2"', 'id="gpPhase"', 'value="tri"', 'function bomHtml', 'gp-bomt', "kind: 'facade'"] as $x) {
            $this->assertStringContainsString($x, $html);
        }
    }

    public function test_hesapla_ucu_malzeme_listesini_dondurur(): void
    {
        Http::fake(['re.jrc.ec.europa.eu/*' => Http::response(['outputs' => ['monthly' => ['fixed' => array_map(fn ($m) => ['month' => $m, 'E_m' => 120], range(1, 12))], 'totals' => ['fixed' => ['E_y' => 1440]]]])]);
        $res = $this->postJson('/araclar/ges-planlayici/hesapla', [
            'lat' => 37.78, 'lon' => 29.09, 'use' => 'konut', 'flat' => false, 'panel' => '550', 'roof' => 'besik', 'floors' => 2, 'phase' => 'auto',
            'faces' => [
                ['kwp' => 6.6, 'angle' => 30, 'azimuth' => 180, 'panels' => 12, 'kind' => 'roof', 'label' => '1. Güney'],
                ['kwp' => 3.3, 'angle' => 90, 'azimuth' => 180, 'panels' => 6, 'kind' => 'facade', 'label' => 'Güney cephe', 'area' => 18],
            ],
            'monthly_kwh' => 600, 'price' => 3.5,
        ])->assertOk()->assertJsonPath('ok', true)->assertJsonPath('bom.ok', true)->assertJsonPath('bom.system.panels_facade', 6);
        $this->assertEquals($res->json('bom.total'), $res->json('eco.total'));
        $this->assertArrayHasKey('Panel', $res->json('eco.items'));
        // eski istemci (panel sayısı göndermeyen) eski hesapla devam eder
        $this->postJson('/araclar/ges-planlayici/hesapla', ['lat' => 37.78, 'lon' => 29.09, 'use' => 'konut', 'faces' => [['kwp' => 5, 'angle' => 30, 'azimuth' => 180]]])
            ->assertOk()->assertJsonPath('bom', null);
    }
}

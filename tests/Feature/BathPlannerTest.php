<?php

namespace Tests\Feature;

use App\Models\PlannerProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BathPlannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_tools.tool_status.overrides.banyo-planlayici' => 'active']);
    }

    private function cfg(string $html): array
    {
        $this->assertSame(1, preg_match('#<script type="application/json" id="plCfg">(.*?)</script>#s', $html, $m));

        return json_decode($m[1], true);
    }

    public function test_banyo_hesabindan_gelen_olculer_plani_doldurur(): void
    {
        $html = $this->get('/araclar/banyo-planlayici?bathType=standart&width=1.8&length=2.4&height=2.6&shower=kuvet&ex_dolap=1&ex_havlupan=1&quality=orta')
            ->assertOk()->assertSee('2D / 3D banyo planlayıcı')->getContent();
        $c = $this->cfg($html);
        $this->assertSame(['w' => 180, 'l' => 240, 'h' => 260, 'shower' => 'kuvet', 'dolap' => true, 'havlupan' => true, 'rezervuar' => false, 'fromCalc' => true], $c['initial']);
        $this->assertStringContainsString('width=1.8', $c['back']);
        $this->assertStringContainsString('quality=orta', $c['back']);
        $this->assertArrayHasKey('carrara-beyaz', $c['tiles']);
        $this->assertSame(60, $c['tiles']['carrara-beyaz']['tw']);
        $this->assertStringContainsString('"importmap"', $html);
    }

    public function test_misafir_wc_dusuz_ve_olculer_sinirlanir(): void
    {
        $c = $this->cfg($this->get('/araclar/banyo-planlayici?bathType=wc&width=0.3&length=12&height=9')->assertOk()->getContent());
        $this->assertNull($c['initial']['shower']);
        $this->assertSame(80, $c['initial']['w']);
        $this->assertSame(1000, $c['initial']['l']);
        $this->assertSame(400, $c['initial']['h']);
    }

    public function test_dogrudan_acilinca_varsayilan_banyo(): void
    {
        $c = $this->cfg($this->get('/araclar/banyo-planlayici')->assertOk()->getContent());
        $this->assertSame(180, $c['initial']['w']);
        $this->assertSame('kabin', $c['initial']['shower']);
        foreach ($c['products'] as $type => $list) {
            $this->assertNotEmpty($list, $type);
            $this->assertArrayHasKey($type, $c['types']);
        }
    }

    public function test_banyo_sayfasinda_planlayici_dugmesi_var(): void
    {
        config(['worksitem_tools.tool_status.overrides.banyo-maliyet-hesaplayici' => 'active']);
        $this->get('/araclar/banyo-maliyet-hesaplayici')->assertOk()
            ->assertSee('id="planBtn"', false)->assertSee('Banyomu 2D / 3D gör')->assertSee('ws_bath_plan', false);
    }

    public function test_sponsor_urunu_planlayicida_once_gelir_ve_tiklama_sayilir(): void
    {
        $p = PlannerProduct::query()->create(['type' => 'lavabo', 'name' => 'Marka lavabo 100', 'brand' => 'Marka', 'width' => 100, 'depth' => 46, 'height' => 85,
            'width_min' => 80, 'width_max' => 120, 'cabinet_color' => '#333333', 'product_url' => 'https://example.com/l', 'price_min' => 10000, 'price_max' => 12000]);
        PlannerProduct::query()->create(['type' => 'klozet', 'name' => 'Kapalı', 'brand' => 'X', 'width' => 38, 'depth' => 55, 'height' => 40, 'active' => false]);

        $c = $this->cfg($this->get('/araclar/banyo-planlayici')->assertOk()->getContent());
        $first = $c['products']['lavabo'][0];
        $this->assertSame('db-'.$p->id, $first['id']);
        $this->assertSame('Marka', $first['sponsor']);
        $this->assertSame([80, 120], $first['wr']);
        $this->assertSame([10000, 12000], $first['price']);
        $this->assertSame('klozet-asma', $c['products']['klozet'][0]['id']);   // yayında olmayan gizli
        $this->assertArrayHasKey('icduvar', $c['products']);
        $this->assertSame(1, $p->fresh()->impressions);

        $this->get('/planlayici/urun/'.$p->id)->assertRedirectContains('https://example.com/l?utm_source=worksitem');
        $this->assertSame(1, $p->fresh()->clicks);
    }

    public function test_admin_planlayici_urunleri_sayfasi_acilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/planner-products')->assertOk()->assertSee('Planlayıcı ürünleri');
        $this->actingAs($admin)->get('/admin/planner-products/create')->assertOk();
    }

    public function test_planlayicidan_gelen_marka_fiyati_banyo_hesabinda_gosterilir(): void
    {
        config(['worksitem_tools.tool_status.overrides.banyo-maliyet-hesaplayici' => 'active']);
        $this->get('/araclar/banyo-maliyet-hesaplayici?hesapla=1&width=2.4&length=2.2&plan_low=18500&plan_high=24000&plan_n=1')
            ->assertOk()->assertSee('id="bPlan"', false)->assertSee('plan_low', false);
        $c = $this->cfg($this->get('/araclar/banyo-planlayici?bathType=standart&scope=komple&quality=orta&evil=<x>')->getContent());
        $this->assertSame(['bathType' => 'standart', 'scope' => 'komple', 'quality' => 'orta'], $c['query']);
    }
}

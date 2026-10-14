<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitchenPlannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_tools.tool_status.default' => 'active']);
    }

    public function test_mutfak_planlayici_mutfak_parcalariyla_acilir(): void
    {
        $html = $this->get('/araclar/mutfak-planlayici?run=4.2&boy_dolabi=1')->assertOk()
            ->assertSee('2D / 3D mutfak planlayıcı')
            ->assertSee('Mutfağınız için markalar')
            ->getContent();
        $this->assertStringContainsString('<title>Mutfak Planlama', $html);
        preg_match('/<script type="application\/json" id="plCfg">(.*?)<\/script>/s', $html, $m);
        $cfg = json_decode(html_entity_decode($m[1] ?? '{}'), true) ?: json_decode($m[1] ?? '{}', true);
        if ($cfg) {
            $this->assertArrayHasKey('eviye', $cfg['types']);
            $this->assertArrayNotHasKey('kabin', $cfg['types']);
            $this->assertSame(420, $cfg['initial']['run']);
            $this->assertTrue($cfg['initial']['boy']);
            $this->assertSame('mutfak', $cfg['variant']);
        }
        $this->assertStringContainsString('/araclar/mutfak-maliyet-hesaplayici', $html);
    }

    public function test_banyo_planlayicida_mutfak_parcasi_yok_ve_mutfak_hesabinda_plan_dugmesi_var(): void
    {
        $html = $this->get('/araclar/banyo-planlayici')->assertOk()->getContent();
        $this->assertStringNotContainsString('"eviye"', $html);
        $this->get('/araclar/mutfak-maliyet-hesaplayici')->assertOk()->assertSee('Mutfağımı 2D / 3D gör')->assertSee('/araclar/mutfak-planlayici', false);
    }

    public function test_urun_renk_model_secenekleri_planlayiciya_gelir(): void
    {
        $p = \App\Models\PlannerProduct::query()->create([
            'type' => 'havlupan', 'name' => 'Havlupan Pro', 'brand' => 'Örnek Marka', 'width' => 50, 'depth' => 8, 'height' => 80, 'active' => true,
            'variants' => [['name' => 'Mat siyah', 'color' => '#111111', 'price_min' => 3000, 'price_max' => 3500], ['name' => ''], ['name' => 'Krom', 'color' => '#DDDDDD']],
        ]);
        $v = $p->toPlanner()['variants'];
        $this->assertCount(2, $v);
        $this->assertSame(['id' => 'v0', 'name' => 'Mat siyah', 'color' => '#111111', 'price' => [3000, 3500]], $v[0]);

        preg_match('#<script type="application/json" id="plCfg">(.*?)</script>#s', $this->get('/araclar/banyo-planlayici')->getContent(), $m);
        $cfg = json_decode($m[1], true);
        $this->assertSame('Örnek Marka', $cfg['products']['havlupan'][0]['sponsor']);
        $this->assertNotEmpty($cfg['products']['havlupan'][0]['variants']);
        $this->assertNotEmpty(collect($cfg['products']['lavabo'])->firstWhere('id', 'lavabo-dolap-80')['variants']);
    }
}

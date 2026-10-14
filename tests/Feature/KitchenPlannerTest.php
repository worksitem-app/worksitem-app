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
}

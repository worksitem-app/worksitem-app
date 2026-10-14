<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolPageEnhancerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_tools.tool_status.default' => 'active']);
    }

    public function test_eski_sablonlu_arac_yeni_baslik_sema_ve_marka_bandi_alir(): void
    {
        $html = $this->get('/araclar/kolon-filiz-boyu')->assertOk()->getContent();
        $this->assertStringContainsString('<title>Kolon Filiz Boyu Hesaplama: Bindirme ve Kenetlenme Boyu</title>', $html);
        $this->assertStringNotContainsString('| WORKSİTEM</title>', $html);
        $this->assertStringContainsString('"WebApplication"', $html);
        $this->assertSame(1, substr_count($html, 'class="ws-bc"'));
        $this->assertStringContainsString('/markalar-icin?arac=kolon-filiz-boyu', $html);
        $this->assertStringContainsString('content="Kolon Filiz Boyu Hesaplama: Bindirme ve Kenetlenme Boyu"', $html);   // og:title
    }

    public function test_yeni_sablonda_cift_bant_ve_sema_eklenmez(): void
    {
        $html = $this->get('/araclar/mantolama-maliyeti')->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, 'class="ws-bc"'));
        $this->assertSame(1, substr_count($html, '"WebApplication"'));
    }

    public function test_tum_kalan_araclar_acilir_ve_baslik_60_karakteri_gecmez(): void
    {
        foreach (config('worksitem_golive.presets.ikinci-dalga') as $slug) {
            $html = $this->get('/araclar/'.$slug)->assertOk()->getContent();
            preg_match('/<title>(.*?)<\/title>/s', $html, $t);
            $this->assertLessThanOrEqual(62, mb_strlen(html_entity_decode(trim($t[1] ?? ''))), $slug);
            $this->assertStringContainsString('class="ws-bc"', $html, $slug);
            $this->assertStringNotContainsString('<small>Yakında</small>', $html, $slug);
        }
        $this->assertCount(48, config('worksitem_golive.presets.ikinci-dalga'));
    }
}

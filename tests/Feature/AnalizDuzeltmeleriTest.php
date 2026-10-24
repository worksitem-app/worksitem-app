<?php

namespace Tests\Feature;

use App\Http\Middleware\BrandShell;
use App\Support\Tools;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v105: UX/UI analizindeki hızlı düzeltmeler */
class AnalizDuzeltmeleriTest extends TestCase
{
    use RefreshDatabase;

    public function test_arac_sayisi_tek_kaynaktan(): void
    {
        config(['worksitem_tools.tool_status.default' => 'active']);
        $n = Tools::activeCount();
        $this->assertSame((string) $n, Tools::label());
        $this->assertGreaterThan(70, $n);
        $home = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString("$n ücretsiz araç, üyelik yok.", $home);
        $this->assertStringContainsString("$n araç tek yerde", $home);
        $this->assertStringNotContainsString("70'ten fazla", $home);
        $this->assertStringContainsString("$n araç ve resmî pozlar içinde ara", $home);

        config(['worksitem_tools.tool_status.default' => 'review', 'worksitem_tools.tool_status.overrides' => ['boya-hesaplayici' => 'active']]);
        $this->assertSame('Onlarca', Tools::label());
    }

    public function test_inceleme_penceresi_ic_dil_kullanmaz(): void
    {
        config(['worksitem_tools.tool_status.default' => 'review', 'worksitem_tools.tool_status.overrides' => []]);
        $html = $this->get('/araclar/cimento-kum-hesaplama')->getContent();
        $this->assertStringNotContainsString('SEO', strip_tags(substr($html, (int) strpos($html, 'wsQaGate'))));
        $this->assertStringContainsString('Bu araç yakında açılıyor', $html);
    }

    public function test_eski_araclarda_etiketler_alana_baglanir(): void
    {
        $out = BrandShell::linkLabels('<div><label>Alan (m²)</label><input type="number" id="area"></div><label class="o"><input type="radio" id="r1"> Eko</label><label>Gizli</label><input type="hidden" id="h">');
        $this->assertStringContainsString('<label for="area">Alan (m²)</label>', $out);
        $this->assertStringContainsString('<label class="o"><input type="radio" id="r1"> Eko</label>', $out);   // saran etiket bozulmaz
        $this->assertStringContainsString('<label>Gizli</label>', $out);

        config(['worksitem_tools.tool_status.default' => 'active']);
        $this->get('/araclar/is-makinesi-maliyeti')->assertOk()->assertSee('<label for="days">', false);
    }

    public function test_kesif_bedeli_acik_yazilir(): void
    {
        $this->get('/')->assertOk()->assertDontSee('ücretli olabilir')->assertSee(\App\Support\InspectionOffer::feeLabel());
    }
}

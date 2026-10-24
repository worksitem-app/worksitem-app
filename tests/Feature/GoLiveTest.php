<?php

namespace Tests\Feature;

use App\Models\ToolStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoLiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_on_ayarlardaki_tum_araclar_tanimli_ve_sayfalari_var(): void
    {
        $tools = (array) config('worksitem_tools.tools');
        foreach ((array) config('worksitem_golive.presets') as $name => $slugs) {
            $this->assertNotEmpty($slugs, $name);
            $this->assertSame(count($slugs), count(array_unique($slugs)), "$name tekrar eden araç");
            foreach ($slugs as $slug) {
                $this->assertArrayHasKey($slug, $tools, "$name: $slug worksitem_tools içinde yok");
            }
        }
        foreach (config('worksitem_golive.presets.ilk-dalga') as $slug) {
            $this->get('/araclar/'.$slug)->assertOk();
        }
    }

    public function test_deneme_modu_hicbir_seyi_degistirmez(): void
    {
        $this->artisan('tools:publish', ['preset' => 'ilk-dalga', '--dry-run' => true])->assertSuccessful();
        $this->assertFalse((bool) ToolStatus::query()->where('slug', 'mutfak-maliyet-hesaplayici')->value('active'));
    }

    public function test_on_ayar_araclari_yayina_alir_digerlerine_dokunmaz(): void
    {
        ToolStatus::syncFromConfig();
        ToolStatus::query()->where('slug', 'cati-maliyet-hesaplayici')->update(['active' => true]);

        $this->artisan('tools:publish', ['preset' => 'ilk-dalga'])->assertSuccessful();

        foreach (config('worksitem_golive.presets.ilk-dalga') as $slug) {
            $this->assertTrue((bool) ToolStatus::query()->where('slug', $slug)->value('active'), $slug);
        }
        $this->assertTrue((bool) ToolStatus::query()->where('slug', 'cati-maliyet-hesaplayici')->value('active'));
        $this->assertSame('tools:publish ilk-dalga', ToolStatus::query()->where('slug', 'mutfak-maliyet-hesaplayici')->value('note'));
    }

    public function test_only_secenegi_listede_olmayanlari_incelemeye_alir(): void
    {
        ToolStatus::syncFromConfig();
        ToolStatus::query()->where('slug', 'statik-proje-bedeli')->update(['active' => true]);

        $this->artisan('tools:publish', ['preset' => 'ilk-dalga', '--only' => true])->assertSuccessful();

        $this->assertFalse((bool) ToolStatus::query()->where('slug', 'statik-proje-bedeli')->value('active'));
        $this->assertSame(count(config('worksitem_golive.presets.ilk-dalga')), ToolStatus::query()->where('active', true)->count());
    }

    public function test_bilinmeyen_on_ayar_hata_verir_ve_liste_calisir(): void
    {
        $this->artisan('tools:publish', ['preset' => 'yok-boyle'])->assertFailed();
        $this->artisan('tools:publish', ['--list' => true])->expectsOutputToContain('ilk-dalga')->assertSuccessful();
    }

    public function test_hesap_motoru_stili_sayfaya_gomulu_gelir(): void
    {
        // Sunucu /css klasörünü farklı yerden sunsa da tasarım bozulmasın (banyo sayfası gibi)
        $html = $this->get('/araclar/kalip-plywood-maliyeti')->assertOk()->getContent();
        // v119: araç düzeni wk-tool.css gömülü gelir (bileşenler wk-ui.css, sürümlü bağlantıyla)
        $this->assertMatchesRegularExpression('#<style data-asset="wk-tool.css">\s*/\*[^<]*\*/\s*\.wk-root#', $html);
        $this->assertStringNotContainsString('href="/css/wk-tool.css', $html);
    }
}

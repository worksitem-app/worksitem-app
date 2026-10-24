<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v118: ana sayfa yeni tasarım dilinde; hesaplayıcının bütün bağlantı noktaları korunur */
class AnaSayfaV118Test extends TestCase
{
    use RefreshDatabase;

    public function test_ana_sayfa_tasarim_dilinde_ve_eski_stiller_yok(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('<body class="wk-root"', $html);
        $this->assertMatchesRegularExpression('#/css/wk-ui\.css\?v=\w+#', $html, 'bileşen stili yüklenir');
        $this->assertStringNotContainsString('/css/worksitem-design.css', $html);
        $this->assertStringNotContainsString('ÖN BÜTÇEYİ OLUŞTUR', $html);
        $this->assertSame(1, substr_count($html, '<h1'), 'tek h1');
        $this->assertStringContainsString('class="wk-display"', $html);
        foreach (['🏗️', '🛠️', '🏙️', '📄'] as $emoji) {
            $this->assertStringNotContainsString($emoji, $html, 'simgeler SVG');
        }
    }

    public function test_hesaplayici_baglanti_noktalari_korunur(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        foreach (['proje-hesaplayici', 'estSubmit', 'estFile', 'estArea', 'estCity', 'estDistrict', 'estQuality', 'estQualityFields',
            'estBuildingCategory', 'estBuildingType', 'estRenovationScope', 'estAiResult', 'estBudgetGate', 'estLeadForm',
            'estSubmitBtn', 'chooseRenewal', 'fiyatlama', 'sss', 'araclar', 'kentsel-donusum'] as $id) {
            $this->assertSame(1, substr_count($html, 'id="'.$id.'"'), $id);
        }
        $this->assertSame(3, substr_count($html, 'class="est-mode'), 'üç mod');
        $this->assertSame(4, substr_count($html, 'type="radio" name="estQualityPick"'), 'kalite segmenti');
        $this->assertStringContainsString("sel.dispatchEvent(new Event('change'", $html, 'segment gizli seçimi günceller');
        $this->assertStringContainsString('name="price_version"', $html);
        $this->assertStringContainsString('name="contact_consent"', $html);
    }

    public function test_one_cikan_araclar_yalniz_yayindakiler_ve_pozlar_var(): void
    {
        config(['worksitem_tools.tool_status.default' => 'active']);
        $html = $this->get('/')->assertOk()->getContent();
        $tools = substr($html, strpos($html, 'class="wk-tools wk-home-tools"'), 6000);
        $this->assertStringContainsString('href="/pozlar"', $tools);
        $this->assertStringContainsString('href="/araclar/banyo-maliyet-hesaplayici"', $tools);
        $this->assertLessThanOrEqual(8, substr_count($tools, 'class="wk-toolcard"'));

        config(['worksitem_tools.tool_status.default' => 'review', 'worksitem_tools.tool_status.overrides' => []]);
        $html = $this->get('/')->assertOk()->getContent();
        $tools = substr($html, strpos($html, 'class="wk-tools wk-home-tools"'), 6000);
        $this->assertStringNotContainsString('href="/araclar/banyo-maliyet-hesaplayici"', $tools, 'incelemedeki araca götürmez');
        $this->assertStringContainsString('href="/pozlar"', $tools);
    }

    public function test_pilot_bilgileri_ayardan_gelir(): void
    {
        config(['worksitem_pilot.enabled' => true, 'worksitem_inspection.report_days' => 5]);
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('5 iş günü', $html);
        $this->assertStringContainsString(\App\Support\InspectionOffer::feeLabel(), $html);
        $this->assertStringContainsString(\App\Support\InspectionOffer::creditDays().' gün içinde', $html);
        $this->assertStringContainsString('Hesapladığınız işi tek elden yaptırın.', $html);

        config(['worksitem_pilot.enabled' => false]);
        $this->assertStringNotContainsString('Hesapladığınız işi tek elden yaptırın.', $this->get('/')->getContent());
    }

    public function test_ortak_bilesenler_token_kullanir(): void
    {
        foreach (['components/site/why.blade.php', 'components/site/audience-band.blade.php'] as $f) {
            $src = file_get_contents(resource_path('views/'.$f));
            $css = substr($src, strpos($src, '<style>'));
            $this->assertStringContainsString('var(--wk-ink)', $css, $f);
            $this->assertDoesNotMatchRegularExpression('/#[0-9a-fA-F]{6}\b/', $css, "$f: sabit renk yok (tema token'ı)");
        }
    }
}

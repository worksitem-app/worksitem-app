<?php

namespace Tests\Feature;

use App\Support\Tools;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v110: UX/UI/ön yüz analizi — AI kapalıyken vaat yok, doğru fiyat iddiası, CTA hedefleri, huni ölçümü */
class UxAnalizV110Test extends TestCase
{
    use RefreshDatabase;

    private const AI = 'otomatik-kesif-fiyatlandirma';

    public function test_ai_acik_sayilmasi_icin_kilit_ve_arac_yayini_birlikte_gerekir(): void
    {
        config(['services.ai_spend_enabled' => false, 'worksitem_tools.tool_status.overrides' => [self::AI => 'active']]);
        $this->assertFalse(Tools::aiFileOn());

        config(['services.ai_spend_enabled' => true, 'worksitem_tools.tool_status.overrides' => [self::AI => 'review'], 'worksitem_tools.tool_status.default' => 'review']);
        $this->assertFalse(Tools::aiFileOn());

        config(['worksitem_tools.tool_status.overrides' => [self::AI => 'active']]);
        $this->assertTrue(Tools::aiFileOn());
    }

    public function test_ai_kapaliyken_ana_sayfa_ve_alt_bilgi_dosya_analizi_vaat_etmez(): void
    {
        config(['services.ai_spend_enabled' => false]);
        $home = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('href="/araclar/otomatik-kesif-fiyatlandirma"', $home);
        $this->assertStringNotContainsString('dosya ile AI ön keşif', $home);
        $this->assertStringNotContainsString('Hangi proje dosyalarını yükleyebilirim?', $home);
        $this->assertStringNotContainsString('<h3>Dosyadan AI ön keşif</h3>', $home);
        $this->assertStringNotContainsString('<small>Dosyalı analiz</small>', $home);
        $this->assertStringNotContainsString('<li>AI ön keşif</li>', $home);
        $this->assertMatchesRegularExpression('/id="estAiUpload"\s+hidden/', $home);
        $this->assertMatchesRegularExpression('/id="estFile"[^>]*\sdisabled/', $home);
        $this->assertStringContainsString('dosyadan iş kalemi analizi yakında açılacak', $home);
        // JSON-LD SSS bozulmadan kalır
        preg_match('/<script type="application\/ld\+json">\s*(\{[^<]*"FAQPage".*?)<\/script>/s', $home, $m);
        $this->assertNotEmpty($m, 'FAQPage şeması bulunamadı');
        $this->assertNotNull(json_decode($m[1], true), 'FAQPage şeması geçerli JSON değil');
    }

    public function test_ai_acikken_ana_sayfa_dosya_analizini_gosterir(): void
    {
        config(['services.ai_spend_enabled' => true, 'worksitem_tools.tool_status.overrides' => [self::AI => 'active']]);
        $home = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('href="/araclar/otomatik-kesif-fiyatlandirma"', $home);
        $this->assertStringContainsString('Hangi proje dosyalarını yükleyebilirim?', $home);
        $this->assertDoesNotMatchRegularExpression('/id="estAiUpload"\s+hidden/', $home);
        preg_match('/<script type="application\/ld\+json">\s*(\{[^<]*"FAQPage".*?)<\/script>/s', $home, $m);
        $this->assertNotNull(json_decode($m[1] ?? '', true), 'FAQPage şeması geçerli JSON değil');
    }

    public function test_olculmemis_fiyat_iddiasi_ve_celisen_metinler_yok(): void
    {
        $home = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('her sabah güncellenir', $home);
        $this->assertStringNotContainsString('genel konum katsayılarıyla', $home);
        $this->assertStringNotContainsString('fatura aynı firmadan', $home);
        $this->assertStringContainsString('fiyat tarihi', $home);
        $this->assertStringContainsString('fiyatı değiştirmez', $home);
    }

    public function test_cta_etiketleri_hedefleriyle_eslesir(): void
    {
        config(['worksitem_pilot.enabled' => true, 'worksitem_pilot.firm_signup' => false]);
        $home = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('href="/#proje-hesaplayici">Ücretsiz hesapla →', $home);
        $this->assertStringContainsString('href="/maliyet-hesapla#calculator">Hesapla, keşif iste →', $home);
        $this->assertStringNotContainsString('href="/araclar">Ücretsiz hesapla', $home);
    }

    public function test_huni_olaylari_ve_sss_erisilebilirligi(): void
    {
        $home = $this->get('/')->assertOk()->getContent();
        foreach (['estimator_start', 'estimator_result', 'estimator_lead_open', 'estimator_lead_submit', 'estimator_abandon'] as $e) {
            $this->assertStringContainsString("'$e'", $home);
        }
        $this->assertStringContainsString('window.wsTrack', $home);
        $this->assertStringContainsString('class="faq-q" type="button" aria-expanded="false"', $home);
    }

    public function test_ai_araci_ic_jargon_kullanmaz(): void
    {
        config(['worksitem_tools.tool_status.overrides' => [self::AI => 'active']]);
        $html = $this->get('/araclar/'.self::AI)->getContent();
        $this->assertStringNotContainsString('Auto-BOQ (', $html);
        $this->assertStringNotContainsString('v3.0', $html);
        $this->assertStringNotContainsString('Gemini Vision', $html);
        $this->assertStringNotContainsString('>Renovasyon<', $html);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * KVKK: analitik/pazarlama etiketleri onaysız yüklenmemeli.
 */
class CookieConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ga4_onaysiz_yuklenmez_ve_onay_bandi_basilir(): void
    {
        config(['services.ga4.measurement_id' => 'G-TEST12345']);

        $html = $this->get('/legal/cerez-politikasi')->assertOk()->getContent();

        $this->assertStringContainsString('id="ws-cookie-banner"', $html);
        $this->assertStringContainsString('window.openCookieSettings', $html);
        // gtag.js statik <script src> olarak gömülmemeli; yalnızca onay sonrası JS ile eklenir.
        $this->assertDoesNotMatchRegularExpression('#<script[^>]+src="https://www\.googletagmanager\.com/gtag#i', $html);
        $this->assertSame(1, substr_count($html, 'id="ws-cookie-banner"'));
    }

    public function test_izleyici_yoksa_bant_basilmaz(): void
    {
        config([
            'services.ga4.measurement_id' => '',
            'lead_conversion.google_ads.conversion_id' => null,
        ]);

        $this->get('/legal/cerez-politikasi')
            ->assertOk()
            ->assertDontSee('ws-cookie-banner', false);
    }

    public function test_gecersiz_ga_id_html_e_basilmaz(): void
    {
        config([
            'services.ga4.measurement_id' => 'G-X"</script><script>alert(1)',
            'lead_conversion.google_ads.conversion_id' => null,
        ]);

        $this->get('/legal/cerez-politikasi')
            ->assertOk()
            ->assertDontSee('alert(1)', false);
    }
}

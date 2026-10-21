<?php

namespace Tests\Feature;

use App\Http\Middleware\BrandShell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Yayına alma kontrol listesi (19.10.2026): hata sayfaları, soft 404, form anahtarı süresi, robots.txt.
 */
class LaunchReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_bilinmeyen_adres_ana_sayfaya_degil_markali_404_e_gider(): void
    {
        $this->get('/boyle-bir-sayfa-yok-'.uniqid())
            ->assertNotFound()
            ->assertSee('Bu sayfa yok ya da taşındı')
            ->assertSee('noindex', false)
            ->assertSee('/araclar', false);
    }

    public function test_hata_sayfalari_turkce(): void
    {
        foreach ([403 => 'yetkiniz yok', 404 => 'taşındı', 419 => 'uzun süre açık', 429 => 'yavaşlayalım', 500 => 'Bizden kaynaklı', 503 => 'bakım'] as $code => $text) {
            Route::get("/_test-hata-$code", fn () => abort($code));
            $this->get("/_test-hata-$code")->assertStatus($code)->assertSee($text);
        }
    }

    public function test_suresi_dolan_form_anahtari_json_da_turkce_mesaj_verir(): void
    {
        Route::post('/_test-419', fn () => throw new TokenMismatchException('CSRF token mismatch.'));

        $this->postJson('/_test-419')->assertStatus(419)
            ->assertJson(['message' => 'Sayfa uzun süre açık kaldı. Sayfayı yenileyip tekrar deneyin.']);
        $this->post('/_test-419')->assertStatus(419)->assertSee('Sayfa uzun süre açık kaldı');
    }

    public function test_form_anahtari_tazeleme_ucu_onbelleksiz_anahtar_doner(): void
    {
        $r = $this->getJson('/csrf-yenile')->assertOk()->assertJsonStructure(['token']);
        $this->assertStringContainsString('no-store', (string) $r->headers->get('Cache-Control'));
        $this->assertSame(csrf_token(), $r->json('token'));
    }

    public function test_formlu_sayfalara_anahtar_tazeleme_betigi_eklenir(): void
    {
        $html = $this->get('/maliyet-hesapla')->assertOk()->getContent();
        $this->assertStringContainsString('id="wsCsrfRefresh"', $html);
        $this->assertSame(1, substr_count($html, 'id="wsCsrfRefresh"'));
        $this->assertTrue(BrandShell::hasCsrfToken($html));
    }

    public function test_canlida_www_ana_alan_adina_yonlenir_ve_yol_korunur(): void
    {
        config(['app.url' => 'https://worksitem.com']);
        $this->app['env'] = 'production';

        $this->get('https://www.worksitem.com/araclar/boya-hesaplayici?x=1')
            ->assertStatus(301)
            ->assertRedirect('https://worksitem.com/araclar/boya-hesaplayici?x=1');
        $this->get('http://www.worksitem.com/')
            ->assertStatus(301)
            ->assertRedirect('https://worksitem.com/');
        // Başka alt alan adlarına (ör. staging) dokunulmaz
        $this->get('https://staging.worksitem.com/up')->assertOk();
    }

    public function test_yazi_tipi_kendi_sunucumuzdan_ve_engellemeden(): void
    {
        foreach (['/', '/araclar', '/araclar/ges-planlayici', '/maliyet-hesapla'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $head = substr($html, 0, stripos($html, '</head>'));

            // Google Fonts'a Inter için gidilmez; yerel dosyalar <head> başında önceden yüklenir
            $this->assertDoesNotMatchRegularExpression('#fonts\.googleapis\.com/css2?\?family=Inter#', $head, $url);
            $this->assertStringNotContainsString('fonts.gstatic.com', $head, $url);
            $this->assertSame(2, substr_count($head, 'as="font" type="font/woff2" crossorigin'), $url);
            $this->assertLessThan(1500, strpos($head, 'rel="preload" href="/fonts/inter/'), "$url: preload başta olmalı");
            $this->assertStringContainsString("font-display:optional", $head, $url);
            $this->assertStringContainsString('U+0100-02BA', $head, "$url: Türkçe (latin-ext)");

            // Küçük ortak CSS'ler gömülü; ayrı istek yok
            $this->assertStringContainsString('<style id="ws-brand-css">', $head, $url);
            $this->assertStringNotContainsString('href="/css/ws-brand.css', $head, $url);
            $this->assertStringNotContainsString('rel="stylesheet" href="/css/worksitem-design.css"', $head, $url);
            $this->assertSame(1, substr_count($html, 'id="ws-fonts"'), $url);
        }
        foreach (array_keys(BrandShell::FONT_FILES) as $f) {
            $this->assertFileExists(public_path($f));
        }
        $this->assertFileExists(public_path('fonts/inter/OFL.txt'));
        $this->assertStringContainsString("'Inter Fallback'", file_get_contents(public_path('css/ws-brand.css')));
    }

    public function test_ges_leaflet_cizimi_engellemez(): void
    {
        $head = $this->get('/araclar/ges-planlayici')->assertOk()->getContent();
        $head = substr($head, 0, stripos($head, '</head>'));
        $this->assertMatchesRegularExpression('#<link rel="stylesheet" href="https://unpkg\.com/leaflet@[^"]+/leaflet\.css" media="print" onload#', $head);
        $this->assertStringContainsString('<link rel="preload" as="script" href="https://unpkg.com/leaflet@', $head);
        $this->assertStringContainsString('<link rel="preconnect" href="https://server.arcgisonline.com">', $head);
    }

    public function test_defer_fonts_zaten_ertelenmis_baglantiya_dokunmaz(): void
    {
        $html = '<html><head><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=X" media="print" onload="this.media=\'all\'"></head><body></body></html>';
        $this->assertSame($html, BrandShell::deferFonts($html));

        $plain = '<html><head><link href="https://fonts.googleapis.com/css2?family=X&display=swap" rel="stylesheet"></head><body><link href="https://fonts.googleapis.com/css2?family=Y" rel="stylesheet"></body></html>';
        $out = BrandShell::deferFonts($plain);
        $this->assertStringContainsString('rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=X&display=swap"', $out);
        $this->assertStringContainsString('media="print"', $out);
        // Gövdedeki bağlantılara dokunulmaz
        $this->assertStringContainsString('<body><link href="https://fonts.googleapis.com/css2?family=Y" rel="stylesheet"></body>', $out);
    }

    public function test_llms_txt_standart_bicimde(): void
    {
        config(['worksitem_tools.tool_status.default' => 'review', 'worksitem_tools.tool_status.overrides' => ['boya-hesaplayici' => 'active']]);
        $txt = $this->get('/llms.txt')->assertOk()->getContent();
        $lines = explode("\n", $txt);

        $this->assertSame('# WORKSİTEM', $lines[0], 'İlk satır tek H1 olmalı');
        $this->assertStringStartsWith('> ', $lines[2], 'H1 ardından özet alıntı satırı olmalı');
        $this->assertSame(1, preg_match_all('/^# /m', $txt), 'Yalnız bir H1');
        $this->assertMatchesRegularExpression('#^- \[[^\]]+\]\(https://worksitem\.com/araclar/boya-hesaplayici\)#m', $txt);
        $this->assertMatchesRegularExpression('#^- \[Ana sayfa\]\(https://worksitem\.com/\)#m', $txt);
        $this->assertStringContainsString('## Optional', $txt);
        $this->assertStringNotContainsString(':updated', $txt);
    }

    public function test_robots_txt_her_bot_bolumunde_kapali_yollari_tekrarlar(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));
        $groups = preg_split('/\n\s*\n(?=(?:#[^\n]*\n)*User-agent:)/', $robots);
        $agentGroups = array_filter($groups, fn ($g) => str_contains($g, 'User-agent:'));

        $this->assertGreaterThanOrEqual(8, count($agentGroups));
        foreach ($agentGroups as $g) {
            foreach (['/admin/', '/firma-paneli/', '/odeme/', '/teklif/', '/*?*utm_'] as $path) {
                $this->assertStringContainsString("Disallow: $path", $g, "robots.txt bölümünde eksik: $path\n$g");
            }
        }
        $this->assertStringContainsString('Sitemap: https://worksitem.com/sitemap.xml', $robots);
    }
}

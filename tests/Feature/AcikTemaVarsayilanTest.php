<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * v117.1: site telefonda ve bilgisayarda her zaman AÇIK temayla açılır (cihaz koyu modda olsa da).
 * Koyu tema yalnız kullanıcı düğmeyle seçince gelir (localStorage worksitem-theme=dark).
 */
class AcikTemaVarsayilanTest extends TestCase
{
    use RefreshDatabase;

    public function test_tema_baslatici_varsayilan_acik_ve_yalniz_kayitli_koyuyu_uygular(): void
    {
        $js = \App\Http\Middleware\BrandShell::THEME_INIT_SCRIPT;
        $this->assertStringContainsString('var t="light"', $js);
        $this->assertStringContainsString('==="dark")t="dark"', $js);
        $this->assertStringNotContainsString('prefers-color-scheme', $js);
    }

    public function test_hicbir_sayfa_cihazin_koyu_modunu_izlemiyor(): void
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));
        $bad = [];
        foreach ($it as $file) {
            if ($file->isFile() && str_contains(file_get_contents($file->getPathname()), 'prefers-color-scheme')) {
                $bad[] = $file->getPathname();
            }
        }
        foreach (['ws-brand.css', 'ws-a11y.css', 'wk-tokens.css', 'wk-shell.css', 'wk-ui.css'] as $css) {
            if (str_contains((string) file_get_contents(public_path('css/'.$css)), 'prefers-color-scheme')) {
                $bad[] = $css;
            }
        }
        $this->assertSame([], $bad);
    }

    public function test_ana_sayfalar_acik_temayla_baslar(): void
    {
        foreach (['/', '/araclar', '/pozlar', '/legal/kullanim-kosullari'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('id="wk-theme-init"', $html, $url);
            $this->assertStringNotContainsString('content="light dark"', $html, $url);
        }
    }
}

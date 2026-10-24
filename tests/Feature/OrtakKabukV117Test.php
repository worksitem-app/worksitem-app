<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * v117 ortak kabuk: her public sayfa aynı üst menüyü, alt bilgiyi ve tasarım token'larını kullanır.
 * Parametresiz tüm GET rotaları gezilir; yeni sayfa eklenince de korunur.
 */
class OrtakKabukV117Test extends TestCase
{
    use RefreshDatabase;

    /** Kabuksuz kalması gereken sayfalar (çevrimdışı ekranı, yönlendirme/indirme uçları vb.) */
    private const SHELL_FREE = ['cevrimdisi'];

    /** @return list<string> */
    private function publicHtmlPages(): array
    {
        $urls = [];
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (! in_array('GET', $route->methods(), true) || str_contains($uri, '{')
                || preg_match('#^(admin|livewire|api|up$|_|storage|sanctum|filament|panel|sitemap|robots|feed|sw\.js|site\.webmanifest)#', $uri)) {
                continue;
            }
            $urls[] = '/'.ltrim($uri, '/');
        }

        // Araçlar /araclar/{slug} altında: yapılandırmadaki her araç da gezilir
        foreach (array_keys(config('worksitem_tools.tools', [])) as $slug) {
            $urls[] = '/araclar/'.$slug;
        }

        return array_values(array_unique($urls));
    }

    public function test_her_public_sayfada_tek_ortak_menu_ve_alt_bilgi_var(): void
    {
        $bad = [];
        $checked = 0;
        foreach ($this->publicHtmlPages() as $url) {
            $res = $this->get($url);
            $type = (string) $res->headers->get('Content-Type', '');
            if ($res->getStatusCode() !== 200 || ! str_contains($type, 'text/html')) {
                continue;
            }
            $html = (string) $res->getContent();
            if (stripos($html, '</head>') === false || in_array(ltrim($url, '/'), self::SHELL_FREE, true)) {
                continue;
            }
            $checked++;
            $h = substr_count($html, '<header class="wk-header"');
            $f = substr_count($html, '<footer class="wk-footer"');
            if ($h !== 1 || $f !== 1) {
                $bad[] = "$url (menü $h, alt bilgi $f)";
            }
            foreach (['id="wk-tokens-css"', 'id="wk-shell-css"', 'id="wk-theme-init"'] as $needle) {
                if (substr_count($html, $needle) !== 1) {
                    $bad[] = "$url: $needle";
                }
            }
        }
        $this->assertGreaterThan(100, $checked, 'gezilen sayfa sayısı');
        $this->assertSame([], $bad, "Ortak kabuk eksik/çift:\n".implode("\n", $bad));
    }

    public function test_ust_menude_araclar_maliyet_pozlar_ve_aktif_sayfa_isareti_var(): void
    {
        $html = $this->get('/pozlar')->assertOk()->getContent();
        $nav = substr($html, strpos($html, 'id="wksMainNav"'), 600);
        $this->assertMatchesRegularExpression('#href="/araclar">Araçlar</a>#', $nav);
        $this->assertMatchesRegularExpression('#href="/maliyet-hesapla">Maliyet</a>#', $nav);
        $this->assertMatchesRegularExpression('#href="/pozlar" aria-current="page">Pozlar</a>#', $nav);
        // Mobilde de aynı üç bağlantı görünür (alt satır)
        $this->assertStringContainsString('class="wk-subnav"', $html);
        // Arama diyaloğu ve tema düğmesi korunur
        foreach (['wksGlobalSearchOpen', 'wksGlobalSearchInput', 'wksGlobalSearchResults', 'wksThemeToggle', 'wksMenuButton'] as $id) {
            $this->assertSame(1, substr_count($html, 'id="'.$id.'"'), $id);
        }
        $this->assertStringContainsString("'/pozlar?q='", $html, 'arama pozlara da yönlendirir');
    }

    public function test_tema_baslatici_head_basinda_ve_kabuk_stilleri_sayfa_stillerinden_sonra(): void
    {
        $html = $this->get('/araclar')->assertOk()->getContent();
        $head = substr($html, 0, stripos($html, '</head>'));
        $this->assertLessThan(200, strpos($head, 'id="wk-theme-init"') - stripos($head, '<head'));
        $this->assertGreaterThan(strpos($head, 'id="ws-brand-css"'), strpos($head, 'id="wk-shell-css"'));
        $this->assertStringContainsString('worksitem-theme', $head);
    }

    public function test_wk_ui_yalniz_yeni_tasarimli_sayfalara_eklenir(): void
    {
        $html = $this->get('/araclar')->assertOk()->getContent();
        $this->assertStringNotContainsString('/css/wk-ui.css', $html);

        $page = \App\Http\Middleware\BrandShell::injectShell('<html><head></head><body><div class="wk-root"></div></body></html>');
        $this->assertMatchesRegularExpression('#/css/wk-ui\.css\?v=\w+#', $page);
        $this->assertMatchesRegularExpression('#/js/wk-ui\.js\?v=\w+" defer#', $page);
        $this->assertSame($page, \App\Http\Middleware\BrandShell::injectShell($page), 'iki kez eklenmez');
    }

    public function test_kabuk_dosyalari_tek_kaynak_ve_onekli(): void
    {
        $tokens = file_get_contents(public_path('css/wk-tokens.css'));
        $this->assertStringNotContainsString('@font-face', $tokens);
        $this->assertStringContainsString(':root[data-theme="dark"]', $tokens);
        $this->assertDoesNotMatchRegularExpression('/--(?!wk-)[a-z]/', $tokens, "token'lar yalnız --wk- önekli");

        $ui = file_get_contents(public_path('css/wk-ui.css'));
        $this->assertStringNotContainsString('.wk-header {', $ui, 'menü kuralları yalnız wk-shell.css’te');
        $this->assertStringNotContainsString('.wk-footer {', $ui);

        $shell = file_get_contents(public_path('css/wk-shell.css'));
        foreach (preg_split('/\}\s*/', preg_replace('#/\*.*?\*/#s', '', $shell)) as $rule) {
            $sel = trim(explode('{', $rule)[0]);
            if ($sel === '' || str_starts_with($sel, '@') || ! str_contains($rule, '{')) {
                continue;
            }
            foreach (explode(',', $sel) as $one) {
                $this->assertMatchesRegularExpression('/^html(\[[^\]]+\])? body /', trim($one), "eski CSS'e dayanıklı önek: $one");
            }
        }
    }

    public function test_eski_kabuk_siniflari_kalmadi(): void
    {
        foreach (['resources/views', 'public/css', 'app'] as $dir) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path($dir)));
            foreach ($it as $file) {
                if ($file->isFile() && preg_match('/\.(php|css|js)$/', $file->getFilename())) {
                    $this->assertDoesNotMatchRegularExpression('/\bwks-(header|footer|foot-|nav\b|menu\b|brand\b)/', file_get_contents($file->getPathname()), $file->getPathname());
                }
            }
        }
    }
}

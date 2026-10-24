<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * UI/UX: tüm public sayfalar aynı header/footer ve marka katmanını kullanır.
 */
class BrandConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return [
            ['/'], ['/araclar'], ['/maliyet-hesapla'], ['/maliyet-hesapla/basarili'], ['/ai-tasarim/ic-mekan'],
            ['/hakkimizda'], ['/vizyon'], ['/ekip'], ['/pozlar'], ['/legal/kullanim-kosullari'],
            ['/araclar/boya-hesaplayici'], ['/araclar/yapi-dinamigi'], ['/araclar/pvc-dograma-hesaplayici'],
            ['/araclar/tahmini-bina-maliyeti'], ['/araclar/otomatik-kesif-fiyatlandirma'], ['/araclar/merdiven-basamak-hesabi'],
        ];
    }

    #[DataProvider('pages')]
    public function test_sayfa_standart_header_footer_ve_marka_katmanini_kullanir(string $url): void
    {
        $html = $this->get($url)->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<header class="wk-header"'), "$url: header");
        $this->assertSame(1, preg_match_all('/<footer class="wk-footer/', $html), "$url: footer");
        $this->assertStringContainsString('id="ws-brand-css"', $html);
        $this->assertStringNotContainsString('Bebas Neue', $html);
        $this->assertStringNotContainsString('Plus Jakarta Sans', $html);
    }

    public function test_header_ve_footer_hicbir_view_da_script_blogu_icinde_degil(): void
    {
        $bad = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'))) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            $s = file_get_contents($file->getPathname());
            foreach (['<x-site.contech-header />', '<x-site.contech-footer />'] as $tag) {
                $offset = 0;
                while (($pos = strpos($s, $tag, $offset)) !== false) {
                    $before = substr($s, 0, $pos);
                    if (strrpos($before, '<script') > (int) strrpos($before, '</script>')) {
                        $bad[] = $file->getFilename();
                    }
                    $offset = $pos + 1;
                }
            }
        }

        $this->assertSame([], $bad);
    }
}

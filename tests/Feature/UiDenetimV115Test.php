<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v115: UI/UX denetimi (canlı ölçüm 24.10.2026) — erişilebilirlik katmanı, AI aracı etiketleri, çerez bandı */
class UiDenetimV115Test extends TestCase
{
    use RefreshDatabase;

    public function test_erisilebilirlik_katmani_sayfa_sonunda_bir_kez(): void
    {
        foreach (['/', '/maliyet-hesapla', '/araclar'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertSame(1, substr_count($html, 'id="ws-a11y-css"'), $url);
            $pos = strpos($html, 'id="ws-a11y-css"');
            $this->assertGreaterThan(strpos($html, 'wks-foot-title') ?: 0, $pos, "$url: katman alt bilgi stillerinden sonra gelmeli");
            $this->assertStringContainsString('font-size: 16px !important', $html);
        }
    }

    public function test_kontrast_renkleri_aa_esigini_gecer(): void
    {
        $css = file_get_contents(public_path('css/ws-a11y.css'));
        $ratio = function (string $a, string $b): float {
            $l = function (string $h): float {
                $c = array_map(fn ($i) => hexdec(substr(ltrim($h, '#'), $i, 2)) / 255, [0, 2, 4]);
                $c = array_map(fn ($v) => $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4, $c);

                return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
            };
            [$x, $y] = [$l($a), $l($b)];

            return (max($x, $y) + 0.05) / (min($x, $y) + 0.05);
        };
        $this->assertStringContainsString('#7A5600', $css);
        $this->assertGreaterThanOrEqual(4.5, $ratio('#7A5600', '#F6F7F9'));   // alt bilgi zemini
        $this->assertGreaterThanOrEqual(4.5, $ratio('#7A5600', '#FFF4D1'));   // sarı kart zemini
        $this->assertGreaterThanOrEqual(4.5, $ratio('#F5B400', '#141A24'));   // koyu kart zemini
        $this->assertGreaterThanOrEqual(4.5, $ratio('#C9CFD8', '#141A24'));
        $this->assertGreaterThanOrEqual(4.5, $ratio('#5B6472', '#FFFFFF'));
    }

    public function test_ai_araci_secim_kutulari_etiketli(): void
    {
        config(['worksitem_tools.tool_status.overrides' => ['otomatik-kesif-fiyatlandirma' => 'active']]);
        $html = $this->get('/araclar/otomatik-kesif-fiyatlandirma')->getContent();
        $this->assertStringContainsString('<label for="boqProjectType">', $html);
        $this->assertStringContainsString('id="boqProjectType"', $html);
        $this->assertStringContainsString('<label for="boqQuality">', $html);
    }

    public function test_cerez_bandi_mobilde_tek_satir_ve_esit_agirlikta(): void
    {
        $html = view('partials.cookie-consent', ['gaId' => 'G-TEST000000'])->render();   // GA4 kimliği varken eklenir
        $this->assertStringContainsString('#ws-cookie-banner .ws-ck-actions{flex-wrap:nowrap', $html);
        // Reddet ve Kabul et aynı görünümde kalır (KVKK: ret, kabul kadar kolay)
        $this->assertMatchesRegularExpression('#<button type="button" data-ws-ck="reject">Reddet</button>\s*<button type="button" data-ws-ck="accept">Kabul et</button>#', $html);
    }
}

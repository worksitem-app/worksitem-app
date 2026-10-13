<?php

namespace Tests\Feature;

use App\Services\FxRates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Banyo v2 tasarım kiti: canlıdaki hesaplayıcılar */
class CalcKitTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        // seramik, boya, mutfak: hesap motoruna taşındı (KitEngineTest)
        return [
            'beton' => ['/araclar/beton-hesaplayici', 'resPrice'],
            'su-tesisat' => ['/araclar/su-tesisat-hesaplama', 'totalPrice'],
            'kablo' => ['/araclar/kablo-kesiti-hesabi', 'totalPrice'],
            'cati' => ['/araclar/cati-maliyet-hesaplayici', 'total'],
            'alcipan' => ['/araclar/alcipan-hesaplayici', 'rTotal'],
        ];
    }

    #[DataProvider('pages')]
    public function test_kit_yuklu_ve_arac_idleri_korunmus(string $url, string $priceId): void
    {
        Cache::put(FxRates::CACHE_KEY, ['date' => '09.10.2026', 'rates' => ['USD' => 49.2, 'EUR' => 55.2, 'GBP' => 65.2]]);
        $html = $this->get($url)->assertOk()->getContent();
        if (str_contains($html, 'id="kCfg"')) {
            $this->assertStringContainsString('data-asset="ws-estimator.css"', $html);   // hesap motoruna taşındı

            return;
        }

        $this->assertStringContainsString('/css/ws-calc.css', $html);
        $this->assertStringContainsString('/js/ws-calc.js', $html);
        $this->assertStringContainsString('class="wc"', $html);
        $this->assertStringContainsString('"BreadcrumbList"', $html);
        $this->assertStringContainsString('Döviz: TCMB 09.10.2026', $html);
        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertMatchesRegularExpression('/id="'.$priceId.'"[^>]*data-wc-price/', $html);
        $this->assertSame(1, substr_count($html, 'data-wc-result'));
    }

    public function test_kablo_gerilim_dusumu_watt_ile_hesaplanir(): void
    {
        $html = $this->get('/araclar/kablo-kesiti-hesabi')->getContent();
        $this->assertStringContainsString('const pW = p * 1000;', $html);
        $this->assertStringContainsString('(200 * pW * l)', $html);
        $this->assertStringContainsString('id="loadType"', $html);
        $this->assertStringNotContainsString('(200 * p * l)', $html);
    }

    public static function allToolPages(): array
    {
        // Hesap motoruna taşınan araçlar (parke, WC, zemin, PVC, mantolama…) KitEngineTest'te
        return array_map(fn ($u) => [$u], [
            '/araclar/ac-calculator', '/araclar/alci-siva-hesaplama', '/araclar/asansor-calculator', '/araclar/building-cost-calculator',
            '/araclar/cement-calculator', '/araclar/concrete-calculator', '/araclar/daire-kira-degeri', '/araclar/demir-metraji-hesaplayici',
            '/araclar/duvar-maliyet-hesaplayici', '/araclar/excavation-calculator', '/araclar/flat-for-land-calculator', '/araclar/formwork-calculator',
            '/araclar/ges-calculator', '/araclar/iscilik-calculator', '/araclar/iskele-calculator', '/araclar/kara-siva-hesaplama',
            '/araclar/map-scale-calculator', '/araclar/metrekup-hacim-hesaplama', '/araclar/sap-hesaplayici',
            '/araclar/sgk-asgari-iscilik', '/araclar/static-project-fee', '/araclar/taks-kaks-calculator', 
            '/araclar/asma-tavan-hesaplayici', '/araclar/epoksi-zemin-hesaplayici', '/araclar/havalandirma-calculator', '/araclar/sandvic-panel-hesaplayici',
            '/araclar/su-yalitimi-hesaplayici', '/araclar/yukseltilmis-doseme-hesaplayici',
            '/araclar/basit-kiris-statigi', '/araclar/bina-agirlik-hesabi', '/araclar/bombeli-tank-hacmi', '/araclar/column-capacity',
            '/araclar/column-starter-bar', '/araclar/core-strength', '/araclar/cutting-optimization', '/araclar/duvar-calculator',
            '/araclar/is-makinesi-maliyeti', '/araclar/logistics-cost', '/araclar/malzeme-agirligi', '/araclar/pompa-gucu-hesabi',
            '/araclar/shelter-regulation', '/araclar/structural-dynamics', '/araclar/azami-konut-kredisi',
            '/araclar/bahce-duvari-maliyeti', '/araclar/deprem-izolator-maliyeti', '/araclar/donati-tahvil-hesaplayici', '/araclar/energy-calculator',
            '/araclar/generator-calculator', '/araclar/insaat-maliyeti-hesaplama', '/araclar/insaat-muhendisi-maas-hesabi', '/araclar/merdiven-basamak-hesabi',
            '/araclar/radiator-calculator', '/araclar/rainpipe-calculator', '/araclar/birim-fiyat-analiz',
        ]);
    }

    #[DataProvider('allToolPages')]
    public function test_tum_hesaplayicilarda_tasarim_kiti(string $url): void
    {
        $html = $this->followingRedirects()->get($url)->assertOk()->getContent();
        if (str_contains($html, 'id="kCfg"')) {
            // Hesap motoruna taşınmış araç: kendi tasarımı (ayrıntı KitEngineTest)
            $this->assertStringContainsString('data-asset="ws-estimator.css"', $html);
            $this->assertStringContainsString('"BreadcrumbList"', $html);
            $this->assertSame(1, substr_count($html, '<h1'));

            return;
        }
        $this->assertStringContainsString('/css/ws-calc.css', $html);
        $this->assertStringContainsString('/js/ws-calc.js', $html);
        $this->assertStringContainsString('"BreadcrumbList"', $html);
        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertLessThanOrEqual(1, substr_count($html, 'data-wc-result'));
    }
}

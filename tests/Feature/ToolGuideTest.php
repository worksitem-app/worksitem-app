<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Dalga 1: en çok aranan 10 araçta hesap yöntemi, SSS (FAQPage),
 * paylaş/yazdır ve olası aralık bileşeni.
 */
class ToolGuideTest extends TestCase
{
    use RefreshDatabase;

    public static function tools(): array
    {
        $rows = [];
        $kit = array_keys((require __DIR__.'/../../config/worksitem_estimators.php')['tools']);   // hesap motoru sayfalarının kendi SSS'si var
        foreach (array_diff(array_keys((require __DIR__.'/../../config/worksitem_tool_guides.php')['tools']), $kit) as $slug) {
            $rows[$slug] = [$slug];
        }

        return $rows;
    }

    public function test_on_arac_tanimli(): void
    {
        $this->assertCount(10, config('worksitem_tool_guides.tools'));
    }

    #[DataProvider('tools')]
    public function test_arac_sayfasi_rehber_ve_gecerli_faq_semasi_icerir(string $slug): void
    {
        $guide = config("worksitem_tool_guides.tools.$slug");
        $html = $this->get("/araclar/$slug")->assertOk()->getContent();

        $this->assertStringContainsString('Nasıl hesaplıyoruz?', $html);
        $this->assertStringContainsString('Sık sorulan sorular', $html);
        $this->assertStringContainsString('/js/ws-tool-extras.js', $html);
        $this->assertSame(1, substr_count($html, '"FAQPage"'), 'Sayfada tek FAQPage olmalı');

        // FAQPage JSON-LD geçerli JSON ve soru sayısı config ile aynı
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $faq = collect($m[1])->map(fn ($j) => json_decode($j, true))->firstWhere('@type', 'FAQPage');
        $this->assertNotNull($faq, 'FAQPage JSON çözülemedi');
        $this->assertCount(count($guide['faq']), $faq['mainEntity']);

        // JS yapılandırması geçerli JSON
        $this->assertMatchesRegularExpression('#<script type="application/json" id="ws-tool-guide-config">\{.*?\}</script>#s', $html);

        // Paylaş linkine yazılan alanlar ve seçiciler sayfada gerçekten var (araç değişirse test kırılır)
        foreach ($guide['fields'] as $id) {
            $this->assertMatchesRegularExpression('/id="'.preg_quote($id, '/').'"/', $html, "$slug: #$id alanı yok");
        }
        foreach (['calc', 'total', 'result'] as $key) {
            $id = ltrim($guide[$key], '#');
            $this->assertMatchesRegularExpression('/id="'.preg_quote($id, '/').'"/', $html, "$slug: $key ($id) yok");
        }
    }

    public function test_paylas_alanlarinda_kisisel_veri_yok(): void
    {
        $forbidden = ['name', 'phone', 'email', 'tel', 'ad', 'soyad', 'telefon', 'eposta', 'address', 'adres'];
        foreach (config('worksitem_tool_guides.tools') as $slug => $guide) {
            foreach ($guide['fields'] as $id) {
                $this->assertNotContains(strtolower($id), $forbidden, "$slug paylaş alanında kişisel veri: $id");
            }
        }
    }
}

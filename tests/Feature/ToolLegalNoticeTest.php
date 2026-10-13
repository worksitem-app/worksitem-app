<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ToolLegalNoticeTest extends TestCase
{
    use RefreshDatabase;

    public static function toolPages(): array
    {
        return [
            'banyo' => ['/araclar/banyo-maliyet-hesaplayici'],
            'beton' => ['/araclar/beton-hesaplayici'],
            'seramik' => ['/araclar/seramik-maliyet-hesaplayici'],
            'boya' => ['/araclar/boya-hesaplayici'],
            'araclar' => ['/araclar'],
        ];
    }

    #[DataProvider('toolPages')]
    public function test_arac_sayfalarinda_hukuki_uyari_var(string $url): void
    {
        $html = $this->get($url)->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'id="ws-legal-notice"'));
        $this->assertStringContainsString('geçerlilik süresi taşımaz', $html);
        $this->assertStringContainsString('kast ve ağır ihmal', $html);
        $this->assertStringContainsString('ülkeye, şehre', $html);
        $this->assertStringContainsString('2.500 TL + KDV', $html);
        $this->assertStringNotContainsString('hiçbir şeyden sorumlu', $html);
    }

    public function test_kit_sonuc_kutusuna_kisa_uyari_gider(): void
    {
        $html = $this->get('/araclar/klima-btu-hesaplayici')->getContent();
        $this->assertStringContainsString('"legal":"Bilgilendirme amaçlı yaklaşık hesaptır', $html);
        // hesap motoru sayfaları uyarıyı doğrudan sonuç kartında gösterir
        $this->assertStringContainsString(config('worksitem_legal.result_short'), $this->get('/araclar/beton-hesaplayici')->getContent());
    }

    public function test_banyo_sss_15_gun_ve_kdv_haric(): void
    {
        $html = $this->get('/araclar/banyo-maliyet-hesaplayici')->getContent();
        $this->assertStringContainsString('15 gün içinde', $html);
        $this->assertStringNotContainsString('{kesif_gun}', $html);
        $this->assertStringNotContainsString('30 gün', $html);
    }

    public function test_ana_sayfada_arac_uyarisi_yok(): void
    {
        $this->assertStringNotContainsString('id="ws-legal-notice"', $this->get('/')->getContent());
    }
}

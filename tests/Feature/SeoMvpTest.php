<?php

namespace Tests\Feature;

use App\Models\TenderPoz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMvpTest extends TestCase
{
    use RefreshDatabase;

    private function poz(string $no, bool $enriched): TenderPoz
    {
        return TenderPoz::query()->create([
            'year' => '2026', 'institution' => 'ÇŞB', 'poz_no' => $no, 'description' => 'Deneme poz '.$no,
            'unit' => 'm²', 'unit_price' => 100, 'is_active' => true, 'seo_enriched' => $enriched,
        ]);
    }

    public function test_zenginlestirilmemis_poz_noindex_ve_sitemapte_yok(): void
    {
        // v113: kural varsayılan kapalı; açıkken eski davranış
        config(['worksitem_seo.poz_index_only_enriched' => true]);
        $this->poz('15.100.1001', false);
        $this->poz('15.100.1002', true);

        $this->get('/poz/15.100.1001')->assertOk()->assertSee('<meta name="robots" content="noindex,follow">', false);
        $this->get('/poz/15.100.1002')->assertOk()->assertSee('content="index,follow,max-image-preview:large', false);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('15.100.1002', $xml);
        $this->assertStringNotContainsString('15.100.1001', $xml);

        config(['worksitem_seo.poz_index_only_enriched' => false]);
        $this->assertStringContainsString('15.100.1001', $this->get('/sitemap.xml')->getContent());
        $this->get('/poz/15.100.1001')->assertDontSee('noindex,follow', false);
    }

    public function test_yasal_sayfalarda_tek_h1(): void
    {
        $html = $this->get('/legal/kvkk-aydinlatma')->assertOk()->getContent();
        $this->assertSame(1, preg_match_all('/<h1[\s>]/', $html));
    }

    public function test_aciklamasi_eksik_araclarda_meta_description_var(): void
    {
        config(['worksitem_tools.tool_status.default' => 'active']);
        foreach (['belge-olusturucu', 'is-makinesi-maliyeti', 'otomatik-kesif-fiyatlandirma'] as $slug) {
            $html = $this->get('/araclar/'.$slug)->assertOk()->getContent();
            $this->assertMatchesRegularExpression('/<meta name="description" content="[^"]{70,170}">/', $html, $slug);
        }
    }

    public function test_veritabani_yedegi_mysql_degilse_atlanir_ve_zamanlanmis(): void
    {
        $this->artisan('app:backup-db')->expectsOutputToContain('yalnızca MySQL')->assertSuccessful();
        $this->artisan('schedule:list')->expectsOutputToContain('app:backup-db')->assertSuccessful();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Sponsor;
use App\Models\TenderPoz;
use App\Services\Sponsors\SponsorPackages;
use App\Support\PozCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v113: poz sayfası SEO (başlık, hesap, ilgili pozlar, kurum seçimi, hub sayfaları, site haritası) ve poz sayfası marka kartları */
class PozSeoV113Test extends TestCase
{
    use RefreshDatabase;

    private function poz(string $no, string $inst = 'ÇŞB', float $price = 100, string $desc = '', string $unit = 'm²'): TenderPoz
    {
        return TenderPoz::query()->create([
            'year' => '2026', 'institution' => $inst, 'poz_no' => $no, 'description' => $desc ?: 'Deneme imalatı '.$no,
            'unit' => $unit, 'unit_price' => $price, 'is_active' => true, 'seo_enriched' => false,
        ]);
    }

    public function test_varsayilan_index_ve_yeni_baslik(): void
    {
        $this->poz('15.150.1006', 'ÇŞB', 873.94, 'Paslanmaz çelik merdiven korkuluğu ve küpeştesi yapılması');
        $html = $this->get('/poz/15.150.1006')->assertOk()->getContent();

        $this->assertStringContainsString('content="index,follow', $html);
        $this->assertStringNotContainsString('noindex', $html);
        $this->assertMatchesRegularExpression('/<title>15\.150\.1006 Birim Fiyatı 2026 – Paslanmaz[^<]*\| ÇŞB<\/title>/u', $html);
        preg_match('/<title>([^<]*)<\/title>/u', $html, $m);
        $this->assertLessThanOrEqual(75, mb_strlen(html_entity_decode($m[1])));
        $this->assertStringContainsString('Miktarı girin, toplam tutarı KDV dahil', $html);
        $this->assertStringContainsString('class="poz-calc"', $html);
        $this->assertStringContainsString('data-price="873.94"', $html);
    }

    public function test_ayni_poz_no_farkli_kurumlar_secilebilir_ve_sitemapte_tek(): void
    {
        $this->poz('10.100.1008', 'MSB', 50);
        $this->poz('10.100.1008', 'ÇŞB', 40);

        $this->get('/poz/10.100.1008')->assertOk()->assertSee('40,00 TL')
            ->assertSee('başka kurumların listesinde de var')->assertSee('?kurum=msb', false);
        $this->get('/poz/10.100.1008?kurum=msb')->assertOk()->assertSee('50,00 TL')
            ->assertSee('<link rel="canonical" href="https://worksitem.com/poz/10.100.1008">', false);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertSame(1, substr_count($xml, '<loc>https://worksitem.com/poz/10.100.1008</loc>'));
    }

    public function test_ilgili_pozlar_ayni_grup_ve_kurumdan(): void
    {
        foreach (['15.150.1001', '15.150.1002', '15.150.1003'] as $no) {
            $this->poz($no);
        }
        $this->poz('15.160.1001');
        $this->poz('15.150.1004', 'MSB');

        $html = $this->get('/poz/15.150.1002')->assertOk()->getContent();
        $this->assertStringContainsString('İlgili pozlar', $html);
        $this->assertStringContainsString('href="/poz/15.150.1001"', $html);
        $this->assertStringContainsString('href="/poz/15.150.1003"', $html);
        $this->assertStringNotContainsString('href="/poz/15.160.1001"', $html);
        $this->assertStringNotContainsString('href="/poz/15.150.1004"', $html);
        $this->assertStringContainsString('href="/pozlar/csb/15-150"', $html);
    }

    public function test_kurum_ve_grup_sayfalari_taranabilir(): void
    {
        $this->poz('15.150.1001', 'ÇŞB', 10, 'Kazı yapılması');
        $this->poz('15.150.1002');
        $this->poz('04.KTB.0001', 'Kültür/Vakıflar');
        PozCatalog::forget();

        $this->get('/pozlar')->assertOk()->assertSee('href="/pozlar/csb"', false)->assertSee('href="/pozlar/kultur-vakiflar"', false);
        $this->get('/pozlar/csb')->assertOk()->assertSee('ÇŞB pozları')->assertSee('href="/pozlar/csb/15-150"', false);
        $this->get('/pozlar/csb/15-150')->assertOk()->assertSee('15.150.1001')->assertSee('15.150.1002')->assertSee('10,00 TL');
        $this->get('/pozlar/kultur-vakiflar/04-ktb')->assertOk()->assertSee('04.KTB.0001');
        $this->get('/pozlar/yok')->assertNotFound();
        $this->get('/pozlar/csb/99-999')->assertNotFound();

        $xml = $this->get('/sitemap.xml')->getContent();
        $this->assertStringContainsString('<loc>https://worksitem.com/pozlar/csb</loc>', $xml);
        $this->assertStringContainsString('<loc>https://worksitem.com/pozlar/csb/15-150</loc>', $xml);
    }

    public function test_sponsor_kategorisi_poz_tanimindan(): void
    {
        $this->assertSame(['yapistirici', 'seramik'], PozCatalog::sponsorCategories('Duvarlara 30x60 cm sırlı seramik karo kaplama yapılması (yapıştırıcı ile)'));
        $this->assertSame(['yalitim_cimento'], PozCatalog::sponsorCategories('Çimento esaslı iki bileşenli su yalıtım malzemesi ile yalıtım yapılması'));
        $this->assertSame(['sap'], PozCatalog::sponsorCategories('200 kg çimento dozlu tesviye tabakası yapılması'));
        $this->assertSame([], PozCatalog::sponsorCategories('Şap sökülmesi'));
        $this->assertSame([], PozCatalog::sponsorCategories('Kazı yapılması'));
        $this->assertSame(['tamir'], PozCatalog::sponsorCategories('Yapısal tamir harcı ile beton yüzey onarımı'));
    }

    public function test_marka_karti_yalniz_eslesen_pozda_sponsorlu_etiketli(): void
    {
        $this->poz('15.275.1101', 'ÇŞB', 900, 'Seramik yer karosu döşenmesi (yapıştırıcı ile)');
        $this->poz('15.100.1001', 'ÇŞB', 10, 'Kazı yapılması');
        $this->poz('15.100.1002', 'ÇŞB', 10, 'Seramik kaplamanın sökülmesi');
        $s = Sponsor::query()->create([
            'brand' => 'Deneme Kimya', 'tool_slug' => 'poz-sayfalari', 'category' => 'yapistirici', 'headline' => 'Karo yapıştırıcısı',
            'cta_label' => 'Ürünü gör', 'target_url' => 'https://example.com', 'priority' => 10, 'active' => true,
        ]);

        $html = $this->get('/poz/15.275.1101')->assertOk()->getContent();
        $this->assertStringContainsString('Deneme Kimya', $html);
        $this->assertStringContainsString('Sponsorlu', $html);
        $this->assertStringContainsString('rel="sponsored nofollow noopener"', $html);
        $this->assertSame(1, $s->fresh()->impressions);

        // bot gösterimi sayılmaz
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1)'])->get('/poz/15.275.1101')->assertSee('Deneme Kimya');
        $this->assertSame(1, $s->fresh()->impressions);

        $this->get('/poz/15.100.1001')->assertOk()->assertDontSee('Deneme Kimya')->assertDontSee('ws-sp-head', false);
        $this->get('/poz/15.100.1002')->assertOk()->assertDontSee('Deneme Kimya');
    }

    public function test_tam_kategori_ve_seramik_paketi_poz_sayfalarini_kapsar(): void
    {
        $this->assertContains('yapistirici', SponsorPackages::get(SponsorPackages::ALL)['tools']['poz-sayfalari']);
        $this->assertContains('yalitim_bitum', SponsorPackages::get(SponsorPackages::ALL)['tools']['poz-sayfalari']);
        $this->assertNotContains('seramik', SponsorPackages::get(SponsorPackages::ALL)['tools']['poz-sayfalari']);
        $this->assertSame(['seramik'], SponsorPackages::get('seramik-banyo')['tools']['poz-sayfalari']);

        $this->artisan('sponsors:demo', ['--brand' => 'Kimya Demo', '--tile-brand' => 'Karo Demo'])->assertSuccessful();
        $this->poz('15.275.1101', 'ÇŞB', 900, 'Seramik yer karosu döşenmesi (yapıştırıcı ile)');
        $this->get('/poz/15.275.1101')->assertOk()->assertSee('Kimya Demo')->assertSee('Karo Demo');
    }
}

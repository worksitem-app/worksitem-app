<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Hukuk + SEO denetimi (2026-10) regresyon testleri.
 */
class LegalSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_kullanim_kosullari_yayinda_ve_muhendislik_uyarisi_iceriyor(): void
    {
        $this->get('/legal/kullanim-kosullari')
            ->assertOk()
            ->assertSee('yerine geçmez', false);
    }

    public function test_eski_aydinlatma_metni_yeniye_kalici_yonlenir(): void
    {
        $this->get('/kvkk-aydinlatma-metni')
            ->assertStatus(301)
            ->assertRedirect('/legal/kvkk-aydinlatma');
    }

    public function test_aydinlatma_metni_ai_saglayicilarini_ve_gorsel_verisini_aciklar(): void
    {
        $this->get('/legal/kvkk-aydinlatma')
            ->assertOk()
            ->assertSee('Replicate')
            ->assertSee('30 gün');
    }

    public function test_muhendislik_aracina_sorumluluk_uyarisi_eklenir(): void
    {
        $this->get('/araclar/kolon-tasima-kapasitesi')
            ->assertOk()
            ->assertSee('id="ws-eng-notice"', false);

        $this->get('/araclar/boya-hesaplayici')
            ->assertOk()
            ->assertDontSee('id="ws-eng-notice"', false);
    }

    public function test_kilitli_arac_varsayilan_olarak_indekslenebilir_istenirse_noindex(): void
    {
        $this->assertNull($this->get('/araclar/kolon-tasima-kapasitesi')->headers->get('X-Robots-Tag'));

        config(['worksitem_tools.noindex_review_tools' => true]);
        $this->get('/araclar/kolon-tasima-kapasitesi')
            ->assertHeader('X-Robots-Tag', 'noindex, follow');

        $this->assertNull($this->get('/araclar/boya-hesaplayici')->headers->get('X-Robots-Tag'));
    }

    public function test_eksik_open_graph_ve_canonical_tamamlanir(): void
    {
        $html = $this->get('/hakkimizda')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'property="og:title"'));
        $this->assertStringContainsString('property="og:image"', $html);
        $this->assertSame(1, preg_match_all('#rel=["\']canonical#i', $html));
    }

    public function test_sitemap_kullanim_kosullarini_icerir_kilitli_araci_icermez(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/legal/kullanim-kosullari', false)
            ->assertDontSee('/araclar/kolon-tasima-kapasitesi', false);
    }
}

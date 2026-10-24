<?php

namespace Tests\Feature;

use App\Estimators\KitRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v119: hesap motorlu araçlar yeni tasarım dilinde + motorun ortak düzeltmeleri (24.10 araç denetimi) */
class AracSayfasiV119Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_tools.tool_status.overrides' => array_fill_keys(KitRegistry::slugs(), 'active')]);
    }

    public function test_tum_kit_araclari_tasarim_dilinde(): void
    {
        foreach (KitRegistry::slugs() as $slug) {
            $html = $this->get('/araclar/'.$slug)->assertOk()->getContent();
            $this->assertStringContainsString('<body class="wk-root"', $html, $slug);
            $this->assertStringContainsString('class="wk-tool kit-tool"', $html, $slug);
            $this->assertStringContainsString('id="resultCard"', $html, $slug);
            $this->assertStringContainsString('<ol class="wk-crumbs">', $html, $slug);
            $this->assertSame(1, substr_count($html, '<h1'), $slug);
            $this->assertStringNotContainsString('type="number"', substr($html, strpos($html, 'id="kForm"'), 60000), "$slug: sayı alanları metin + ondalık klavye");
            $this->assertStringNotContainsString('fonts.googleapis.com', $html, $slug);
        }
    }

    public function test_sayi_alani_virgulu_korur_ve_sunucu_virgulu_okur(): void
    {
        $html = $this->get('/araclar/parke-maliyet-hesaplayici')->getContent();
        $this->assertMatchesRegularExpression('#<input type="text" class="wk-input" id="f_area" name="area" inputmode="decimal"#', $html);

        $a = $this->getJson('/araclar/parke-maliyet-hesaplayici/hesapla?type=laminat&area=12,5&rooms=tek&quality=orta&scope=hazir')->json();
        $b = $this->getJson('/araclar/parke-maliyet-hesaplayici/hesapla?type=laminat&area=12.5&rooms=tek&quality=orta&scope=hazir')->json();
        $this->assertSame($b['mid'] ?? null, $a['mid'] ?? null);
    }

    public function test_dizi_parametre_500_vermez(): void
    {
        foreach (['/araclar/boya-hesaplayici/hesapla?target[]=x', '/araclar/parke-maliyet-hesaplayici/hesapla?area[]=5', '/araclar/zemin-kaplama/hesapla?extras[][]=x', '/araclar/seramik-maliyet-hesaplayici?en[]=1'] as $u) {
            $this->get($u)->assertOk();
        }
    }

    public function test_sinir_disi_deger_not_dusulur_sifir_girilmedi_sayilir(): void
    {
        $r = $this->getJson('/araclar/parke-maliyet-hesaplayici/hesapla?type=laminat&area=999999&rooms=tek&quality=orta&scope=hazir')->json();
        $this->assertTrue($r['ready']);
        $this->assertNotEmpty(array_filter($r['notes'], fn ($n) => str_contains($n, 'girildi; hesap en fazla')));

        $r = $this->getJson('/araclar/parke-maliyet-hesaplayici/hesapla?type=laminat&area=0&rooms=tek&quality=orta&scope=hazir')->json();
        $this->assertFalse($r['ready'], '0 m² ile fiyat gösterilmez');
    }

    public function test_sure_tek_degerse_tekrar_yazilmaz(): void
    {
        foreach (KitRegistry::slugs() as $slug) {
            $e = KitRegistry::get($slug);
            $this->assertInstanceOf(\App\Estimators\KitEstimator::class, $e);
        }
        $r = $this->getJson('/araclar/parke-maliyet-hesaplayici/hesapla?type=laminat&area=8&rooms=tek&quality=orta&scope=hazir')->json();
        foreach ($r['meta'] as [$k, $v]) {
            $this->assertDoesNotMatchRegularExpression('/\b(\d+)–\1 iş günü/u', (string) $v);
        }
    }

    public function test_sonuc_kartinda_kdv_durumu_yazar(): void
    {
        $this->get('/araclar/boya-hesaplayici')->assertSee('id="kVat">KDV dahil</span>', false);
    }
}

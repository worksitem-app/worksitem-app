<?php

namespace Tests\Feature;

use App\Estimators\KitRegistry;
use App\Estimators\PozEstimator;
use App\Http\Controllers\ConTech\DrywallCalculatorController;
use App\Models\Lead;
use App\Support\ToolSeo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WaveOneSeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        PozEstimator::flushCache();
    }

    public function test_baslik_ve_aciklama_kurallari(): void
    {
        $this->assertSame('Şap Fiyatı 2026: m² Şap Maliyeti | Worksitem', ToolSeo::title('Şap Fiyatı 2026: m² Şap Maliyeti | Worksitem'));
        $long = ToolSeo::title('Asma Tavan Fiyatı 2026: m² Alüminyum, Galvaniz, Lamel Tavan (YFK) | Worksitem');
        $this->assertSame('Asma Tavan Fiyatı 2026: m² Alüminyum, Galvaniz, Lamel Tavan', $long);
        $this->assertLessThanOrEqual(158, mb_strlen(ToolSeo::desc(str_repeat('Uzun bir açıklama cümlesi. ', 12))));
        $this->assertCount(1, ToolSeo::faq([['A kaç TL?', 'yaklaşık  tutar.'], ['B?', 'Yaklaşık 50–70 bin TL tutar.']]));
    }

    public function test_ilk_dalga_sayfalari_kisa_baslik_cta_ve_kisa_cevap(): void
    {
        $wave = config('worksitem_golive.presets.ilk-dalga');
        $this->assertCount(25, $wave);
        config(['worksitem_tools.tool_status.overrides' => array_fill_keys($wave, 'active')]);

        foreach ($wave as $slug) {
            $html = $this->get('/araclar/'.$slug)->assertOk()->getContent();
            preg_match('/<title>(.*?)<\/title>/s', $html, $t);
            $this->assertLessThanOrEqual(62, mb_strlen(html_entity_decode(trim($t[1] ?? ''))), $slug.' başlık');
            $this->assertStringNotContainsString('(YFK)', $t[1] ?? '', $slug);
            $this->assertStringContainsString('class="ws-bc"', $html, $slug.' marka CTA');
            $this->assertStringContainsString('/markalar-icin?arac='.$slug, $html, $slug.' CTA bağlantısı');
            $this->assertStringContainsString('"WebApplication"', $html, $slug);
            $this->assertStringNotContainsString('yaklaşık  tutar', $html, $slug);
            if (in_array($slug, KitRegistry::slugs(), true) && ! in_array($slug, ['alcipan-hesaplayici', 'asma-tavan-hesaplayici', 'su-yalitimi-hesaplayici', 'epoksi-zemin-hesaplayici', 'cati-maliyet-hesaplayici'], true)) {
                $this->assertStringContainsString('Kısa cevap:', $html, $slug);
            }
        }
    }

    public function test_yfk_ssss_ornek_m2_fiyati_verir_fiyat_yoksa_uydurmaz(): void
    {
        $e = KitRegistry::get('alcipan-hesaplayici');
        $this->assertStringContainsString('resmî YFK birim fiyatından', $e->page()['faq'][0][1]);

        $poz = DrywallCalculatorController::catalog()['partition_c50_standard']['poz'];
        DB::table('rayic_prices')->insert(['source' => 'YFK', 'period' => '2026-08', 'poz_no' => $poz, 'description' => 'Test', 'unit' => 'm²', 'price' => 1000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        PozEstimator::flushCache();
        $a = KitRegistry::get('alcipan-hesaplayici')->page()['faq'][0][1];
        $this->assertMatchesRegularExpression('/m² başına yaklaşık [\d.]+–[\d.]+ TL tutar/u', $a);
    }

    public function test_asma_tavan_ilk_sistemin_fiyati_yoksa_fiyati_olan_sistemi_adiyla_yazar(): void
    {
        $poz = \App\Http\Controllers\ConTech\CeilingCalculatorController::catalog()['alu60_sarkmali']['poz'];
        DB::table('rayic_prices')->insert(['source' => 'YFK', 'period' => '2026-08', 'poz_no' => $poz, 'description' => 'Test', 'unit' => 'm²', 'price' => 800, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        PozEstimator::flushCache();
        $a = KitRegistry::get('asma-tavan-hesaplayici')->page()['faq'][0][1];
        $this->assertStringContainsString('60x60 Alüminyum Sarkmalı Sistem KDV dahil m² başına yaklaşık', $a);

        // Sistemi sabit adlandıran metin başka sisteme kaymaz (alçıpan fiyatı yok → yedek metin)
        $this->assertStringContainsString('resmî YFK birim fiyatından', KitRegistry::get('alcipan-hesaplayici')->page()['faq'][0][1]);
    }

    public function test_markalar_sayfasi_araci_tanir_ve_basvuruya_yazar(): void
    {
        $this->get('/markalar-icin?arac=boya-hesaplayici')->assertOk()
            ->assertSee('Seçtiğiniz araç')
            ->assertSee('Boya hesaplayıcı', false)
            ->assertSee('name="tool" value="boya-hesaplayici"', false);
        $this->get('/markalar-icin?arac=olmayan-arac')->assertOk()->assertDontSee('Seçtiğiniz araç');

        $this->post('/markalar-icin', [
            'company' => 'Örnek Boya A.Ş.', 'name' => 'Ayşe Yılmaz', 'phone' => '05321234567', 'email' => 'ayse@ornek.com',
            'interest' => 'Boya', 'tool' => 'boya-hesaplayici', 'contact_consent' => '1',
        ])->assertRedirect();
        $this->assertStringContainsString('Araç: boya-hesaplayici', (string) Lead::query()->latest('id')->value('notes'));
    }

    public function test_boya_iscilik_daire_m2_si_basina_hesaplanir(): void
    {
        $e = KitRegistry::get('boya-hesaplayici');
        $r = $e->estimate($e->normalize(['target' => 'daire', 'floor' => 100, 'surfaces' => 'duvar_tavan', 'color' => 'ayni', 'state' => 'iyi', 'quality' => 'orta']));
        // 250 TL × 100 m² daire ≈ 25.000 TL ustalık (boyanan 370 m² yüzeye 250 TL değil)
        $this->assertEqualsWithDelta(25000, $r['items']['iscilik']['mid'], 2500);
        $this->assertLessThan(90000, $r['high']);
    }

    public function test_llms_txt_yalniz_aktif_araclari_listeler(): void
    {
        config(['worksitem_tools.tool_status.default' => 'review', 'worksitem_tools.tool_status.overrides' => ['boya-hesaplayici' => 'active']]);
        $txt = $this->get('/llms.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')->getContent();
        $this->assertStringContainsString('https://worksitem.com/araclar/boya-hesaplayici', $txt);
        $this->assertStringNotContainsString('https://worksitem.com/araclar/mantolama-maliyeti', $txt);
        $this->assertStringContainsString('https://worksitem.com/markalar-icin', $txt);
        $this->assertStringContainsString('Kainta İnşaat Uzmanları', $txt);
        $this->assertStringNotContainsString(':tools', $txt);
    }
}

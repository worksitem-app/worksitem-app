<?php

namespace Tests\Feature;

use App\Http\Controllers\Tools\BathPlannerController;
use App\Models\TenderPoz;
use App\Services\PriceService;
use App\Services\Solar\SolarPlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v119 araç denetimi (G, D, E): ihale, metraj, belge, planlayıcılar, havalandırma, GES PVGIS */
class AracDenetimV119IhaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['worksitem_tools.tool_status.overrides' => ['ges-planlayici' => 'active', 'gunes-paneli-hesaplama' => 'active']]);
    }

    private function poz(array $a): TenderPoz
    {
        return TenderPoz::create($a + ['year' => '2026', 'period' => '2026-08', 'unit' => 'm²', 'is_active' => true, 'description' => 'Poz']);
    }

    // ---------- İhale modülü ----------

    public function test_arama_resmi_fiyati_dondurur_fiyatlilari_one_alir_ve_nan_uretmez(): void
    {
        $this->poz(['institution' => 'ÇŞB', 'poz_no' => '10.000.0001', 'unit_price' => 0, 'description' => 'Fiyatsız poz']);
        $this->poz(['institution' => 'ÇŞİDB/YFK', 'poz_no' => '10.000.0002', 'unit_price' => 1000, 'description' => 'Kâr durumu bilinmiyor']);
        $this->poz(['institution' => 'ÇŞB', 'poz_no' => '10.000.0003', 'unit_price' => 1250, 'profit_status' => 'included', 'included_profit_rate' => 0.25, 'description' => 'Teklif fiyatlı']);

        $rows = $this->getJson('/api/pozlar/search?q=&institution=all')->assertOk()->json();
        $this->assertSame(['10.000.0003', '10.000.0002', '10.000.0001'], array_column($rows, 'poz_no'));   // fiyatlı önce

        [$offer, $official, $none] = $rows;
        $this->assertTrue($offer['has_offer_price']);
        $this->assertEquals(1250, $offer['official_unit_price']);
        $this->assertNotNull($offer['offer_unit_price']);
        // kâr durumu bilinmiyor: teklif yok ama resmî fiyat var (eskiden yanıtta hiç fiyat yoktu → NaN)
        $this->assertNull($official['offer_unit_price']);
        $this->assertEquals(1000, $official['official_unit_price']);
        $this->assertTrue($official['has_official_price']);
        // 0 fiyat "fiyat yok" sayılır
        $this->assertNull($none['official_unit_price']);
        $this->assertFalse($none['has_official_price']);
        $this->assertSame('no_price', $none['quote_status']);
    }

    public function test_csb_filtresi_csidb_yfk_etiketlerini_de_kapsar_ve_kultur_ptt_secilebilir(): void
    {
        $this->poz(['institution' => 'ÇŞB', 'poz_no' => 'A.1', 'unit_price' => 10]);
        $this->poz(['institution' => 'ÇŞİDB/YFK', 'poz_no' => 'A.2', 'unit_price' => 10]);
        $this->poz(['institution' => 'Kültür/Vakıflar', 'poz_no' => 'V.1', 'unit_price' => 10]);
        $this->poz(['institution' => 'PTT', 'poz_no' => 'P.1', 'unit_price' => 10]);
        $this->poz(['institution' => 'Ulaştırma/Liman', 'poz_no' => 'L.1', 'unit_price' => 10]);

        $csb = array_column($this->getJson('/api/pozlar/search?institution='.urlencode('ÇŞB'))->json(), 'poz_no');
        sort($csb);
        $this->assertSame(['A.1', 'A.2'], $csb);
        $this->assertSame(['V.1'], array_column($this->getJson('/api/pozlar/search?institution='.urlencode('Kültür/Vakıflar'))->json(), 'poz_no'));
        $this->assertSame(['P.1'], array_column($this->getJson('/api/pozlar/search?institution=PTT')->json(), 'poz_no'));
        $this->assertSame(['L.1'], array_column($this->getJson('/api/pozlar/search?institution='.urlencode('Ulaştırma'))->json(), 'poz_no'));

        $html = $this->get('/araclar/ihale-modulu')->assertOk()->getContent();
        $this->assertStringContainsString('value="Kültür/Vakıflar"', $html);
        $this->assertStringContainsString('value="PTT"', $html);
        $this->assertStringContainsString('ÇŞB / YFK', $html);
    }

    public function test_ihale_sayfasi_resmi_ve_teklif_fiyatini_ayirir_innerhtml_ve_onclick_kullanmaz(): void
    {
        $html = $this->get('/araclar/ihale-modulu')->assertOk()->getContent();
        $this->assertStringContainsString('RESMÎ BİRİM FİYAT', $html);
        $this->assertStringContainsString('TEKLİF BİRİM FİYATI', $html);
        $this->assertStringContainsString('resmî fiyat değildir', $html);
        $this->assertStringContainsString('poz fiyatsız', $html);
        $start = strpos($html, 'let items = [];');
        $js = substr($html, $start, strpos($html, '</script>', $start) - $start);
        $this->assertStringNotContainsString('innerHTML', $js);           // depolanmış XSS
        $this->assertStringNotContainsString("removeRow('", $html);         // ' içeren poz no
        $this->assertStringNotContainsString('?? p.unit_price', $js);       // yanıtta olmayan alana düşme
    }

    // ---------- Dijital metraj / belge oluşturucu ----------

    public function test_dijital_metraj_merkezleme_kirpmaz_pan_ve_yakinlastirma_var(): void
    {
        $html = $this->get('/araclar/dijital-metraj')->assertOk()->getContent();
        $this->assertStringNotContainsString('display: flex; align-items: center; justify-content: center; }', $html);
        $this->assertStringContainsString("canvas.addEventListener('pointermove'", $html);
        $this->assertStringContainsString('id="zoomIn"', $html);
        $this->assertStringContainsString('isEvalSupported: false', $html);
    }

    public function test_belge_olusturucu_pdf_kaydirma_ve_mantikli_sablon_metni(): void
    {
        $html = $this->get('/araclar/belge-olusturucu')->assertOk()->getContent();
        $this->assertStringContainsString('scrollX: 0, scrollY: 0', $html);
        $this->assertStringNotContainsString('tarihi itibariyle', $html);
        $this->assertStringNotContainsString('cezai kesinti', $html);
        $this->assertStringNotContainsString("outDuration\"></span>'dır", $html);
        $this->assertStringContainsString("'İhlal başına kesinti tutarı'", $html);
        $this->assertStringContainsString("'İşin bitirildiği tarih'", $html);
    }

    // ---------- Planlayıcılar ----------

    public function test_mutfak_planlayici_tek_parca_siniri(): void
    {
        $this->assertSame(24, BathPlannerController::maxItems('mutfak'));
        $this->assertSame(12, BathPlannerController::maxItems('banyo'));
        $html = $this->get('/araclar/mutfak-planlayici')->assertOk()->getContent();
        $this->assertStringContainsString('"maxItems":24', $html);
        $this->assertStringContainsString('.slice(0, MAX_ALL)', $html);
        $this->assertStringNotContainsString('.slice(0, 20).map', $html);
        $this->assertStringNotContainsString('S.items.length >= 12) return', $html);
    }

    public function test_oda_planlayici_ctabox_yokken_kilitlenmez_ve_dogru_nasil_calisir(): void
    {
        config(['services.ai_spend_enabled' => true]);
        config(['worksitem_tools.tool_status.overrides' => ['oda-planlayici' => 'active', 'mutfak-planlayici' => 'active']]);
        $html = $this->get('/araclar/oda-planlayici')->assertOk()->getContent();
        $this->assertStringNotContainsString("getElementById('ctaBox').style", $html);
        $this->assertStringContainsString("show('ctaBox'", $html);
        $this->assertStringNotContainsString('ürünleri sürükleyip bırakın', $html);
        $this->assertStringNotContainsString('2D / 3D görün', $html);
        $this->assertStringContainsString('Örnek görsel üretin', $html);
        // diğer planlayıcılar 2D/3D adımlarını korur
        $this->assertStringContainsString('2D / 3D görün', $this->get('/araclar/mutfak-planlayici')->getContent());
    }

    // ---------- Havalandırma ----------

    public function test_havalandirma_config_donemini_kullanir_ve_eksik_kalemde_butce_kapali(): void
    {
        config(['worksitem_estimators.yfk_period' => '2027-02']);
        $periods = [];
        $this->mock(PriceService::class, function ($m) use (&$periods) {
            $m->shouldReceive('byPoz')->andReturnUsing(function ($poz, $period) use (&$periods) {
                $periods[] = $period;

                return ['price' => 100.0, 'unit' => 'm²'];
            });
        });
        $html = $this->get('/araclar/havalandirma-hesaplayici')->assertOk()->getContent();
        $this->assertNotEmpty($periods);
        $this->assertSame(['2027-02'], array_values(array_unique($periods)));
        $this->assertStringContainsString('katalog dışı ölçü', $html);
        $this->assertStringContainsString('let allFound = !!duct.item', $html);
        $this->assertStringContainsString('allFound && grandTotal > 0', $html);
    }

    // ---------- GES / PVGIS ----------

    public function test_pvgis_asili_kalinca_sure_butcesi_ve_basarisizlik_onbellegi(): void
    {
        $calls = 0;
        Http::fake(['re.jrc.ec.europa.eu/*' => function () use (&$calls) {
            $calls++;
            throw new ConnectionException('timed out');
        }]);
        $faces = [];
        foreach ([0, 45, 90, 135, 180, 225, 270, 315, 180, 180, 90, 270] as $az) {
            $faces[] = ['kwp' => 2, 'angle' => 30, 'azimuth' => $az];
        }
        $s = app(SolarPlanService::class);
        $y = $s->yield(39.9, 32.8, $faces);
        $this->assertSame('yaklaşık', $y['source']);
        $this->assertTrue($y['estimated']);
        $this->assertStringContainsString('tahmini', $y['note']);
        $this->assertSame(1, $calls);              // bağlantı hatasında yeniden deneme yok, PVGIS kısa süre "kapalı"
        $this->assertGreaterThan(0, $y['yearly']);

        $s->yield(39.9, 32.8, $faces);
        $this->assertSame(1, $calls);              // başarısızlık önbellekte: tekrar gidilmez
    }

    public function test_pvgis_ayni_yuzey_tek_istek_ve_basarili_yanit_onbellekte(): void
    {
        Http::fake(['re.jrc.ec.europa.eu/*' => Http::response(['outputs' => ['monthly' => ['fixed' => array_map(fn ($m) => ['month' => $m, 'E_m' => 100], range(1, 12))]]])]);
        $s = app(SolarPlanService::class);
        $faces = [['kwp' => 1, 'angle' => 30, 'azimuth' => 180], ['kwp' => 2, 'angle' => 30, 'azimuth' => 180], ['kwp' => 1, 'angle' => 30, 'azimuth' => 90]];
        $y = $s->yield(40.0, 29.0, $faces);
        $this->assertSame('PVGIS', $y['source']);
        $this->assertFalse($y['estimated']);
        $this->assertNull($y['note']);
        $this->assertEquals(4 * 1200, $y['yearly']);
        Http::assertSentCount(2);                  // aynı eğim/yön için tek istek
        $s->yield(40.0, 29.0, $faces);
        Http::assertSentCount(2);                  // önbellekten
    }

    public function test_pvgis_5xx_basarisizligi_yuzey_icin_onbellege_alinir(): void
    {
        Http::fake(['re.jrc.ec.europa.eu/*' => Http::response('down', 529)]);
        $s = app(SolarPlanService::class);
        $s->yield(38.4, 27.1, [['kwp' => 5, 'angle' => 15, 'azimuth' => 180]]);
        Http::assertSentCount(2);                  // 5xx: bir yeniden deneme
        $s->yield(38.4, 27.1, [['kwp' => 5, 'angle' => 15, 'azimuth' => 180]]);
        Http::assertSentCount(2);
    }
}

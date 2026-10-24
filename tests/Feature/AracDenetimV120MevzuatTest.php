<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v120 denetimi: yatırım ve mevzuat araçları (rapor F, G) */
class AracDenetimV120MevzuatTest extends TestCase
{
    use RefreshDatabase;

    private function html(string $slug): string
    {
        return $this->get('/araclar/'.$slug)->assertOk()->getContent();
    }

    public function test_mevzuat_config_2026_degerleri(): void
    {
        $c = config('worksitem_mevzuat_2026');
        $this->assertSame(21050, $c['yapi_sinif']['3B']);            // ÇŞB 2026 III-B
        $this->assertSame(26450, $c['yapi_sinif']['4A']);
        $this->assertSame(0.3875, $c['asgari_iscilik_prim']);         // 2026 prim
        $this->assertSame([0.70, 0.60, 0.50], $c['bddk_ltv'][2][1]);  // 7–10 M dilimi
        $this->assertSame(10, $c['siginak']['konut_bb_esik']);
        $this->assertSame(20, $c['siginak']['emsal_kisi_bolen']);
    }

    public function test_viewport_ve_box_sizing_bagimsiz_sayfalarda(): void
    {
        foreach (['azami-konut-kredisi', 'lojistik-maliyeti', 'kesim-optimizasyonu', 'birim-fiyat-analiz', 'siginak-yonetmeligi', 'insaat-muhendisi-maas-hesabi'] as $slug) {
            $h = $this->html($slug);
            $this->assertStringContainsString('name="viewport"', $h, $slug);
            $this->assertMatchesRegularExpression('/box-sizing:\s*border-box/', $h, $slug);
        }
    }

    public function test_konut_kredisi_bddk_dilimli_ve_faiz_vade_girilebilir(): void
    {
        $h = $this->html('azami-konut-kredisi');
        $this->assertStringContainsString('const LTV = [[5000000,[0.9,0.8,0.7]]', $h);
        $this->assertStringContainsString('id="rate"', $h);
        $this->assertStringContainsString('id="term"', $h);
        $this->assertStringNotContainsString('İkinci El - %80', $h);
        $this->assertStringNotContainsString('Peşinatı artırarak limiti', $h);
    }

    public function test_kira_uyarisi_durum_katsayili_sureyi_kullanir(): void
    {
        $h = $this->html('daire-kira-degeri');
        $this->assertStringContainsString('const effYears = propertyValue / adjustedAnnualRent;', $h);
        $this->assertStringContainsString('if (effYears <= 15)', $h);
        $this->assertStringNotContainsString("'% ' + roi.toFixed(2)", $h);
        $this->assertStringNotContainsString('alert(', $h);
    }

    public function test_kat_karsiligi_maliyet_resmi_kaynakli_ve_emsal_harici_satisa_girmez(): void
    {
        $h = $this->html('kat-karsiligi-analiz');
        $this->assertStringNotContainsString('value="18000"', $h);
        $this->assertStringContainsString('21.050 TL/m²', $h);                 // resmî 3B
        $this->assertStringContainsString('value="33680"', $h);                // 21.050 × 1,6 piyasa
        $this->assertStringContainsString('const contractorArea = emsalArea * contractorPercentage;', $h);
        $this->assertStringNotContainsString('* 1.30', $h);
        $this->assertStringContainsString('id="exRate"', $h);
        $this->assertStringContainsString('id="extraRate"', $h);
    }

    public function test_taks_kaks_dogrulama_ve_yasal_artis_ifadesi_yok(): void
    {
        $h = $this->html('taks-kaks-hesaplayici');
        $this->assertStringContainsString('if (taks > 1)', $h);
        $this->assertStringContainsString('if (kaks < taks)', $h);
        $this->assertStringContainsString('Math.floor(ratio + 1e-9)', $h);
        $this->assertStringNotContainsString('Yasal Artış', $h);
        $this->assertStringNotContainsString('Satılabilir', $h);
    }

    public function test_lojistik_mesafe_sonuca_girer(): void
    {
        $h = $this->html('lojistik-maliyeti');
        $this->assertStringContainsString('const tripCost = fee + d * kmRate;', $h);
        $this->assertStringContainsString('id="vtype"', $h);
        $this->assertStringContainsString('Tonaj ve kapasite için pozitif', $h);
    }

    public function test_kesim_ffd_ve_parca_stoktan_buyuk_hatasi(): void
    {
        $h = $this->html('kesim-optimizasyonu');
        $this->assertStringContainsString('function ffd(', $h);
        $this->assertStringContainsString('x.used + b + p <= s', $h);         // n−1 testere kesimi
        $this->assertStringContainsString('stok boyundan', $h);
        $this->assertStringNotContainsString('if(piecesPerStock < 1) piecesPerStock = 1', $h);
    }

    public function test_sgk_2026_degerleri_ve_kosullu_eksiltme(): void
    {
        $h = $this->html('sgk-asgari-iscilik');
        $this->assertStringContainsString('value="21050"', $h);
        $this->assertStringContainsString('const PRIM = 0.3875;', $h);
        $this->assertStringContainsString("review === 'unite' && declared > 0", $h);
        $this->assertStringNotContainsString('0.375', $h);
        $this->assertStringNotContainsString('16.000 TL', $h);
    }

    public function test_iscilik_2026_brutlestirme(): void
    {
        $h = $this->html('iscilik-maliyeti');
        $this->assertStringContainsString('const MW_GROSS = 33030', $h);
        $this->assertStringContainsString('id="incentive"', $h);
        $this->assertStringNotContainsString('netTotal * 0.375', $h);
        $this->assertStringNotContainsString('%37.5', $h);
    }

    public function test_iscilik_yuk_orani_elle_hesapla_uyumlu(): void
    {
        // 1.500 TL/gün net, teşvikli: rapordaki el hesabı işveren maliyeti ≈ 69.037 TL (nete göre %53,4)
        $c = config('worksitem_mevzuat_2026');
        $net = 45000;
        $k = 1 - $c['sgk_isci'] - $c['gv_ilk_dilim'] * (1 - $c['sgk_isci']) - $c['damga'];
        $ex = $c['gv_ilk_dilim'] * $c['asgari_brut'] * (1 - $c['sgk_isci']) + $c['damga'] * $c['asgari_brut'];
        $gross = ($net - $ex) / $k;
        $emp = $gross * (1 + $c['sgk_isveren'] - $c['sgk_tesvik_puan']);
        $this->assertEqualsWithDelta(56704, $gross, 60);
        $this->assertEqualsWithDelta(69037, $emp, 80);
    }

    public function test_muhendis_maasi_tmmob_iddiasi_yok(): void
    {
        $h = $this->html('insaat-muhendisi-maas-hesabi');
        $this->assertStringNotContainsString('TMMOB Brüt Asgari', $h);
        $this->assertStringNotContainsString('45.000 TL', $h);
        $this->assertStringContainsString('piyasa tahmini', $h);
    }

    public function test_asansor_paiy_34_kurallari_ve_metre_olcutu_yok(): void
    {
        $h = $this->html('asansor-zorunlulugu');
        $this->assertStringNotContainsString('13.50', $h);
        $this->assertStringNotContainsString('13,50', $h);
        $this->assertStringContainsString('id="basementUsed"', $h);
        $this->assertStringContainsString('id="singleUnit"', $h);
        $this->assertStringContainsString('countedFloors >= 4', $h);
        $this->assertStringContainsString('countedFloors === 3', $h);
    }

    public function test_siginak_yonetmeligi_2025_kurallari(): void
    {
        $h = $this->html('siginak-yonetmeligi');
        $this->assertStringContainsString('amount >= S.konut_bb_esik', $h);
        $this->assertStringContainsString('S.emsal_kisi_bolen', $h);
        $this->assertStringContainsString('id="rooms"', $h);
        $this->assertStringNotContainsString('amount * 1.5', $h);
        $this->assertStringNotContainsString('SİĞINAK', $h);
    }

    public function test_birim_fiyat_negatif_ve_kurus(): void
    {
        $h = $this->html('birim-fiyat-analiz');
        $this->assertStringContainsString('Tutarlar negatif olamaz.', $h);
        $this->assertStringContainsString('minimumFractionDigits: 2', $h);
        $this->assertStringContainsString('KDV hariç', $h);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * v118 araç denetimi (W2 mühendislik / mekanik): raporlar B ve E.
 * Hesaplar istemci tarafında; testler sayfanın doğru formülü ve uyarıları içerdiğini denetler.
 * Tarayıcı doğrulaması: scratchpad/dev-W2-muhendislik-mekanik/check.py
 */
class AracDenetimV118MuhendislikTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_tools.tool_status.default' => 'active']);
    }

    private function html(string $slug): string
    {
        return $this->followingRedirects()->get('/araclar/'.$slug)->assertOk()->getContent();
    }

    public static function araclar(): array
    {
        return array_map(fn ($s) => [$s], [
            'basit-kiris-statigi', 'bina-agirlik-hesabi', 'donati-tahvil-hesaplayici', 'karot-dayanim', 'kolon-filiz-boyu',
            'kolon-tasima-kapasitesi', 'yapi-dinamigi', 'merdiven-basamak-hesabi', 'is-makinesi-maliyeti', 'bombeli-tank-hacmi',
            'pompa-gucu-hesabi', 'yagmur-borusu-hesabi', 'jenerator-hesaplama', 'enerji-tuketim-hesaplama', 'kablo-kesiti-hesabi',
            'klima-btu-hesaplayici',
        ]);
    }

    #[DataProvider('araclar')]
    public function test_mobil_viewport_box_sizing_ve_girdi_uyarisi(string $slug): void
    {
        $html = $this->html($slug);
        $this->assertStringContainsString('name="viewport" content="width=device-width, initial-scale=1', $html);
        $this->assertMatchesRegularExpression('/box-sizing:\s*border-box/', $html);
        // negatif/0/boş girdi için Türkçe uyarı (kolon-filiz yalnız seçim kutusu, serbest girdi yok)
        if ($slug === 'kolon-filiz-boyu') {
            $this->assertStringNotContainsString('type="number"', $html);

            return;
        }
        $this->assertMatchesRegularExpression('/pozitif bir değer|pozitif bir tam sayı|en az 1 olan bir tam sayı/u', $html);
    }

    public function test_yapi_dinamigi_ct_tbdy_2018(): void
    {
        $html = $this->html('yapi-dinamigi');
        $this->assertStringContainsString('<option value="0.1">Yalnız betonarme çerçeve (Ct = 0,10)</option>', $html);
        $this->assertStringContainsString('<option value="0.08">Çelik / çaprazlı çelik çerçeve (Ct = 0,08)</option>', $html);
        $this->assertStringContainsString('<option value="0.07" selected>', $html);
        $this->assertStringNotContainsString('value="0.05"', $html);
        $this->assertStringContainsString('TpA', $html);
        $this->assertStringContainsString('1.4 * t', $html);
    }

    public function test_kolon_filiz_kenetlenme_ve_bindirme_ayri(): void
    {
        $html = $this->html('kolon-filiz-boyu');
        $this->assertStringContainsString('0.12 * (fyd / fctd) * d, 20 * d', $html);   // TS 500 Denk. 9.1
        $this->assertStringContainsString('0.35 * Math.sqrt(fck) / 1.5', $html);
        $this->assertStringContainsString('<option value="1.5" selected>', $html);       // alt uçta yarısından fazlası → 1,5·lb
        $this->assertStringContainsString('<option value="1.25">', $html);
        $this->assertStringContainsString('TBDY 2018 7.3.3.1', $html);
        $this->assertStringContainsString('Kenetlenme boyu lb', $html);
        $this->assertStringContainsString('MİNİMUM BİNDİRME (EK) BOYU', $html);
        // eski hatalı çarpan (C25 → 40Ø "bindirme") kalmadı
        $this->assertStringNotContainsString('<option value="40">C25', $html);
    }

    public function test_kolon_tasima_tbdy_siniri_ana_sonuc(): void
    {
        $html = $this->html('kolon-tasima-kapasitesi');
        $this->assertStringContainsString('const NdMax = 0.40 * fck * Ac;', $html);
        $this->assertStringContainsString("res.innerText = fmtT(NdMax);", $html);
        $this->assertStringContainsString('Saf eksenel taşıma gücü (Nor', $html);
        $this->assertStringContainsString('rho < 0.01 || rho > 0.04', $html);
    }

    public function test_donati_tahvil_azami_aralik(): void
    {
        $html = $this->html('donati-tahvil-hesaplayici');
        $this->assertStringContainsString('<option value="20" selected>Döşeme ana donatısı', $html);
        $this->assertStringContainsString('Perde gövde donatısı (≤ 25 cm)', $html);
        $this->assertStringContainsString('if (safeSpacing > sMax)', $html);
        $this->assertStringNotContainsString('if(safeSpacing > 30)', $html);
    }

    public function test_karot_sifir_ve_hd_denetimi(): void
    {
        $html = $this->html('karot-dayanim');
        $this->assertStringNotContainsString("|| 1;", $html);   // d=0 artık 1 mm sayılmıyor
        $this->assertStringContainsString('if (ratio < 1)', $html);
        $this->assertStringContainsString('binanın beton dayanımı değildir', $html);
        $this->assertStringContainsString('en az 3 karot', $html);
    }

    public function test_bina_agirligi_g_arti_03q(): void
    {
        $html = $this->html('bina-agirlik-hesabi');
        $this->assertStringContainsString('W = G + n·Q', $html);
        $this->assertStringContainsString('<option value="0.2|0.3" selected>Konut', $html);
        $this->assertStringContainsString('const w1 = g + n * q;', $html);
        $this->assertStringNotContainsString('f * a * 1.15', $html);
    }

    public function test_is_makinesi_donem_kullanici_secimi(): void
    {
        $html = $this->html('is-makinesi-maliyeti');
        $this->assertStringContainsString('data-period="month">Kule', $html);
        $this->assertStringContainsString("dataset.period === 'month'", $html);
        $this->assertStringNotContainsString('rentVal > 100000', $html);
    }

    public function test_bombeli_tank_kapak_tipi(): void
    {
        $html = $this->html('bombeli-tank-hacmi');
        $this->assertStringContainsString('<option value="ell" selected>2:1 elips', $html);
        $this->assertStringContainsString('ell: Math.PI * Math.pow(d, 3) / 24', $html);
        $this->assertStringContainsString('klop: 0.0998 * Math.pow(d, 3)', $html);
        $this->assertStringContainsString('hemi: Math.PI * Math.pow(d, 3) / 12', $html);
    }

    public function test_pompa_verim_ve_motor_kademesi(): void
    {
        $html = $this->html('pompa-gucu-hesabi');
        $this->assertStringContainsString('id="eff"', $html);
        $this->assertStringContainsString('(q * h) / (367 * eta)', $html);
        $this->assertStringContainsString('Pompa mil gücü', $html);
        $this->assertStringContainsString('2.2, 3, 4, 5.5', $html);
        $this->assertStringNotContainsString('MİNUMUM', $html);
    }

    public function test_yagmur_borusu_tl_bicimi_ve_adet(): void
    {
        $html = $this->html('yagmur-borusu-hesabi');
        $this->assertStringContainsString("toLocaleString('tr-TR'", $html);
        $this->assertStringContainsString('en az 1 olan bir tam sayı', $html);
        $this->assertStringNotContainsString("parseInt(document.getElementById('count').value)||1", $html);
        $this->assertStringNotContainsString('(2026 ÇŞB)', $html);
    }

    public function test_jenerator_motor_kalkis_payi(): void
    {
        $html = $this->html('jenerator-hesaplama');
        $this->assertStringContainsString('id="motorW"', $html);
        $this->assertStringContainsString('Math.max(kw * 0.20, motorW / 1000)', $html);
    }

    public function test_enerji_fiyati_tek_kaynak_ve_guncel(): void
    {
        $this->assertEquals(3.24, config('pricing.enerji.elektrik_kwh_fiyati'));   // EPDK Nisan 2026, 100 kWh = 323,8 TL
        // dizide yinelenen anahtar kalmadı
        $src = file_get_contents(config_path('pricing.php'));
        $this->assertSame(1, substr_count($src, "'enerji' => ["));
        $this->assertSame(1, substr_count($src, "'harc' => ["));

        config(['pricing.enerji.elektrik_kwh_fiyati' => 4.1]);
        $html = $this->html('enerji-tuketim-hesaplama');
        $this->assertStringContainsString('"elektrik_kwh_fiyati":4.1', $html);
        $this->assertStringContainsString('Birim fiyat: 4,10 TL/kWh', $html);
        $this->assertStringNotContainsString('|| 2.25', $html);
    }

    public function test_kablo_cos_phi_ve_sinir_uyarisi(): void
    {
        $html = $this->html('kablo-kesiti-hesabi');
        $this->assertStringContainsString('id="cosphi"', $html);
        $this->assertStringContainsString('pW / (220 * cosPhi)', $html);
        $this->assertStringContainsString('pW / (1.732 * 380 * cosPhi)', $html);
        $this->assertStringContainsString('const overCurrent = current > 330;', $html);
        $this->assertStringContainsString('const overDrop = actual_e > limitE;', $html);
        $this->assertStringContainsString('id="limitWarn"', $html);
    }

    public function test_klima_onerisi_yuku_karsilar_ve_kesilmez(): void
    {
        $html = $this->html('klima-btu-hesaplayici');
        $this->assertStringContainsString('SIZES.find(x => x >= totalLoad)', $html);
        $this->assertStringContainsString('Math.ceil(totalLoad / multiSize)', $html);
        $this->assertStringNotContainsString('iki adet 12.000 BTU cihaz', $html);
        $this->assertStringNotContainsString('bilimsel olarak', $html);
    }

    public function test_basit_kiris_uyari_notu_sonuc_kutusunu_bozmaz(): void
    {
        $html = $this->html('basit-kiris-statigi');
        // iki sonuç sütunu ayrı sarmalayıcıda; sonuç kutusu flex değil (hukuki not 3. sütun olmaz)
        $this->assertStringContainsString('<div class="res-row">', $html);
        $this->assertDoesNotMatchRegularExpression('/\.result-box \{[^}]*display:flex/', $html);
    }
}

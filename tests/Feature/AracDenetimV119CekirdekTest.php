<?php

namespace Tests\Feature;

use App\Estimators\KitRegistry;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\MessageBag;
use Tests\TestCase;

/** v119 araç denetimi — çekirdek: DB bağlantı hatası 503, hesap motoru girdi/yuvarlama/uyarılar, araç formülleri */
class AracDenetimV119CekirdekTest extends TestCase
{
    use RefreshDatabase;

    private function pdo(string $msg, string $state, ?int $code = null): \PDOException
    {
        $e = new \PDOException($msg, 0);
        $e->errorInfo = [$state, $code, $msg];

        return $e;
    }

    private function qex(\PDOException $prev): QueryException
    {
        return new QueryException('mysql', 'select 1', [], $prev);
    }

    // ── 1. Veritabanı bağlantı/limit hatası → 503 + Retry-After ──

    public static function connectionErrors(): array
    {
        return [
            '1040 too many' => ['SQLSTATE[08004] [1040] Too many connections', '08004', 1040],
            '1203 max_user_connections' => ["SQLSTATE[42000] [1203] User 'u' already has more than 'max_user_connections' active connections", '42000', 1203],
            '2002 refused' => ['SQLSTATE[HY000] [2002] Connection refused', 'HY000', 2002],
            '2006 gone away' => ['SQLSTATE[HY000]: General error: 2006 MySQL server has gone away', 'HY000', 2006],
            'HY000 mesaj' => ['SQLSTATE[HY000]: General error: Too many connections', 'HY000', null],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('connectionErrors')]
    public function test_baglanti_hatasi_html_ve_json_503_retry_after(string $msg, string $state, ?int $code): void
    {
        $e = $this->qex($this->pdo($msg, $state, $code));
        Route::get('/_t/db-baglanti', fn () => throw $e);
        Route::get('/_t/pdo-ham', fn () => throw $this->pdo($msg, $state, $code));

        $this->get('/_t/db-baglanti')->assertStatus(503)->assertHeader('Retry-After', '30')->assertSee('Şu an çok yoğunuz');
        $this->getJson('/_t/db-baglanti')->assertStatus(503)->assertHeader('Retry-After', '30')
            ->assertJsonPath('message', 'Sunucu şu an yoğun. Lütfen birkaç saniye sonra tekrar deneyin.');
        $this->getJson('/_t/pdo-ham')->assertStatus(503)->assertHeader('Retry-After', '30');
    }

    public function test_baglanti_hatasi_yine_raporlanir(): void
    {
        Log::spy();
        $e = $this->qex($this->pdo('SQLSTATE[08004] [1040] Too many connections', '08004', 1040));
        Route::get('/_t/db-log', fn () => throw $e);

        $this->get('/_t/db-log')->assertStatus(503);
        Log::shouldHaveReceived('error')->withArgs(fn ($m) => str_contains((string) $m, 'Too many connections'))->once();
    }

    public function test_girdi_kaynakli_query_exception_json_422_kalir(): void
    {
        $e = $this->qex($this->pdo('SQLSTATE[22003]: Numeric value out of range: 1264 Out of range value', '22003', 1264));
        Route::get('/_t/db-girdi', fn () => throw $e);

        $this->getJson('/_t/db-girdi')->assertStatus(422)->assertJsonPath('message', 'Girilen değerler işlenemedi. Lütfen kontrol edin.');
        $this->get('/_t/db-girdi')->assertStatus(500);
    }

    // ── 2. Hesap motoru ──

    public static function arrayParams(): array
    {
        return [
            ['/araclar/boya-hesaplayici/hesapla?target[]=x'],
            ['/araclar/parke-maliyet-hesaplayici/hesapla?area[]=5'],
            ['/araclar/zemin-kaplama/hesapla?extras[][]=x'],
            ['/araclar/seramik-maliyet-hesaplayici/hesapla?surface=zemin&floor[]=5'],
            ['/araclar/seramik-maliyet-hesaplayici?en[]=1'],
            ['/araclar/boya-hesaplayici?target=daire&floor[]=4'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('arrayParams')]
    public function test_dizi_parametre_500_vermez(string $url): void
    {
        $this->get($url)->assertOk();
    }

    public function test_dizi_parametre_pro_rapor_500_vermez(): void
    {
        $this->get('/araclar/boya-hesaplayici/pro-rapor?target[]=x')->assertRedirect();
    }

    public function test_kucuk_is_sifir_tl_gostermez(): void
    {
        $r = $this->getJson('/araclar/kara-siva-hesaplama/hesapla?method=geleneksel&side=ic&area=1')->assertOk()->json();
        $this->assertTrue($r['ready']);
        $this->assertGreaterThan(0, $r['low']);
        $this->assertLessThanOrEqual($r['mid'], $r['low']);
        $this->assertLessThan(1000, $r['mid']);   // gerçek tutar (≈₺290), ₺0 ya da ₺1.000 değil
    }

    public function test_aralik_disi_deger_notu(): void
    {
        $r = $this->getJson('/araclar/duvar-maliyet-hesaplayici/hesapla?material=tugla135&length=10&height=28&plaster=yok')->json();
        $this->assertContains('Yükseklik en fazla 10 m olabilir; 10 m ile sınırlandı.', $r['notices']);

        $this->get('/araclar/duvar-maliyet-hesaplayici?material=tugla135&length=10&height=28&plaster=yok')
            ->assertSee('10 m ile sınırlandı', false);

        // Negatif değer hesaplanmaz, uyarı döner
        $r = $this->getJson('/araclar/duvar-maliyet-hesaplayici/hesapla?material=tugla135&length=-5&height=2.8&plaster=yok')->json();
        $this->assertFalse($r['ready']);
        $this->assertContains('Toplam uzunluk: pozitif bir değer girin.', $r['notices']);

        // Normal girdi: not yok
        $r = $this->getJson('/araclar/duvar-maliyet-hesaplayici/hesapla?material=tugla135&length=10&height=2.8&plaster=yok')->json();
        $this->assertSame([], $r['notices']);
    }

    // ── 3. Araç formülleri ──

    private function est(string $slug, array $q): array
    {
        $e = KitRegistry::get($slug);

        return $e->estimate($e->normalize($q));
    }

    public function test_kalip_radye_cevre_kalibi_ve_kdv(): void
    {
        // 20×20 m, 60 cm radye = 240 m³ → kalıp 80 m × 0,6 m = 48 m² (eskiden 720 m²)
        $r = $this->est('kalip-plywood-maliyeti', ['element' => 'radye', 'volume' => '240', 'material' => 'plywood', 'thickness' => '60']);
        $this->assertContains(['Kalıp alanı', '48 m²'], $r['meta']);
        $r = $this->est('kalip-plywood-maliyeti', ['element' => 'radye', 'perimeter' => '80', 'thickness' => '60', 'material' => 'plywood']);
        $this->assertTrue($r['ready']);
        $this->assertSame(round(48 * 1175.39 * 1.2 / 100) * 100, (float) $r['items']['kalip']['mid']);   // ÇŞB fiyatı + %20 KDV
    }

    public function test_beton_kiris_adet_carpani(): void
    {
        $r = $this->est('beton-hesaplayici', ['element' => 'kiris', 'length' => '5', 'width' => '0.3', 'thickness' => '50', 'count' => '4', 'class' => 'c25', 'pump' => 'hayir']);
        $this->assertSame('3,0 / 3,2 m³', $r['meta'][0][1]);
        $r = $this->est('beton-hesaplayici', ['element' => 'doseme', 'length' => '10', 'width' => '10', 'thickness' => '20', 'count' => '4', 'class' => 'c25', 'pump' => 'hayir']);
        $this->assertStringStartsWith('20,0 /', $r['meta'][0][1]);   // döşemede adet çarpılmaz
    }

    public function test_boya_iscilik_esikte_dusmez(): void
    {
        $q = ['target' => 'daire', 'surfaces' => 'duvar_tavan', 'color' => 'ayni', 'state' => 'iyi', 'quality' => 'orta'];
        $prev = 0;
        foreach ([400, 499, 500, 501, 520, 800] as $f) {
            $mid = $this->est('boya-hesaplayici', $q + ['floor' => (string) $f])['items']['iscilik']['mid'];
            $this->assertGreaterThanOrEqual($prev, $mid, "$f m² işçilik düşmemeli");
            $prev = $mid;
        }
    }

    public function test_zemin_hazir_zeminde_ek_sap_tutara_girer(): void
    {
        $base = ['type' => 'laminat', 'area' => '30', 'quality' => 'orta', 'scope' => 'hazir'];
        $a = $this->est('zemin-kaplama', $base);
        $b = $this->est('zemin-kaplama', $base + ['extras' => ['sap']]);
        $this->assertArrayNotHasKey('sap', $a['items']);
        $this->assertArrayHasKey('sap', $b['items']);
        $this->assertGreaterThan($a['mid'], $b['mid']);
    }

    public function test_seramik_eski_floor_baglantisi_100_m2de_kesilmez(): void
    {
        $r = $this->est('seramik-maliyet-hesaplayici', ['surface' => 'zemin', 'floor' => '500', 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir']);
        $this->assertContains(['Kaplanacak alan', '500,0 m²'], $r['meta']);
    }

    public function test_bims_12_5_adet_ve_harc_dozlari(): void
    {
        $this->assertSame(12.5, config('worksitem_estimators.duvar.materials.bims19')[1]);
        $html = $this->get('/araclar/cimento-kum-hesaplama')->assertOk()->getContent();
        $this->assertStringContainsString('250 doz: kara sıva', $html);
        $this->assertStringContainsString('350 doz: şap', $html);
        $this->assertStringNotContainsString('Normal Şap İçin İdeal', $html);
        $this->get('/araclar/kara-siva-hesaplama')->assertSee('250 dozlu', false)->assertDontSee('6–7 kg çimento', false);
    }

    public function test_bahce_duvari_temel_kazisi(): void
    {
        $r = $this->est('bahce-duvari-maliyeti', ['type' => 'blok', 'length' => '40', 'height' => '1.5']);
        $this->assertArrayHasKey('kazi', $r['items']);
        $this->assertGreaterThan(0, $r['items']['kazi']['mid']);
    }

    public function test_bina_sinif_kodlari_ve_statik_proje(): void
    {
        $r = $this->est('tahmini-bina-maliyeti', ['type' => 'mustakil_basit', 'area' => '100', 'quality' => 'orta']);
        $this->assertStringContainsString('(2C)', collect($r['meta'])->firstWhere(0, 'Resmî sınıf maliyeti')[1] ?? '');
        $html = $this->get('/araclar/tahmini-bina-maliyeti')->getContent();
        $this->assertStringContainsString('5D', $html);
        $this->assertStringNotContainsString('· 5C', $html);
        // Statik proje bedeli aynı sınıfı kullanır: II-C 15.100 TL/m² × 100 m²
        $s = $this->est('statik-proje-bedeli', ['type' => 'mustakil_basit', 'area' => '100', 'kind' => 'yeni']);
        $this->assertSame('1.510.000 TL', collect($s['meta'])->firstWhere(0, 'Yapı yaklaşık maliyeti')[1]);
    }

    public function test_unit_price_seeder_3b_4a(): void
    {
        $this->seed(\Database\Seeders\UnitPriceSeeder::class);
        $this->assertEquals(21050, \App\Models\UnitPrice::where('key', 'bina_sinif_3b_m2')->value('price'));
        $this->assertEquals(26450, \App\Models\UnitPrice::where('key', 'bina_sinif_4a_m2')->value('price'));
    }

    public function test_mantolama_ts825_alti_bolge(): void
    {
        $this->get('/araclar/mantolama-maliyeti')->assertSee('6 iklim bölgesine', false)->assertDontSee('İstanbul (2. bölge)', false);
    }

    // ── Banyo ──

    public function test_banyo_rapor_alanlari_tum_ekstralari_tasir(): void
    {
        $fields = config('worksitem_tool_guides.tools.banyo-maliyet-hesaplayici.fields');
        foreach (array_keys(config('worksitem_bathroom.extras')) as $k) {
            $this->assertContains($k, $fields, "$k PDF/Pro rapor/paylaş bağlantısına taşınmalı");
        }
    }

    public function test_banyo_pdf_olcu_hatasi_teklif_basligi_altinda_degil(): void
    {
        $this->get('/araclar/banyo-maliyet-hesaplayici/rapor.pdf')->assertRedirect('/araclar/banyo-maliyet-hesaplayici');
        $errors = (new ViewErrorBag)->put('default', new MessageBag(['width' => 'PDF rapor için önce banyonun en ve boy ölçülerini girin.']));
        $html = $this->withSession(['errors' => $errors])->get('/araclar/banyo-maliyet-hesaplayici')->getContent();
        $this->assertStringContainsString('id="bPdfErr"', $html);
        $this->assertStringNotContainsString('Teklif talebiniz gönderilemedi', $html);
    }

    public function test_banyo_pdf_saati_istanbul(): void
    {
        foreach (['reports/banyo-pdf.blade.php', 'reports/banyo-pro-pdf.blade.php'] as $v) {
            $src = file_get_contents(resource_path('views/'.$v));
            $this->assertStringNotContainsString("{{ now()->format('d.m.Y H:i') }}", $src, $v);
        }
    }

    // ── Statik araçlar ──

    public function test_metrekup_ve_malzeme_agirligi_turkce_bicim_ve_uyari(): void
    {
        $mw = $this->get('/araclar/malzeme-agirligi')->assertOk()->getContent();
        $this->assertStringContainsString('name="viewport"', $mw);
        $this->assertStringContainsString('Hacim için pozitif bir değer girin.', $mw);
        $this->assertStringNotContainsString("toFixed(2) + ' Ton (", $mw);

        $vol = $this->get('/araclar/metrekup-hacim-hesaplama')->assertOk()->getContent();
        $this->assertStringContainsString("classList.remove('active')", $vol);
        $this->assertStringNotContainsString("volume.toFixed(2) + ' m³'", $vol);
    }
}

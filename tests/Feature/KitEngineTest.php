<?php

namespace Tests\Feature;

use App\Estimators\KitRegistry;
use App\Models\Lead;
use App\Models\Sponsor;
use App\Services\FxRates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KitEngineTest extends TestCase
{
    use RefreshDatabase;

    public static function tools(): array
    {
        return [
            'mutfak' => ['mutfak-maliyet-hesaplayici', ['scope' => 'dolap', 'length' => '3.6', 'quality' => 'orta', 'counter' => 'granit', 'extras' => ['ankastre']]],
            'seramik' => ['seramik-maliyet-hesaplayici', ['surface' => 'zemin', 'floor' => '20', 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir']],
            'boya' => ['boya-hesaplayici', ['target' => 'daire', 'floor' => '100', 'surfaces' => 'duvar_tavan', 'color' => 'ayni', 'state' => 'iyi', 'quality' => 'orta']],
            'wc' => ['wc-maliyet-hesabi', ['w' => '1.2', 'l' => '1.5', 'scope' => 'komple', 'quality' => 'orta']],
            'parke' => ['parke-maliyet-hesaplayici', ['type' => 'laminat', 'area' => '40', 'rooms' => 'tek', 'quality' => 'orta', 'scope' => 'hazir', 'extras' => ['supurgelik']]],
            'zemin' => ['zemin-kaplama', ['type' => 'mermer', 'area' => '30', 'quality' => 'orta', 'scope' => 'sert']],
            'mantolama' => ['mantolama-maliyeti', ['perimeter' => '60', 'floors' => '5', 'board' => 'grafit', 'thickness' => '5', 'finish' => 'boya', 'quality' => 'orta', 'scaffold' => 'var']],
            'pvc' => ['pvc-dograma-hesaplayici', ['count' => '6', 'width' => '150', 'height' => '150', 'quality' => 'orta', 'color' => 'beyaz', 'glass' => 'isi', 'opening' => 'tek', 'scope' => 'degisim']],
            'sap' => ['sap-hesaplayici', ['type' => 'klasik', 'area' => '100', 'thickness' => '5', 'extras' => ['fiber']]],
            'kara_siva' => ['kara-siva-hesaplama', ['method' => 'geleneksel', 'side' => 'ic', 'area' => '120']],
            'alci_siva' => ['alci-siva-hesaplama', ['type' => 'perlit_saten', 'wall' => '120', 'ceiling' => '40']],
            'duvar' => ['duvar-maliyet-hesaplayici', ['material' => 'gazbeton20', 'length' => '12', 'height' => '2.8', 'openings' => '2', 'plaster' => 'tek']],
            'duvar_orgu' => ['duvar-orgu', ['material' => 'tugla135', 'length' => '10', 'height' => '2.8', 'plaster' => 'yok']],
            'bahce' => ['bahce-duvari-maliyeti', ['type' => 'betonarme', 'length' => '40', 'height' => '1.5', 'extras' => ['coping']]],
            'iskele' => ['iskele-metraji-maliyeti', ['type' => 'cephe', 'length' => '60', 'height' => '15', 'months' => '2', 'net' => 'evet']],
            'kalip' => ['kalip-plywood-maliyeti', ['element' => 'doseme', 'volume' => '100', 'material' => 'plywood']],
            'hafriyat' => ['hafriyat-kazi-hesabi', ['width' => '15', 'length' => '20', 'depth' => '3', 'soil' => 'normal', 'truck' => '16']],
            'saha' => ['saha-betonu-hesaplayici', ['area' => '200', 'thickness' => '15', 'class' => 'c25', 'mesh' => 'tek', 'finish' => 'helikopter', 'pump' => 'evet']],
            'alcipan' => ['alcipan-hesaplayici', ['system' => 'c75', 'board' => 'water', 'area' => '30']],
            'asma_tavan' => ['asma-tavan-hesaplayici', ['system' => 'alu60_sarkmali', 'area' => '50']],
            'su_yalitimi' => ['su-yalitimi-hesaplayici', ['system' => 'cement_2k_mesh', 'area' => '60']],
            'epoksi' => ['epoksi-zemin-hesaplayici', ['area' => '300', 'layers' => ['uv_topcoat']]],
            'sandvic' => ['sandvic-panel-hesaplayici', ['system' => 'roof_pu50', 'area' => '500']],
            'radyator' => ['radyator-petek-hesaplayici', ['insulation' => 'standart', 'area' => '100', 'type' => 'h60', 'scope' => 'petek', 'extras' => ['thermo']]],
            'ges' => ['gunes-paneli-hesaplama', ['bill' => '2000', 'sun' => '4.5', 'mount' => 'cati', 'quality' => 'orta']],
            'beton_onarim' => ['beton-onarim-maliyeti', ['mode' => 'onarim', 'area' => '12', 'depth' => '3', 'element' => 'kolon', 'rebar' => 'hafif', 'material' => 'cimento']],
            'izolator' => ['deprem-izolator-maliyeti', ['columns' => '40', 'floors' => '6', 'class' => 'konut', 'scope' => 'yeni']],
            'bina' => ['tahmini-bina-maliyeti', ['type' => 'apartman', 'area' => '1000', 'quality' => 'orta', 'extras' => ['projects']]],
            'statik' => ['statik-proje-bedeli', ['type' => 'apartman', 'area' => '1000', 'kind' => 'yeni', 'extras' => ['soil']]],
            'beton' => ['beton-hesaplayici', ['element' => 'doseme', 'length' => '10', 'width' => '10', 'thickness' => '20', 'class' => 'c25', 'pump' => 'evet']],
            'su_tesisati' => ['su-tesisat-hesaplama', ['area' => '100', 'baths' => '2', 'kitchens' => '1', 'scope' => 'komple', 'quality' => 'orta']],
            'cati' => ['cati-maliyet-hesaplayici', ['covering' => 'clay_alaturka', 'length' => '20', 'width' => '12', 'form' => 'gable', 'underlay' => 'osb', 'membrane' => 'breathable']],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_tools.tool_status.overrides' => array_fill_keys(KitRegistry::slugs(), 'active')]);
        // Resmî YFK birim fiyatları (staging'de rayiç tablosu dolu; testte örnek kayıtlar)
        \App\Estimators\PozEstimator::flushCache();
        foreach (['15.530.1260' => 1180.50, '15.535.1013' => 1450.00, '15.270.1006' => 690.00, '15.190.1014' => 1650.00, '15.190.1017' => 520.00, '15.320.1001' => 2150.00, '15.305.1001' => 980.00, '15.305.1005' => 310.00, '15.300.1006' => 640.00, '15.330.1003' => 210.00] as $poz => $price) {
            \Illuminate\Support\Facades\DB::table('rayic_prices')->insert(['source' => 'YFK', 'period' => '2026-08', 'poz_no' => $poz, 'description' => 'Test '.$poz, 'unit' => 'm²', 'price' => $price, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
        Cache::put(FxRates::CACHE_KEY, ['date' => '09.10.2026', 'rates' => ['USD' => 41.2, 'EUR' => 48.1, 'GBP' => 55.3]]);
    }

    #[DataProvider('tools')]
    public function test_sayfa_secimler_bos_gelir_seo_ve_sponsor_alani_var(string $slug, array $q): void
    {
        $html = $this->get('/araclar/'.$slug)->assertOk()
            ->assertSee('Seçim bekleniyor')
            ->assertSee('Nasıl hesaplıyoruz?')
            ->assertSee('"FAQPage"', false)
            ->assertSee('/markalar-icin', false)
            ->assertSee('>İlerle</button>', false)
            ->getContent();
        $this->assertDoesNotMatchRegularExpression('/type="radio"[^>]*checked/', $html, 'hiçbir seçim varsayılan işaretli gelmemeli');
        $this->assertDoesNotMatchRegularExpression('/type="checkbox"[^>]*checked/', $html);
    }

    #[DataProvider('tools')]
    public function test_canli_hesap_eksikleri_soyler_sonra_kalem_dokumu_ve_doviz_verir(string $slug, array $q): void
    {
        $this->getJson('/araclar/'.$slug.'/hesapla')->assertOk()->assertJson(['ready' => false])->assertJsonStructure(['missing']);

        $r = $this->getJson('/araclar/'.$slug.'/hesapla?'.http_build_query($q))->assertOk()->assertJson(['ready' => true])->json();
        $this->assertGreaterThan(0, $r['low']);
        $this->assertLessThan($r['high'], $r['low']);
        $this->assertSame($r['mid'], (int) round(array_sum(array_column($r['items'], 'mid')) / 1000) * 1000);
        if (KitRegistry::get($slug)->contingencyRate() > 0) {
            $this->assertArrayHasKey('beklenmedik', $r['items']);
        }
        $this->assertCount(3, $r['fx']);
        $this->assertSame('$', $r['fx'][0][0]);

        // Paylaşılan bağlantı: aynı seçimlerle sayfa dolu açılır
        $this->get('/araclar/'.$slug.'?'.http_build_query($q))->assertOk()->assertSee('En olası tutar')->assertSee('checked', false);
    }

    #[DataProvider('tools')]
    public function test_pro_rapor_sms_ile_iner_ve_lead_olur(string $slug, array $q): void
    {
        Mail::fake();
        config(['services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem']);
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
        Sponsor::query()->create(['brand' => 'Örnek Marka', 'headline' => 'Başlık', 'target_url' => 'https://example.com', 'tool_slug' => $slug]);

        $this->get('/araclar/'.$slug.'/pro-rapor')->assertRedirect('/araclar/'.$slug);
        $this->get('/araclar/'.$slug.'/pro-rapor?'.http_build_query($q))->assertOk()->assertSee('detaylı Pro rapor')->assertSee('Kod gönder');

        $this->postJson('/araclar/'.$slug.'/pro-rapor/kod', ['phone' => '05327654321'])->assertOk();
        preg_match('/(\d{6})/', Http::recorded()->last()[0]['messages'][0]['msg'], $m);
        $res = $this->post('/araclar/'.$slug.'/pro-rapor', $q + ['name' => 'Ayşe Demir', 'phone' => '05327654321', 'code' => $m[1], 'kvkk' => '1']);
        $res->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $res->headers->get('Content-Type'));

        $key = KitRegistry::get($slug)->key();
        $lead = Lead::query()->where('source', $key.'_pro_rapor')->firstOrFail();
        $this->assertFalse((bool) $lead->contact_consent);
        $this->assertGreaterThan(0, (int) $lead->estimated_cost);
    }

    public function test_pro_rapor_pdf_icerigi(): void
    {
        $e = KitRegistry::get('seramik-maliyet-hesaplayici');
        $in = $e->normalize(['surface' => 'zemin_duvar', 'floor' => '6', 'wall' => '22', 'size' => '30x60', 'quality' => 'premium', 'scope' => 'sokum', 'extras' => ['yalitim', 'profil']]);
        $r = $e->proReport($in);
        $this->assertCount(3, $r['compare']);
        $this->assertTrue($r['compare']['premium']['selected']);
        $this->assertNotEmpty($r['materials']);
        $this->assertStringContainsString('kutu', collect($r['materials'])->pluck(2)->implode(' '));
        $this->assertNotEmpty($r['program']);
        $this->assertArrayHasKey('sokum', $r['est']['items']);
        $this->assertArrayHasKey('yalitim', $r['est']['items']);

        $html = view('reports.kit-pro-pdf', $r + ['e' => $e, 'page' => $e->page(), 'fx' => null, 'sponsors' => collect(), 'logo' => null, 'pricesLabel' => 'x', 'reportNo' => 'WS-PRO-TEST'])->render();
        $this->assertStringContainsString('Kalem kalem maliyet', $html);
        $this->assertStringContainsString('Üç kalite seviyesinde karşılaştırma', $html);
        $this->assertStringContainsString('Yaklaşık malzeme listesi', $html);
    }

    public function test_her_kayitli_arac_test_listesinde(): void
    {
        $this->assertEqualsCanonicalizing(KitRegistry::slugs(), array_column(self::tools(), 0), 'Yeni araç eklendiyse KitEngineTest::tools() listesine örnek girdisiyle ekleyin');
    }

    public function test_resmi_fiyat_yoksa_fiyat_uydurulmaz(): void
    {
        $e = KitRegistry::get('asma-tavan-hesaplayici');
        $r = $e->estimate($e->normalize(['system' => 'alcipan', 'area' => '50']));
        $this->assertFalse($r['ready']);
        $this->assertStringContainsString('fiyat uydurmuyoruz', mb_strtolower($r['blocked']));
        $this->getJson('/araclar/asma-tavan-hesaplayici/hesapla?system=alcipan&area=50')->assertJson(['ready' => false])->assertJsonStructure(['blocked']);
        $this->get('/araclar/asma-tavan-hesaplayici/pro-rapor?system=alcipan&area=50')->assertRedirect();
    }

    public function test_beton_tedarikci_basvurusu_ve_hacim(): void
    {
        $this->get('/araclar/beton-hesaplayici')->assertOk()->assertSee('worksitem_beton_tedarikci_basvuru', false)->assertSee('worksitem_beton_tedarikci_teklif', false);
        $e = KitRegistry::get('beton-hesaplayici');
        $r = $e->estimate($e->normalize(['element' => 'kolon', 'length' => '0.4', 'width' => '0.4', 'thickness' => '300', 'count' => '10', 'class' => 'c30', 'pump' => 'hayir', 'waste' => '0']));
        $this->assertTrue($r['ready']);
        $this->assertSame('4,8 / 4,8 m³', $r['meta'][0][1]);   // 10 × 0,4 × 0,4 × 3 m
    }

    public function test_seramik_alan_secilen_yuzeye_gore(): void
    {
        $e = KitRegistry::get('seramik-maliyet-hesaplayici');
        $miss = $e->estimate($e->normalize(['surface' => 'zemin', 'wall' => '20', 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir']));
        $this->assertFalse($miss['ready']);
        $this->assertContains('zemin eni ve boyu', $miss['missing']);
    }

    public function test_seramik_olculerden_alan(): void
    {
        $e = KitRegistry::get('seramik-maliyet-hesaplayici');
        $base = ['size' => '30x60', 'quality' => 'orta', 'scope' => 'hazir'];

        // Zemin: en × boy (4 × 5 = 20 m²); duvar alanı sorulmaz
        $z = $e->estimate($e->normalize(['surface' => 'zemin', 'en' => '4', 'boy' => '5'] + $base));
        $this->assertTrue($z['ready']);
        $area = fn ($r) => collect($r['meta'])->firstWhere(0, 'Kaplanacak alan')[1];
        $this->assertSame('20,0 m²', $area($z));
        $this->assertStringContainsString('4,00 × 5,00 m = 20', collect($z['meta'])->pluck(1)->implode(' '));
        $steps = collect($e->steps())->filter(fn ($s) => $s['type'] === 'numbers' && $e->visible($s, ['surface' => 'zemin']))->pluck('title')->all();
        $this->assertSame(['Zemin ölçüleri'], array_values($steps));

        // Duvar: genişlik × yükseklik (yükseklik boşsa 2,6 m)
        $d = $e->estimate($e->normalize(['surface' => 'duvar', 'dw' => '3'] + $base));
        $this->assertSame('7,8 m²', $area($d));

        // Zemin + duvar: zemin en × boy, duvar 2 × (en + boy) × yükseklik − kapı
        $zd = $e->estimate($e->normalize(['surface' => 'zemin_duvar', 'en' => '1.8', 'boy' => '2.4', 'kapi' => '1.9'] + $base));
        $this->assertSame('24,3 m²', $area($zd));   // 4,32 + (21,84 − 1,9 = 19,94)
        $this->assertStringContainsString('2 × (1,80 + 2,40) × 2,60 m − 1,90 m² kapı/pencere', collect($zd['meta'])->pluck(1)->implode(' '));

        // Malzeme listesinde karo adedi: 30×60 = 0,18 m², 1 m² ≈ 5,56 karo
        $mat = collect($e->materials($e->normalize(['surface' => 'zemin', 'en' => '4', 'boy' => '5'] + $base)));
        $row = $mat->first(fn ($r) => str_contains($r[0], 'adedi'));
        $this->assertSame((int) ceil(22 / 0.18), $row[1]);   // 20 m² + %10 fire = 22 m²
        $this->assertStringContainsString('5,56', $row[3]);

        // Eski bağlantı (?floor=20) aynı sonucu verir
        $old = $e->estimate($e->normalize(['surface' => 'zemin', 'floor' => '20'] + $base));
        $this->assertSame($z['mid'], $old['mid']);
    }

    public function test_boya_dis_cephe_modu_iskele_ve_kosullu_adimlar(): void
    {
        $e = KitRegistry::get('boya-hesaplayici');
        $q = ['target' => 'dis_cephe', 'perimeter' => '60', 'floors' => '5', 'color' => 'ayni', 'state' => 'orta', 'quality' => 'orta', 'access' => 'iskele',
            'floor' => '100', 'surfaces' => 'duvar_tavan', 'extras' => ['furniture'], 'xextras' => ['wash']];
        $in = $e->normalize($q);
        $this->assertNull($in['floor']);                       // gizli iç cephe alanı hesaba girmez
        $this->assertSame([], $in['extras']);
        $r = $e->estimate($in);
        $this->assertTrue($r['ready']);
        $this->assertSame('cephe m² başına', $r['per_unit_label']);
        $this->assertArrayHasKey('iskele', $r['items']);
        $this->assertArrayNotHasKey('tavan', $r['items']);
        $this->assertGreaterThan(0, $r['items']['iskele']['mid']);
        $this->assertStringContainsString('765', collect($r['meta'])->pluck(1)->implode(' '));   // 60 × 15 m × 0,85

        $noScaffold = $e->estimate($e->normalize(['access' => 'yok'] + $q));
        $this->assertArrayNotHasKey('iskele', $noScaffold['items']);
        $this->assertLessThan($r['mid'], $noScaffold['mid']);
        $this->assertContains('kat sayısı', $e->estimate($e->normalize(['target' => 'dis_cephe', 'perimeter' => '60'] + array_diff_key($q, ['floors' => 1])))['missing']);

        // Rapor: dış cephe kalite seçenekleri, malzeme listesi
        $pro = $e->proReport($in);
        $this->assertSame('Silikonlu', $pro['compare']['orta']['label']);
        $this->assertStringContainsString('Dış cephe boyası', collect($pro['materials'])->pluck(0)->implode(' '));
        $this->assertSame(['Neresi boyanacak?', 'Boyanacak cephe alanı (biliyorsanız)'], array_slice(array_column($e->inputRows($in), 0), 0, 2));

        // Sayfa: dış cephe seçiliyken iç cephe adımları gizli ve alanları gönderilmez
        $html = $this->get('/araclar/boya-hesaplayici?'.http_build_query($q))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/data-step="surfaces"[^>]*hidden/', $html);
        $this->assertMatchesRegularExpression('/data-step="access"[^>]*data-show/', $html);

        // İç cephe sonucu değişmedi
        $this->assertTrue($e->estimate($e->normalize(self::tools()['boya'][1]))['ready']);
    }

    public function test_seramik_cok_yuzey_zemin_ve_duvar_ayri_urun(): void
    {
        $e = KitRegistry::get('seramik-maliyet-hesaplayici');
        $base = ['surface' => 'zemin_duvar', 'floor' => '5', 'wall' => '22', 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir'];
        $same = $e->estimate($e->normalize($base));
        $this->assertSame(0.0, (float) ($same['items']['seramik_duvar']['mid'] ?? 0));

        $split = $e->estimate($e->normalize($base + ['wsize' => '30x60', 'wquality' => 'ekonomik']));
        $this->assertStringContainsString('Zemin karosu 60×60', $split['items']['seramik']['label']);
        $this->assertStringContainsString('Duvar karosu 30×60', $split['items']['seramik_duvar']['label']);
        $this->assertLessThan($same['mid'], $split['mid']);        // duvar ekonomik seramik → daha ucuz
        $mats = collect($e->materials($e->normalize($base + ['wsize' => '30x60'])))->pluck(0)->all();
        $this->assertContains('Zemin karosu kutusu', $mats);
        $this->assertContains('Duvar karosu kutusu', $mats);

        // Yalnız zeminde duvar ürünü yok sayılır
        $zemin = $e->normalize(['surface' => 'zemin', 'floor' => '5', 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir', 'wsize' => '120x120']);
        $this->assertSame('', $zemin['wsize']);
    }
}

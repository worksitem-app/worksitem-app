<?php

namespace Tests\Feature;

use App\Models\Sponsor;
use App\Services\Sponsors\SponsorPackages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v76: yapı kimyasalları boşluk kapatma, beton onarım aracı, kalem sponsorluğu ve paketler */
class YapiKimyasallariTest extends TestCase
{
    use RefreshDatabase;

    private function calc(string $slug, array $q): array
    {
        return $this->getJson('/araclar/'.$slug.'/hesapla?'.http_build_query($q))->assertOk()->json();
    }

    public function test_seramik_epoksi_derz_seviyeleme_ve_temizlik(): void
    {
        $base = ['surface' => 'zemin', 'floor' => 40, 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir'];
        $plain = $this->calc('seramik-maliyet-hesaplayici', $base);
        $full = $this->calc('seramik-maliyet-hesaplayici', $base + ['grout' => 'epoksi', 'extras' => ['seviye', 'temizlik']]);

        $this->assertTrue($full['ready']);
        $this->assertArrayHasKey('derz_epoksi', $full['items']);
        $this->assertArrayHasKey('seviye', $full['items']);
        $this->assertArrayHasKey('temizlik', $full['items']);
        $this->assertStringContainsString('320 klips', $full['items']['seviye']['label']);
        $this->assertSame('Yapıştırıcı', $full['items']['yapistirici']['label']);
        $this->assertArrayNotHasKey('derz_epoksi', $plain['items']);
        $this->assertGreaterThan($plain['mid'], $full['mid']);
    }

    public function test_saha_betonu_korund_kur_ve_sivi_sertlestirici(): void
    {
        $r = $this->calc('saha-betonu-hesaplayici', ['area' => 500, 'thickness' => 15, 'class' => 'c30', 'mesh' => 'tek', 'finish' => 'korund', 'pump' => 'hayir', 'extras' => ['curing', 'densifier']]);
        $this->assertTrue($r['ready']);
        $this->assertStringContainsString('korund', $r['items']['perdah']['label']);
        $this->assertArrayHasKey('kur', $r['items']);
        $this->assertArrayHasKey('sertlestirici', $r['items']);
    }

    public function test_akrilik_ve_pu_su_yalitimi_fiyatsizken_uydurmaz(): void
    {
        $html = $this->get('/araclar/su-yalitimi-hesaplayici')->assertOk()->getContent();
        $this->assertStringContainsString('Akrilik Esaslı Likit Yalıtım', $html);
        $this->assertStringContainsString('Poliüretan Esaslı Likit Membran', $html);

        $r = $this->calc('su-yalitimi-hesaplayici', ['system' => 'pu_liquid', 'area' => 100]);
        $this->assertFalse($r['ready']);
        $this->assertStringContainsString('piyasa fiyatı henüz doğrulanmadı', $r['blocked']);
    }

    public function test_beton_onarim_araci_onarim_ve_grout(): void
    {
        $this->get('/araclar/beton-onarim-maliyeti')->assertOk()->assertSee('Beton onarım ve grout maliyeti', false);

        $r = $this->calc('beton-onarim-maliyeti', ['mode' => 'onarim', 'area' => 10, 'depth' => 3, 'element' => 'kolon', 'rebar' => 'agir', 'material' => 'cimento', 'crack' => 12, 'extras' => ['kaplama', 'iskele']]);
        $this->assertTrue($r['ready']);
        foreach (['hazirlik', 'pas', 'astar', 'harc', 'iscilik', 'donati', 'enjeksiyon', 'kaplama', 'iskele'] as $k) {
            $this->assertArrayHasKey($k, $r['items'], $k);
        }
        $this->assertStringContainsString('660 kg', $r['items']['harc']['label']);   // 10 m² × 3 cm × 20 kg × 1,1
        $this->assertNotEmpty($r['notes']);                                          // kolon / kesit kaybı uyarısı

        $epoxy = $this->calc('beton-onarim-maliyeti', ['mode' => 'onarim', 'area' => 10, 'depth' => 3, 'element' => 'kolon', 'rebar' => 'agir', 'material' => 'epoksi', 'crack' => 12, 'extras' => ['kaplama', 'iskele']]);
        $this->assertGreaterThan($r['mid'], $epoxy['mid']);

        $g = $this->calc('beton-onarim-maliyeti', ['mode' => 'grout', 'pieces' => 8, 'plate_w' => 50, 'plate_l' => 50, 'gap' => 4, 'anchors' => 32, 'gmaterial' => 'cimento']);
        $this->assertTrue($g['ready']);
        $this->assertSame(['hazirlik', 'grout', 'iscilik', 'beklenmedik'], array_keys($g['items']));
        $this->assertArrayNotHasKey('harc', $g['items']);

        $missing = $this->calc('beton-onarim-maliyeti', ['mode' => 'grout']);
        $this->assertFalse($missing['ready']);
    }

    public function test_kalem_sponsoru_sonuc_satirinda_gorunur_ve_fiyati_degistirmez(): void
    {
        $q = ['surface' => 'zemin', 'floor' => 40, 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir', 'grout' => 'epoksi'];
        $before = $this->calc('seramik-maliyet-hesaplayici', $q);
        $s = Sponsor::create(['brand' => 'Deneme Kimya', 'headline' => 'Epoksi derz', 'target_url' => 'https://example.com', 'tool_slug' => 'seramik-maliyet-hesaplayici', 'category' => 'derz', 'active' => true]);
        $after = $this->calc('seramik-maliyet-hesaplayici', $q);

        $this->assertSame($before['mid'], $after['mid']);
        $this->assertSame('Deneme Kimya', $after['sponsor_lines']['derz_epoksi']['brand']);
        $this->assertSame(route('sponsor.go', $s), $after['sponsor_lines']['derz_epoksi']['url']);
        $this->assertArrayNotHasKey('yapistirici', $after['sponsor_lines']);

        $this->get('/araclar/seramik-maliyet-hesaplayici?'.http_build_query($q))->assertOk()->assertSee('Sponsorlu · Deneme Kimya', false);
    }

    public function test_su_yalitimi_kalem_kategorisi_sisteme_gore(): void
    {
        $cats = \App\Services\Sponsors\SponsorLines::categories('su-yalitimi-hesaplayici', ['imalat'], ['system' => 'cement_2k_mesh']);
        $this->assertSame(['imalat' => 'cimento'], $cats);
        $this->assertSame(['imalat' => 'likit'], \App\Services\Sponsors\SponsorLines::categories('su-yalitimi-hesaplayici', ['imalat'], ['system' => 'pure_polyurea']));
        $this->assertSame(['imalat' => 'membran'], \App\Services\Sponsors\SponsorLines::categories('su-yalitimi-hesaplayici', ['imalat'], ['system' => 'roof_bituminous']));
    }

    public function test_paketler_tutarli_ve_komutla_kapali_acilir(): void
    {
        $tools = (array) config('worksitem_sponsors.tools');
        foreach (SponsorPackages::all() as $k => $p) {
            foreach ($p['tools'] as $t => $cats) {
                foreach ($cats as $c) {
                    $this->assertArrayHasKey($c, $tools[$t]['categories'] ?? [], "$k: $t/$c");
                }
            }
        }
        $this->artisan('sponsors:package', ['package' => 'seramik-uygulama', '--brand' => 'Paket Marka', '--url' => 'https://example.com'])->assertSuccessful();
        $this->assertSame(SponsorPackages::slots(SponsorPackages::get('seramik-uygulama')), Sponsor::where('brand', 'Paket Marka')->count());
        $this->assertSame(0, Sponsor::live()->where('brand', 'Paket Marka')->count());   // sözleşmesiz yayına çıkmaz

        $this->get('/markalar-icin')->assertOk()->assertSee('Yapı kimyasalları paketleri')->assertSee('Onarım ve Grout');
    }

    public function test_demo_sponsor_canlida_acilmaz_ve_gosterilmez(): void
    {
        $this->artisan('sponsors:demo')->assertSuccessful();
        $n = Sponsor::where('brand', \App\Console\Commands\SponsorDemo::BRAND)->count();
        $this->assertGreaterThan(20, $n);
        $this->assertSame($n, Sponsor::live()->where('brand', \App\Console\Commands\SponsorDemo::BRAND)->count());

        $this->app['env'] = 'production';
        $this->assertSame(0, Sponsor::live()->where('brand', \App\Console\Commands\SponsorDemo::BRAND)->count());
        $this->artisan('sponsors:demo')->assertFailed();
        $this->app['env'] = 'testing';

        $this->artisan('sponsors:demo', ['--remove' => true])->assertSuccessful();
        $this->assertSame(0, Sponsor::where('brand', \App\Console\Commands\SponsorDemo::BRAND)->count());
    }

    public function test_demo_gercek_marka_ve_seramik_markasiyla_kurulur(): void
    {
        $this->artisan('sponsors:demo', ['--brand' => 'Kalekim', '--url' => 'https://www.kalekim.com', '--tile-brand' => 'Kale', '--tile-url' => 'https://www.kale.com.tr', '--tile-logo' => '/storage/sponsor-demo/kale.png'])->assertSuccessful();
        $this->assertSame(0, Sponsor::where('brand', \App\Console\Commands\SponsorDemo::BRAND)->count());
        $this->assertTrue(Sponsor::where('brand', 'Kale')->where('tool_slug', 'seramik-maliyet-hesaplayici')->where('category', 'seramik')->where('logo_url', '/storage/sponsor-demo/kale.png')->exists());
        $this->assertTrue(Sponsor::where('brand', 'Kalekim')->where('tool_slug', 'su-yalitimi-hesaplayici')->exists());

        $r = $this->calc('seramik-maliyet-hesaplayici', ['surface' => 'zemin', 'floor' => 40, 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir', 'grout' => 'epoksi']);
        $this->assertSame('Kale', $r['sponsor_lines']['seramik']['brand']);
        $this->assertSame('Kalekim', $r['sponsor_lines']['derz_epoksi']['brand']);

        $this->app['env'] = 'production';
        $this->assertSame(0, Sponsor::live()->whereIn('brand', ['Kale', 'Kalekim'])->count());
        $this->app['env'] = 'testing';
    }
}

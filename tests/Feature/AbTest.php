<?php

namespace Tests\Feature;

use App\Support\Ab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AbTest extends TestCase
{
    use RefreshDatabase;

    public function test_surum_cerezle_sabit_kalir_ve_olcum_sayilir(): void
    {
        $r = $this->get('/');
        $r->assertOk()->assertSee('data-ab="home_hero_cta:', false);
        $cookie = collect($r->headers->getCookies())->first(fn ($c) => $c->getName() === Ab::COOKIE);
        $this->assertNotNull($cookie);
        preg_match('/data-ab="home_hero_cta:([ab])"/', $r->getContent(), $m);

        // aynı çerezle aynı sürüm
        $again = $this->withCookie(Ab::COOKIE, json_encode(['home_hero_cta' => $m[1]]))->get('/');
        preg_match('/data-ab="home_hero_cta:([ab])"/', $again->getContent(), $m2);
        $this->assertSame($m[1], $m2[1]);
        $this->assertStringContainsString(config('worksitem_ab.tests.home_hero_cta.variants.'.$m[1]), $again->getContent());

        $this->post('/ab', ['ab' => 'home_hero_cta:'.$m[1], 'e' => 'view'])->assertNoContent();
        $this->post('/ab', ['ab' => 'home_hero_cta:'.$m[1], 'e' => 'click'])->assertNoContent();
        $this->post('/ab', ['ab' => 'home_hero_cta:zz', 'e' => 'view'])->assertNoContent();             // bilinmeyen sürüm sayılmaz
        $this->withHeader('User-Agent', 'Googlebot/2.1')->post('/ab', ['ab' => 'home_hero_cta:a', 'e' => 'view']);
        $row = DB::table('ab_stats')->where('test', 'home_hero_cta')->first();
        $this->assertSame(1, (int) $row->views);
        $this->assertSame(1, (int) $row->clicks);
        $this->assertSame(1, DB::table('ab_stats')->count());
    }

    public function test_kazanan_sabitlenince_herkes_ayni_metni_gorur_ve_rapor(): void
    {
        config(['worksitem_ab.tests.home_hero_cta.winner' => 'b']);
        $this->get('/')->assertSee('Ücretsiz Hesapla: Bütçemi Gör')->assertDontSee('data-ab="home_hero_cta', false);

        config(['worksitem_ab.tests.home_hero_cta.winner' => null]);
        $day = now('Europe/Istanbul')->toDateString();
        DB::table('ab_stats')->insert([
            ['day' => $day, 'test' => 'home_hero_cta', 'variant' => 'a', 'views' => 1000, 'clicks' => 50, 'conversions' => 0],
            ['day' => $day, 'test' => 'home_hero_cta', 'variant' => 'b', 'views' => 1000, 'clicks' => 90, 'conversions' => 0],
        ]);
        $b = collect(Ab::report())->first(fn ($r) => $r['test'] === 'home_hero_cta' && $r['variant'] === 'b');
        $this->assertSame("A'dan anlamlı derecede iyi", $b['note']);

        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/ab-testleri')->assertOk()->assertSee('Anasayfa ana düğme')->assertSee('9,0%');
    }

    public function test_firma_kaydi_donusum_sayar_ve_arac_sayfasinda_test(): void
    {
        $this->get('/araclar/boya-hesaplayici')->assertOk()->assertSee('data-ab="tool_lead_submit:', false);
        $this->get('/firma-uyeligi')->assertOk()->assertSee('data-ab="firm_signup_cta:', false);
    }
}

<?php

namespace Tests\Feature;

use App\Services\Pricing\PriceRobot;
use App\Services\Pricing\PriceRobotOverrides;
use App\Support\PriceDate;
use Tests\TestCase;

/** Fiyat robotu: her araç ya robotta, ya bağlı araçta, ya resmî kaynakta, ya da fiyatsız (açıkça). */
class FiyatRobotuKapsamaTest extends TestCase
{
    public function test_her_arac_kapsamda(): void
    {
        $robotTools = array_unique(array_column(app(PriceRobot::class)->catalog(), 'tool'));
        $cov = (array) config('worksitem_price_robot.coverage');
        $groups = [$robotTools, array_keys($cov['linked']), array_keys($cov['official']), array_keys($cov['no_price'])];
        foreach (array_keys((array) config('worksitem_tools.tools')) as $slug) {
            $hits = count(array_filter($groups, fn ($g) => in_array($slug, $g, true)));
            $this->assertSame(1, $hits, "$slug: tam bir kapsama grubunda olmalı ($hits)");
        }
        foreach ($cov['linked'] as $from => $to) {
            $this->assertContains($to, $robotTools, "$from → $to robotta değil");
        }
        $this->assertGreaterThanOrEqual(250, count(app(PriceRobot::class)->catalog()));
    }

    public function test_robot_kalemleri_gecerli(): void
    {
        $tools = array_keys((array) config('worksitem_tools.tools'));
        $paths = [];
        foreach ((array) config('worksitem_price_robot.items') as $it) {
            $this->assertNotContains($it['path'], $paths, 'tekrarlanan kalem: '.$it['path']);
            $paths[] = $it['path'];
            $this->assertContains($it['tool'], $tools, $it['path'].': araç yok');
            $this->assertTrue(PriceRobotOverrides::applies($it['path']), $it['path'].': robot bu dala yazamaz');
            $v = config($it['path']);
            $ok = is_numeric($v) || (is_array($v) && $v !== [] && collect($v)->every(fn ($x) => is_numeric($x)));
            $this->assertTrue($ok, $it['path'].': config değeri sayı değil');
            foreach (['label', 'q', 'unit'] as $k) {
                $this->assertNotSame('', trim((string) ($it[$k] ?? '')), $it['path'].": $k boş");
            }
        }
    }

    public function test_haftalik_yenileme_ve_gunluk_limit_yeter(): void
    {
        $n = count(app(PriceRobot::class)->catalog());
        $days = (int) config('worksitem_price_robot.refresh_days');
        $this->assertSame(7, $days);
        $this->assertGreaterThanOrEqual((int) ceil($n / $days), (int) config('worksitem_price_robot.daily_limit'), 'günlük limit haftalık döngüye yetmiyor');
        $this->assertLessThan(5000, $n * 31 / $days, 'aylık arama ücretsiz kotayı aşar');
    }

    public function test_rapor_notlari(): void
    {
        $this->assertStringContainsString('resmî kaynaktan', PriceDate::note('tahmini-bina-maliyeti'));
        $this->assertStringContainsString('fiyat robotuyla', PriceDate::note('wc-maliyet-hesabi'));   // banyoya bağlı
        $this->assertMatchesRegularExpression('/Bu araçtaki \d+ kalem/', PriceDate::note('seramik-maliyet-hesaplayici'));
    }

    public function test_hesaplayicilarda_fiyat_merkezden_gelir(): void
    {
        config(['worksitem_tools.tool_status.default' => 'active']);
        config(['pricing.harc.cimento_50kg_torba' => 333]);
        $this->get('/araclar/cimento-kum-hesaplama')->assertOk()->assertSee('const cementPricePerBag = 333;', false);
        config(['pricing.akaryakit.motorin_l' => 61.5]);
        $this->get('/araclar/is-makinesi-maliyeti')->assertOk()->assertSee('id="fuelPrice" class="form-control" value="61.5"', false);
    }

    public function test_her_fiyat_kalemi_robotta(): void
    {
        $paths = array_column((array) config('worksitem_price_robot.items'), 'path');
        $tracked = fn ($p) => (bool) array_filter($paths, fn ($t) => $p === $t || str_starts_with($p, $t.'.') || str_starts_with($t, $p.'.'));
        // Fiyat olmayan katsayılar: oran, fire, sarfiyat, kutu m², yükseklik, liste içindeki ad / adet / kabarma
        $skip = '/(factor|waste|ratio|share|coverage|per_l|kg_m2|clips_m2|size_labor|box_m2|height|extra_month|kg_m2_cm|duvar\.materials\.\w+\.[01]$|opening\.sabit$)/';
        $missing = [];
        $walk = function (array $arr, string $pre) use (&$walk, &$missing, $tracked, $skip) {
            foreach ($arr as $k => $v) {
                $p = $pre.'.'.$k;
                if (is_array($v) && ! (array_is_list($v) && collect($v)->every(fn ($x) => is_numeric($x)))) {
                    $walk($v, $p);
                } elseif ((is_numeric($v) || is_array($v)) && ! preg_match($skip, $p) && ! $tracked($p)) {
                    $missing[] = $p;
                }
            }
        };
        $e = (array) config('worksitem_estimators');
        unset($e['tools'], $e['prices_reviewed'], $e['yfk_period']);
        $walk($e, 'worksitem_estimators');
        $walk((array) config('worksitem_solar.bom.prices'), 'worksitem_solar.bom.prices');
        $this->assertSame([], $missing, 'robotta olmayan fiyat kalemleri');
    }
}

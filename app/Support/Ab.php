<?php

namespace App\Support;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

/**
 * Basit A/B testi: ziyaretçiye çerezle sabit bir sürüm atanır (config/worksitem_ab.php).
 * Görüntülenme / tıklama tarayıcıdan /ab adresine gelir; dönüşüm sunucuda Ab::convert() ile sayılır.
 */
class Ab
{
    public const COOKIE = 'ws_ab';

    public static function variant(string $test): string
    {
        $cfg = config("worksitem_ab.tests.$test");
        if (! is_array($cfg)) {
            return 'a';
        }
        if (! empty($cfg['winner'])) {
            return (string) $cfg['winner'];
        }
        $map = self::map();
        $keys = array_keys((array) $cfg['variants']);
        if (! isset($map[$test]) || ! in_array($map[$test], $keys, true)) {
            $map[$test] = $keys[random_int(0, count($keys) - 1)];
            request()->attributes->set('ws_ab', $map);
            Cookie::queue(self::COOKIE, json_encode($map), 60 * 24 * 365, null, null, null, false, false, 'Lax');
        }

        return $map[$test];
    }

    public static function text(string $test): string
    {
        return (string) config("worksitem_ab.tests.$test.variants.".self::variant($test), '');
    }

    /** data-ab özniteliği: tarayıcı görüntülenme / tıklamayı bu değerle bildirir (kazanan sabitse ölçüm yok) */
    public static function attr(string $test): string
    {
        return config("worksitem_ab.tests.$test.winner") ? '' : 'data-ab="'.e($test.':'.self::variant($test)).'"';
    }

    public static function count(string $test, string $variant, string $field): void
    {
        if (! isset(config("worksitem_ab.tests.$test.variants")[$variant]) || ! in_array($field, ['views', 'clicks', 'conversions'], true)) {
            return;
        }
        $day = now('Europe/Istanbul')->toDateString();
        try {
            DB::table('ab_stats')->insertOrIgnore(['day' => $day, 'test' => $test, 'variant' => $variant]);
            DB::table('ab_stats')->where(['day' => $day, 'test' => $test, 'variant' => $variant])->increment($field);
        } catch (\Throwable) {
            // ölçüm hatası sayfayı bozmasın
        }
    }

    /** Sunucu tarafı dönüşüm: ziyaretçinin çerezindeki sürüme yazılır */
    public static function convert(string $test): void
    {
        $v = self::map()[$test] ?? null;
        if ($v && ! config("worksitem_ab.tests.$test.winner")) {
            self::count($test, $v, 'conversions');
        }
    }

    /** Sürüm başına toplamlar + tıklama/dönüşüm oranı ve A'ya göre anlamlılık (iki oran z testi, %95) */
    public static function report(): array
    {
        $rows = [];
        foreach ((array) config('worksitem_ab.tests') as $test => $cfg) {
            $sum = DB::table('ab_stats')->where('test', $test)->selectRaw('variant, SUM(views) v, SUM(clicks) c, SUM(conversions) k')->groupBy('variant')->get()->keyBy('variant');
            $base = null;
            foreach ((array) $cfg['variants'] as $key => $text) {
                $s = $sum[$key] ?? null;
                $v = (int) ($s->v ?? 0);
                $c = (int) ($s->c ?? 0);
                $k = (int) ($s->k ?? 0);
                $rate = $v ? $c / $v : 0;
                $row = ['test' => $test, 'label' => $cfg['label'], 'variant' => $key, 'text' => $text, 'views' => $v, 'clicks' => $c, 'conversions' => $k,
                    'ctr' => $rate, 'winner' => ($cfg['winner'] ?? null) === $key, 'note' => ''];
                if ($base === null) {
                    $base = $row;
                } elseif ($v >= 100 && $base['views'] >= 100) {
                    $p = ($c + $base['clicks']) / ($v + $base['views']);
                    $se = sqrt(max($p * (1 - $p) * (1 / $v + 1 / $base['views']), 1e-12));
                    $z = ($rate - $base['ctr']) / $se;
                    $row['note'] = abs($z) >= 1.96 ? ($z > 0 ? 'A\'dan anlamlı derecede iyi' : 'A\'dan anlamlı derecede kötü') : 'Fark henüz anlamlı değil';
                } else {
                    $row['note'] = 'Her sürümde en az 100 görüntülenme gerekli';
                }
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private static function map(): array
    {
        $req = request();
        if ($req->attributes->has('ws_ab')) {
            return (array) $req->attributes->get('ws_ab');
        }
        $m = json_decode((string) $req->cookie(self::COOKIE, ''), true);
        $m = is_array($m) ? array_filter($m, 'is_string') : [];
        $req->attributes->set('ws_ab', $m);

        return $m;
    }
}

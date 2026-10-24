<?php

namespace App\Services\Solar;

use App\Estimators\SolarEstimator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** GES planlayıcı sunucu tarafı: adres arama, bina dış hattı, PVGIS üretim tahmini, maliyet ve geri dönüş */
class SolarPlanService
{
    private function http()
    {
        return Http::timeout(20)->withHeaders(['User-Agent' => (string) config('worksitem_solar.user_agent'), 'Accept-Language' => 'tr']);
    }

    /** @return list<array{name:string, lat:float, lon:float}> */
    public function geocode(string $q): array
    {
        $q = trim(mb_substr($q, 0, 200));
        if (mb_strlen($q) < 3) {
            return [];
        }

        return Cache::remember('ges:geo:'.md5(mb_strtolower($q)), now()->addDays(30), function () use ($q) {
            $r = $this->http()->get((string) config('worksitem_solar.nominatim'), ['q' => $q, 'format' => 'jsonv2', 'countrycodes' => 'tr', 'limit' => 5]);
            if (! $r->successful()) {
                return [];
            }

            return collect((array) $r->json())->map(fn ($p) => ['name' => (string) ($p['display_name'] ?? ''), 'lat' => (float) ($p['lat'] ?? 0), 'lon' => (float) ($p['lon'] ?? 0)])
                ->filter(fn ($p) => $p['lat'] && $p['lon'])->values()->all();
        });
    }

    /**
     * İl / ilçe / mahalle / sokak alanlarıyla adım adım arama (v88).
     * OpenStreetMap'te Türkiye'de her sokak ve kapı numarası yok; en ayrıntılı bulunan seviyeye düşülür:
     * sokak → mahalle → ilçe → il. Sonuçlar seçilen ilin içindekilerle sınırlanır.
     *
     * @return array{level:string, results:list<array{name:string, lat:float, lon:float}>}
     */
    public function geocodeParts(string $il, string $ilce = '', string $mahalle = '', string $sokak = '', string $no = ''): array
    {
        $clean = fn (string $s, int $n) => trim(preg_replace('/\s+/u', ' ', mb_substr(strip_tags($s), 0, $n)));
        [$il, $ilce, $mahalle, $sokak, $no] = [$clean($il, 40), $clean($ilce, 60), $clean($mahalle, 80), $clean($sokak, 100), $clean($no, 10)];
        if ($il === '') {
            return ['level' => 'yok', 'results' => []];
        }
        $ilceQ = ($ilce !== '' && mb_strtolower($ilce) !== 'merkez') ? $ilce : '';
        // "Moda Mah." / "Moda Mahallesi" / "Moda" → "Moda Mahallesi" (OSM adı) ve yalın "Moda"
        $mahKok = trim(preg_replace('/\s+(mahallesi|mahalle|mah\.?|mh\.?)$/iu', '', $mahalle));
        $place = fn (array $parts) => implode(', ', array_values(array_filter($parts, fn ($p) => $p !== '')));

        $steps = [];
        if ($sokak !== '') {
            $steps[] = ['sokak', $place([trim($sokak.' '.$no), $mahKok !== '' ? $mahKok.' Mahallesi' : '', $ilceQ, $il])];
            $steps[] = ['sokak', $place([$sokak, $ilceQ, $il])];
        }
        if ($mahKok !== '') {
            $steps[] = ['mahalle', $place([$mahKok.' Mahallesi', $ilceQ, $il])];
            $steps[] = ['mahalle', $place([$mahKok, $ilceQ, $il])];
        }
        if ($ilceQ !== '') {
            $steps[] = ['ilce', $place([$ilceQ, $il])];
        }
        $steps[] = ['il', $il];

        $ilNorm = self::trLower($il);
        $tried = [];
        foreach ($steps as [$level, $q]) {
            if (isset($tried[$q])) {
                continue;
            }
            $tried[$q] = true;
            // Nominatim kullanım kuralı: saniyede en çok 1 istek (önbellekte olanlar sayılmaz)
            $cached = Cache::has('ges:geo:'.md5(mb_strtolower(trim(mb_substr($q, 0, 200)))));
            if (! $cached && ($netUsed ?? false) && ! app()->runningUnitTests()) {
                usleep(1_050_000);
            }
            $netUsed = ($netUsed ?? false) || ! $cached;
            $hits = array_values(array_filter($this->geocode($q), fn ($p) => str_contains(self::trLower($p['name']), $ilNorm)));
            if ($hits) {
                return ['level' => $level, 'results' => $hits];
            }
        }

        return ['level' => 'yok', 'results' => []];
    }

    private static function trLower(string $s): string
    {
        return mb_strtolower(str_replace(['I', 'İ'], ['ı', 'i'], $s));
    }

    /** Noktadaki (yoksa en yakın) bina dış hattı: ['ring' => [[lat,lon],...], 'levels' => ?int] */
    public function building(float $lat, float $lon): ?array
    {
        $key = 'ges:bld:'.round($lat, 5).':'.round($lon, 5);

        return Cache::remember($key, now()->addDays(14), function () use ($lat, $lon) {
            $ql = sprintf('[out:json][timeout:15];way["building"](around:60,%.6F,%.6F);out geom tags;', $lat, $lon);
            // Birden çok Overpass sunucusu: biri yavaşsa/engelliyse sıradakine geçilir
            $r = null;
            foreach ((array) config('worksitem_solar.overpass_mirrors', [config('worksitem_solar.overpass')]) as $url) {
                try {
                    $try = $this->http()->timeout((int) config('worksitem_solar.overpass_timeout', 8))->asForm()->post((string) $url, ['data' => $ql]);
                    if ($try->successful() && is_array($try->json('elements'))) {
                        $r = $try;
                        break;
                    }
                    Log::info('GES bina verisi alınamadı', ['host' => parse_url((string) $url, PHP_URL_HOST), 'status' => $try->status()]);
                } catch (\Throwable $e) {
                    Log::info('GES bina verisi alınamadı', ['host' => parse_url((string) $url, PHP_URL_HOST), 'error' => class_basename($e)]);
                }
            }
            if (! $r) {
                return null;
            }
            $best = null;
            $bestD = INF;
            foreach ((array) $r->json('elements') as $el) {
                $ring = array_map(fn ($g) => [(float) $g['lat'], (float) $g['lon']], (array) ($el['geometry'] ?? []));
                if (count($ring) < 4) {
                    continue;
                }
                if (self::inside($lat, $lon, $ring)) {
                    $best = ['ring' => $ring, 'tags' => (array) ($el['tags'] ?? [])];
                    break;
                }
                $c = self::centroid($ring);
                $d = hypot(($c[0] - $lat) * 111320, ($c[1] - $lon) * 111320 * cos(deg2rad($lat)));
                if ($d < $bestD && $d < 40) {
                    $bestD = $d;
                    $best = ['ring' => $ring, 'tags' => (array) ($el['tags'] ?? [])];
                }
            }
            if (! $best) {
                return null;
            }
            $ring = $best['ring'];
            if ($ring[0] === end($ring)) {
                array_pop($ring);
            }
            $lv = $best['tags']['building:levels'] ?? null;

            return ['ring' => $ring, 'levels' => is_numeric($lv) ? (int) $lv : null, 'type' => (string) ($best['tags']['building'] ?? 'yes')];
        });
    }

    /** v118: PVGIS süre sınırları (sn). Tüm çağrılar için toplam bütçe; asılı PVGIS PHP zaman aşımına yol açmasın. */
    private const PVGIS_BUDGET = 15.0;
    private const PVGIS_TIMEOUT = 6;
    private const PVGIS_CONNECT_TIMEOUT = 3;
    /** Başarısız yanıt bu kadar dakika önbellekte: her hesapta/yüzeyde yeniden denenmesin */
    private const PVGIS_FAIL_MINUTES = 10;
    /** Bağlantı hatası/zaman aşımı/5xx → PVGIS'e bu kadar dakika hiç gidilmez (tüm konumlar) */
    private const PVGIS_DOWN_MINUTES = 3;

    /** Bu istekteki PVGIS süre bütçesinin bitiş anı (microtime) */
    private ?float $pvgisDeadline = null;

    /**
     * @param  list<array{kwp:float, angle:float, azimuth:float}>  $faces  azimuth: pusula (0=K, 90=D, 180=G, 270=B)
     * @return array{monthly: list<float>, yearly: float, source: string, faces: list<float>, estimated: bool, note: ?string}
     */
    public function yield(float $lat, float $lon, array $faces, bool $ground = false): array
    {
        $monthly = array_fill(0, 12, 0.0);
        $perFace = [];
        $source = 'PVGIS';
        $budget = (float) config('worksitem_solar.pvgis_budget', self::PVGIS_BUDGET);
        $this->pvgisDeadline = microtime(true) + max(1.0, min(25.0, $budget));
        $seen = [];   // aynı (eğim, yön) yüzeyler için PVGIS tek kez sorulur
        foreach ($faces as $f) {
            $kwp = max(0.0, (float) $f['kwp']);
            if ($kwp <= 0) {
                $perFace[] = 0.0;

                continue;
            }
            $angle = (int) max(0, min(90, round((float) $f['angle'])));
            $aspect = (int) round(fmod(((float) $f['azimuth'] - 180) + 540, 360) - 180);   // PVGIS: 0=güney, 90=batı, -90=doğu
            $k = $angle.':'.$aspect;
            if (! array_key_exists($k, $seen)) {
                $seen[$k] = $this->pvgisPerKwp($lat, $lon, $angle, $aspect, $ground ? 'free' : 'building');
            }
            $m = $seen[$k];
            if (! $m) {
                $source = 'yaklaşık';
                $m = self::fallbackPerKwp($lat, $angle, $aspect);
            }
            $sum = 0.0;
            foreach ($m as $i => $v) {
                $monthly[$i] += $v * $kwp;
                $sum += $v * $kwp;
            }
            $perFace[] = round($sum);
        }
        $this->pvgisDeadline = null;
        $estimated = $source !== 'PVGIS';

        return ['monthly' => array_map(fn ($v) => round($v), $monthly), 'yearly' => round(array_sum($monthly)), 'source' => $source, 'faces' => $perFace,
            'estimated' => $estimated,
            'note' => $estimated ? 'Üretim tahminidir: PVGIS güneş verisi şu an alınamadı, bölgesel yaklaşık değer kullanıldı.' : null];
    }

    /**
     * 1 kWp için aylık kWh (PVGIS), önbellekli.
     * v118: kısa zaman aşımı, toplam süre bütçesi, başarısız yanıt kısa süre önbellekte (Cache::remember null saklamıyordu),
     * PVGIS erişilemezse birkaç dakika hiç denenmez.
     */
    private function pvgisPerKwp(float $lat, float $lon, int $angle, int $aspect, string $place = 'building'): ?array
    {
        $key = sprintf('ges:pvgis:%.2F:%.2F:%d:%d:%s', $lat, $lon, $angle, $aspect, $place);
        $hit = Cache::get($key);
        if (is_array($hit) && count($hit) === 12) {
            return $hit;
        }
        if (Cache::has($key.':fail') || Cache::has('ges:pvgis:down')) {
            return null;
        }

        $data = null;
        $down = false;
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $left = $this->pvgisDeadline === null ? (float) self::PVGIS_TIMEOUT : $this->pvgisDeadline - microtime(true);
            if ($left < 1.0) {   // bütçe bitti: bu istekte yedek katsayı (önbelleğe "başarısız" yazılmaz, sonra tekrar denenir)
                return null;
            }
            $timeout = (int) max(1, min((int) config('worksitem_solar.pvgis_timeout', self::PVGIS_TIMEOUT), floor($left)));
            try {
                $r = Http::timeout($timeout)->connectTimeout(min(self::PVGIS_CONNECT_TIMEOUT, $timeout))
                    ->withHeaders(['User-Agent' => (string) config('worksitem_solar.user_agent'), 'Accept-Language' => 'tr'])
                    ->get((string) config('worksitem_solar.pvgis'), [
                        'lat' => round($lat, 4), 'lon' => round($lon, 4), 'peakpower' => 1, 'loss' => (int) config('worksitem_solar.system_loss', 14),
                        'angle' => $angle, 'aspect' => $aspect, 'mountingplace' => $place, 'outputformat' => 'json',
                    ]);
            } catch (\Throwable $e) {   // bağlantı hatası / zaman aşımı: yeniden deneme yok, PVGIS kısa süre "kapalı"
                Log::info('PVGIS alınamadı', ['error' => $e->getMessage()]);
                $down = true;

                break;
            }
            $rows = (array) $r->json('outputs.monthly.fixed');
            if ($r->successful() && count($rows) === 12) {
                $data = array_map(fn ($row) => (float) ($row['E_m'] ?? 0), $rows);

                break;
            }
            Log::info('PVGIS yanıtı kullanılamadı', ['status' => $r->status(), 'body' => mb_substr($r->body(), 0, 200)]);
            if (! $r->serverError()) {   // 4xx (ör. denizde konum): yeniden denemenin anlamı yok
                break;
            }
            $down = $attempt === 2;
            if ($attempt === 1) {
                usleep(300000);
            }
        }

        if ($data === null) {
            Cache::put($key.':fail', 1, now()->addMinutes(self::PVGIS_FAIL_MINUTES));
            if ($down) {
                Cache::put('ges:pvgis:down', 1, now()->addMinutes(self::PVGIS_DOWN_MINUTES));
            }

            return null;
        }
        Cache::put($key, $data, now()->addDays(90));

        return $data;
    }

    /** PVGIS yanıt vermezse: enleme göre kaba aylık üretim (kWh/kWp) × yön/eğim kaybı */
    public static function fallbackPerKwp(float $lat, int $angle, int $aspect): array
    {
        $year = 1700 - max(0, $lat - 36) * 45;                 // Antalya ≈1.700, Samsun ≈1.300 kWh/kWp
        $shape = [0.045, 0.058, 0.083, 0.095, 0.11, 0.117, 0.122, 0.115, 0.095, 0.075, 0.05, 0.04];
        $orient = 1 - 0.0016 * abs($aspect) * min(1, $angle / 30) - 0.004 * abs($angle - 30) * 0.3;
        if ($angle > 60) {   // v90: dikey cephe — güney cephe ≈ %70, doğu/batı ≈ %55 (yaz/kış şekli de değişir)
            $orient *= 1 - ($angle - 60) * 0.01;
            $shape = [0.07, 0.076, 0.088, 0.085, 0.087, 0.084, 0.088, 0.09, 0.09, 0.087, 0.078, 0.077];
        }

        return array_map(fn ($s) => $year * $s * max(0.35, $orient), $shape);
    }

    /** Maliyet ve geri dönüş: GES maliyet aracının kWp fiyatlarıyla */
    public function economics(float $kwp, string $use, bool $flat, float $yearly, float $monthlyKwh, float $price, bool $battery = false, array $site = []): array
    {
        $P = SolarEstimator::prices();
        $scale = 1.0;
        foreach ((array) config('worksitem_solar.scale') as [$min, $f]) {
            if ($kwp >= $min) {
                $scale = $f;
            }
        }
        $land = $use === 'arazi';
        $mount = $land ? $P['mount_kwp']['arazi'] : $P['mount_kwp']['cati'] * ($flat ? (float) config('worksitem_solar.flat_mount_factor', 1.15) : 1);
        $items = [
            'Güneş paneli' => $kwp * $P['panel_kwp'] * $scale,
            'İnverter' => $kwp * $P['inverter_kwp'] * $scale,
            'Konstrüksiyon' => $kwp * $mount * $scale,
            'Kablo, koruma ve montaj' => $kwp * $P['install_kwp'] * $scale,
            'Proje, başvuru ve kabul' => $land ? (float) config('worksitem_solar.land.project', 250000) : $P['project'] * ($use === 'fabrika' ? 3 : 1),
        ];
        if ($land) {
            $a = (float) ($site['area'] ?? 0);
            $per = (float) ($site['perimeter'] ?? 0);
            $L = (array) config('worksitem_solar.land');
            if ($a > 0) {
                $items['Saha hazırlığı ve tesviye ('.number_format($a, 0, ',', '.').' m²)'] = $a * (float) ($L['prep_m2'] ?? 40);
            }
            if ($per > 0) {
                $items['Çevre çiti ve giriş kapısı ('.number_format($per, 0, ',', '.').' m)'] = $per * (float) ($L['fence_m'] ?? 1500);
            }
            $items['Kamera, aydınlatma ve topraklama'] = max(60000, $kwp * (float) ($L['site_kwp'] ?? 400));
        }
        if ($battery) {
            $kwh = round($kwp * (float) config('worksitem_solar.battery_ratio', 2), 1);
            $items['Batarya ('.str_replace('.', ',', (string) $kwh).' kWh)'] = $kwh * $P['battery_kwh'] * $scale;
            $items['Otomatik güç kontrolü ve enerji kesme sistemi'] = (float) (config('worksitem_solar.storage_control')[$use === 'arazi' ? 'fabrika' : $use] ?? 35000);
        }
        $total = array_sum($items) * 1.05;

        return ['items' => array_map(fn ($v) => round($v, -2), $items),
            'total' => round($total, -3), 'low' => round($total * 0.85, -3), 'high' => round($total * 1.2, -3)]
            + $this->finance($total, $yearly, $monthlyKwh, $price);
    }

    /** Tasarruf, geri dönüş, tüketimi karşılama, fazla üretim, CO₂ (toplam maliyetten bağımsız ortak hesap) */
    public function finance(float $total, float $yearly, float $monthlyKwh, float $price): array
    {
        $consYear = $monthlyKwh * 12;
        $self = $consYear > 0 ? min($yearly, $consYear) : $yearly;
        $saving = $self * $price;

        return [
            'saving' => round($saving, -2), 'payback' => $saving > 0 ? round($total / $saving, 1) : null,
            'coverage' => $consYear > 0 ? round(min(1, $yearly / $consYear) * 100) : null,
            'excess' => $consYear > 0 ? max(0, round($yearly - $consYear)) : 0,
            'co2' => round($yearly * 0.44 / 1000, 1),   // ton/yıl (≈0,44 kg CO₂/kWh şebeke)
        ];
    }

    public static function inside(float $lat, float $lon, array $ring): bool
    {
        $in = false;
        for ($i = 0, $j = count($ring) - 1; $i < count($ring); $j = $i++) {
            [$yi, $xi] = $ring[$i];
            [$yj, $xj] = $ring[$j];
            if ((($yi > $lat) !== ($yj > $lat)) && ($lon < ($xj - $xi) * ($lat - $yi) / (($yj - $yi) ?: 1e-12) + $xi)) {
                $in = ! $in;
            }
        }

        return $in;
    }

    private static function centroid(array $ring): array
    {
        $n = count($ring);

        return [array_sum(array_column($ring, 0)) / $n, array_sum(array_column($ring, 1)) / $n];
    }
}

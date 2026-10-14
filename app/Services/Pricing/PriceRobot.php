<?php

namespace App\Services\Pricing;

use App\Estimators\CatalogPozEstimator;
use App\Estimators\KitRegistry;
use App\Estimators\PozEstimator;
use App\Models\PriceRobotItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Her sabah: önce resmî poz, yoksa Google'da (Gemini + Google Arama) güncel fiyat.
 * Ortanca × çarpan yayına yazılır; şüpheli sonuç (tek kaynak, ani sıçrama) onay bekler.
 * Ayrıntı: config/worksitem_price_robot.php
 */
class PriceRobot
{
    /** Son Gemini cevabı (teşhis için: prices:robot --debug) */
    public array $lastRaw = [];

    /** "12.500 TL", "12.500,50", "1,250.00", 12500 → float */
    public static function parsePrice(mixed $v): float
    {
        if (is_int($v) || is_float($v)) {
            return (float) $v;
        }
        $t = preg_replace('/[^\d.,]/u', '', (string) $v);
        if ($t === '' || $t === null) {
            return 0.0;
        }
        if (str_contains($t, ',') && str_contains($t, '.')) {
            $t = strrpos($t, ',') > strrpos($t, '.') ? str_replace(['.', ','], ['', '.'], $t) : str_replace(',', '', $t);
        } elseif (str_contains($t, ',')) {
            $t = preg_match('/,\d{3}$/', $t) ? str_replace(',', '', $t) : str_replace(',', '.', $t);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $t)) {
            $t = str_replace('.', '', $t);   // Türkçe binlik ayırıcı
        }

        return is_numeric($t) ? round((float) $t, 2) : 0.0;
    }

    /** Araştırılacak tüm kalemler (config kalemleri + resmî fiyatı olmayan poz sistemleri) */
    public function catalog(): array
    {
        $items = [];
        foreach ((array) config('worksitem_price_robot.items', []) as $it) {
            $items[$it['path']] = $it + ['kind' => 'config', 'multiplier' => (float) config('worksitem_price_robot.market_multiplier', 1.0)];
        }
        foreach (KitRegistry::slugs() as $slug) {
            try {
                $e = KitRegistry::get($slug);
            } catch (\Throwable) {
                continue;
            }
            if (! $e instanceof CatalogPozEstimator) {
                continue;
            }
            foreach ($e->robotSystems() as $sys) {
                $items['poz.'.$sys['key']] = [
                    'path' => 'poz.'.$sys['key'], 'tool' => $slug, 'label' => $sys['label'], 'unit' => 'm²',
                    'q' => $sys['label'].' m2 fiyatı malzeme ve işçilik dahil', 'kind' => 'poz', 'poz' => $sys['key'],
                    'official' => $sys['official'], 'multiplier' => (float) config('worksitem_price_robot.poz_multiplier', 1.40),
                ];
            }
        }

        return $items;
    }

    /** Bugün araştırılacaklar: hiç araştırılmamışlar önce, sonra eskiyenler; kilitli ve resmî fiyatı olanlar hariç */
    public function due(int $limit, ?string $only = null): array
    {
        $refresh = now()->subDays((int) config('worksitem_price_robot.refresh_days', 30));
        $rows = PriceRobotItem::query()->get()->keyBy('path');
        $due = [];
        foreach ($this->catalog() as $path => $it) {
            if ($only && $path !== $only) {
                continue;
            }
            $r = $rows->get($path);
            if ($r && $r->locked && ! $only) {
                continue;
            }
            if ($it['kind'] === 'poz' && ! $only && $this->officialPrice($it['poz'])) {
                continue;   // önce poz: resmî fiyat varsa web araştırması yapılmaz
            }
            if ($r && $r->researched_at && $r->researched_at->gt($refresh) && ! $only) {
                continue;
            }
            $due[] = $it + ['_last' => $r?->researched_at?->timestamp ?? 0];
        }
        usort($due, fn ($a, $b) => $a['_last'] <=> $b['_last']);

        return array_slice($due, 0, max(1, $limit));
    }

    /** Tek kalemi araştırır, kaydeder; yayına girdiyse true */
    public function run(array $it, bool $dryRun = false): array
    {
        $current = $this->currentValue($it);
        $row = PriceRobotItem::query()->firstOrNew(['path' => $it['path']]);
        $row->fill(['tool_slug' => $it['tool'] ?? null, 'label' => $it['label'], 'unit' => $it['unit'] ?? null]);
        if (! $row->exists) {
            $row->default_value = $current;
        }

        try {
            $found = $this->search($it);
        } catch (\Throwable $e) {
            $row->fill(['status' => $row->status === 'live' ? 'live' : 'error', 'note' => 'Arama hatası: '.mb_substr($e->getMessage(), 0, 300), 'researched_at' => now()]);
            $dryRun || $row->save();

            return ['status' => 'error', 'message' => $e->getMessage(), 'row' => $row];
        }

        $prices = array_values(array_filter(array_map(fn ($s) => (float) $s['price_tl'], $found['sources'])));
        $median = $prices ? $this->median($prices) : null;
        $mult = (float) $it['multiplier'];
        $proposedMid = $median ? round($median / (float) ($it['divide'] ?? 1) * (float) ($it['factor'] ?? 1) * $mult, 2) : null;
        if ($proposedMid && ($it['kind'] ?? '') === 'poz') {
            $proposedMid = round($proposedMid / 1.2, 2);   // YFK birim fiyatları KDV hariç tutulur
        }
        $proposed = $proposedMid ? $this->scale($current, $proposedMid) : null;

        $oldMid = PriceRobotItem::mid($current);
        $jump = (float) config('worksitem_price_robot.max_jump', 2.0);
        $reasons = [];
        if (! $proposedMid) {
            $reasons[] = 'fiyat bulunamadı';
        }
        if (count($prices) < (int) config('worksitem_price_robot.min_sources', 2)) {
            $reasons[] = count($prices).' kaynak (en az '.config('worksitem_price_robot.min_sources', 2).' gerekir)';
        }
        if ($proposedMid && $oldMid && ($proposedMid / $oldMid > $jump || $proposedMid / $oldMid < 1 / $jump)) {
            $reasons[] = 'önceki değere göre '.round($proposedMid / $oldMid, 2).' kat';
        }

        $row->fill([
            'web_median' => $median, 'multiplier' => $mult, 'source_count' => count($prices),
            'sources' => array_slice($found['sources'], 0, 8), 'researched_at' => now(),
        ]);

        if ($reasons) {
            $row->fill(['status' => $proposed ? 'needs_review' : ($row->status === 'live' ? 'live' : 'error'), 'proposed_value' => $proposed,
                'note' => 'Onay bekliyor: '.implode('; ', $reasons).($found['note'] ? ' · '.$found['note'] : '')]);
            $dryRun || $row->save();

            return ['status' => $proposed ? 'needs_review' : 'error', 'proposed' => $proposed, 'reasons' => array_merge($reasons, array_filter([$found['note'] ?? null])), 'row' => $row];
        }

        $row->fill(['status' => 'live', 'previous_value' => $row->value ?? $current, 'value' => $proposed, 'proposed_value' => null,
            'note' => $found['note'] ?: null]);
        if (! $dryRun) {
            DB::transaction(function () use ($row, $it, $proposed) {
                $row->save();
                if (($it['kind'] ?? '') === 'poz') {
                    $this->writeWebPoz($it, (float) $proposed);
                }
                $this->writeUnitPrices($it, $proposed);
            });
        }

        return ['status' => 'live', 'value' => $proposed, 'row' => $row];
    }

    /** Uzman onayı (admin): öneriyi yayına alır */
    public function approve(PriceRobotItem $row, ?int $userId = null): void
    {
        $value = $row->proposed_value ?? $row->value;
        if ($value === null) {
            return;
        }
        $row->fill(['previous_value' => $row->value ?? $row->default_value, 'value' => $value, 'proposed_value' => null, 'status' => 'live',
            'reviewed_by' => $userId, 'reviewed_at' => now()])->save();
        if (str_starts_with($row->path, 'poz.')) {
            $this->writeWebPoz(['poz' => substr($row->path, 4), 'label' => $row->label, 'unit' => $row->unit], (float) $value);
        }
        $this->writeUnitPrices($this->catalog()[$row->path] ?? [], $value);
    }

    /** Kalem admin fiyat defterine bağlıysa (unit_keys) yeni fiyatı oraya da yazar: ekonomik/orta/premium sırasıyla */
    private function writeUnitPrices(array $it, mixed $value): void
    {
        $keys = (array) ($it['unit_keys'] ?? []);
        if (! $keys || ! Schema::hasTable('unit_prices')) {
            return;
        }
        $vals = is_array($value) ? array_values($value) : [$value];
        foreach (array_values($keys) as $i => $key) {
            if (! isset($vals[$i]) || ! is_numeric($vals[$i])) {
                continue;
            }
            DB::table('unit_prices')->where('key', $key)->update([
                'price' => round((float) $vals[$i], 2), 'source_name' => 'Fiyat robotu (Google, ortanca)',
                'source_date' => now()->toDateString(), 'updated_at' => now(),
            ]);
        }
        \Illuminate\Support\Facades\Cache::forget(\App\Services\BathroomPriceBook::CACHE_KEY);
    }

    // ── yardımcılar ──────────────────────────────────────────────

    private function currentValue(array $it): mixed
    {
        if (($it['kind'] ?? '') === 'poz') {
            return $this->webPozPrice($it['poz']);
        }
        if (! empty($it['unit_keys']) && str_starts_with($it['path'], 'worksitem_bathroom.')) {
            // Banyo fiyatları admin fiyat defterinde (unit_prices) tutulur; geçerli değer oradan okunur
            $cfg = app(\App\Services\BathroomPriceBook::class)->apply((array) config('worksitem_bathroom'));

            return Arr::get($cfg, substr($it['path'], strlen('worksitem_bathroom.')));
        }
        $live = PriceRobotOverrides::map()[$it['path']] ?? null;

        return $live ?? config($it['path']);
    }

    /** Dizi ise orta değere göre oranla ölçekler */
    private function scale(mixed $current, float $mid): mixed
    {
        if (! is_array($current)) {
            return $mid;
        }
        $old = PriceRobotItem::mid($current);
        if (! $old) {
            return $current;
        }
        $r = $mid / $old;

        return array_map(fn ($v) => round((float) $v * $r, $v >= 100 ? 0 : 2), $current);
    }

    private function median(array $v): float
    {
        sort($v);
        $n = count($v);

        return $n % 2 ? $v[intdiv($n, 2)] : ($v[$n / 2 - 1] + $v[$n / 2]) / 2;
    }

    private function officialPrice(string $poz): ?float
    {
        if (str_starts_with($poz, 'WEB-') || ! Schema::hasTable('rayic_prices')) {
            return null;
        }
        $p = DB::table('rayic_prices')->where('poz_no', $poz)->where('source', 'YFK')->where('is_active', true)->value('price');

        return is_numeric($p) && (float) $p > 0 ? (float) $p : null;
    }

    private function webPozPrice(string $poz): ?float
    {
        if (! Schema::hasTable('rayic_prices')) {
            return null;
        }
        $p = DB::table('rayic_prices')->where('poz_no', $poz)->where('source', 'WEB')->where('is_active', true)->orderByDesc('id')->value('price');

        return is_numeric($p) ? (float) $p : null;
    }

    private function writeWebPoz(array $it, float $price): void
    {
        if (! Schema::hasTable('rayic_prices')) {
            return;
        }
        DB::table('rayic_prices')->where('poz_no', $it['poz'])->where('source', 'WEB')->update(['is_active' => false, 'updated_at' => now()]);
        DB::table('rayic_prices')->insert([
            'source' => 'WEB', 'period' => now()->format('Y-m'), 'poz_no' => $it['poz'],
            'description' => mb_substr('Piyasa (web) × '.config('worksitem_price_robot.poz_multiplier', 1.40).' · '.$it['label'], 0, 250),
            'unit' => $it['unit'] ?? 'm²', 'price' => $price, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        PozEstimator::flushCache();
    }

    /**
     * Gemini + Google Arama ile fiyat kaynakları.
     *
     * @return array{sources: list<array{name:string,url:string,price_tl:float}>, note:string}
     */
    public function search(array $it): array
    {
        $key = (string) config('services.gemini.api_key');
        if ($key === '') {
            throw new RuntimeException('GEMINI_API_KEY tanımlı değil');
        }
        $model = (string) config('worksitem_price_robot.model');
        $prompt = "ÖNCE MUTLAKA Google'da ara (hafızandan fiyat yazma). Türkiye'de bugünkü güncel piyasa fiyatını araştır: \"{$it['q']}\". Birim: {$it['unit']}.\n"
            ."Google'da ara; satıcı, mağaza, firma veya fiyat listesi sayfalarından en az 3, en fazla 6 farklı kaynak bul.\n"
            ."Kurallar: yalnız Türkiye ve Türk lirası; son 6 ayın fiyatları; aynı birim; kampanya/indirim değil normal satış fiyatı; "
            ."fiyat KDV hariçse 1,20 ile çarpıp KDV dahil yaz; aralık verilmişse ortasını al. Fiyat bulamazsan uydurma, boş liste döndür.\n"
            .'Yalnız şu JSON ile cevap ver, başka metin yazma: '
            .'{"sources":[{"name":"site veya firma adı","url":"https://...","price_tl":0}],"note":"kısa not"}';

        // Google arama (grounding): Interactions API — Google'ın güncel belgelediği yol
        $res = Http::timeout(120)->retry(1, 2000, throw: false)
            ->withHeaders(['x-goog-api-key' => $key, 'Content-Type' => 'application/json'])
            ->post('https://generativelanguage.googleapis.com/v1beta/interactions', [
                'model' => $model,
                'input' => $prompt,
                'tools' => [['type' => 'google_search']],
            ]);
        if (! $res->successful()) {
            throw new RuntimeException('Gemini HTTP '.$res->status().': '.mb_substr((string) $res->body(), 0, 200));
        }
        $texts = [];
        $queries = [];
        $cites = [];
        foreach ((array) Arr::get($res->json(), 'steps', []) as $step) {
            $type = $step['type'] ?? '';
            if ($type === 'google_search_call') {
                $queries = array_merge($queries, (array) Arr::get($step, 'arguments.queries', []));
            }
            if ($type === 'model_output') {
                foreach ((array) ($step['content'] ?? []) as $c) {
                    if (($c['type'] ?? '') === 'text' && isset($c['text'])) {
                        $texts[] = $c['text'];
                        foreach ((array) ($c['annotations'] ?? []) as $an) {
                            if (($an['type'] ?? '') === 'url_citation' && ! empty($an['url'])) {
                                $cites[] = ['url' => (string) $an['url'], 'title' => (string) ($an['title'] ?? '')];
                            }
                        }
                    }
                }
            }
        }
        $text = implode('', $texts);
        $chunks = collect($cites)->unique('url')->values();
        $this->lastRaw = ['text' => mb_substr($text, 0, 3000), 'grounding' => $chunks->count(), 'queries' => $queries,
            'finish' => (string) Arr::get($res->json(), 'status', '-')];
        if (! preg_match('/\{.*\}/s', $text, $m) || ! is_array($j = json_decode($m[0], true))) {
            throw new RuntimeException('Cevap okunamadı: '.mb_substr(trim($text), 0, 160));
        }
        $sources = collect($j['sources'] ?? [])
            ->filter(fn ($s) => is_array($s))
            ->values()
            ->map(function ($s, $i) use ($chunks) {
                $price = self::parsePrice($s['price_tl'] ?? ($s['price'] ?? null));
                $url = (string) ($s['url'] ?? '');
                if (! str_starts_with($url, 'http')) {
                    $url = $chunks->get($i)['url'] ?? '';   // model bağlantı yazmadıysa arama sonucunun bağlantısı
                }
                $name = trim((string) ($s['name'] ?? '')) ?: ($chunks->get($i)['title'] ?? '');

                return ['name' => mb_substr($name, 0, 80), 'url' => mb_substr($url, 0, 500), 'price_tl' => $price];
            })
            ->filter(fn ($s) => $s['price_tl'] > 0 && ($s['url'] !== '' || $s['name'] !== ''))
            ->unique(fn ($s) => $s['url'] ?: $s['name'])->values()->all();
        if ($queries === []) {
            return ['sources' => [], 'note' => 'Google araması yapılmadı; sonuç kullanılmadı'];
        }

        return ['sources' => $sources, 'note' => mb_substr((string) ($j['note'] ?? ''), 0, 300)];
    }
}

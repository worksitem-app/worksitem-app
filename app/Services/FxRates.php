<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * TCMB döviz satış kurları (USD, EUR, GBP).
 *
 * Sayfa açılışında dış istek YAPILMAZ: kur, zamanlayıcıdaki fx:update
 * komutuyla günde iki kez çekilip önbelleğe yazılır. Önbellek boşsa
 * sayfa döviz satırını gizler. Önbellekte yalnızca dizi (array) tutulur
 * (Laravel 13 veritabanı önbelleği nesne okumaz).
 */
class FxRates
{
    public const CACHE_KEY = 'fx:tcmb:v1';

    /** @return array{date:string,rates:array<string,float>}|null */
    public function current(): ?array
    {
        $v = Cache::get(self::CACHE_KEY);

        return is_array($v) && ! empty($v['rates']) ? $v : null;
    }

    /** TCMB'den çeker ve önbelleğe yazar. Başarısızsa eski değer korunur. */
    public function refresh(): ?array
    {
        $url = config('worksitem_bathroom.fx.source_url');
        $codes = array_keys(config('worksitem_bathroom.fx.currencies'));

        $res = Http::timeout(8)->retry(2, 500, throw: false)->get($url);
        if (! $res->successful()) {
            return null;
        }

        $parsed = $this->parse($res->body(), $codes);
        if (! $parsed) {
            return null;
        }

        Cache::put(self::CACHE_KEY, $parsed, now()->addDays(10));

        return $parsed;
    }

    /** @return array{date:string,rates:array<string,float>}|null */
    public function parse(string $xml, array $codes): ?array
    {
        $prev = libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml, \SimpleXMLElement::class, LIBXML_NONET);
        libxml_use_internal_errors($prev);
        if (! $doc) {
            return null;
        }

        $rates = [];
        foreach ($doc->Currency as $cur) {
            $code = (string) $cur['CurrencyCode'];
            if (! in_array($code, $codes, true)) {
                continue;
            }
            $unit = max(1, (int) $cur->Unit);
            $sell = (float) str_replace(',', '.', (string) $cur->ForexSelling);
            if ($sell > 0) {
                $rates[$code] = round($sell / $unit, 4);
            }
        }

        if (count($rates) !== count($codes)) {
            return null;
        }

        // Tarih="09.10.2026" (GG.AA.YYYY); yoksa Date="10/09/2026" (AA/GG/YYYY)
        $date = (string) ($doc['Tarih'] ?? '');
        if ($date === '' && preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', (string) $doc['Date'], $m)) {
            $date = "$m[2].$m[1].$m[3]";
        }

        return ['date' => $date, 'rates' => $rates];
    }
}

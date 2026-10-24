<?php

namespace App\Support;

use App\Models\TenderPoz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Poz kataloğu yardımcıları (v113):
 * - kurum ve poz grubu adresleri (/pozlar/{kurum}/{grup}) — Google'ın tarayabildiği HTML bağlantılar
 * - aynı poz numarası birden fazla kurumda olduğunda hangisinin gösterileceği
 * - sayfa başlığında kısa kurum adı
 * - poz tanımından sponsor kategorisi (yalnız Sponsorlu etiketli marka kartı için; fiyatı etkilemez)
 */
final class PozCatalog
{
    /** Aynı poz no birden çok kurumda varsa öncelik (en genel kullanılan önce) */
    private const PRIORITY = ['ÇŞB', 'ÇŞİDB/YFK', 'İLBANK İller Bankası', 'Karayolları', 'DSİ', 'MSB', 'Kültür/Vakıflar'];

    private const SHORT = [
        'Ulaştırma/Hava Meydanları' => 'DHMİ',
        'Ulaştırma/Liman' => 'Liman',
        'İLBANK İller Bankası' => 'İLBANK',
        'Kültür/Vakıflar' => 'Vakıflar',
        'ÇŞİDB/YFK' => 'YFK',
    ];

    public static function shortInstitution(?string $inst): string
    {
        $inst = trim((string) $inst);

        return self::SHORT[$inst] ?? $inst;
    }

    public static function institutionSlug(string $inst): string
    {
        return Str::slug(str_replace('/', ' ', $inst)) ?: 'kurum';
    }

    /** "15.150.1006" → "15.150"; "04.KTB.0001" → "04.KTB"; "V.1865" → "V"; noktasız → kendisi */
    public static function groupOf(string $pozNo): string
    {
        $pos = strrpos($pozNo, '.');

        return $pos === false || $pos === 0 ? $pozNo : substr($pozNo, 0, $pos);
    }

    public static function groupSlug(string $group): string
    {
        return Str::lower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', Str::ascii($group)), '-')) ?: 'grup';
    }

    public static function institutionUrl(string $inst): string
    {
        return '/pozlar/'.self::institutionSlug($inst);
    }

    public static function groupUrl(string $inst, string $group): string
    {
        return self::institutionUrl($inst).'/'.self::groupSlug($group);
    }

    /** @return array<string, string> slug => kurum adı */
    public static function institutions(): array
    {
        return Cache::remember('poz_catalog_institutions_v1', 3600, function () {
            return TenderPoz::query()->where('is_active', true)->distinct()->orderBy('institution')->pluck('institution')
                ->filter()->mapWithKeys(fn ($i) => [self::institutionSlug($i) => $i])->all();
        });
    }

    /** @return array<string, array{group:string, slug:string, count:int, sample:string}> grup slug => özet */
    public static function groups(string $inst): array
    {
        return Cache::remember('poz_catalog_groups_v1_'.md5($inst), 3600, function () use ($inst) {
            $out = [];
            TenderPoz::query()->where('is_active', true)->where('institution', $inst)
                ->select('poz_no', 'description')->orderBy('poz_no')
                ->chunk(2000, function ($rows) use (&$out) {
                    foreach ($rows as $r) {
                        $g = self::groupOf((string) $r->poz_no);
                        $s = self::groupSlug($g);
                        if (! isset($out[$s])) {
                            $out[$s] = ['group' => $g, 'slug' => $s, 'count' => 0, 'sample' => Str::limit((string) $r->description, 90)];
                        }
                        $out[$s]['count']++;
                    }
                });

            return $out;
        });
    }

    /**
     * Aynı poz numarasının aktif kayıtları, gösterim önceliğine göre (kurum önceliği, yıl, güncellik).
     *
     * @return Collection<int, TenderPoz>
     */
    public static function variants(string $pozNo): Collection
    {
        $rank = fn (TenderPoz $p) => [
            ($i = array_search($p->institution, self::PRIORITY, true)) === false ? 99 : $i,
            -((int) $p->year),
            -optional($p->updated_at)->getTimestamp(),
        ];

        return TenderPoz::query()->where('poz_no', $pozNo)->where('is_active', true)->get()
            ->sortBy($rank)->values()
            // aynı kurumun aynı poz için birden fazla kaydı varsa yalnız en güncelini tut
            ->unique('institution')->values();
    }

    /** Türkçe küçük harf (PHP mb_strtolower 'İ'yi "i̇", 'I'yı "i" yapar; poz metinleri büyük harfli olabilir) */
    public static function lower(string $s): string
    {
        return mb_strtolower(strtr($s, ['İ' => 'i', 'I' => 'ı']), 'UTF-8');
    }

    /** Poz tanımı tek başına anlamsız bir parça mı ("(40 x 40 cm) …", "3.3 mm …") */
    public static function isFragment(string $description): bool
    {
        $d = trim($description);
        $re = (string) config('worksitem_sponsors.poz_fragment', '');

        return mb_strlen($d) < 12 || ($re !== '' && preg_match($re, $d) === 1);
    }

    /**
     * Poz tanımından (ve parça ise grubun başlığından) sponsor ürün grupları.
     * Kurallar: config/worksitem_sponsors.php › poz_matching. Yalnız kart seçimi içindir; hesap ve fiyat değişmez.
     *
     * @return list<string>
     */
    public static function sponsorCategories(?string $description, ?string $context = null): array
    {
        $txt = self::lower((string) $description);
        if (trim($txt) === '') {
            return [];
        }
        if ($context !== null && trim($context) !== '') {
            $txt .= ' || '.self::lower($context);
        }
        $exclude = (string) config('worksitem_sponsors.poz_matching_exclude', '');
        if ($exclude !== '' && @preg_match($exclude, $txt) === 1) {
            return [];
        }
        $out = [];
        foreach ((array) config('worksitem_sponsors.poz_matching', []) as $key => $rule) {
            $match = is_array($rule) ? ($rule['match'] ?? null) : null;
            if (! $match || @preg_match($match, $txt) !== 1) {
                continue;
            }
            if (! empty($rule['except']) && @preg_match($rule['except'], $txt) === 1) {
                continue;
            }
            $out[] = (string) $key;
        }

        // Hiyerarşi: daha özel bir alt grup eşleştiyse genel üst grup düşer (yalitim_bitum varsa yalitim yok)
        return array_values(array_filter($out, fn ($k) => ! collect($out)->contains(fn ($o) => $o !== $k && str_starts_with($o, $k.'_'))));
    }

    /**
     * Kart sorgusu için üst grupları da ekler: 'yalitim_bitum' → ['yalitim_bitum', 'yalitim'].
     *
     * @param  list<string>  $cats
     * @return list<string>
     */
    public static function withParents(array $cats): array
    {
        $out = [];
        foreach ($cats as $c) {
            $out[] = $c;
            $parts = explode('_', $c);
            while (count($parts) > 1) {
                array_pop($parts);
                $out[] = implode('_', $parts);
            }
        }
        $known = array_keys((array) config('worksitem_sponsors.tools.poz-sayfalari.categories', []));

        return array_values(array_intersect(array_unique($out), $known));
    }

    /** Kayıtlı eşleşme (sponsors:poz-esle) varsa onu, yoksa yalnız tanımdan hesaplanan */
    public static function categoriesFor(TenderPoz $poz): array
    {
        $stored = $poz->getAttribute('sponsor_categories');
        if ($stored !== null) {
            return array_values(array_filter(explode(',', (string) $stored)));
        }

        return self::sponsorCategories((string) $poz->description);
    }

    public static function forget(): void
    {
        Cache::forget('poz_catalog_institutions_v1');
        foreach (self::institutions() as $inst) {
            Cache::forget('poz_catalog_groups_v1_'.md5($inst));
        }
    }
}

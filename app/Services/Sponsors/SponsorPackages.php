<?php

namespace App\Services\Sponsors;

/** Tematik sponsor paketleri (config/worksitem_sponsors.php › packages, pricing) */
class SponsorPackages
{
    public const ALL = 'tam-kategori';

    /** @return array<string, array{label:string, desc:string, tools:array<string, list<string>>, addon?:bool, exclusive?:bool}> */
    public static function all(): array
    {
        $out = (array) config('worksitem_sponsors.packages', []);
        $merged = [];
        foreach ($out as $p) {
            if (! empty($p['addon'])) {
                continue;   // ek paketler Tam Kategori'ye girmez
            }
            foreach ($p['tools'] as $tool => $cats) {
                $merged[$tool] = array_values(array_unique(array_merge($merged[$tool] ?? [], $cats)));
            }
        }
        $all = [self::ALL => [
            'label' => 'Tam Kategori',
            'desc' => 'Beş yapı kimyasalı paketinin bütün araç ve kalemleri, kategoride tek marka.',
            'tools' => $merged,
            'exclusive' => true,
        ]];
        // Sıra: beş aile, Tam Kategori, ek paketler
        $families = array_filter($out, fn ($p) => empty($p['addon']));
        $addons = array_filter($out, fn ($p) => ! empty($p['addon']));

        return $families + $all + $addons;
    }

    public static function get(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    /** Beş aile paketi (ek paketler ve Tam Kategori hariç) */
    public static function families(): array
    {
        return array_filter(self::all(), fn ($p, $k) => $k !== self::ALL && empty($p['addon']), ARRAY_FILTER_USE_BOTH);
    }

    /** Paketteki (araç, kategori) çifti sayısı */
    public static function slots(array $pkg): int
    {
        return array_sum(array_map('count', $pkg['tools']));
    }

    /** Pilot dönem aylık bedeli (TL, KDV hariç) */
    public static function monthly(string $key): ?int
    {
        $v = config("worksitem_sponsors.pricing.monthly.$key");

        return $v === null ? null : (int) $v;
    }

    public static function pilotMonths(): int
    {
        return (int) config('worksitem_sponsors.pricing.pilot_months', 3);
    }

    /** @return array{package:string, months:int, monthly:int} */
    public static function founding(): array
    {
        $f = (array) config('worksitem_sponsors.pricing.founding', []);

        return ['package' => (string) ($f['package'] ?? self::ALL), 'months' => (int) ($f['months'] ?? 6), 'monthly' => (int) ($f['monthly'] ?? 0)];
    }

    public static function tl(int $amount): string
    {
        return '₺'.number_format($amount, 0, ',', '.');
    }
}

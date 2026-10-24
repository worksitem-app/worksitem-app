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
        $exclusiveSlots = [];
        foreach ($out as $p) {
            if (! empty($p['addon'])) {
                continue;   // ek paketler Tam Kategori'ye girmez
            }
            foreach ($p['tools'] as $tool => $cats) {
                $merged[$tool] = array_values(array_unique(array_merge($merged[$tool] ?? [], $cats)));
                if (empty($p['shared'])) {
                    foreach ($cats as $c) {
                        $exclusiveSlots[$tool.'|'.$c] = true;
                    }
                }
            }
        }
        $all = [self::ALL => [
            'label' => 'Tam Kategori',
            'desc' => 'Beş paketin bütün araç ve kalemleri; yapı kimyasallarında kategoride tek marka, boya ve cephede kalem başına en çok 3 marka.',
            'tools' => $merged,
            'exclusive' => true,
            // v115: münhasır olan (araç|kategori) çiftleri; 'shared' paketlerin (Boya ve Cephe) kalemleri hariç
            'exclusive_slots' => array_keys($exclusiveSlots),
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

    /** Bu pakette bu kalem münhasır mı (Tam Kategori'de paylaşımlı aileler hariç; --exclusive ile açılan pakette hepsi) */
    public static function isExclusiveSlot(array $pkg, string $tool, string $cat, bool $forced = false): bool
    {
        if (isset($pkg['exclusive_slots'])) {
            return in_array($tool.'|'.$cat, $pkg['exclusive_slots'], true);
        }

        return $forced || ! empty($pkg['exclusive']);
    }

    /** Münhasır kalem sayısı */
    public static function exclusiveSlots(array $pkg): int
    {
        return isset($pkg['exclusive_slots']) ? count($pkg['exclusive_slots']) : (! empty($pkg['exclusive']) ? self::slots($pkg) : 0);
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

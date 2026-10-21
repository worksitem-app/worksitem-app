<?php

namespace App\Services\Sponsors;

/** Tematik sponsor paketleri (config/worksitem_sponsors.php › packages) */
class SponsorPackages
{
    public const ALL = 'tam-kategori';

    /** @return array<string, array{label:string, desc:string, tools:array<string, list<string>>}> */
    public static function all(): array
    {
        $out = (array) config('worksitem_sponsors.packages', []);
        $merged = [];
        foreach ($out as $p) {
            foreach ($p['tools'] as $tool => $cats) {
                $merged[$tool] = array_values(array_unique(array_merge($merged[$tool] ?? [], $cats)));
            }
        }
        $out[self::ALL] = [
            'label' => 'Tam Kategori',
            'desc' => 'Yapı kimyasalları kategorisinde tüm paketlerin araçları, tek marka.',
            'tools' => $merged,
        ];

        return $out;
    }

    public static function get(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    /** Paketteki (araç, kategori) çifti sayısı */
    public static function slots(array $pkg): int
    {
        return array_sum(array_map('count', $pkg['tools']));
    }
}

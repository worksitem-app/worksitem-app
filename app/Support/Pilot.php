<?php

namespace App\Support;

/** Pilot dönem kuralları: hizmet bölgesi ve firma tarafının açık/kapalı olması (config/worksitem_pilot.php) */
class Pilot
{
    public static function enabled(): bool
    {
        return (bool) config('worksitem_pilot.enabled', false);
    }

    /** Firma üyeliği (kayıt, kod, öne çıkan üyelik) açık mı */
    public static function firmSignupOpen(): bool
    {
        return ! self::enabled() || (bool) config('worksitem_pilot.firm_signup', false);
    }

    /** Kullanıcının kayıtlı firmalardan teklif alması açık mı */
    public static function marketOpen(): bool
    {
        return (bool) config('worksitem_market.enabled') && ! self::enabled();
    }

    /** Firma rehberi açık mı */
    public static function directoryOpen(): bool
    {
        return (bool) config('worksitem_firms.directory_enabled') && ! self::enabled();
    }

    public static function areaLabel(): string
    {
        return (string) config('worksitem_pilot.area_label', 'İstanbul');
    }

    public static function provider(): string
    {
        return (string) (config('worksitem_company.legal_name') ?: config('worksitem_pilot.provider', 'Kainta İnşaat'));
    }

    /**
     * Konum pilot bölgede mi? true: içinde · false: dışında · null: bilinmiyor.
     * Pilot kapalıysa her yer hizmet bölgesidir.
     * v111: Yalnız il adı değil, pilot ilin ilçe adları da tanınır ("Kadıköy" → İstanbul).
     * "Dışında" ancak başka bir il ya da başka ilin ilçesi açıkça tanınırsa denir; tanınmayan metin
     * bilinmiyor (null) sayılır — İstanbul'daki müşteri yanlışlıkla geri çevrilmesin.
     */
    public static function inArea(?string ...$parts): ?bool
    {
        if (! self::enabled()) {
            return true;
        }
        $text = ' '.preg_replace('/[^a-z0-9]+/', ' ', self::norm(implode(' ', array_filter(array_map(fn ($p) => trim((string) $p), $parts))))).' ';
        if (trim($text) === '') {
            return null;
        }
        $has = fn (string $name) => str_contains($text, ' '.trim(preg_replace('/[^a-z0-9]+/', ' ', self::norm($name))).' ');
        $districts = (array) config('turkey_districts', []);
        $pilot = (array) config('worksitem_pilot.cities', []);

        foreach ($pilot as $city) {
            if ($has((string) $city)) {
                return true;
            }
        }
        foreach ($pilot as $city) {
            foreach ((array) ($districts[$city] ?? []) as $d) {
                if ($has((string) $d)) {
                    return true;
                }
            }
        }
        foreach ($districts as $province => $list) {
            if (in_array($province, $pilot, true)) {
                continue;
            }
            if ($has((string) $province)) {
                return false;
            }
        }

        return null;
    }

    private static function norm(string $s): string
    {
        $s = strtr($s, ['İ' => 'i', 'I' => 'ı', 'Ş' => 'ş', 'Ğ' => 'ğ', 'Ü' => 'ü', 'Ö' => 'ö', 'Ç' => 'ç']);
        $s = mb_strtolower($s, 'UTF-8');

        return strtr($s, ['ı' => 'i', 'ş' => 's', 'ğ' => 'g', 'ü' => 'u', 'ö' => 'o', 'ç' => 'c', 'â' => 'a', 'î' => 'i', 'û' => 'u']);
    }
}

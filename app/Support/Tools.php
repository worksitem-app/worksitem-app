<?php

namespace App\Support;

/**
 * Araç yayın durumu ve sayısı için tek kaynak. Sitede yazan "N araç" her yerde buradan gelir
 * (önce üç yerde üç farklı sayı yazıyordu: 70+, 74, 76).
 */
class Tools
{
    /** Ziyaretçi bu aracı kullanabilir mi (staging'de QA için hepsi açık) */
    public static function isActive(string $slug): bool
    {
        if (app()->environment('staging')) {
            return true;
        }
        $st = (array) config('worksitem_tools.tool_status', []);

        return (((array) ($st['overrides'] ?? []))[$slug] ?? ($st['default'] ?? 'review')) === 'active';
    }

    /** Yayındaki araç sayısı (yönlendirme takma adları hariç) */
    public static function activeCount(): int
    {
        return collect(array_keys((array) config('worksitem_tools.tools', [])))
            ->reject(fn ($s) => $s === 'havalandirma-kanali')
            ->filter(fn ($s) => self::isActive($s))
            ->count();
    }

    /**
     * Dosyadan AI ön keşif ziyaretçiye gerçekten açık mı: AI harcama kilidi açık VE araç yayında.
     * Kapalıyken ana sayfa ve alt bilgi bu özelliği vaat etmez (v110).
     */
    public static function aiFileOn(): bool
    {
        return (bool) config('services.ai_spend_enabled') && self::isActive('otomatik-kesif-fiyatlandirma');
    }

    /** Metinde kullanılacak sayı: yayındaki araç 10'dan azsa (yerel / test kurulum) "Onlarca" */
    public static function label(): string
    {
        $n = self::activeCount();

        return $n >= 10 ? (string) $n : 'Onlarca';
    }
}

<?php

namespace App\Support;

/**
 * Şirket künyesinin herkese açık sayfalarda gösterimi.
 *
 * Kimlik avı / sahtecilik riskine karşı MERSİS, vergi dairesi/no ve KEP gibi
 * kimlik numaraları yayındaki sayfalarda "*****" olarak gösterilir ve
 * "talep edilmesi halinde verilecektir" notu eklenir. Gerçek siparişe ait
 * sözleşme/ön bilgilendirme belgesinde (alıcıya özel) tam bilgi kullanılır.
 * Kapatmak için .env: COMPANY_MASK_IDS=false
 */
class CompanyIdentity
{
    public const MASKED_KEYS = ['mersis', 'tax_office', 'tax_no', 'kep', 'tax_line'];

    public static function masked(): bool
    {
        return (bool) config('worksitem_company.mask_public', true);
    }

    public static function mask(): string
    {
        return '***** ('.config('worksitem_company.mask_note', 'talep edilmesi halinde verilecektir').')';
    }

    /** Şirket bilgileri; $full=false ise kimlik numaraları maskelenir. */
    public static function display(bool $full = false): array
    {
        $co = (array) config('worksitem_company');
        $co['tax_line'] = trim(($co['tax_office'] ?? '').' / '.($co['tax_no'] ?? ''), ' /') ?: null;

        if (! $full && self::masked()) {
            foreach (self::MASKED_KEYS as $k) {
                $co[$k] = self::mask();
            }
        }

        return $co;
    }
}

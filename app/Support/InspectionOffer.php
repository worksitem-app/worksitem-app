<?php

namespace App\Support;

/**
 * Yerinde keşif bedeli bilgileri (tek kaynak: config/worksitem_bathroom.php + worksitem_inspection.php).
 */
class InspectionOffer
{
    public static function fee(): int
    {
        return (int) config('worksitem_bathroom.inspection_fee', 2500);
    }

    public static function vatIncluded(): bool
    {
        return (bool) config('worksitem_bathroom.inspection_vat_included', false);
    }

    public static function creditDays(): int
    {
        return (int) config('worksitem_bathroom.inspection_credit_days', 15);
    }

    /** "2.500 TL + KDV" */
    public static function feeLabel(): string
    {
        return number_format(self::fee(), 0, ',', '.').' TL '.(self::vatIncluded() ? '(KDV dahil)' : '+ KDV');
    }

    /** Metindeki {kesif_bedeli} ve {kesif_gun} yer tutucularını doldurur. */
    public static function fill(string $text): string
    {
        return strtr($text, [
            '{kesif_bedeli}' => self::feeLabel(),
            '{kesif_gun}' => (string) self::creditDays(),
        ]);
    }

    public static function vatRate(): int
    {
        return (int) config('worksitem_inspection.vat_rate', 20);
    }

    /** @return array{net:int, vat:int, total:int} */
    public static function amounts(): array
    {
        $net = self::fee();
        if (self::vatIncluded()) {
            $vat = (int) round($net - $net / (1 + self::vatRate() / 100));

            return ['net' => $net - $vat, 'vat' => $vat, 'total' => $net];
        }
        $vat = (int) round($net * self::vatRate() / 100);

        return ['net' => $net, 'vat' => $vat, 'total' => $net + $vat];
    }

    public static function tl(int|float $v): string
    {
        return number_format((float) $v, 0, ',', '.').' TL';
    }

    /**
     * Sözleşmelere yazılacak sipariş/alıcı bilgileri. Lead yoksa boş (şablon) döner.
     *
     * @return array<string, mixed>
     */
    public static function orderData(?\App\Models\Lead $lead): array
    {
        $data = (array) ($lead?->inspection_data ?? []);
        $methods = (array) config('worksitem_inspection.payment_methods', []);
        $method = $lead?->inspection_payment_method;

        return [
            'filled' => (bool) $lead,
            'name' => $lead?->name,
            'phone' => $lead?->phone,
            'email' => $lead?->email,
            'place' => $lead ? collect([$lead->city, $lead->district])->filter()->implode(' / ') : null,
            'project' => $lead?->project_type,
            'order_no' => $lead?->public_id,
            'date' => ($lead?->inspection_accepted_at ?? ($lead ? now() : null))?->format('d.m.Y H:i'),
            'method' => $method ? ($methods[$method]['label'] ?? $method) : null,
            'invoice' => $data['invoice'] ?? null,
            'accepted' => (bool) $lead?->inspection_accepted_at,
        ];
    }

    /**
     * Keşif sözleşmesi şablonlarının ortak değişkenleri (legal/kesif/_obf, _mhs, PDF).
     *
     * @return array<string, mixed>
     */
    public static function docVars(?\App\Models\Lead $lead): array
    {
        // Alıcıya özel belge (siparişli) tam künye; herkese açık sayfa maskeli
        $co = CompanyIdentity::display(full: $lead !== null);
        $o = self::orderData($lead);

        return [
            'co' => $co,
            'ic' => (array) config('worksitem_inspection'),
            'o' => $o,
            'a' => self::amounts(),
            'tl' => fn ($v) => self::tl($v),
            'vatRate' => self::vatRate(),
            'days' => self::creditDays(),
            'f' => fn ($v, string $ph) => filled($v)
                ? '<span class="lg-filled">'.e($v).'</span>'
                : '<span class="lg-field">'.e($ph).'</span>',
            'seller' => array_filter([
                'Ticari unvan' => $co['legal_name'] ?? null,
                'MERSİS no' => $co['mersis'] ?? null,
                'Vergi dairesi / no' => $co['tax_line'] ?? null,
                'Adres' => $co['address'] ?? null,
                'Telefon' => $co['phone'] ?? null,
                'E-posta' => $co['email'] ?? null,
                'KEP adresi' => $co['kep'] ?? null,
                'Satış platformu' => ($co['web'] ?? 'worksitem.com').' (Kainta İnşaat tarafından işletilir)',
            ]),
            'inv' => $o['invoice'] ?? null,
        ];
    }

    /** Sipariş (ödeme) adımındaki onay kutusu metinleri — sözleşme Ek-2 ile birebir aynı. */
    public static function consentTexts(): array
    {
        return [
            'kesif_onay' => "Zorunlu: Keşif Ön Bilgilendirme Formu'nu ve Keşif Mesafeli Hizmet Sözleşmesi'ni okudum, anladım ve kabul ediyorum.",
            'kesif_erken' => 'İsteğe bağlı: Keşfin 14 günlük cayma süresi dolmadan yapılmasını talep ediyorum. Keşif yerinde yapıldıktan sonra cayma hakkımı kaybedeceğimi biliyorum. (İşaretlemezseniz keşif, 14 günlük süre dolduktan sonra yapılır.)',
        ];
    }

    public static function whatsappUrl(string $message): string
    {
        $number = preg_replace('/\D+/', '', (string) config('worksitem_inspection.whatsapp'));

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }
}

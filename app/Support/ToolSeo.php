<?php

namespace App\Support;

/**
 * Araç sayfaları için arama sonucu metinleri.
 *  - Başlık Google'da kesilmesin: 60 karakteri aşarsa "| Worksitem" eki düşer, "(YFK)" kısaltması çıkar.
 *  - Açıklama ~158 karakterde cümle/kelime sınırından kısalır.
 *  - SSS: fiyatı hesaplanamamış ("yaklaşık  tutar") cevaplar yayınlanmaz.
 *  - Kısa cevap: fiyat sorusunun cevabı sayfa başında gösterilir (öne çıkan snippet + yapay zekâ aramaları).
 */
class ToolSeo
{
    public const BRAND = 'Worksitem';

    public static function title(string $t, int $max = 60): string
    {
        $t = trim((string) preg_replace('/\s*\(YFK\)/u', '', $t));
        $core = trim((string) preg_replace('/\s*\|\s*(Worksitem|WORKSİTEM)\s*$/u', '', $t));
        $with = $core.' | '.self::BRAND;

        return mb_strlen($with) <= $max ? $with : $core;
    }

    public static function desc(string $d, int $max = 158): string
    {
        $d = trim((string) preg_replace('/\s+/u', ' ', $d));
        if (mb_strlen($d) <= $max) {
            return $d;
        }
        $cut = mb_substr($d, 0, $max);
        $dot = mb_strrpos($cut, '. ');
        if ($dot !== false && $dot > $max * 0.6) {
            return mb_substr($cut, 0, $dot + 1);
        }

        return rtrim(mb_substr($cut, 0, (int) mb_strrpos($cut, ' ')), ' ,;:–-').'…';
    }

    /** @param list<array{0:string,1:string}> $faq */
    public static function faq(array $faq): array
    {
        return array_values(array_filter($faq, fn ($f) => ! preg_match('/yaklaşık\s+(tutar|arası|TL\b)|yaklaşık\s{2,}|\s–\s*TL/u', (string) ($f[1] ?? ''))));
    }

    /** İlk fiyat sorusu ve cevabı (yoksa null). */
    public static function quickAnswer(array $faq): ?array
    {
        foreach ($faq as $f) {
            if (preg_match('/kaç TL|fiyat|ne kadar|maliyet|kaç metre|kaç m³|kaç BTU/iu', (string) $f[0]) && preg_match('/\d/', (string) $f[1])) {
                return [$f[0], $f[1]];
            }
        }

        return null;
    }
}

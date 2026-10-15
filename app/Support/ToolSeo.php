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

    /**
     * Arama sonucu açıklaması (yüksek tıklama oranı için): "Soru? Yaklaşık fiyat. Ücretsiz hesapla."
     * Fiyat, aracın "Kısa cevap"ından gelir (fiyat robotu güncelledikçe kendiliğinden değişir). Fiyat bulunamazsa null.
     */
    public static function priceSnippet(array $faq, string $tail = 'Kendi ölçünüzle ücretsiz hesaplayın, dökümü PDF alın.'): ?string
    {
        $qa = self::quickAnswer(self::faq($faq));
        if (! $qa || ! preg_match('/(kaç TL|fiyat|ne kadar|maliyet)/iu', $qa[0])) {
            return null;
        }
        if (! preg_match('/((?:m²|m³|metre) başına\s+)?yaklaşık\s+[\d.,]+(?:\s*[–-]\s*[\d.,]+)?\s*(?:bin\s*)?TL(?:\s*\/\s*m²)?/u', $qa[1], $m, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        // Yanıltmasın: kısmi kapsam ("yalnız işçilik"), iki uçlu ifade ("X TL ile Y TL") veya birim uyuşmazlığı varsa otomatik yazılmaz
        $before = mb_substr(substr($qa[1], 0, $m[0][1]), -40);
        $after = substr($qa[1], $m[0][1] + strlen($m[0][0]), 12);
        $unitQ = (bool) preg_match('/m²\s*fiyat|^1 m²/u', $qa[0]);   // birim fiyat soruluyor
        $unitP = (bool) preg_match('/m²/u', $m[0][0]);
        if (str_contains(mb_strtolower($before), 'yalnız') || preg_match('/^\s*(\(|ile)/u', $after) || ($unitQ && ! $unitP)) {
            return null;
        }
        $m[0] = $m[0][0];
        $year = (string) now()->year;
        $price = trim($m[0]);
        $q = rtrim(trim($qa[0]), '?').'?';
        foreach ([$q.' '.$year.' fiyatlarıyla '.$price.'. '.$tail, $q.' '.$year.' fiyatlarıyla '.$price.'. Ücretsiz hesaplayın.', $q.' '.$year.': '.$price.'.'] as $d) {
            if (mb_strlen($d) <= 155) {
                return $d;
            }
        }

        return null;
    }
}

<?php

namespace App\Support;

/** Teklif formlarındaki serbest metinde uygunsuz kelime kontrolü */
class LeadTextGuard
{
    public static function isOffensive(?string $text): bool
    {
        $t = self::normalize((string) $text);
        if ($t === '') {
            return false;
        }

        foreach (config('worksitem_lead_guard.stems', []) as $stem) {
            if (str_contains($t, self::normalize($stem))) {
                return true;
            }
        }

        $words = preg_split('/[^\p{L}\p{N}]+/u', $t, -1, PREG_SPLIT_NO_EMPTY);
        $blocked = array_map([self::class, 'normalize'], config('worksitem_lead_guard.words', []));

        return (bool) array_intersect($words, $blocked);
    }

    private const VOWELS = 'aeıioöuüâîû';

    /**
     * Anlamsız / sahte metin: "lkjnhbv", "fkfkdfgf", "asdasd", "qwer", "aaaa", "test".
     * Aynı kurallar istemci tarafında (lead-consent-fields) birebir uygulanır.
     */
    public static function looksFake(?string $text): bool
    {
        $words = self::words($text);
        if ($words === []) {
            return false;
        }

        $fake = array_map([self::class, 'normalize'], config('worksitem_lead_guard.fake_words', []));
        $rows = array_map([self::class, 'normalize'], config('worksitem_lead_guard.keyboard_rows', []));
        $v = self::VOWELS;

        foreach ($words as $w) {
            $len = mb_strlen($w);
            if (in_array($w, $fake, true)) {
                return true;
            }
            if ($len < 3) {
                continue;
            }
            if (! preg_match('/['.$v.']/u', $w)                       // hiç sesli harf yok: lkjnhbv
                || preg_match('/[^'.$v.']{5,}/u', $w)                 // 5+ ünsüz art arda: asdfgh
                || preg_match('/(.)\1\1/u', $w)                      // aynı harf 3 kez: aaa
                || ($len >= 6 && preg_match('/^(.{2,3})\1+$/u', $w)) // tekrar: asdasd, kdkdkd
                || ($len >= 8 && preg_match_all('/['.$v.']/u', $w) / $len < 0.15)) {
                return true;
            }
            foreach ($rows as $row) {
                for ($i = 0, $n = mb_strlen($row) - 3; $i < $n; $i++) {
                    if (str_contains($w, mb_substr($row, $i, 4))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /** Ad soyad: en az iki kelime (her biri en az 2 harf), rakam yok, anlamsız/uygunsuz değil. */
    public static function isValidFullName(?string $text): bool
    {
        $text = (string) $text;
        if (preg_match('/\d/', $text)) {
            return false;
        }
        $words = array_filter(self::words($text), fn ($w) => mb_strlen($w) >= 2);

        return count($words) >= 2 && ! self::looksFake($text) && ! self::isOffensive($text);
    }

    /** @return list<string> */
    private static function words(?string $text): array
    {
        return array_values(preg_split('/[^\p{L}]+/u', self::normalize((string) $text), -1, PREG_SPLIT_NO_EMPTY));
    }

    public static function normalize(string $s): string
    {
        $s = str_replace(['İ', 'I'], ['i', 'ı'], $s);

        return trim(mb_strtolower($s, 'UTF-8'));
    }
}

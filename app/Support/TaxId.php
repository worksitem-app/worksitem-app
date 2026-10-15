<?php

namespace App\Support;

/**
 * Vergi kimlik numarası (10 hane, tüzel kişi / şirket) ve TC kimlik numarası (11 hane, şahıs işletmesi)
 * kontrol hanesi doğrulaması. GİB'in herkese açık bir sorgu servisi olmadığından bu yalnızca biçim/hane
 * kontrolüdür; mükellefiyet, vergi levhası ve uzman kontrolüyle doğrulanır.
 */
class TaxId
{
    public static function valid(?string $no): bool
    {
        $no = preg_replace('/\D+/', '', (string) $no);

        return strlen($no) === 10 ? self::vkn($no) : (strlen($no) === 11 && self::tckn($no));
    }

    public static function vkn(string $v): bool
    {
        if (! preg_match('/^\d{10}$/', $v)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $t = ((int) $v[$i] + 9 - $i) % 10;
            $p = ($t * (2 ** (9 - $i))) % 9;
            if ($t !== 0 && $p === 0) {
                $p = 9;
            }
            $sum += $p;
        }

        return (10 - ($sum % 10)) % 10 === (int) $v[9];
    }

    public static function tckn(string $t): bool
    {
        if (! preg_match('/^[1-9]\d{10}$/', $t)) {
            return false;
        }
        $d = array_map('intval', str_split($t));
        $odd = $d[0] + $d[2] + $d[4] + $d[6] + $d[8];
        $even = $d[1] + $d[3] + $d[5] + $d[7];
        $d10 = ((($odd * 7) - $even) % 10 + 10) % 10;

        return $d10 === $d[9] && (array_sum(array_slice($d, 0, 10)) % 10) === $d[10];
    }

    public static function mask(?string $no): string
    {
        $no = (string) $no;

        return strlen($no) >= 4 ? str_repeat('•', strlen($no) - 4).substr($no, -4) : '—';
    }
}

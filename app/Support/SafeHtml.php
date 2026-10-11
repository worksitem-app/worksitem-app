<?php

namespace App\Support;

/**
 * LLM / kullanıcı kaynaklı HTML'i {!! !!} ile basmadan önce temizler.
 *
 * - Yalnızca izinli etiketler kalır
 * - Tüm attribute'lar silinir (onclick, style, href=javascript: vb.)
 * - <script>/<style> içerikleri tamamen atılır
 */
final class SafeHtml
{
    private const ALLOWED = ['p', 'br', 'strong', 'b', 'em', 'i', 'ul', 'ol', 'li', 'h3', 'h4'];

    public static function clean(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        // Script/style bloklarını içerikleriyle birlikte at
        $html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1\s*>#is', '', $html) ?? '';

        $allowed = '<'.implode('><', self::ALLOWED).'>';
        $html = strip_tags($html, $allowed);

        // Kalan izinli etiketlerden tüm attribute'ları sök: <li onclick="..."> → <li>
        $html = preg_replace('#<(/?)([a-z0-9]+)\b[^>]*?(/?)>#i', '<$1$2$3>', $html) ?? '';

        return $html;
    }
}

<?php

namespace App\Support;

/**
 * Önbellek kırıcı statik dosya adresi: /js/x.js?v=<içerik özeti>.
 * Dosya değişince adres de değişir; tarayıcı ve sunucu önbelleği eski JS/CSS'i vermez.
 * (Elle yazılan ?v=tarih unutulunca "paylaş / PDF çalışmıyor" hatası oluşuyordu.)
 */
class Asset
{
    /** @var array<string, string> */
    private static array $cache = [];

    public static function url(string $path): string
    {
        $path = '/'.ltrim($path, '/');

        if (! isset(self::$cache[$path])) {
            $file = public_path(ltrim($path, '/'));
            self::$cache[$path] = is_file($file) ? substr(md5_file($file), 0, 10) : 'x';
        }

        return $path.'?v='.self::$cache[$path];
    }

    /**
     * Dosyanın içeriğini sayfaya gömmek için (<style>/<script> içinde).
     * Web sunucusunun /css, /js klasörünü nereden sunduğundan bağımsız çalışır:
     * banyo sayfası gibi stil her zaman sayfayla birlikte gelir.
     */
    public static function inline(string $path): string
    {
        $file = public_path(ltrim($path, '/'));
        $s = is_file($file) ? (string) file_get_contents($file) : '';

        // Güvenlik: gömülü içerik etiketi erken kapatamasın
        return str_ireplace(['</style', '</script'], ['<\/style', '<\/script'], $s);
    }
}

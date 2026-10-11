<?php
namespace App\Support;
final class PozUrl {
public static function path(string $no): string {
return "/poz/" . implode("/", array_map("rawurlencode", explode("/", $no)));
}
public static function absolute(string $no): string {
return "https://worksitem.com" . self::path($no);
}
}

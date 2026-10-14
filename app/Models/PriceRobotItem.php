<?php

namespace App\Models;

use App\Services\Pricing\PriceRobotOverrides;
use Illuminate\Database\Eloquent\Model;

class PriceRobotItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'default_value' => 'array', 'value' => 'array', 'previous_value' => 'array', 'proposed_value' => 'array',
            'sources' => 'array', 'locked' => 'boolean', 'web_median' => 'float', 'multiplier' => 'float',
            'researched_at' => 'datetime', 'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => PriceRobotOverrides::flush());
        static::deleted(fn () => PriceRobotOverrides::flush());
    }

    /** Dizi (ekonomik/orta/premium) ise orta değer, değilse kendisi */
    public static function mid(mixed $v): ?float
    {
        if (is_array($v)) {
            $vals = array_values($v);

            return isset($vals[1]) ? (float) $vals[1] : (isset($vals[0]) ? (float) $vals[0] : null);
        }

        return is_numeric($v) ? (float) $v : null;
    }

    public static function fmt(mixed $v): string
    {
        $f = fn ($x) => number_format((float) $x, 0, ',', '.');

        return is_array($v) ? implode(' / ', array_map($f, $v)) : ($v === null ? '—' : $f($v));
    }
}

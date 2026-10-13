<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Araç yayın durumu (admin › Sistem Yönetimi › Araç Yayın Durumu).
 * Kayıt varsa config/worksitem_tools.php içindeki varsayılanı ezer.
 */
class ToolStatus extends Model
{
    public const CACHE_KEY = 'tool_statuses:overrides';

    protected $fillable = ['slug', 'active', 'note', 'updated_by'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** @return array<string, string> slug => active|review */
    public static function overrides(): array
    {
        try {
            $cached = Cache::get(self::CACHE_KEY);
            if (is_array($cached)) {
                return $cached;
            }
            if (! Schema::hasTable('tool_statuses')) {
                return [];   // tablo yokken önbelleğe yazma (migration sonrası hemen devreye girsin)
            }
            $map = static::query()->pluck('active', 'slug')->map(fn ($a) => $a ? 'active' : 'review')->all();
            Cache::forever(self::CACHE_KEY, $map);

            return $map;
        } catch (\Throwable) {
            return [];
        }
    }

    public static function applyToConfig(): void
    {
        $db = static::overrides();
        if ($db) {
            config(['worksitem_tools.tool_status.overrides' => array_merge((array) config('worksitem_tools.tool_status.overrides', []), $db)]);
        }
    }

    /** Config'teki tüm araçlar için kayıt yoksa mevcut (etkin) durumla oluşturur */
    public static function syncFromConfig(): void
    {
        $cfg = (array) config('worksitem_tools.tool_status', []);
        $default = $cfg['default'] ?? 'review';
        $overrides = (array) ($cfg['overrides'] ?? []);
        $existing = static::query()->pluck('slug')->all();
        foreach (array_keys((array) config('worksitem_tools.tools', [])) as $slug) {
            if (! in_array($slug, $existing, true)) {
                static::query()->create(['slug' => $slug, 'active' => ($overrides[$slug] ?? $default) === 'active']);
            }
        }
    }

    public function title(): string
    {
        return (string) (config('worksitem_tools.tools.'.$this->slug.'.title') ?? $this->slug);
    }

    public function category(): string
    {
        return (string) (config('worksitem_tools.tools.'.$this->slug.'.cat') ?? '');
    }
}

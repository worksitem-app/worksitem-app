<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Planlayıcıda seçilebilen sponsor seramiği (tek karo dokusu + gerçek ebat) */
class PlannerTile extends Model
{
    protected $fillable = [
        'name', 'brand', 'series', 'sku', 'sponsor_id', 'width_cm', 'height_cm', 'finish', 'usage',
        'image_path', 'image_url', 'product_url', 'price_m2_min', 'price_m2_max', 'active', 'priority',
    ];

    protected $casts = ['active' => 'boolean'];

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function textureUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : $this->image_url;
    }

    /** Sunucu tarafı render için yerel dosya yolu (yalnızca yüklenen görsel) */
    public function localPath(): ?string
    {
        return $this->image_path && Storage::disk('public')->exists($this->image_path) ? Storage::disk('public')->path($this->image_path) : null;
    }

    /** @return array<string, array> 'db-ID' => planlayıcı karo kaydı (görseli olanlar) */
    public static function forPlanner(): array
    {
        try {
            if (! Schema::hasTable('planner_tiles')) {
                return [];
            }
            $rows = static::query()->where('active', true)->orderBy('priority')->orderBy('id')->get()->filter(fn ($t) => $t->textureUrl());
        } catch (\Throwable) {
            return [];
        }
        if ($rows->isNotEmpty()) {
            static::query()->whereKey($rows->pluck('id'))->increment('impressions');
        }

        return $rows->mapWithKeys(fn (self $t) => ['db-'.$t->id => [
            'name' => trim($t->brand.' '.$t->name),
            'style' => 'Sponsorlu · '.$t->brand,
            'size' => $t->width_cm.'x'.$t->height_cm,
            'tw' => (int) $t->width_cm, 'th' => (int) $t->height_cm,
            'url' => $t->textureUrl(),
            'brand' => $t->brand,
            'finish' => $t->finish,
            'usage' => $t->usage,
            'link' => $t->product_url ? route('planner.tile', $t->id) : null,
            'price' => $t->price_m2_min ? [(int) $t->price_m2_min, (int) ($t->price_m2_max ?: $t->price_m2_min)] : null,
        ]])->all();
    }

    public function trackedUrl(): string
    {
        $utm = array_merge((array) config('worksitem_sponsors.utm'), ['utm_campaign' => 'banyo-planlayici', 'utm_content' => 'seramik']);
        $sep = str_contains((string) $this->product_url, '?') ? '&' : '?';

        return $this->product_url.$sep.http_build_query($utm);
    }
}

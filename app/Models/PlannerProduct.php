<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Banyo planlayıcıda seçilebilen sponsor ürünü */
class PlannerProduct extends Model
{
    protected $fillable = [
        'type', 'name', 'brand', 'sku', 'sponsor_id', 'image_path', 'model_path', 'width', 'depth', 'height', 'width_min', 'width_max', 'depth_min', 'depth_max',
        'color', 'cabinet_color', 'tank', 'image_url', 'product_url', 'price_min', 'price_max', 'active', 'priority',
    ];

    protected $casts = ['active' => 'boolean', 'tank' => 'boolean'];

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function scopeLive(Builder $q): Builder
    {
        return $q->where('active', true);
    }

    /** Planlayıcının beklediği biçim (config ürünleriyle aynı alanlar) */
    public function toPlanner(): array
    {
        $w = (int) $this->width;
        $d = (int) $this->depth;

        return array_filter([
            'id' => 'db-'.$this->id,
            'name' => $this->name,
            'w' => $w, 'd' => $d, 'h' => (int) $this->height,
            'wr' => [(int) ($this->width_min ?: $w), (int) max($this->width_max ?: $w, $w)],
            'dr' => [(int) ($this->depth_min ?: $d), (int) max($this->depth_max ?: $d, $d)],
            'color' => $this->color ?: '#FFFFFF',
            'cabinet' => $this->cabinet_color ?: false,
            'tank' => (bool) $this->tank,
            'sponsor' => $this->brand,
            'image' => $this->image_path ? Storage::disk('public')->url($this->image_path) : $this->image_url,
            'model' => $this->model_path ? Storage::disk('public')->url($this->model_path) : null,
            'link' => $this->product_url ? route('planner.product', $this->id) : null,
            'price' => $this->price_min ? [(int) $this->price_min, (int) ($this->price_max ?: $this->price_min)] : null,
        ], fn ($v) => $v !== null);
    }

    /** @return array<string, array<int, array>> tür → ürünler (sponsorlu önce) */
    public static function forPlanner(): array
    {
        try {
            if (! Schema::hasTable('planner_products')) {
                return [];
            }
            $rows = static::query()->live()->orderBy('priority')->orderBy('id')->get();
        } catch (\Throwable) {
            return [];
        }
        if ($rows->isNotEmpty()) {
            static::query()->whereKey($rows->pluck('id'))->increment('impressions');
        }

        return $rows->groupBy('type')->map(fn ($g) => $g->map->toPlanner()->values()->all())->all();
    }

    public function trackedUrl(): string
    {
        $utm = array_merge((array) config('worksitem_sponsors.utm'), ['utm_campaign' => 'banyo-planlayici', 'utm_content' => $this->type]);
        $sep = str_contains((string) $this->product_url, '?') ? '&' : '?';

        return $this->product_url.$sep.http_build_query($utm);
    }
}

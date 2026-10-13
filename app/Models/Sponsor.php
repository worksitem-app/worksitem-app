<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
        'brand', 'headline', 'body', 'cta_label', 'target_url', 'logo_url', 'tool_slug', 'category',
        'priority', 'active', 'starts_on', 'ends_on', 'contract_amount', 'contact', 'notes',
    ];

    protected $casts = [
        'active' => 'boolean',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'contract_amount' => 'decimal:2',
    ];

    /** Bugün yayında olanlar */
    public function scopeLive(Builder $q): Builder
    {
        $today = now('Europe/Istanbul')->toDateString();

        return $q->where('active', true)
            ->where(fn ($w) => $w->whereNull('starts_on')->orWhere('starts_on', '<=', $today))
            ->where(fn ($w) => $w->whereNull('ends_on')->orWhere('ends_on', '>=', $today));
    }

    public function ctr(): float
    {
        return $this->impressions > 0 ? round($this->clicks / $this->impressions * 100, 2) : 0.0;
    }

    /** Takip parametreli hedef adres */
    public function trackedUrl(): string
    {
        $utm = array_merge((array) config('worksitem_sponsors.utm'), ['utm_campaign' => $this->tool_slug, 'utm_content' => $this->category]);
        $sep = str_contains($this->target_url, '?') ? '&' : '?';

        return $this->target_url.$sep.http_build_query($utm);
    }
}

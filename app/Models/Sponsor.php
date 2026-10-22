<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
        'brand', 'headline', 'body', 'cta_label', 'target_url', 'logo_url', 'tech_sheet_url', 'tech_sheet_checked_on', 'tool_slug', 'category',
        'priority', 'active', 'starts_on', 'ends_on', 'contract_amount', 'contact', 'notes',
    ];

    protected $casts = [
        'active' => 'boolean',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'tech_sheet_checked_on' => 'date',
        'contract_amount' => 'decimal:2',
    ];

    /** Staging demo kayıtlarının işareti (sponsors:demo); canlı ortamda hiçbir zaman gösterilmez */
    public const DEMO_MARK = '[DEMO]';

    /** Münhasır (Tam Kategori) kayıtların işareti: aynı araç ve kategoride başka marka gösterilmez */
    public const EXCLUSIVE_MARK = '[TEK MARKA]';

    public function isDemo(): bool
    {
        return str_contains((string) $this->notes, self::DEMO_MARK);
    }

    public function isExclusive(): bool
    {
        return str_contains((string) $this->notes, self::EXCLUSIVE_MARK);
    }

    /**
     * Münhasırlık: bir (araç, kategori) çiftinde yayında münhasır kayıt varsa o çiftte yalnız münhasır kayıtlar kalır.
     *
     * @param  \Illuminate\Support\Collection<int, self>  $rows
     * @return \Illuminate\Support\Collection<int, self>
     */
    public static function applyExclusive(\Illuminate\Support\Collection $rows): \Illuminate\Support\Collection
    {
        $locked = $rows->filter(fn (self $s) => $s->isExclusive())->map(fn (self $s) => $s->tool_slug.'|'.$s->category)->unique()->all();
        if (! $locked) {
            return $rows;
        }

        return $rows->filter(fn (self $s) => $s->isExclusive() || ! in_array($s->tool_slug.'|'.$s->category, $locked, true))->values();
    }

    /** Bugün yayında olanlar */
    public function scopeLive(Builder $q): Builder
    {
        $today = now('Europe/Istanbul')->toDateString();
        if (app()->environment('production')) {
            $q->where(fn ($w) => $w->whereNull('notes')->orWhere('notes', 'not like', '%'.self::DEMO_MARK.'%'));
        }

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

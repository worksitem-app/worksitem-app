<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Firm extends Model
{
    protected $fillable = [
        'name', 'type', 'services', 'cities', 'district', 'contact_name', 'phone', 'email', 'website', 'tax_no',
        'founded_year', 'description', 'status', 'plan', 'plan_until', 'admin_note', 'approved_at', 'terms_at',
    ];

    protected $casts = [
        'services' => 'array', 'cities' => 'array', 'plan_until' => 'datetime', 'approved_at' => 'datetime', 'terms_at' => 'datetime',
    ];

    public const STATUSES = ['pending' => 'Onay bekliyor', 'approved' => 'Onaylı', 'rejected' => 'Reddedildi', 'paused' => 'Durduruldu'];

    protected static function booted(): void
    {
        static::creating(function (Firm $f) {
            $f->public_id ??= 'WS-FRM-'.strtoupper(Str::random(6));
            $base = Str::slug(Str::limit($f->name, 80, ''), '-', 'tr') ?: 'firma';
            $slug = $base;
            for ($i = 2; static::query()->where('slug', $slug)->exists(); $i++) {
                $slug = $base.'-'.$i;
            }
            $f->slug = $slug;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro' && $this->plan_until && $this->plan_until->isFuture();
    }

    public function typeLabel(): string
    {
        return (string) config("worksitem_firms.types.{$this->type}", $this->type);
    }

    /** @return list<string> */
    public function serviceLabels(): array
    {
        $all = (array) config('worksitem_firms.services');

        return array_values(array_filter(array_map(fn ($k) => $all[$k][0] ?? null, (array) $this->services)));
    }

    public function maskedPhone(): string
    {
        $p = (string) $this->phone;

        return strlen($p) === 10 ? '0'.substr($p, 0, 3).' *** ** '.substr($p, -2) : '***';
    }
}

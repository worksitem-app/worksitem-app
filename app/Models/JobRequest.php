<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/** Kullanıcının kayıtlı firmalardan teklif talebi */
class JobRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['expires_at' => 'datetime', 'terms_at' => 'datetime'];

    protected $hidden = ['token', 'phone', 'email', 'name'];

    public const STATUSES = ['open' => 'Teklif bekleniyor', 'matched' => 'Eşleşti', 'closed' => 'Kapatıldı', 'expired' => 'Süresi doldu'];

    protected static function booted(): void
    {
        static::creating(function (JobRequest $j) {
            $j->public_id ??= 'WS-IS-'.strtoupper(Str::random(6));
            $j->token ??= Str::random(40);
            $j->expires_at ??= now()->addDays((int) config('worksitem_market.request_days', 14));
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function offers(): HasMany
    {
        return $this->hasMany(JobOffer::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(JobInvite::class);
    }

    public function matchedOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class, 'matched_offer_id');
    }

    public function tradeLabel(): string
    {
        return (string) config("worksitem_trades.{$this->trade}.label", $this->trade);
    }

    public function location(): string
    {
        return $this->district.' / '.$this->city;
    }

    public function range(): ?string
    {
        if (! $this->est_low || ! $this->est_high) {
            return null;
        }
        $f = fn ($v) => $v >= 10000 ? number_format($v / 1000, 0, ',', '.').' bin' : number_format($v, 0, ',', '.');

        return $f($this->est_low).' – '.$f($this->est_high).' TL';
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && (! $this->expires_at || $this->expires_at->isFuture());
    }

    public function customerUrl(): string
    {
        return route('market.request.show', ['job' => $this->public_id, 't' => $this->token]);
    }

    public function firstName(): string
    {
        return (string) (preg_split('/\s+/u', trim((string) $this->name))[0] ?? '');
    }
}

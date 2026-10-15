<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JobOffer extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['includes_material' => 'boolean', 'fee_net' => 'float', 'accepted_at' => 'datetime', 'fee_due_at' => 'datetime', 'matched_at' => 'datetime'];

    public const STATUSES = [
        'submitted' => 'Teklif verildi', 'accepted' => 'Kabul edildi · eşleşme bedeli bekleniyor', 'matched' => 'Eşleşti',
        'not_selected' => 'Seçilmedi', 'expired' => 'Süresi doldu', 'withdrawn' => 'Geri çekildi',
    ];

    protected static function booted(): void
    {
        static::creating(function (JobOffer $o) {
            $o->public_id ??= 'WS-TKL-'.strtoupper(Str::random(6));
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobRequest::class, 'job_request_id');
    }

    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }

    public function feeGross(): float
    {
        return round((float) $this->fee_net * (1 + (float) config('worksitem_payments.vat_rate', 0.20)), 2);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AiGeneration extends Model
{
    protected $guarded = ['id', 'public_id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cost_breakdown' => 'array',
        'is_free_quota' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (AiGeneration $generation) {
            $generation->public_id ??= (string) Str::ulid();
        });
    }

    /**
     * Public endpoint'ler yalnızca ULID ile çalışır; sıralı ID ile erişim kapalı.
     */
    public static function findPublicOrFail(string $publicId): self
    {
        return static::query()->where('public_id', $publicId)->firstOrFail();
    }
}

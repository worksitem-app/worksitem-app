<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiWallet extends Model
{
    protected $fillable = ['phone', 'name', 'credits', 'free_day', 'free_used', 'total_used', 'verified_at', 'last_used_at', 'blocked', 'notes'];

    protected function casts(): array
    {
        return [
            'free_day' => 'date',
            'verified_at' => 'datetime',
            'last_used_at' => 'datetime',
            'blocked' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AiCreditTransaction::class);
    }

    public static function today(): string
    {
        return now('Europe/Istanbul')->toDateString();
    }

    public function freeLeft(): int
    {
        $limit = (int) config('worksitem_ai_credits.free_per_day', 1);
        $used = $this->free_day?->toDateString() === self::today() ? (int) $this->free_used : 0;

        return max(0, $limit - $used);
    }

    public function maskedPhone(): string
    {
        return '0'.substr($this->phone, 0, 3).' *** ** '.substr($this->phone, -2);
    }
}

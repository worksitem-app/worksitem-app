<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['amount' => 'float', 'paid_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (Order $o) {
            $o->public_id ??= 'WS-ODM-'.strtoupper(Str::random(8));
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}

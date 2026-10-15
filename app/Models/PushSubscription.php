<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['pending' => 'array', 'last_sent_at' => 'datetime'];

    protected $hidden = ['endpoint', 'p256dh', 'auth'];

    public static function hash(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }

    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }
}

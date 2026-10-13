<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiCreditTransaction extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['ai_wallet_id', 'delta', 'kind', 'feature', 'note', 'admin_id'];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(AiWallet::class, 'ai_wallet_id');
    }
}

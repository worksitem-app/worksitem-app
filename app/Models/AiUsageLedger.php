<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLedger extends Model
{
    protected $table = 'ai_usage_ledger';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',

        'prompt_tokens' => 'integer',
        'output_tokens' => 'integer',
        'thought_tokens' => 'integer',
        'cached_tokens' => 'integer',
        'total_tokens' => 'integer',

        'input_image_count' => 'integer',
        'output_image_count' => 'integer',

        'provider_cost_usd' => 'decimal:8',
        'provider_cost_try' => 'decimal:4',

        'credits_reserved' => 'integer',
        'credits_charged' => 'integer',
        'credits_refunded' => 'integer',

        'latency_ms' => 'integer',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialPriceHistory extends Model
{
    protected $table = 'material_price_history';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:4',
            'confidence_score' => 'decimal:2',
            'source_date' => 'date',
            'observed_at' => 'datetime',
            'vat_included' => 'boolean',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(
            MaterialPrice::class,
            'material_price_id'
        );
    }
}

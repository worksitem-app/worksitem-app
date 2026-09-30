<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialPrice extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'current_price' => 'decimal:4',
            'previous_price' => 'decimal:4',
            'change_amount' => 'decimal:4',
            'change_percent' => 'decimal:4',
            'confidence_score' => 'decimal:2',
            'source_date' => 'date',
            'observed_at' => 'datetime',
            'vat_included' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function history(): HasMany
    {
        return $this->hasMany(MaterialPriceHistory::class);
    }
}

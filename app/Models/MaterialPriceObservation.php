<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialPriceObservation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:4',
        'source_date' => 'date',
        'observed_at' => 'datetime',
        'confidence_score' => 'decimal:2',
        'metadata' => 'array',
    ];
}

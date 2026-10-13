<?php

namespace App\Models;

use App\Services\CostEngine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UnitPrice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        $flush = function () {
            CostEngine::clearCache();
            Cache::forget('pricing_engine_snapshot');
            Cache::forget(\App\Services\BathroomPriceBook::CACHE_KEY);
        };

        static::saved($flush);
        static::deleted($flush);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPrice extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    protected static function booted(): void {
        static::saved(function () { \Illuminate\Support\Facades\Cache::forget("global_system_prices"); });
        static::deleted(function () { \Illuminate\Support\Facades\Cache::forget("global_system_prices"); });
    }
}

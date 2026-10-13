<?php

use App\Services\BathroomPriceBook;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Banyo hesaplayıcısının birim fiyatlarını admin paneline
 * (Fiyat Merkezi → Araç Fiyatları, kategori: banyo_tadilati) ekler.
 * Var olan satırlara dokunmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = app(BathroomPriceBook::class)->seedRows(config('worksitem_bathroom'), '2026-10-12');
        DB::table('unit_prices')->insertOrIgnore($rows);
        Cache::forget(BathroomPriceBook::CACHE_KEY);
    }

    public function down(): void
    {
        DB::table('unit_prices')->where('category', BathroomPriceBook::CATEGORY)->delete();
        Cache::forget(BathroomPriceBook::CACHE_KEY);
    }
};

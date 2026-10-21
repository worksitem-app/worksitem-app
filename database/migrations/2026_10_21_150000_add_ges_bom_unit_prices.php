<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * v90 GES malzeme listesi fiyatları → admin › Birim Fiyatlar (kategori "ges").
 * Başlangıç değerleri TAHMİNİDİR (perakende siteler, Ekim 2026, KDV dahil) ve "needs_review" olarak girilir:
 * onaylanana kadar araç config('worksitem_solar.bom.prices') değerlerini kullanır (aynı rakamlar).
 * Kainta saha fiyatıyla düzeltilip "approved" yapılınca o fiyat geçerli olur. Var olan kayda dokunulmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('unit_prices')) {
            return;
        }
        $now = now();
        $prices = (array) config('worksitem_solar.bom.prices');
        $labels = (array) config('worksitem_solar.bom.labels');
        $existing = DB::table('unit_prices')->whereIn('key', array_keys($prices))->pluck('key')->all();

        $rows = [];
        foreach ($prices as $key => $price) {
            if (in_array($key, $existing, true)) {
                continue;
            }
            if (preg_match('/^ges_inv_(mono|tri)_(\d+)$/', $key, $m)) {
                [$name, $unit] = [$m[2].' kW '.($m[1] === 'mono' ? 'monofaze' : 'trifaze').' şebeke bağlantılı inverter', 'TL/adet'];
            } else {
                [$name, $unit] = $labels[$key] ?? [$key, 'TL'];
            }
            $rows[] = [
                'category' => 'ges', 'key' => $key, 'name' => $name, 'price' => $price, 'unit' => $unit,
                'price_pool' => 'market', 'source_name' => 'Worksitem piyasa taraması (tahmini)', 'source_date' => '2026-10-21',
                'vat_included' => 1, 'is_active' => 1, 'review_status' => 'needs_review',
                'notes' => 'GES malzeme listesi başlangıç değeri. Kainta saha fiyatıyla kontrol edip "Onaylı" yapın; onaylanınca GES planlayıcıda bu fiyat kullanılır.',
                'created_at' => $now, 'updated_at' => $now,
            ];
        }
        if ($rows) {
            DB::table('unit_prices')->insert($rows);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('unit_prices')) {
            DB::table('unit_prices')->where('category', 'ges')->where('review_status', 'needs_review')
                ->where('source_name', 'Worksitem piyasa taraması (tahmini)')->delete();
        }
    }
};

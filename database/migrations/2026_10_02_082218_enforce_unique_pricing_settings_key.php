<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bu migration yalnızca duplicate satır temizliği yapar.
        // `pricing_settings.key` üzerindeki unique index zaten
        // create_central_price_engine_fields migration'ında
        // `->unique()` ile tanımlıdır. Burada tekrar eklemek
        // SQLite'ta "index already exists" hatası verir.

        if (! Schema::hasTable('pricing_settings')) {
            return;
        }

        $duplicates = DB::table('pricing_settings')
            ->select('key', DB::raw('MIN(id) as keep_id'))
            ->groupBy('key')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('pricing_settings')
                ->where('key', $dup->key)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }
    }

    public function down(): void
    {
        // Unique index bu migration'ın parçası değil.
        // create_central_price_engine_fields migration'ında yönetilir.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duplicate'leri temizle — driver-agnostic
        if (Schema::hasTable('pricing_settings')) {
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

        // Unique index ekle (idempotent — MySQL'de index adı ile kontrol)
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $indexExists = collect(DB::select("SHOW INDEX FROM pricing_settings"))
                ->contains(fn ($i) => $i->Key_name === 'pricing_settings_key_unique');
        } else {
            $indexExists = false;
        }

        if (! $indexExists) {
            Schema::table('pricing_settings', function (Blueprint $table) {
                $table->unique('key', 'pricing_settings_key_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pricing_settings', function (Blueprint $table) {
            $table->dropUnique('pricing_settings_key_unique');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * IDOR kapatma: /maliyet-hesapla/proje/{id} ve /ai-tasarim/ic-mekan/status/{id}
 * sıralı ID ile herkese açıktı (başkasının projesi, yüklediği oda fotoğrafı).
 * Public URL'lerde artık tahmin edilemez ULID kullanılır.
 */
return new class extends Migration
{
    private array $tables = [
        'projects' => 'public_token',
        'ai_generations' => 'public_id',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $column) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
                continue;
            }

            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->string($column, 26)->nullable()->after('id');
            });

            DB::table($table)->whereNull($column)->orderBy('id')->chunkById(500, function ($rows) use ($table, $column) {
                foreach ($rows as $row) {
                    DB::table($table)->where('id', $row->id)->update([$column => (string) Str::ulid()]);
                }
            });

            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->unique($column);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                Schema::table($table, function (Blueprint $t) use ($table, $column) {
                    $t->dropUnique([$column]);
                    $t->dropColumn($column);
                });
            }
        }
    }
};

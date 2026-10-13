<?php

use App\Models\Lead;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Kısa talep no (WS-XXXXXX): SMS'te uzun ULID yerine; mevcut talepler de numaralandırılır */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('leads', 'ref_code')) {
            Schema::table('leads', function (Blueprint $t) {
                $t->string('ref_code', 12)->nullable()->unique()->after('public_id');
            });
        }
        DB::table('leads')->whereNull('ref_code')->orderBy('id')->pluck('id')->each(function ($id) {
            DB::table('leads')->where('id', $id)->update(['ref_code' => Lead::newRefCode()]);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('leads', 'ref_code')) {
            Schema::table('leads', function (Blueprint $t) {
                $t->dropUnique(['ref_code']);
                $t->dropColumn('ref_code');
            });
        }
    }
};

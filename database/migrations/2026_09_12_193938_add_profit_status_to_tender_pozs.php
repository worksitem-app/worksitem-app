<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_pozs', function (Blueprint $table) {
            $table->string('profit_status', 16)
                ->default('unknown')
                ->after('profit_included')
                ->index();
        });

        DB::table('tender_pozs')
            ->where('profit_included', true)
            ->update(['profit_status' => 'included']);

        DB::table('tender_pozs')
            ->where('institution', 'Karayolları')
            ->where('period', '2026-09')
            ->where('profit_included', false)
            ->update(['profit_status' => 'excluded']);
    }

    public function down(): void
    {
        Schema::table('tender_pozs', function (Blueprint $table) {
            $table->dropIndex(['profit_status']);
            $table->dropColumn('profit_status');
        });
    }
};

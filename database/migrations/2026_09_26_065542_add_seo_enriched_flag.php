<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tender_pozs', 'seo_enriched')) {
            Schema::table('tender_pozs', function (Blueprint $table) {
                $table->boolean('seo_enriched')->default(false)->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tender_pozs', 'seo_enriched')) {
            Schema::table('tender_pozs', function (Blueprint $table) {
                $table->dropColumn('seo_enriched');
            });
        }
    }
};

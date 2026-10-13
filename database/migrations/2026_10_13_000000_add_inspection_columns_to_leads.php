<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'inspection_status')) {
                // null: teklif gösterilmedi/kabul edilmedi · kabul: ödeme bekleniyor · odendi · iptal
                $table->string('inspection_status', 16)->nullable()->index();
                $table->timestamp('inspection_accepted_at')->nullable();
                $table->unsignedInteger('inspection_fee')->nullable();
                $table->string('inspection_terms_version', 20)->nullable();
                $table->timestamp('inspection_paid_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'inspection_status')) {
                $table->dropIndex(['inspection_status']);
                $table->dropColumn(['inspection_status', 'inspection_accepted_at', 'inspection_fee', 'inspection_terms_version', 'inspection_paid_at']);
            }
        });
    }
};

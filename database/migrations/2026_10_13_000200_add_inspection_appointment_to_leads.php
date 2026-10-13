<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'inspection_appointment_at')) {
                $table->timestamp('inspection_appointment_at')->nullable()->index();
                $table->string('inspection_address', 255)->nullable();
                $table->timestamp('inspection_reminder_sent_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'inspection_appointment_at')) {
                $table->dropIndex(['inspection_appointment_at']);
                $table->dropColumn(['inspection_appointment_at', 'inspection_address', 'inspection_reminder_sent_at']);
            }
        });
    }
};

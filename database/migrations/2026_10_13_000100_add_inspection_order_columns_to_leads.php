<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'inspection_payment_method')) {
                $table->string('inspection_payment_method', 16)->nullable();      // kart | havale
                $table->json('inspection_data')->nullable();                     // fatura bilgisi + onay kayıtları
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'inspection_payment_method')) {
                $table->dropColumn(['inspection_payment_method', 'inspection_data']);
            }
        });
    }
};

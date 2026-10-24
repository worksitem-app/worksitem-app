<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** v114: poz → sponsor ürün grubu eşleşmesi (php artisan sponsors:poz-esle doldurur; virgülle ayrılmış anahtarlar) */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tender_pozs', 'sponsor_categories')) {
            Schema::table('tender_pozs', function (Blueprint $table) {
                $table->string('sponsor_categories', 255)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tender_pozs', 'sponsor_categories')) {
            Schema::table('tender_pozs', fn (Blueprint $table) => $table->dropColumn('sponsor_categories'));
        }
    }
};

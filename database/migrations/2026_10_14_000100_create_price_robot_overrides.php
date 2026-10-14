<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fiyat robotu: hesap motoru varsayılan fiyatlarının (config/worksitem_estimators.php)
 * web araştırmasıyla güncellenen değerleri. "live" satırlar açılışta config'in üzerine yazılır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('price_robot_items')) {
            return;
        }
        Schema::create('price_robot_items', function (Blueprint $table) {
            $table->id();
            $table->string('path', 191)->unique();          // örn. worksitem_estimators.mutfak.cabinet_m
            $table->string('tool_slug', 80)->nullable()->index();
            $table->string('label', 191);
            $table->string('unit', 40)->nullable();
            $table->json('default_value')->nullable();       // koddaki varsayılan (karşılaştırma için)
            $table->json('value')->nullable();               // yayındaki değer
            $table->json('previous_value')->nullable();
            $table->json('proposed_value')->nullable();      // onay bekleyen öneri
            $table->decimal('web_median', 14, 2)->nullable(); // KDV dahil, çarpan öncesi
            $table->decimal('multiplier', 6, 3)->default(1);
            $table->unsignedSmallInteger('source_count')->default(0);
            $table->json('sources')->nullable();
            $table->string('status', 20)->default('new')->index();  // new|live|needs_review|rejected|error
            $table->boolean('locked')->default(false);       // uzman elle girdi: robot değiştirmez
            $table->text('note')->nullable();
            $table->timestamp('researched_at')->nullable()->index();
            $table->foreignId('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_robot_items');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sponsors')) {
            return;
        }
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 120);
            $table->string('headline', 120);
            $table->string('body', 240)->nullable();
            $table->string('cta_label', 40)->default('İncele');
            $table->string('target_url', 500);
            $table->string('logo_url', 500)->nullable();
            $table->string('tool_slug', 120)->index();
            $table->string('category', 40)->default('genel');
            $table->unsignedSmallInteger('priority')->default(10);
            $table->boolean('active')->default(true)->index();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->decimal('contract_amount', 12, 2)->nullable();   // anlaşma bedeli (TL, KDV hariç) — raporlama için
            $table->string('contact', 200)->nullable();              // marka yetkilisi
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};

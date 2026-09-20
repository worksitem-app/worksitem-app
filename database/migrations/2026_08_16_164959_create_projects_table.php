<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_type_id')
                ->constrained('project_types')
                ->restrictOnDelete();

            // Müşteri bilgileri
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Lokasyon
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->text('address')->nullable();

            // Proje temel bilgileri
            $table->decimal('land_area', 12, 2)->nullable();
            $table->decimal('construction_area', 12, 2)->nullable();

            // Proje durumu
            $table->string('status')->default('draft');
            // draft, submitted, calculating, calculated,
            // render_pending, render_ready, quoted, completed

            // Müşterinin ek açıklaması
            $table->text('notes')->nullable();

            // Sonraki aşamalarda kullanılacak toplam maliyet
            $table->decimal('estimated_cost', 15, 2)->nullable();

            $table->timestamps();

            $table->index(['project_type_id', 'status']);
            $table->index(['city', 'district']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

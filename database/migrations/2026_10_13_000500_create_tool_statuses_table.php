<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->boolean('active')->default(false);   // true: yayında · false: incelemede (hesaplama kilitli)
            $table->string('note', 255)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_statuses');
    }
};

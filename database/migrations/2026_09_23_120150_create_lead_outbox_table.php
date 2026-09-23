<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lead_outbox')) {
            Schema::create('lead_outbox', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lead_id')->nullable()->index();
                $table->string('status', 30)->default('pending')->index();
                $table->json('payload')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_outbox');
    }
};

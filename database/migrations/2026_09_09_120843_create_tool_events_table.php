<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tool_events', function (Blueprint $table) {
            $table->id();
            $table->string('tool_slug', 120);
            $table->string('event_type', 50);
            $table->string('session_key', 64)->nullable();
            $table->string('sponsor_key', 120)->nullable();
            $table->text('target_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tool_slug', 'event_type', 'created_at']);
            $table->index('session_key');
            $table->index('sponsor_key');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tool_events');
    }
};

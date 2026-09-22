<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_generations', function (Blueprint $table) {
            $table->string('ip_hash', 64)->nullable()->after('user_id')->index();
            $table->string('session_key', 64)->nullable()->after('ip_hash')->index();
            $table->boolean('is_free_quota')->default(true)->after('session_key');
        });
    }

    public function down(): void
    {
        Schema::table('ai_generations', function (Blueprint $table) {
            $table->dropColumn(['ip_hash', 'session_key', 'is_free_quota']);
        });
    }
};

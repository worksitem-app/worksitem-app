<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_generations', function (Blueprint $table) {
            // Kullanıcı prompt'u ve AI prompt'u
            $table->text('user_prompt')->nullable()->after('style');
            $table->text('ai_prompt')->nullable()->after('user_prompt');

            // Oda bilgisi
            $table->decimal('room_area', 8, 2)->nullable()->after('ai_prompt');

            // Maliyet
            $table->decimal('estimated_cost', 15, 2)->nullable()->after('room_area');
            $table->json('cost_breakdown')->nullable()->after('estimated_cost');
            $table->timestamp('cost_calculated_at')->nullable()->after('cost_breakdown');
        });
    }

    public function down(): void
    {
        Schema::table('ai_generations', function (Blueprint $table) {
            $table->dropColumn([
                'user_prompt',
                'ai_prompt',
                'room_area',
                'estimated_cost',
                'cost_breakdown',
                'cost_calculated_at',
            ]);
        });
    }
};

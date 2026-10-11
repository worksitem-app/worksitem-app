<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_ledger', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('ai_generation_id')->nullable()->index();

            $table->char('credit_reservation_id', 26)->nullable()->index();

            $table->string('tool_key', 100)->index();
            $table->string('provider', 50)->default('gemini')->index();
            $table->string('model', 150)->nullable()->index();
            $table->string('operation', 80)->nullable();

            $table->string('provider_request_id', 191)->nullable()->index();

            $table->unsignedBigInteger('prompt_tokens')->default(0);
            $table->unsignedBigInteger('output_tokens')->default(0);
            $table->unsignedBigInteger('thought_tokens')->default(0);
            $table->unsignedBigInteger('cached_tokens')->default(0);
            $table->unsignedBigInteger('total_tokens')->default(0);

            $table->unsignedSmallInteger('input_image_count')->default(0);
            $table->unsignedSmallInteger('output_image_count')->default(0);

            $table->decimal('provider_cost_usd', 14, 8)->default(0);
            $table->decimal('provider_cost_try', 14, 4)->default(0);

            $table->unsignedInteger('credits_reserved')->default(0);
            $table->unsignedInteger('credits_charged')->default(0);
            $table->unsignedInteger('credits_refunded')->default(0);

            $table->string('status', 30)->default('pending')->index();
            $table->string('error_code', 100)->nullable();
            $table->unsignedInteger('latency_ms')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['tool_key', 'created_at'],
                'ai_usage_tool_created_idx'
            );

            $table->index(
                ['provider', 'model', 'created_at'],
                'ai_usage_provider_model_created_idx'
            );

            $table->index(
                ['status', 'created_at'],
                'ai_usage_status_created_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_ledger');
    }
};

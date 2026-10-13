<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 10)->unique();          // 5XXXXXXXXX (SMS ile doğrulanmış)
            $table->string('name', 120)->nullable();
            $table->unsignedInteger('credits')->default(0);
            $table->date('free_day')->nullable();             // ücretsiz hakkın sayıldığı gün (İstanbul)
            $table->unsignedSmallInteger('free_used')->default(0);
            $table->unsignedInteger('total_used')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('blocked')->default(false);
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('ai_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_wallet_id')->constrained('ai_wallets')->cascadeOnDelete();
            $table->integer('delta');                          // + yükleme / − kullanım
            $table->string('kind', 20);                        // free, use, refund, grant
            $table->string('feature', 80)->nullable();
            $table->string('note', 255)->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_credit_transactions');
        Schema::dropIfExists('ai_wallets');
    }
};

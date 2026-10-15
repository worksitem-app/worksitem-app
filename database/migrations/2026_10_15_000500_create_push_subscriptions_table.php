<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable()->constrained('firms')->cascadeOnDelete();
            $t->char('endpoint_hash', 64)->unique();
            $t->text('endpoint');
            $t->string('p256dh', 200)->nullable();
            $t->string('auth', 100)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->json('pending')->nullable();
            $t->timestamp('last_sent_at')->nullable();
            $t->unsignedSmallInteger('failed_count')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};

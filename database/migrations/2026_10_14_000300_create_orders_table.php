<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Online siparişler: yapay zekâ kredisi, ücretli Pro rapor (iyzico) */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return;
        }
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('public_id', 24)->unique();
            $t->string('kind', 20)->index();              // credits | pro_report
            $t->foreignId('ai_wallet_id')->nullable()->index();
            $t->string('ref', 191)->nullable()->index();  // pro_report: araç + hesap anahtarı
            $t->string('title', 191);
            $t->unsignedInteger('credits')->default(0);
            $t->decimal('amount', 10, 2);                 // KDV dahil, TL
            $t->string('name', 120)->nullable();
            $t->string('phone', 20)->nullable()->index();
            $t->string('email', 150)->nullable();
            $t->text('return_url')->nullable();
            $t->string('status', 20)->default('pending')->index();   // pending | paid | failed
            $t->string('iyzico_token', 200)->nullable()->unique();
            $t->string('payment_id', 60)->nullable();
            $t->text('error')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

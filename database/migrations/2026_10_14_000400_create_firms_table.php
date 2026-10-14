<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firms', function (Blueprint $t) {
            $t->id();
            $t->string('public_id', 20)->unique();
            $t->string('slug', 140)->unique();
            $t->string('name', 150);
            $t->string('type', 30);
            $t->json('services')->nullable();
            $t->json('cities')->nullable();
            $t->string('district', 100)->nullable();
            $t->string('contact_name', 120);
            $t->string('phone', 20)->index();
            $t->string('email', 150)->nullable();
            $t->string('website', 200)->nullable();
            $t->string('tax_no', 20)->nullable();
            $t->unsignedSmallInteger('founded_year')->nullable();
            $t->text('description')->nullable();
            $t->string('status', 20)->default('pending')->index();
            $t->string('plan', 20)->default('free');
            $t->timestamp('plan_until')->nullable();
            $t->unsignedInteger('views')->default(0);
            $t->unsignedInteger('contact_clicks')->default(0);
            $t->text('admin_note')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->timestamp('terms_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firms');
    }
};

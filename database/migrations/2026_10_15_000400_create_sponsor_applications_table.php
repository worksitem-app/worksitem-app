<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Sponsor firma kaydı (ön başvuru). Kart, imzalı sözleşme olmadan yayına alınmaz. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_applications', function (Blueprint $t) {
            $t->id();
            $t->string('public_id', 20)->unique();
            $t->string('company', 150);
            $t->string('legal_name', 200)->nullable();
            $t->string('tax_no', 14)->nullable();
            $t->string('tax_office', 80)->nullable();
            $t->string('contact_name', 120);
            $t->string('contact_title', 100)->nullable();
            $t->string('phone', 30);
            $t->string('email', 150);
            $t->string('website', 200)->nullable();
            $t->json('categories')->nullable();
            $t->json('tools')->nullable();
            $t->string('budget', 20)->nullable();
            $t->string('start', 20)->nullable();
            $t->string('logo_path', 200)->nullable();
            $t->text('message')->nullable();
            $t->string('status', 20)->default('new')->index();   // new | contacted | offer | signed | rejected
            $t->foreignId('lead_id')->nullable();
            $t->text('admin_note')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_applications');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** A/B testi sayaçları (gün × test × sürüm) */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_stats', function (Blueprint $t) {
            $t->id();
            $t->date('day');
            $t->string('test', 40);
            $t->string('variant', 10);
            $t->unsignedInteger('views')->default(0);
            $t->unsignedInteger('clicks')->default(0);
            $t->unsignedInteger('conversions')->default(0);
            $t->unique(['day', 'test', 'variant']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_stats');
    }
};

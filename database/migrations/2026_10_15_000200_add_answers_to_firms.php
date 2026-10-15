<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Firma hizmet bilgileri (kısa sorular): eşleştirme ve saha fiyat verisi */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('firms', function (Blueprint $t) {
            $t->json('answers')->nullable();
            $t->timestamp('answers_at')->nullable();
            $t->unsignedInteger('min_job')->nullable();   // en küçük iş (TL); daha küçük işlere davet gitmez
        });
    }

    public function down(): void
    {
        Schema::table('firms', fn (Blueprint $t) => $t->dropColumn(['answers', 'answers_at', 'min_job']));
    }
};

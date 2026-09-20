<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unit_prices', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // Örn: 'kaba_insaat', 'ince_isler', 'demir', 'boya'
            $table->string('key')->unique(); // Örn: 'demir_nervurlu_ton', 'boya_ekonomik_m2'
            $table->string('name'); // Örn: 'Nervürlü İnşaat Demiri (Ton)'
            $table->decimal('price', 15, 2); // Fiyat
            $table->string('unit'); // Örn: 'TL/Ton', 'TL/m2'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit_prices');
    }
};

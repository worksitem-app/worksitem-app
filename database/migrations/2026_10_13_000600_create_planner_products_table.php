<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** 2D/3D banyo planlayıcı: sponsor markaların ürün kataloğu (admin › Gelir › Planlayıcı ürünleri) */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planner_products', function (Blueprint $t) {
            $t->id();
            $t->string('type', 20)->index();          // kabin, kuvet, gomme, lavabo, klozet, havlupan
            $t->string('name', 150);
            $t->string('brand', 120);
            $t->foreignId('sponsor_id')->nullable()->constrained('sponsors')->nullOnDelete();
            $t->unsignedSmallInteger('width');           // cm, duvar boyunca
            $t->unsignedSmallInteger('depth');           // cm
            $t->unsignedSmallInteger('height');          // cm
            $t->unsignedSmallInteger('width_min')->nullable();
            $t->unsignedSmallInteger('width_max')->nullable();
            $t->unsignedSmallInteger('depth_min')->nullable();
            $t->unsignedSmallInteger('depth_max')->nullable();
            $t->string('color', 9)->default('#FFFFFF');
            $t->string('cabinet_color', 9)->nullable();  // dolaplı lavabo
            $t->boolean('tank')->default(false);         // rezervuarlı klozet
            $t->string('image_url', 500)->nullable();
            $t->string('product_url', 500)->nullable();
            $t->unsignedInteger('price_min')->nullable(); // TL, KDV dahil
            $t->unsignedInteger('price_max')->nullable();
            $t->boolean('active')->default(true);
            $t->unsignedSmallInteger('priority')->default(10);
            $t->unsignedInteger('impressions')->default(0);
            $t->unsignedInteger('clicks')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planner_products');
    }
};

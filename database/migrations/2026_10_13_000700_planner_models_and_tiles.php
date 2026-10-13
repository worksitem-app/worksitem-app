<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Planlayıcı: ürünlere stok kodu, yüklenen görsel ve 3D model (GLB); sponsor seramik kataloğu */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planner_products', function (Blueprint $t) {
            $t->string('sku', 80)->nullable()->after('brand');
            $t->string('image_path', 300)->nullable()->after('image_url');
            $t->string('model_path', 300)->nullable()->after('image_path');
        });

        Schema::create('planner_tiles', function (Blueprint $t) {
            $t->id();
            $t->string('name', 150);
            $t->string('brand', 120);
            $t->string('series', 120)->nullable();
            $t->string('sku', 80)->nullable();
            $t->foreignId('sponsor_id')->nullable()->constrained('sponsors')->nullOnDelete();
            $t->unsignedSmallInteger('width_cm');
            $t->unsignedSmallInteger('height_cm');
            $t->string('finish', 30)->nullable();      // mat, parlak, yarı mat…
            $t->string('usage', 20)->default('both');  // floor, wall, both
            $t->string('image_path', 300)->nullable(); // tek karo, desenli tekrar eden doku
            $t->string('image_url', 500)->nullable();
            $t->string('product_url', 500)->nullable();
            $t->unsignedInteger('price_m2_min')->nullable(); // TL/m², KDV dahil
            $t->unsignedInteger('price_m2_max')->nullable();
            $t->boolean('active')->default(true);
            $t->unsignedSmallInteger('priority')->default(10);
            $t->unsignedInteger('impressions')->default(0);
            $t->unsignedInteger('clicks')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planner_tiles');
        Schema::table('planner_products', function (Blueprint $t) {
            $t->dropColumn(['sku', 'image_path', 'model_path']);
        });
    }
};

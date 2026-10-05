<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paint_color_brands', function (Blueprint $table) {
            $table->id();

            $table->string('name', 160);
            $table->string('slug', 180)->unique();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_sponsored')->default(false);

            $table->string('sponsor_label', 120)->nullable();
            $table->string('logo_path', 255)->nullable();

            $table->string('website_url', 500)->nullable();
            $table->string('product_url', 500)->nullable();
            $table->string('dealer_url', 500)->nullable();

            /*
             * Katalog kullanım hakkı / veri kaynağı takibi.
             * Sponsorluk tek başına katalog kullanım hakkı anlamına gelmez.
             */
            $table->string('catalog_source', 255)->nullable();
            $table->string('catalog_version', 120)->nullable();
            $table->string('rights_status', 40)->default('unverified');
            $table->text('rights_reference')->nullable();
            $table->date('rights_expires_at')->nullable();

            $table->unsignedInteger('sort_order')->default(100);

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index(['is_sponsored', 'is_active']);
        });

        Schema::create('paint_colors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_id')
                ->constrained('paint_color_brands')
                ->cascadeOnDelete();

            $table->string('series', 160)->nullable();
            $table->string('code', 120)->nullable();
            $table->string('name', 180);

            $table->string('hex', 7);

            /*
             * Sponsor daha doğru dijital renk verisi sağlarsa
             * ileride RGB / Lab değerleri kullanılabilir.
             */
            $table->unsignedTinyInteger('rgb_r')->nullable();
            $table->unsignedTinyInteger('rgb_g')->nullable();
            $table->unsignedTinyInteger('rgb_b')->nullable();

            $table->decimal('lab_l', 7, 3)->nullable();
            $table->decimal('lab_a', 7, 3)->nullable();
            $table->decimal('lab_b', 7, 3)->nullable();

            $table->string('category', 120)->nullable();

            $table->string('product_url', 500)->nullable();
            $table->string('dealer_url', 500)->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(100);

            $table->timestamps();

            $table->index(['brand_id', 'is_active', 'sort_order']);
            $table->index('category');
            $table->index('code');

            $table->unique(
                ['brand_id', 'code', 'name'],
                'paint_colors_brand_code_name_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paint_colors');
        Schema::dropIfExists('paint_color_brands');
    }
};

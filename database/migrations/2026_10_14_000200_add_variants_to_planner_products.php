<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Planlayıcı ürünlerine renk / model seçenekleri: [{name, color, cabinet_color, image_url, sku, price_min, price_max}] */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('planner_products') && ! Schema::hasColumn('planner_products', 'variants')) {
            Schema::table('planner_products', fn (Blueprint $t) => $t->json('variants')->nullable()->after('tank'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('planner_products', 'variants')) {
            Schema::table('planner_products', fn (Blueprint $t) => $t->dropColumn('variants'));
        }
    }
};

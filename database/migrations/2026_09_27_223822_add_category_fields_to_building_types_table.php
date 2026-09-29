<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('building_types', function (Blueprint $table) {
            $table->string('category_key', 50)
                ->nullable()
                ->after('building_class_id');

            $table->string('category_name', 100)
                ->nullable()
                ->after('category_key');

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('category_name');

            $table->index(
                ['category_key', 'is_active'],
                'building_types_category_active_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('building_types', function (Blueprint $table) {
            $table->dropIndex(
                'building_types_category_active_index'
            );

            $table->dropColumn([
                'category_key',
                'category_name',
                'sort_order',
            ]);
        });
    }
};

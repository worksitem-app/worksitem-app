<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('unit_prices', 'price_pool')) {
            Schema::table('unit_prices', function (Blueprint $table) {
                $table->string('price_pool', 20)
                    ->default('market')
                    ->after('unit');

                $table->string('source_name')
                    ->nullable()
                    ->after('price_pool');

                $table->date('source_date')
                    ->nullable()
                    ->after('source_name');

                $table->boolean('vat_included')
                    ->default(false)
                    ->after('source_date');

                $table->boolean('is_active')
                    ->default(true)
                    ->after('vat_included');

                $table->string('review_status', 30)
                    ->default('needs_review')
                    ->after('is_active');

                $table->text('notes')
                    ->nullable()
                    ->after('review_status');

                $table->index(
                    ['price_pool', 'is_active'],
                    'unit_prices_pool_active_index'
                );
            });
        }

        if (!Schema::hasTable('building_classes')) {
            Schema::create('building_classes', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('name');
                $table->string('unit', 20)->default('TL/m2');
                $table->text('description')->nullable();
                $table->string('source_name')->nullable();
                $table->date('source_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('building_class_costs')) {
            Schema::create('building_class_costs', function (Blueprint $table) {
                $table->id();

                $table->foreignId('building_class_id')
                    ->constrained('building_classes')
                    ->cascadeOnDelete();

                $table->decimal('unit_price', 18, 2);
                $table->string('unit', 20)->default('TL/m2');
                $table->string('period', 7)->nullable();
                $table->string('source_name')->nullable();
                $table->date('source_date')->nullable();
                $table->boolean('vat_included')->default(false);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();

                $table->timestamps();

                $table->index(
                    ['building_class_id', 'period', 'is_active'],
                    'building_class_cost_lookup_index'
                );
            });
        }

        if (!Schema::hasTable('building_types')) {
            Schema::create('building_types', function (Blueprint $table) {
                $table->id();

                $table->foreignId('building_class_id')
                    ->nullable()
                    ->constrained('building_classes')
                    ->nullOnDelete();

                $table->string('key')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('source_name')->nullable();
                $table->date('source_date')->nullable();
                $table->boolean('is_active')->default(true);

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('building_types');
        Schema::dropIfExists('building_class_costs');
        Schema::dropIfExists('building_classes');

        if (Schema::hasColumn('unit_prices', 'price_pool')) {
            Schema::table('unit_prices', function (Blueprint $table) {
                $table->dropIndex('unit_prices_pool_active_index');

                $table->dropColumn([
                    'price_pool',
                    'source_name',
                    'source_date',
                    'vat_included',
                    'is_active',
                    'review_status',
                    'notes',
                ]);
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('material_prices')) {
            Schema::create('material_prices', function (Blueprint $table): void {
                $table->id();

                $table->string('key')->unique();
                $table->string('name');
                $table->string('category')->index();
                $table->string('spec')->nullable();

                $table->string('unit', 50);
                $table->string('region', 100)->default('Türkiye');
                $table->string('currency', 3)->default('TRY');

                $table->decimal('current_price', 18, 4)->nullable();
                $table->decimal('previous_price', 18, 4)->nullable();
                $table->decimal('change_amount', 18, 4)->nullable();
                $table->decimal('change_percent', 9, 4)->nullable();

                $table->string('source_name')->nullable();
                $table->text('source_url')->nullable();
                $table->date('source_date')->nullable();
                $table->timestamp('observed_at')->nullable();

                $table->boolean('vat_included')->default(false);

                $table->decimal(
                    'confidence_score',
                    5,
                    2
                )->nullable();

                $table->string(
                    'verification_status',
                    30
                )->default('needs_review');

                $table->boolean('is_active')->default(true);

                $table->timestamps();

                $table->index([
                    'verification_status',
                    'is_active',
                ]);

                $table->index('observed_at');
            });
        }

        if (! Schema::hasTable('material_price_history')) {
            Schema::create(
                'material_price_history',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId('material_price_id')
                        ->nullable()
                        ->constrained('material_prices')
                        ->nullOnDelete();

                    /*
                     * Ana kayıt daha sonra silinse bile
                     * tarihsel kayıt kimliğini korur.
                     */
                    $table->string('material_key')->index();
                    $table->string('name');
                    $table->string('category');

                    $table->decimal('price', 18, 4);
                    $table->string('unit', 50);
                    $table->string('region', 100)
                        ->default('Türkiye');
                    $table->string('currency', 3)
                        ->default('TRY');

                    $table->string('source_name')->nullable();
                    $table->text('source_url')->nullable();
                    $table->date('source_date')->nullable();
                    $table->timestamp('observed_at')->nullable();

                    $table->boolean('vat_included')->default(false);

                    $table->decimal(
                        'confidence_score',
                        5,
                        2
                    )->nullable();

                    $table->string(
                        'verification_status',
                        30
                    )->default('needs_review');

                    /*
                     * new_material
                     * daily_refresh
                     * price_change
                     * manual
                     */
                    $table->string(
                        'snapshot_reason',
                        30
                    )->default('daily_refresh');

                    $table->timestamps();

                    $table->index([
                        'verification_status',
                        'created_at',
                    ]);

                    $table->index([
                        'material_key',
                        'created_at',
                    ]);
                }
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('material_price_history');
        Schema::dropIfExists('material_prices');
    }
};

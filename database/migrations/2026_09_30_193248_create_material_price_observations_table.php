<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_price_observations', function (Blueprint $table) {
            $table->id();

            $table->string('material_key')->index();

            $table->string('product_name');
            $table->string('brand')->nullable();
            $table->text('spec')->nullable();

            $table->decimal('price', 14, 4);
            $table->string('unit', 32);
            $table->string('region')->nullable();
            $table->string('currency', 3)->default('TRY');

            /*
             * retail
             * manufacturer_list
             * wholesale
             * marketplace
             */
            $table->string('market_level')->index();

            $table->string('source_name');
            $table->text('source_url');

            $table->date('source_date')->nullable();
            $table->dateTime('observed_at');

            /*
             * included
             * excluded
             * unknown
             */
            $table->string('vat_status')
                ->default('unknown');

            $table->string('seller')->nullable();

            $table->decimal(
                'confidence_score',
                5,
                2
            )->nullable();

            $table->string(
                'verification_status'
            )->default('needs_review');

            /*
             * Aynı kaynak + aynı ürün + aynı fiyat
             * tekrar tekrar eklenmesin.
             */
            $table->char(
                'observation_hash',
                64
            )->unique();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index([
                'material_key',
                'market_level',
            ]);

            $table->index([
                'material_key',
                'observed_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'material_price_observations'
        );
    }
};

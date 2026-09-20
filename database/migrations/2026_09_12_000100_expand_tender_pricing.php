<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tender_pozs', function (Blueprint $table) {
            $table->string('period', 7)->nullable()->after('year');
            $table->string('price_type', 30)->default('unit_price')->after('unit_price');

            $table->decimal('material_price', 18, 4)->nullable()->after('price_type');
            $table->decimal('installation_price', 18, 4)->nullable()->after('material_price');

            $table->boolean('profit_included')->default(false)->after('installation_price');
            $table->decimal('included_profit_rate', 7, 4)->nullable()->after('profit_included');

            $table->boolean('vat_included')->default(false)->after('included_profit_rate');

            $table->string('source_file')->nullable()->after('vat_included');
            $table->unsignedInteger('source_page')->nullable()->after('source_file');
            $table->text('source_note')->nullable()->after('source_page');

            $table->boolean('is_active')->default(true)->after('source_note');
        });

        Schema::create('tool_price_bindings', function (Blueprint $table) {
            $table->id();
            $table->string('tool_slug');
            $table->string('price_key');
            $table->string('role')->default('base');
            $table->decimal('multiplier', 12, 4)->default(1);
            $table->boolean('is_required')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['tool_slug', 'price_key', 'role'],
                'tool_price_binding_unique'
            );
        });

        Schema::create('pricing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('value');
            $table->string('type')->default('decimal');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_settings');
        Schema::dropIfExists('tool_price_bindings');

        Schema::table('tender_pozs', function (Blueprint $table) {
            $table->dropColumn([
                'period','price_type','material_price',
                'installation_price','profit_included',
                'included_profit_rate','vat_included',
                'source_file','source_page','source_note',
                'is_active'
            ]);
        });
    }
};

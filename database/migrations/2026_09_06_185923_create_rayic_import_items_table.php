<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rayic_import_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rayic_import_id')
                ->constrained('rayic_imports')
                ->cascadeOnDelete();

            $table->string('poz_no', 50);
            $table->text('description');
            $table->string('unit', 30);
            $table->decimal('price', 15, 2);

            $table->unsignedInteger('source_page')->nullable();
            $table->string('category', 100)->nullable();

            $table->decimal('old_price', 15, 2)->nullable();

            $table->string('validation_status', 30)->default('valid');
            $table->text('validation_note')->nullable();

            $table->timestamps();

            $table->index(['rayic_import_id', 'poz_no']);
            $table->index('validation_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rayic_import_items');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_type_id')
                ->constrained('project_types')
                ->cascadeOnDelete();

            $table->string('question');
            $table->string('slug');

            $table->string('type')->default('text');
            // text, number, select, radio, checkbox, date, textarea, file

            $table->text('description')->nullable();

            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);

            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['project_type_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_questions');
    }
};

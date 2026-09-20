<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained('project_questions')
                ->cascadeOnDelete();

            // Seçenekli sorularda seçilen seçenek
            $table->foreignId('option_id')
                ->nullable()
                ->constrained('project_question_options')
                ->nullOnDelete();

            // Her türlü cevabı saklamak için
            $table->text('answer_value')->nullable();

            $table->timestamps();

            $table->index(['project_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_answers');
    }
};

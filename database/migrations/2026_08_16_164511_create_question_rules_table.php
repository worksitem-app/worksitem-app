<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_rules', function (Blueprint $table) {
            $table->id();

            // Kural hangi sorunun cevabına bakacak?
            $table->foreignId('source_question_id')
                ->constrained('project_questions')
                ->cascadeOnDelete();

            // Hangi seçenek seçildiğinde kural çalışacak?
            $table->foreignId('source_option_id')
                ->nullable()
                ->constrained('project_question_options')
                ->nullOnDelete();

            // Hangi soruyu gösterecek/gizleyecek?
            $table->foreignId('target_question_id')
                ->constrained('project_questions')
                ->cascadeOnDelete();

            // show = göster, hide = gizle
            $table->string('action')->default('show');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(
    [
        'source_question_id',
        'source_option_id',
        'target_question_id'
    ],
    'qr_lookup_idx'
);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_rules');
    }
};

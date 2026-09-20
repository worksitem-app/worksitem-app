<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('tool_name'); // ic-mekan, dis-cephe vb.
            $table->string('provider')->default('system'); // openai, replicate vb.
            $table->string('input_image')->nullable();
            $table->string('output_image')->nullable();
            $table->text('prompt')->nullable();
            $table->string('style')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('ai_generations'); }
};

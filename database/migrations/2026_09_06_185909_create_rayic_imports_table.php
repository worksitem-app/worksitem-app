<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rayic_imports', function (Blueprint $table) {
            $table->id();

            $table->string('source', 50)->default('YFK');
            $table->string('period', 7);
            $table->string('file_name');
            $table->string('file_path')->nullable();

            $table->unsignedInteger('total_records')->default(0);
            $table->unsignedInteger('valid_records')->default(0);
            $table->unsignedInteger('invalid_records')->default(0);
            $table->unsignedInteger('changed_records')->default(0);

            $table->string('status', 30)->default('uploaded');
            $table->text('error_message')->nullable();

            $table->timestamp('analyzed_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(['source', 'period']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rayic_imports');
    }
};

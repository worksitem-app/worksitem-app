<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->constrained()->cascadeOnDelete();
            $table->string('key', 50); // Limit sorunu yaşamamak için 50 yaptık
            $table->decimal('value', 15, 4);
            $table->string('description')->nullable();
            $table->timestamps();

            // Aynı proje tipi için aynı anahtardan birden fazla olmasın
            $table->unique(['project_type_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_rates');
    }
};

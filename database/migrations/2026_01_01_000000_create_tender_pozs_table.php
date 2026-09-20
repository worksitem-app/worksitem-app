<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tender_pozs')) {
            Schema::create('tender_pozs', function (Blueprint $table) {
                $table->id();
                $table->string('year', 10)->default('2026');
                $table->string('institution', 100)->default('ÇŞB');
                $table->string('poz_no', 50)->index();
                $table->text('description');
                $table->string('unit', 30);
                $table->decimal('unit_price', 15, 2);
                $table->timestamps();

                $table->index(['institution', 'poz_no']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tender_pozs');
    }
};

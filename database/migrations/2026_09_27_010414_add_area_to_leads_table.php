<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('leads', 'area')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->decimal('area', 15, 2)
                    ->nullable()
                    ->after('district');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('leads', 'area')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->dropColumn('area');
            });
        }
    }
};
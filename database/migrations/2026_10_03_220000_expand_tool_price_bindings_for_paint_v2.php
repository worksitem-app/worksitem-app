<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tool_price_bindings')) {
            return;
        }

        $columns = [
            'source_type' => fn (Blueprint $table) =>
                $table->string('source_type', 50)->nullable(),

            'source_ref' => fn (Blueprint $table) =>
                $table->string('source_ref')->nullable(),

            'institution' => fn (Blueprint $table) =>
                $table->string('institution')->nullable(),

            'poz_no' => fn (Blueprint $table) =>
                $table->string('poz_no')->nullable(),

            'period' => fn (Blueprint $table) =>
                $table->string('period', 30)->nullable(),
        ];

        foreach ($columns as $column => $definition) {
            if (!Schema::hasColumn('tool_price_bindings', $column)) {
                Schema::table(
                    'tool_price_bindings',
                    function (Blueprint $table) use ($definition) {
                        $definition($table);
                    }
                );
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('tool_price_bindings')) {
            return;
        }

        foreach ([
            'source_type',
            'source_ref',
            'institution',
            'poz_no',
            'period',
        ] as $column) {
            if (Schema::hasColumn('tool_price_bindings', $column)) {
                Schema::table(
                    'tool_price_bindings',
                    function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    }
                );
            }
        }
    }
};

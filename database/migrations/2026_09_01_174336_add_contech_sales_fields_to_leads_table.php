<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {

            if (!Schema::hasColumn('leads', 'source')) {
                $table->string('source', 100)
                    ->nullable()
                    ->index();
            }

            if (!Schema::hasColumn('leads', 'project_data')) {
                $table->json('project_data')
                    ->nullable();
            }

            if (!Schema::hasColumn('leads', 'estimated_cost')) {
                $table->decimal('estimated_cost', 15, 2)
                    ->nullable();
            }

            if (!Schema::hasColumn('leads', 'estimated_min')) {
                $table->decimal('estimated_min', 15, 2)
                    ->nullable();
            }

            if (!Schema::hasColumn('leads', 'estimated_max')) {
                $table->decimal('estimated_max', 15, 2)
                    ->nullable();
            }

            if (!Schema::hasColumn('leads', 'budget_confirmed')) {
                $table->boolean('budget_confirmed')
                    ->default(false)
                    ->index();
            }

            if (!Schema::hasColumn('leads', 'lead_quality')) {
                $table->string('lead_quality', 20)
                    ->nullable()
                    ->index();
            }

            /*
             * Eski tabloda project_type zorunluydu.
             * Yeni ConTech funnel bilgiyi project_data içinde
             * taşıyor. Bu nedenle alanı nullable yapıyoruz.
             */
            if (Schema::hasColumn('leads', 'project_type')) {
                $table->string('project_type')
                    ->nullable()
                    ->change();
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'source',
            'project_data',
            'estimated_cost',
            'estimated_min',
            'estimated_max',
            'budget_confirmed',
            'lead_quality',
        ];

        Schema::table('leads', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('leads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

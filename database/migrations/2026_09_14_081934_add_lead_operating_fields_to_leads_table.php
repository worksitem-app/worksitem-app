<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->ulid('public_id')->nullable()->unique();

            $table->unsignedTinyInteger('lead_score')
                ->default(0);

            $table->boolean('sales_ready')
                ->default(false);

            $table->string('phone_normalized', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 150)->nullable();
            $table->string('timeline', 30)->nullable();

            $table->string('utm_source', 150)->nullable();
            $table->string('utm_medium', 150)->nullable();
            $table->string('utm_campaign', 150)->nullable();
            $table->string('utm_content', 150)->nullable();
            $table->string('utm_term', 150)->nullable();

            $table->text('landing_page')->nullable();
            $table->text('referrer')->nullable();

            $table->string('price_version', 100)->nullable();
            $table->json('price_snapshot')->nullable();

            $table->boolean('contact_consent')
                ->default(false);

            $table->boolean('marketing_consent')
                ->default(false);

            $table->string('consent_version', 50)->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->string('ip_hash', 64)->nullable();

            $table->unsignedBigInteger('assigned_user_id')->nullable();

            $table->timestamp('first_contact_at')->nullable();
            $table->timestamp('next_action_at')->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('proposal_sent_at')->nullable();
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            $table->string('loss_reason', 255)->nullable();

            $table->index(
                ['sales_ready', 'status', 'created_at'],
                'leads_sales_pipeline_idx'
            );

            $table->index(
                ['lead_quality', 'created_at'],
                'leads_quality_created_idx'
            );

            $table->index(
                ['source', 'created_at'],
                'leads_source_created_idx'
            );

            $table->index(
                ['phone_normalized', 'created_at'],
                'leads_phone_created_idx'
            );

            $table->index(
                ['next_action_at', 'status'],
                'leads_next_action_idx'
            );

            $table->index(
                ['assigned_user_id', 'status'],
                'leads_assignment_idx'
            );
        });

        DB::table('leads')
            ->whereNull('public_id')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($lead): void {
                DB::table('leads')
                    ->where('id', $lead->id)
                    ->update([
                        'public_id' => (string) Str::ulid(),
                        'last_activity_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_sales_pipeline_idx');
            $table->dropIndex('leads_quality_created_idx');
            $table->dropIndex('leads_source_created_idx');
            $table->dropIndex('leads_phone_created_idx');
            $table->dropIndex('leads_next_action_idx');
            $table->dropIndex('leads_assignment_idx');
            $table->dropUnique('leads_public_id_unique');

            $table->dropColumn([
                'public_id',
                'lead_score',
                'sales_ready',
                'phone_normalized',
                'city',
                'district',
                'timeline',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_content',
                'utm_term',
                'landing_page',
                'referrer',
                'price_version',
                'price_snapshot',
                'contact_consent',
                'marketing_consent',
                'consent_version',
                'consent_at',
                'ip_hash',
                'assigned_user_id',
                'first_contact_at',
                'next_action_at',
                'qualified_at',
                'proposal_sent_at',
                'won_at',
                'lost_at',
                'last_activity_at',
                'loss_reason',
            ]);
        });
    }
};

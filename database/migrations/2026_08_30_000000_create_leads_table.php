<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Kimlik
            $table->ulid('public_id')->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('phone_normalized', 20)->nullable()->index();
            $table->string('email')->nullable();

            // Proje bilgisi
            $table->string('source', 100)->nullable()->index();
            $table->string('project_type')->nullable();
            $table->text('notes')->nullable();
            $table->json('project_data')->nullable();

            // Maliyet
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->decimal('estimated_min', 15, 2)->nullable();
            $table->decimal('estimated_max', 15, 2)->nullable();
            $table->boolean('budget_confirmed')->default(false)->index();

            // Lead scoring
            $table->unsignedTinyInteger('lead_score')->default(0);
            $table->string('lead_quality', 20)->nullable()->index();
            $table->boolean('sales_ready')->default(false);

            // Durum & konum
            $table->string('status', 30)->default('yeni')->index();
            $table->string('city', 100)->nullable();
            $table->string('district', 150)->nullable();
            $table->string('timeline', 30)->nullable();

            // UTM & referans
            $table->string('utm_source', 150)->nullable();
            $table->string('utm_medium', 150)->nullable();
            $table->string('utm_campaign', 150)->nullable();
            $table->string('utm_content', 150)->nullable();
            $table->string('utm_term', 150)->nullable();
            $table->text('landing_page')->nullable();
            $table->text('referrer')->nullable();

            // Fiyat snapshot
            $table->string('price_version', 100)->nullable();
            $table->json('price_snapshot')->nullable();

            // KVKK izinleri
            $table->boolean('contact_consent')->default(false);
            $table->boolean('marketing_consent')->default(false);
            $table->string('consent_version', 50)->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->string('ip_hash', 64)->nullable();

            // Atama & pipeline
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->timestamp('first_contact_at')->nullable();
            $table->timestamp('next_action_at')->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('proposal_sent_at')->nullable();
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->string('loss_reason', 255)->nullable();

            $table->timestamps();

            // Composite indeksler
            $table->index(['sales_ready', 'status', 'created_at'], 'leads_sales_pipeline_idx');
            $table->index(['lead_quality', 'created_at'], 'leads_quality_created_idx');
            $table->index(['source', 'created_at'], 'leads_source_created_idx');
            $table->index(['phone_normalized', 'created_at'], 'leads_phone_created_idx');
            $table->index(['next_action_at', 'status'], 'leads_next_action_idx');
            $table->index(['assigned_user_id', 'status'], 'leads_assignment_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

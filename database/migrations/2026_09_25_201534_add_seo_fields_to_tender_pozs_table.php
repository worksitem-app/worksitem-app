<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_pozs', function (Blueprint $table) {
            $table->text('long_description')->nullable()->after('description');
            $table->json('seo_data')->nullable()->after('long_description');
            $table->json('faq_data')->nullable()->after('seo_data');
            $table->string('meta_description', 500)->nullable()->after('faq_data');
            $table->string('meta_keywords', 500)->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('tender_pozs', function (Blueprint $table) {
            $table->dropColumn([
                'long_description',
                'seo_data',
                'faq_data',
                'meta_description',
                'meta_keywords',
            ]);
        });
    }
};

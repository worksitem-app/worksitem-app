<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sponsorun teknik föyü: sarfiyat oranlarımız föyle karşılaştırılıp tarih girildiğinde
 * Pro raporda "malzeme listesi … teknik föyüne göre kontrol edilmiştir" ibaresi çıkar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsors', function (Blueprint $t) {
            $t->string('tech_sheet_url', 500)->nullable()->after('logo_url');
            $t->date('tech_sheet_checked_on')->nullable()->after('tech_sheet_url');
        });
    }

    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $t) {
            $t->dropColumn(['tech_sheet_url', 'tech_sheet_checked_on']);
        });
    }
};

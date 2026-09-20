<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rayic_prices', function (Blueprint $table) {
            $table->id();

            // Kaynağın kurumu
            $table->string('source', 50)->default('YFK')->index();

            // Fiyat dönemi: örn. 2026-08
            $table->string('period', 7)->index();

            // Resmi poz numarası
            $table->string('poz_no', 50)->index();

            // Resmi poz açıklaması
            $table->text('description');

            // Birim: m2, m3, ton, adet vb.
            $table->string('unit', 30);

            // Rayiç fiyat
            $table->decimal('price', 15, 2);

            // Kaynak dokümanın adı
            $table->string('source_file')->nullable();

            // Kaynak dokümandaki sayfa
            $table->unsignedInteger('source_page')->nullable();

            // İsteğe bağlı kategori
            $table->string('category', 100)->nullable()->index();

            // Aktif kayıt mı?
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            // Aynı kurum + dönem + poz tekrar eklenmesin
            $table->unique(
                ['source', 'period', 'poz_no'],
                'rayic_prices_source_period_poz_unique'
            );

            // Sık kullanılacak sorgular
            $table->index(
                ['period', 'category'],
                'rayic_prices_period_category_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rayic_prices');
    }
};

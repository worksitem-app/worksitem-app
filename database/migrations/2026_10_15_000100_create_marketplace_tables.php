<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Teklif pazaryeri: firma vergi/fatura bilgileri, iş kolları, teklif talepleri, davetler, teklifler;
 * siparişlerde fatura takibi (e-fatura / e-arşiv).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('firms', function (Blueprint $t) {
            $t->string('legal_type', 10)->default('sirket')->after('type');       // sahis | sirket
            $t->string('legal_name', 200)->nullable()->after('legal_type');       // fatura unvanı
            $t->string('tax_office', 80)->nullable()->after('tax_no');
            $t->string('mersis_no', 20)->nullable()->after('tax_office');
            $t->string('kep', 150)->nullable()->after('mersis_no');
            $t->string('efatura', 10)->default('unknown')->after('kep');          // unknown | yes | no
            $t->string('invoice_city', 40)->nullable();
            $t->string('invoice_district', 60)->nullable();
            $t->string('invoice_address', 300)->nullable();
            $t->string('tax_doc_path', 200)->nullable();
            $t->timestamp('tax_verified_at')->nullable();
            $t->json('trades')->nullable();
            $t->json('subservices')->nullable();
            $t->json('districts')->nullable();
            $t->string('referrer', 40)->nullable()->index();
            $t->timestamp('last_invited_at')->nullable();
            $t->boolean('accepts_jobs')->default(true);
            $t->unsignedInteger('offers_count')->default(0);
            $t->unsignedInteger('matches_count')->default(0);
        });

        // Eski hizmet anahtarları → yeni iş kolları
        $map = ['banyo' => 'ince-insaat', 'mutfak' => 'ahsap-ve-mobilya', 'boya' => 'ince-insaat', 'seramik' => 'zemin-kaplama',
            'parke' => 'zemin-kaplama', 'alci' => 'ince-insaat', 'mantolama' => 'izolasyon', 'pvc' => 'aluminyum-pvc', 'cati' => 'cati',
            'elektrik' => 'elektrik', 'tesisat' => 'mekanik-tesisat', 'beton' => 'kaba-insaat', 'anahtar' => 'insaat-taahhut',
            'proje' => 'mimarlik-ve-muhendislik', 'malzeme' => 'insaat-taahhut', 'vitrifiye' => 'mekanik-tesisat'];
        foreach (DB::table('firms')->select('id', 'services')->get() as $f) {
            $old = json_decode((string) $f->services, true) ?: [];
            $trades = array_values(array_unique(array_filter(array_map(fn ($k) => $map[$k] ?? null, $old))));
            DB::table('firms')->where('id', $f->id)->update(['trades' => json_encode($trades)]);
        }

        Schema::create('job_requests', function (Blueprint $t) {
            $t->id();
            $t->string('public_id', 20)->unique();
            $t->string('token', 64);
            $t->string('tool_slug', 80)->nullable();
            $t->string('trade', 40)->index();
            $t->string('subservice', 150)->nullable();
            $t->string('title', 200);
            $t->text('summary')->nullable();
            $t->unsignedInteger('est_low')->nullable();
            $t->unsignedInteger('est_high')->nullable();
            $t->string('city', 40)->index();
            $t->string('district', 60);
            $t->string('name', 120);
            $t->string('phone', 20)->index();
            $t->string('email', 150)->nullable();
            $t->text('description')->nullable();
            $t->string('start_pref', 20)->default('belirsiz');
            $t->string('status', 20)->default('open')->index();   // open | matched | closed | expired
            $t->unsignedSmallInteger('invited_count')->default(0);
            $t->foreignId('matched_offer_id')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamp('terms_at')->nullable();
            $t->timestamps();
        });

        Schema::create('job_invites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('job_request_id')->index();
            $t->foreignId('firm_id')->index();
            $t->string('status', 20)->default('sent');            // sent | viewed | offered | declined
            $t->timestamp('viewed_at')->nullable();
            $t->timestamps();
            $t->unique(['job_request_id', 'firm_id']);
        });

        Schema::create('job_offers', function (Blueprint $t) {
            $t->id();
            $t->string('public_id', 20)->unique();
            $t->foreignId('job_request_id')->index();
            $t->foreignId('firm_id')->index();
            $t->unsignedInteger('amount');                         // KDV dahil toplam teklif, TL
            $t->unsignedSmallInteger('duration_days')->nullable();
            $t->string('start_when', 40)->nullable();
            $t->boolean('includes_material')->default(true);
            $t->text('note')->nullable();
            $t->string('status', 20)->default('submitted')->index(); // submitted | accepted | matched | not_selected | expired | withdrawn
            $t->decimal('fee_net', 10, 2)->nullable();
            $t->timestamp('accepted_at')->nullable();
            $t->timestamp('fee_due_at')->nullable();
            $t->timestamp('matched_at')->nullable();
            $t->foreignId('order_id')->nullable();
            $t->timestamps();
            $t->unique(['job_request_id', 'firm_id']);
        });

        Schema::table('orders', function (Blueprint $t) {
            $t->foreignId('firm_id')->nullable()->index();
            $t->string('invoice_no', 40)->nullable();
            $t->timestamp('invoiced_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offers');
        Schema::dropIfExists('job_invites');
        Schema::dropIfExists('job_requests');
        Schema::table('orders', fn (Blueprint $t) => $t->dropColumn(['firm_id', 'invoice_no', 'invoiced_at']));
        Schema::table('firms', fn (Blueprint $t) => $t->dropColumn(['legal_type', 'legal_name', 'tax_office', 'mersis_no', 'kep', 'efatura',
            'invoice_city', 'invoice_district', 'invoice_address', 'tax_doc_path', 'tax_verified_at', 'trades', 'subservices', 'districts',
            'referrer', 'last_invited_at', 'accepts_jobs', 'offers_count', 'matches_count']));
    }
};

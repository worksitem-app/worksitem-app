<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Sponsor;
use App\Models\SponsorApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GrowthPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sponsor_firma_kaydi_basvuru_ve_admin_taslak_kart(): void
    {
        Mail::fake();
        $this->get('/markalar-icin?arac=banyo-maliyet-hesaplayici')->assertOk()->assertSee('Sponsor firma kaydı')->assertSee('Seramik ve karo');
        $this->post('/markalar-icin', [
            'company' => 'Örnek Seramik', 'name' => 'Ayşe Yılmaz', 'contact_title' => 'Pazarlama Müdürü', 'phone' => '05321234567', 'email' => 'ayse@ornek.com',
            'website' => 'https://ornek.com', 'categories' => ['seramik', 'vitrifiye'], 'tools' => ['banyo-planlayici'], 'tool' => 'banyo-maliyet-hesaplayici',
            'budget' => 'k75', 'start' => 'ay', 'tax_no' => '1234567890', 'logo' => UploadedFile::fake()->image('logo.png', 200, 80), 'contact_consent' => '1',
        ])->assertRedirect(route('sponsor.page'))->assertSessionHas('sponsor_ok');
        $a = SponsorApplication::query()->firstOrFail();
        $this->assertSame(['banyo-planlayici', 'banyo-maliyet-hesaplayici'], $a->tools);
        $this->assertSame(['seramik', 'vitrifiye'], $a->categories);
        $this->assertTrue(Storage::disk('local')->exists($a->logo_path));
        Storage::disk('local')->delete($a->logo_path);
        $this->assertStringContainsString('Kategoriler: Seramik ve karo', (string) Lead::query()->find($a->lead_id)->notes);

        $this->post('/markalar-icin', ['company' => 'X', 'name' => 'Y Z', 'phone' => '05321234567', 'email' => 'a@b.com', 'tax_no' => '1234567891', 'contact_consent' => '1'])
            ->assertSessionHasErrors('tax_no');
        $this->post('/markalar-icin', ['website_url' => 'http://spam', 'company' => 'X'])->assertRedirect();   // bot tuzağı
        $this->assertSame(1, SponsorApplication::query()->count());

        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/sponsor-applications')->assertOk()->assertSee('Örnek Seramik');
        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Resources\SponsorApplicationResource\Pages\ListSponsorApplications::class)->callTableAction('draft', $a);
        $s = Sponsor::query()->firstOrFail();
        $this->assertFalse((bool) $s->active);
        $this->assertSame('banyo-planlayici', $s->tool_slug);
    }

    public function test_anasayfa_kitle_bandi_breadcrumb_sitemap_llms(): void
    {
        $this->get('/')->assertOk()->assertSee('Bütçemi ücretsiz hesapla')->assertSee('Bölgemdeki işleri al')->assertSee('Sponsor firma kaydı');
        $this->get('/araclar')->assertOk()->assertSee('Bölgemdeki işleri al');
        $this->get('/araclar/boya-hesaplayici')->assertSee('"BreadcrumbList"', false);
        $sm = $this->get('/sitemap.xml')->getContent();
        $this->assertStringContainsString('/markalar-icin', $sm);
        $this->assertStringContainsString('/hakkimizda', $sm);
        $llms = $this->get('/llms.txt')->assertOk()->getContent();
        $this->assertStringContainsString('## Kimler için?', $llms);
        $this->assertStringContainsString('Kurucu firma programında', $llms);
    }
}

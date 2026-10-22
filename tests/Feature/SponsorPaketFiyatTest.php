<?php

namespace Tests\Feature;

use App\Models\Sponsor;
use App\Services\Sponsors\SponsorLines;
use App\Services\Sponsors\SponsorPackages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Sunumdaki paket adları, araç sayısı ve fiyatlar koddakiyle aynı kalmalı (Kale Grubu sunumu, 23.10.2026). */
class SponsorPaketFiyatTest extends TestCase
{
    use RefreshDatabase;

    public function test_paketler_ve_fiyatlar_sunumla_ayni(): void
    {
        $this->assertSame(
            ['su-yalitimi', 'seramik-uygulama', 'endustriyel-zemin', 'boya-cephe', 'onarim-grout', 'tam-kategori', 'seramik-banyo'],
            array_keys(SponsorPackages::all())
        );
        $this->assertCount(5, SponsorPackages::families());
        $tam = SponsorPackages::get('tam-kategori');
        $this->assertTrue($tam['exclusive']);
        $this->assertCount(16, $tam['tools']);                       // sunum: "16 araç"
        $this->assertArrayNotHasKey('banyo-planlayici', $tam['tools']); // ek paket Tam Kategori'ye girmez
        $this->assertContains('montaj', $tam['tools']['pvc-dograma-hesaplayici']);

        foreach (array_keys(SponsorPackages::families()) as $k) {
            $this->assertSame(18000, SponsorPackages::monthly($k), $k);
        }
        $this->assertSame(55000, SponsorPackages::monthly('tam-kategori'));
        $this->assertSame(20000, SponsorPackages::monthly('seramik-banyo'));
        $this->assertSame(['package' => 'tam-kategori', 'months' => 6, 'monthly' => 45000], SponsorPackages::founding());
        $this->assertSame(3, SponsorPackages::pilotMonths());
        $this->assertSame('₺55.000', SponsorPackages::tl(55000));
    }

    public function test_tam_kategori_munhasir_baska_marka_gostermez(): void
    {
        $this->artisan('sponsors:package', ['package' => 'seramik-uygulama', '--brand' => 'Diger Marka', '--url' => 'https://example.com', '--active' => true])->assertSuccessful();
        $line = SponsorLines::for('seramik-maliyet-hesaplayici', ['yapistirici'], []);
        $this->assertSame('Diger Marka', $line['yapistirici']['brand']);

        $this->artisan('sponsors:package', ['package' => 'tam-kategori', '--brand' => 'Kurucu Marka', '--url' => 'https://example.com', '--active' => true])->assertSuccessful();
        $this->assertSame(SponsorPackages::slots(SponsorPackages::get('tam-kategori')), Sponsor::where('notes', 'like', '%'.Sponsor::EXCLUSIVE_MARK.'%')->count());

        // Diğer marka daha öncelikli olsa bile münhasır kategoride görünmez
        Sponsor::where('brand', 'Diger Marka')->update(['priority' => 1]);
        $line = SponsorLines::for('seramik-maliyet-hesaplayici', ['yapistirici'], []);
        $this->assertSame('Kurucu Marka', $line['yapistirici']['brand']);

        $html = $this->get('/araclar/seramik-maliyet-hesaplayici')->assertOk()->getContent();
        $this->assertStringNotContainsString('Diger Marka', $html);
    }

    public function test_markalar_sayfasi_fiyatlari_gosterir(): void
    {
        $this->get('/markalar-icin')->assertOk()
            ->assertSee('Tam Kategori')->assertSee('₺55.000 / ay', false)->assertSee('₺18.000 / ay', false)
            ->assertSee('Seramik ve Banyo')->assertSee('kategoride tek marka')->assertSee('Kurucu sponsor');
    }

    public function test_demo_seramik_markasi_ek_pakette(): void
    {
        $this->artisan('sponsors:demo', ['--brand' => 'Kimya Demo', '--url' => 'https://example.com', '--tile-brand' => 'Karo Demo', '--tile-url' => 'https://example.org'])->assertSuccessful();
        $this->assertSame(SponsorPackages::slots(SponsorPackages::get('seramik-banyo')), Sponsor::where('brand', 'Karo Demo')->count());
        $this->assertSame(SponsorPackages::slots(SponsorPackages::get('tam-kategori')), Sponsor::where('brand', 'Kimya Demo')->count());
        $this->assertSame(0, Sponsor::where('brand', 'Karo Demo')->where('notes', 'like', '%'.Sponsor::EXCLUSIVE_MARK.'%')->count());
    }
}

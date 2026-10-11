<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Models\TenderPoz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPozImportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function upload(string $csv)
    {
        return $this->actingAs($this->admin())->post('/admin/poz-import', [
            'file' => UploadedFile::fake()->createWithContent('pozlar.csv', $csv),
            'institution' => 'ÇŞİDB',
            'year' => '2026',
        ]);
    }

    public function test_virgul_ayracli_csv_ice_aktarilir(): void
    {
        // Eski kod: ';' ile okuyup tek kolon görünce satırı atlıyordu → 0 kayıt.
        $this->upload("poz,tanim,birim,fiyat\n15.150.1001,Beton,m3,1250.50\n")->assertRedirect();

        $this->assertSame(1250.50, (float) TenderPoz::where('poz_no', '15.150.1001')->value('unit_price'));
    }

    public function test_noktali_virgul_ve_turkce_fiyat_formati(): void
    {
        $this->upload("poz;tanim;birim;fiyat\n15.150.1002;Demir;ton;28.400,75 TL\n")->assertRedirect();

        $this->assertSame(28400.75, (float) TenderPoz::where('poz_no', '15.150.1002')->value('unit_price'));
    }

    public function test_ayni_dosya_iki_kez_yuklenince_cogalmaz(): void
    {
        $csv = "poz;tanim;birim;fiyat\n15.150.1003;Sıva;m2;100,00\n";
        $this->upload($csv);
        $this->upload(str_replace('100,00', '120,00', $csv));

        $this->assertSame(1, TenderPoz::where('poz_no', '15.150.1003')->count());
        $this->assertSame(120.0, (float) TenderPoz::where('poz_no', '15.150.1003')->value('unit_price'));
    }

    public function test_admin_olmayan_kullanici_import_edemez(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post('/admin/poz-import', [])
            ->assertForbidden();
    }

    public function test_eski_admin_ekranlari_admin_icin_acilir(): void
    {
        $admin = $this->admin();

        foreach (['/admin/fiyatlar', '/admin/talepler', '/admin/poz-import', '/admin/ai-fiyat-guncelle', '/admin/rayic', '/admin/rayic/yeni'] as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }
    }

    #[DataProvider('prices')]
    public function test_fiyat_ayristirma(string $raw, ?float $expected): void
    {
        $this->assertSame($expected, AdminController::parseTurkishPrice($raw));
    }

    public static function prices(): array
    {
        return [
            'TR binlik+ondalık' => ['1.250,50', 1250.50],
            'EN ondalık (eski kod 125050 yazıyordu)' => ['1250.50', 1250.50],
            'EN binlik+ondalık' => ['1,250.50', 1250.50],
            'TL sembolü' => ['₺ 3.000', 3000.0],
            'TL soneki' => ['45,9 TL', 45.9],
            'TR çoklu binlik' => ['1.250.000', 1250000.0],
            'boş' => ['', null],
            'metin' => ['fiyat yok', null],
        ];
    }
}

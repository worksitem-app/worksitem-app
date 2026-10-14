<?php

namespace Tests\Feature;

use App\Support\CompanyIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyIdentityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'worksitem_company.legal_name' => 'Kainta İnşaat',
            'worksitem_company.mersis' => '0123456789012345',
            'worksitem_company.tax_office' => 'Ümraniye',
            'worksitem_company.tax_no' => '9876543210',
            'worksitem_company.kep' => 'kainta@hs01.kep.tr',
            'worksitem_company.mask_public' => true,
        ]);
    }

    public function test_yasal_sayfalarda_kimlik_numaralari_maskeli(): void
    {
        foreach (['/legal/kvkk-aydinlatma', '/legal/kesif-on-bilgilendirme-formu', '/legal/kesif-mesafeli-hizmet-sozlesmesi'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringNotContainsString('9876543210', $html, $url);
            $this->assertStringNotContainsString('0123456789012345', $html, $url);
            $this->assertStringNotContainsString('kainta@hs01.kep.tr', $html, $url);
            $this->assertStringContainsString('***** (talep edilmesi halinde verilecektir)', $html, $url);
        }
    }

    public function test_alici_belgesi_tam_kunye_ve_kapatilabilir(): void
    {
        $this->assertSame('9876543210', CompanyIdentity::display(full: true)['tax_no']);
        $this->assertSame('Ümraniye / 9876543210', CompanyIdentity::display(full: true)['tax_line']);

        config(['worksitem_company.mask_public' => false]);
        $this->assertStringContainsString('9876543210', $this->get('/legal/kvkk-aydinlatma')->getContent());
    }

    public function test_marka_metinleri(): void
    {
        $this->assertStringContainsString("env('COMPANY_LEGAL_NAME', 'Kainta İnşaat')", file_get_contents(config_path('worksitem_company.php')));
        $html = $this->get('/legal/kvkk-aydinlatma')->getContent();
        $this->assertStringNotContainsString('Mimarlık & Mühendislik', $html);
        $this->assertStringNotContainsString('Mimarlık &amp; Mühendislik', $html);
        $this->assertStringContainsString('Uzmanımız', (string) config('worksitem_sms.lead_received.text'));
    }
}

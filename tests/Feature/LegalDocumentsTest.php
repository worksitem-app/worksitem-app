<?php

namespace Tests\Feature;

use App\Support\LeadTextGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Logolu yasal belge şablonu, sahte ad/konum engeli, önbellek kırıcı dosya adresleri */
class LegalDocumentsTest extends TestCase
{
    use RefreshDatabase;

    public static function legalPages(): array
    {
        return [
            ['/legal/kullanim-kosullari', 'Kullanım Koşulları'],
            ['/legal/kvkk-aydinlatma', 'KVKK Aydınlatma Metni'],
            ['/legal/acik-riza', 'Açık Rıza Metni'],
            ['/legal/kvkk-basvuru', 'KVKK Başvuru Formu'],
            ['/legal/gizlilik-politikasi', 'Gizlilik Politikası'],
            ['/legal/cerez-politikasi', 'Çerez Politikası'],
            ['/legal/kesif-on-bilgilendirme-formu', 'Keşif Ön Bilgilendirme Formu'],
            ['/legal/kesif-mesafeli-hizmet-sozlesmesi', 'Keşif Mesafeli Hizmet Sözleşmesi'],
        ];
    }

    #[DataProvider('legalPages')]
    public function test_yasal_sayfalar_logolu_belge_sablonunda(string $url, string $title): void
    {
        $html = $this->get($url)->assertOk()->getContent();

        $this->assertStringContainsString('brand/worksitem-logo.png', $html);
        $this->assertStringContainsString('class="lg-paper"', $html);
        $this->assertStringContainsString('<h1>'.e($title).'</h1>', $html);
        $this->assertStringContainsString('Yasal merkez', $html);
        $this->assertStringContainsString('"BreadcrumbList"', $html);
        $this->assertStringContainsString('<link rel="canonical" href="'.url($url).'">', $html);
        $this->assertMatchesRegularExpression('/<h2 id="[a-z0-9-]+"/', $html);
        $this->assertSame(1, substr_count($html, 'class="wks-footer'));
    }

    public function test_cerez_ayarlari_dugmesi_korunur(): void
    {
        $this->get('/legal/cerez-politikasi')->assertSee('openCookieSettings', false);
    }

    public static function fakeInputs(): array
    {
        return [['lkjnhbv'], ['fkfkdfgf'], ['asdasd qwe'], ['Test Test'], ['aaaa bbbb'], ['Ali'], ['qwerty asdf']];
    }

    #[DataProvider('fakeInputs')]
    public function test_sahte_ad_soyad_reddedilir(string $name): void
    {
        $this->postJson('/lead-kaydet', ['name' => $name, 'phone' => '0532 123 45 67', 'contact_consent' => true])
            ->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    public function test_sahte_konum_reddedilir_gercek_kabul_edilir(): void
    {
        $this->postJson('/lead-kaydet', ['name' => 'Ayşe Yılmaz', 'phone' => '0532 123 45 67', 'city' => 'fkfkdfgf', 'contact_consent' => true])
            ->assertStatus(422)->assertJsonValidationErrors(['city']);
        $this->postJson('/lead-kaydet', ['name' => 'Ayşe Yılmaz', 'phone' => '0532 123 45 68', 'location' => 'İzmir, Karşıyaka', 'contact_consent' => true])
            ->assertStatus(201);
    }

    public function test_gercek_isim_ve_81_il_yanlislikla_engellenmez(): void
    {
        foreach (['Şükrü Kırkpınar', 'Mehmet Çağrı Öztürk', 'Hans Schmidt', 'Ozan Türk', 'Halil İbrahim Kurt'] as $n) {
            $this->assertTrue(LeadTextGuard::isValidFullName($n), $n);
        }
        foreach (config('worksitem_lead_guard.cities') as $il) {
            $this->assertFalse(LeadTextGuard::looksFake($il), $il);
        }
    }

    public function test_js_ve_css_adresleri_icerik_ozetli(): void
    {
        $html = $this->get('/araclar/banyo-maliyet-hesaplayici')->getContent();
        $this->assertMatchesRegularExpression('#/js/ws-tool-extras\.js\?v=[0-9a-f]{10}#', $html);
        $this->assertStringContainsString('<style id="ws-brand-css">', $html);
        $this->assertMatchesRegularExpression('#/fonts/inter/inter-latin-wght-normal\.woff2\?v=[0-9a-f]{10}#', $html);
    }
}

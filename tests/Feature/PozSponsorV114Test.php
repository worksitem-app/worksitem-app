<?php

namespace Tests\Feature;

use App\Models\Sponsor;
use App\Models\TenderPoz;
use App\Services\Sponsors\SponsorPackages;
use App\Support\PozCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v114: 40 ürün grubu kuralı, parça tanımda grup başlığı, kayıtlı eşleşme, üst grup sponsorları, marka tekilliği, rapor/CSV */
class PozSponsorV114Test extends TestCase
{
    use RefreshDatabase;

    private function poz(string $no, string $desc, string $inst = 'ÇŞİDB/YFK'): TenderPoz
    {
        return TenderPoz::query()->create([
            'year' => '2026', 'institution' => $inst, 'poz_no' => $no, 'description' => $desc,
            'unit' => 'm²', 'unit_price' => 100, 'is_active' => true,
        ]);
    }

    private function sponsor(string $brand, string $cat, int $priority = 10): Sponsor
    {
        return Sponsor::query()->create([
            'brand' => $brand, 'tool_slug' => 'poz-sayfalari', 'category' => $cat, 'headline' => $brand.' ürünü',
            'cta_label' => 'Gör', 'target_url' => 'https://example.com', 'priority' => $priority, 'active' => true,
        ]);
    }

    public function test_urun_gruplari_gercek_poz_metinleriyle(): void
    {
        $c = fn (string $d, ?string $ctx = null) => PozCatalog::sponsorCategories($d, $ctx);

        $this->assertSame(['yalitim_bitum'], $c('3 mm kalınlıkta elastomer esaslı polyester keçe taşıyıcılı polimer bitümlü örtü ile su yalıtımı yapılması'));
        $this->assertSame(['yalitim_poliurea'], $c('Hibrit Poliürea bazlı iki bileşenli su yalıtım malzemesi ile 2 mm kalınlıkta su yalıtımı yapılması'));
        $this->assertSame(['yalitim_cimento'], $c('ÇİMENTO ESASLI TEK BİLEŞENLİ KRİSTALİZE SU YALITIM HARCI İLE 2 KAT'));   // büyük harf + Türkçe İ
        $this->assertSame(['yapistirici', 'derz', 'seramik'], $c('Seramik Yer Karoları İle Döşeme Kaplaması Yapılması İşleri (30 x 30 cm) beyaz seramik yer karoları ile 3 mm derz aralıklı döşeme kaplaması yapılması (karo yapıştırıcısı ile)'));
        $this->assertSame(['derz_reaktif'], $c('Reaksiyon reçine esaslı (iki veya daha fazla bileşenli) derz dolgusu'));
        $this->assertSame(['boya_ic'], $c('Su bazlı Silikonlu İpek Mat İç Cephe Boyası (Örtücülük Sınıf:2)'));
        $this->assertSame(['mantolama', 'isi_yalitim'], $c('EPS ile Mantolama Yapılması 5 cm kalınlıkta yüzeye dik çekme mukavemeti en az 100 kPa EPS levhalar ile'));
        $this->assertSame(['sap'], $c('Çimento esaslı kendiliğinden yerleşen (self leveling) harç ile ortalama 2 mm kalınlıkta zemin tesviyesi yapılması'));
        $this->assertSame(['sertlestirici'], $c('Korund agregalı yüzey sertleştirici (Gri)'));
        $this->assertSame(['grout'], $c('Çimento Esaslı Döküm Harcı (Grout) (TS EN 1504-3)'));

        // Karıştırılmaması gerekenler
        $this->assertSame([], $c('Her uzunlukta Ø 60 cm jet grout kolonu imalatı yapılması'));
        $this->assertSame([], $c('Derz dolgu maddeleri - Kürleşme oranının tayini'));          // laboratuvar
        $this->assertSame([], $c('Fayans kaplama ustası'));                                     // işçilik rayici
        $this->assertSame([], $c('Grout karma ve harç enjeksiyon pompa makinesinin 1 saatlik ücreti'));
        $this->assertSame([], $c('Bitümlü sıcak karışım aşınma tabakası yapılması'));             // yol asfaltı
        $this->assertSame([], $c('Gaz yakıtlı seramik radyant ısıtıcı'));
        $this->assertSame([], $c('KOLON MANTOLAMA DEMİRLERİNİN KAYNAKLA MEVCUT DEMİRE BAĞLANMASI'));

        // Parça tanım grup başlığından anlam alır
        $this->assertTrue(PozCatalog::isFragment('(40 x 40 cm) anma ebatlarında'));
        $this->assertSame(['yalitim_bitum'], $c('3.3 mm bir yüzü kırmızı reflektif mineral kaplı', 'Bitüm muhtevası en az 1300 g/m² olan plastomer APP modifiyeli bitümlü örtü'));
    }

    public function test_komut_grup_basligiyla_eslestirip_kaydeder_ve_csv_yazar(): void
    {
        $this->poz('10.330.5101', 'Bitüm muhtevası en az 1300 g/m² olan plastomer APP modifiyeli bitümlü örtü 3 mm');
        $this->poz('10.330.5105', '3.3 mm bir yüzü kırmızı reflektif mineral kaplı');
        $this->poz('15.100.1001', 'Kazı yapılması');
        $csv = tempnam(sys_get_temp_dir(), 'esle');

        $this->artisan('sponsors:poz-esle', ['--rapor' => true, '--csv' => $csv])
            ->expectsOutputToContain('3 aktif poz · 2 pozda en az bir ürün grubu')->assertSuccessful();

        $this->assertSame('yalitim_bitum', TenderPoz::where('poz_no', '10.330.5105')->value('sponsor_categories'));
        $this->assertSame('', TenderPoz::where('poz_no', '15.100.1001')->value('sponsor_categories'));
        $out = file_get_contents($csv);
        $this->assertStringContainsString('10.330.5105', $out);
        $this->assertStringContainsString('Bitümlü membran ve örtüler', $out);
        $this->assertStringNotContainsString('15.100.1001', $out);

        // ikinci çalıştırma yalnız değişeni yazar
        $this->artisan('sponsors:poz-esle')->expectsOutputToContain('0 kayıt güncellendi')->assertSuccessful();
    }

    public function test_sayfada_kayitli_eslesme_ust_grup_sponsoru_ve_tek_marka(): void
    {
        $this->poz('10.330.5101', 'Bitüm muhtevası en az 1300 g/m² olan plastomer APP modifiyeli bitümlü örtü 3 mm');
        $p = $this->poz('10.330.5105', '3.3 mm bir yüzü kırmızı reflektif mineral kaplı');
        $this->artisan('sponsors:poz-esle')->assertSuccessful();

        $this->sponsor('Genel Yalıtım', 'yalitim');            // üst grup sponsoru alt grupta da görünür
        $this->sponsor('Membran Marka', 'yalitim_bitum');
        $this->sponsor('Membran Marka', 'yalitim');             // aynı marka iki kategoride → tek kart

        $html = $this->get('/poz/10.330.5105')->assertOk()->getContent();
        $this->assertStringContainsString('Genel Yalıtım', $html);
        $this->assertSame(1, substr_count($html, '>Membran Marka ürünü<'));
        $this->assertStringContainsString('Sponsorlu', $html);
    }

    public function test_paketler_tum_urun_gruplarini_kapsar_isi_yalitim_haric(): void
    {
        $covered = [];
        foreach (SponsorPackages::all() as $p) {
            $covered = array_merge($covered, $p['tools']['poz-sayfalari'] ?? []);
        }
        $all = array_keys((array) config('worksitem_sponsors.poz_matching'));
        $this->assertSame(['isi_yalitim'], array_values(array_diff($all, $covered)));
        $this->assertSame($all, array_keys((array) config('worksitem_sponsors.tools.poz-sayfalari.categories')));
    }
}

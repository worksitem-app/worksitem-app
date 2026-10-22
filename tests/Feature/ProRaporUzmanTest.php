<?php

namespace Tests\Feature;

use App\Estimators\KitRegistry;
use App\Models\Sponsor;
use App\Services\FxRates;
use App\Services\Sponsors\SponsorLines;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/** Pro rapor: saat, döviz, sponsorlu eşdeğer ürün, teknik föy ibaresi, uzman notları */
class ProRaporUzmanTest extends TestCase
{
    use RefreshDatabase;

    private const IN = ['surface' => 'zemin', 'floor' => '5', 'wall' => '2', 'size' => '30x60', 'quality' => 'ekonomik', 'scope' => 'hazir', 'grout' => 'epoksi', 'extras' => ['sap', 'yalitim', 'seviye', 'profil', 'supurgelik', 'temizlik']];

    private function render(bool $pdf = false): string
    {
        $e = KitRegistry::get('seramik-maliyet-hesaplayici');
        $in = $e->normalize(self::IN);
        $content = $e->proReport($in);
        $data = $content + [
            'e' => $e, 'page' => $e->page(), 'fx' => app(FxRates::class)->current(),
            'sponsors' => collect(), 'logo' => null, 'pricesLabel' => $e->pricesLabel(), 'reportNo' => 'WS-PRO-TEST',
            'equivalents' => SponsorLines::forMaterials($e->slug(), $content['materials']),
            'issuedAt' => now('Europe/Istanbul')->setTime(13, 8),
        ];
        if ($pdf && ($out = getenv('PRO_PDF_OUT'))) {
            file_put_contents($out, Pdf::loadView('reports.kit-pro-pdf', $data)->setPaper('a4')->output());
        }

        return view('reports.kit-pro-pdf', $data)->render();
    }

    public function test_saat_doviz_ve_uzman_notlari(): void
    {
        Cache::put(FxRates::CACHE_KEY, ['date' => '22.10.2026', 'rates' => ['USD' => 42.1, 'EUR' => 48.9, 'GBP' => 56.2]], 60);
        $html = $this->render();
        $this->assertStringContainsString('saat 13:08', $html);
        $this->assertStringContainsString('<b>USD</b> $', $html);
        $this->assertStringContainsString('<b>GBP</b> £', $html);
        $this->assertStringContainsString('Uzman notları', $html);
        $this->assertStringContainsString('1/3', $html);                 // 30×60 şaşırtma
        $this->assertStringContainsString('göllendirme testi', $html);
        $this->assertStringContainsString('Bu raporla ne yapmalı?', $html);
        $this->assertStringNotContainsString('Sponsorlu eşdeğer', $html);  // sponsor yokken çıkmaz
    }

    public function test_sponsorlu_esdeger_ve_teknik_foy_ibaresi(): void
    {
        Cache::put(FxRates::CACHE_KEY, ['date' => '22.10.2026', 'rates' => ['USD' => 42.1, 'EUR' => 48.9, 'GBP' => 56.2]], 60);
        $this->artisan('sponsors:demo', ['--brand' => 'Kalekim', '--url' => 'https://www.kalekim.com', '--tile-brand' => 'Kale', '--tile-url' => 'https://www.kale.com.tr'])->assertSuccessful();
        $html = $this->render(true);
        $this->assertStringContainsString('Sponsorlu eşdeğer', $html);
        $this->assertStringContainsString('Kalekim · Karo yapıştırıcısı', $html);
        $this->assertStringContainsString('Kale · Seramik ve porselen karo', $html);
        $this->assertStringContainsString('Örnek görünüm (staging)', $html);   // föy yok, demo: yalnız örnek kutusu
        $this->assertStringNotContainsString('Teknik föy kontrolü', $html);

        // Föyle karşılaştırma tarihi girilince gerçek ibare çıkar
        Sponsor::where('brand', 'Kalekim')->update(['tech_sheet_checked_on' => '2026-10-20', 'notes' => 'Paket: Tam Kategori '.Sponsor::EXCLUSIVE_MARK]);
        $html = $this->render();
        $this->assertStringContainsString('Teknik föy kontrolü', $html);
        $this->assertStringContainsString('Kalekim teknik föyüyle karşılaştırılmıştır (20.10.2026)', $html);
    }
}

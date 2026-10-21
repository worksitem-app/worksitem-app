<?php

namespace Tests\Feature;

use App\Services\KaintaCostEngine;
use App\Models\TenderPoz;
use App\Services\Solar\SolarPlanService;
use App\Services\TenderPricingService;
use Database\Seeders\BuildingPriceSeeder;
use Database\Seeders\BuildingTypeSeeder;
use Database\Seeders\PricingSettingSeeder;
use Database\Seeders\RenovationPriceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * v87 — Hesap motoru dışındaki PHP para yolları için altın değerler (brick/money öncesi kilit).
 * Banyo hesaplayıcısının kalem kalem değerleri zaten BathroomCalculatorTest'te.
 */
class MoneyPathsGoldenTest extends TestCase
{
    use RefreshDatabase;

    // ── Ana sayfa yapı maliyeti: m² × sınıf birim maliyeti × piyasa çarpanı ──

    public function test_yapi_maliyeti_ucu(): void
    {
        $this->seed([BuildingPriceSeeder::class, BuildingTypeSeeder::class, PricingSettingSeeder::class]);

        // elle: 250 m² × 15.100 (2C) × 1,60 = 6.040.000; aralık ±%10; dağılım %34/27/21/18
        $r = $this->postJson('/hesaplama/yapi-maliyeti', ['building_type_key' => 'konut_kirsal_200_alti', 'area' => 250])->assertOk()->json();
        $this->assertSame([5436000, 6040000, 6644000], [$r['estimate_low'], $r['total'], $r['estimate_high']]);
        $this->assertSame([2053600, 1630800, 1268400, 1087200], array_column($r['budget_breakdown']['items'], 'amount'));
        $this->assertSame($r['total'], array_sum(array_column($r['budget_breakdown']['items'], 'amount')));

        $r = $this->postJson('/hesaplama/yapi-maliyeti', ['building_type_key' => 'konut_apartman_3_kata_kadar', 'area' => 250])->assertOk()->json();
        $this->assertSame([7128000, 7920000, 8712000], [$r['estimate_low'], $r['total'], $r['estimate_high']]);
    }

    // ── Ana sayfa tadilat maliyeti: m² × kapsam birim fiyatı × kalite; ±%20 ──

    public static function renovation(): array
    {
        return [
            'kısmi' => ['partial', [468000, 585000, 702000]],      // elle: 90 × 6.500
            'orta' => ['medium', [684000, 855000, 1026000]],       // 90 × 9.500
            'komple' => ['complete', [972000, 1215000, 1458000]],  // 90 × 13.500
        ];
    }

    #[DataProvider('renovation')]
    public function test_tadilat_maliyeti_ucu(string $scope, array $range): void
    {
        $this->seed([RenovationPriceSeeder::class, PricingSettingSeeder::class]);
        $r = $this->postJson('/hesaplama/tadilat-maliyeti', ['scope' => $scope, 'quality' => 'standard', 'area' => 90])->assertOk()->json();
        $this->assertSame($range, [$r['estimate_low'], $r['total'], $r['estimate_high']]);
    }

    // ── /maliyet-hesapla proje sonucu (KaintaCostEngine) ──

    public static function kainta(): array
    {
        // [tip, m², kalite, şehir] → [toplam, birim, malzeme, işçilik, genel gider, kâr]
        return [
            'komple ankara' => [['komple', 100, 'standart', 'Ankara'], [1200000, 12000, 540000, 420000, 120000, 120000]],
            'hafif istanbul' => [['hafif', 60, 'standart', 'İstanbul'], [241500, 4025, 108675, 84525, 24150, 24150]],   // 3.500 × 1,15
            'lüks izmir' => [['lüks', 140, 'lüks', 'İzmir'], [3402000, 24300, 1530900, 1190700, 340200, 340200]],
            'bina' => [['bina inşaat', 500, 'standart', 'Genel'], [10525000, 21050, 4736250, 3683750, 1052500, 1052500]],
            'varsayılan' => [['banyo', 8, 'ekonomik', 'Genel'], [60000, 7500, 27000, 21000, 6000, 6000]],
        ];
    }

    #[DataProvider('kainta')]
    public function test_kainta_proje_maliyeti(array $args, array $expected): void
    {
        $r = (new KaintaCostEngine())->calculate(...$args);
        $got = [$r['hesaplanan_maliyet_tl'], $r['unit_price'], ...array_values($r['ana_kalemler'])];
        // Bugün float kayması var (ör. 241.499,99999999997); brick/money ile tam sayıya oturmalı
        foreach ($expected as $i => $v) {
            $this->assertEqualsWithDelta($v, $got[$i], 0.01, implode(' ', $args).' #'.$i);
        }
        $this->assertEqualsWithDelta($got[0], array_sum(array_slice($got, 2)), 0.01, 'kalemler toplamı tutmalı');
    }

    // ── GES planlayıcı ekonomisi ──

    public function test_ges_konut(): void
    {
        $r = app(SolarPlanService::class)->economics(7.7, 'konut', false, 11088, 600, 3.5);
        $this->assertEquals(['Güneş paneli' => 77000, 'İnverter' => 46200, 'Konstrüksiyon' => 27000, 'Kablo, koruma ve montaj' => 30800, 'Proje, başvuru ve kabul' => 15000], $r['items']);
        $this->assertEquals([175000, 206000, 247000], [$r['low'], $r['total'], $r['high']]);
        $this->assertEquals(25200, $r['saving']);     // elle: 7.200 kWh tüketim × 3,5 TL
        $this->assertEquals(8.2, $r['payback']);
        $this->assertEquals(100, $r['coverage']);
        $this->assertEquals(3888, $r['excess']);      // 11.088 − 7.200
        $this->assertEquals(4.9, $r['co2']);
    }

    public function test_ges_fabrika_duz_cati_bataryali(): void
    {
        $r = app(SolarPlanService::class)->economics(120, 'fabrika', true, 165000, 15000, 4.2, true);
        $this->assertEquals(['Güneş paneli' => 1056000, 'İnverter' => 633600, 'Konstrüksiyon' => 425000, 'Kablo, koruma ve montaj' => 422400, 'Proje, başvuru ve kabul' => 45000, 'Batarya (240 kWh)' => 2534400, 'Otomatik güç kontrolü ve enerji kesme sistemi' => 150000], $r['items']);
        $this->assertEquals([4700000, 5530000, 6636000], [$r['low'], $r['total'], $r['high']]);
        $this->assertEquals(693000, $r['saving']);    // elle: 165.000 kWh × 4,2
        $this->assertEquals(8.0, $r['payback']);
        $this->assertEquals(92, $r['coverage']);
    }

    public function test_ges_arazi(): void
    {
        $r = app(SolarPlanService::class)->economics(1000, 'arazi', false, 1450000, 0, 3.0, false, ['area' => 12000, 'perimeter' => 460]);
        $this->assertEquals(480000, $r['items']['Saha hazırlığı ve tesviye (12.000 m²)']);   // elle: 12.000 × 40
        $this->assertEquals(690000, $r['items']['Çevre çiti ve giriş kapısı (460 m)']);       // 460 × 1.500
        $this->assertEquals(400000, $r['items']['Kamera, aydınlatma ve topraklama']);         // 1.000 kWp × 400
        $this->assertEquals([19376000, 22796000, 27355000], [$r['low'], $r['total'], $r['high']]);
        $this->assertEquals(4350000, $r['saving']);
        $this->assertEquals(5.2, $r['payback']);
        $this->assertNull($r['coverage']);
    }

    // ── Poz / ihale teklif fiyatı (kuruş hassasiyetli) ──

    public static function tender(): array
    {
        return [
            // elle: (1.000 + 500) / (1 − 0,60) = 3.750; × 12,5
            'kâr hariç' => [['material_price' => 1000, 'installation_price' => 500, 'profit_status' => 'excluded'], 12.5, [1500.0, 1500.0, 3750.0, 18750.0, 18750.0, 46875.0]],
            // elle: 1.180,50 / 1,25 = 944,40 → / 0,4 = 2.361; × 3
            'kâr dahil %25' => [['unit_price' => 1180.50, 'profit_status' => 'included', 'included_profit_rate' => 0.25], 3, [1180.5, 944.4, 2361.0, 3541.5, 2833.2, 7083.0]],
            // 333,33 / 0,4 = 833,325 → 833,33 (yarım yukarı); toplam yuvarlanmamış birimden: 5.833,275 → 5.833,28
            'kuruş sınırı' => [['unit_price' => 333.33, 'profit_status' => 'excluded'], 7, [333.33, 333.33, 833.33, 2333.31, 2333.31, 5833.28]],
        ];
    }

    #[DataProvider('tender')]
    public function test_ihale_teklif_fiyati(array $poz, float $qty, array $expected): void
    {
        $r = app(TenderPricingService::class)->calculate((new TenderPoz())->forceFill($poz + ['vat_included' => false]), $qty);
        $this->assertSame('priced', $r['status']);
        $this->assertSame(0.60, $r['target_margin']);
        $this->assertSame($expected, [$r['official_unit_price'], $r['normalized_unit_cost'], $r['offer_unit_price'], $r['official_total'], $r['cost_total'], $r['offer_total']]);
    }

    public function test_ihale_kar_durumu_bilinmiyorsa_fiyat_verilmez(): void
    {
        $r = app(TenderPricingService::class)->calculate((new TenderPoz())->forceFill(['unit_price' => 500, 'profit_status' => 'unknown']), 2);
        $this->assertSame('requires_review', $r['status']);
        $this->assertNull($r['offer_total']);
        $this->assertSame(1000.0, $r['official_total']);
    }
}

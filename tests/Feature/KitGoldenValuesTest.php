<?php

namespace Tests\Feature;

use App\Estimators\KitEstimator;
use App\Estimators\KitRegistry;
use App\Estimators\PozEstimator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * v87 — Hesap motoru altın değerleri (bilinen girdi → beklenen TL).
 *
 * brick/money'ye (kuruş hassasiyeti) geçmeden önce bugünkü sonuçları kilitler:
 * geçişten sonra aynı girdiler kuruşu kuruşuna aynı kalem, alt/orta/üst ve birim fiyatı vermeli.
 *
 * Fiyatlar: config varsayılanları + migration'ların yazdığı onaylı fiyatlar + aşağıdaki örnek YFK kayıtları.
 * Bir fiyatı BİLEREK değiştirdiysen (config veya migration) ilgili satırı yeni değerle güncelle;
 * fiyat değişmediği halde sayı değiştiyse bu bir hesap hatasıdır.
 * "elle:" yorumlu satırlar formülden elle doğrulandı, diğerleri 21.10.2026 v86 çıktısıdır.
 */
class KitGoldenValuesTest extends TestCase
{
    use RefreshDatabase;

    public static function cases(): array
    {
        return [
            // elle: söküm 3.500+3,6×400=4.940→4.900; dolap 3,6×14.000; tezgah 3,6×7.500; ankastre orta 38.000; pay %10 12.030→12.000
            'mutfak' => ['mutfak-maliyet-hesaplayici', ['scope' => 'dolap', 'length' => '3.6', 'quality' => 'orta', 'counter' => 'granit', 'extras' => ['ankastre']], ['sokum' => 4900, 'dolap' => 50400, 'tezgah' => 27000, 'ekstra' => 38000, 'beklenmedik' => 12000], [112000, 132000, 159000], 36750],
            // elle: 20×1,1×750=16.500; yapıştırıcı 4×362,9+2×285,7=2.023→2.000; işçilik 20×450; pay 2.750→2.800 (yarım yukarı)
            'seramik' => ['seramik-maliyet-hesaplayici', ['surface' => 'zemin', 'floor' => '20', 'size' => '60x60', 'quality' => 'orta', 'scope' => 'hazir'], ['seramik' => 16500, 'yapistirici' => 2000, 'iscilik' => 9000, 'beklenmedik' => 2800], [26000, 30000, 36000], 1520],
            'boya' => ['boya-hesaplayici', ['target' => 'daire', 'floor' => '100', 'surfaces' => 'duvar_tavan', 'color' => 'ayni', 'state' => 'iyi', 'quality' => 'orta'], ['macun' => 3700, 'boya' => 17300, 'tavan' => 2400, 'iscilik' => 25700, 'sarf' => 2800, 'beklenmedik' => 5200], [49000, 57000, 69000], 570],
            'wc' => ['wc-maliyet-hesabi', ['w' => '1.2', 'l' => '1.5', 'scope' => 'komple', 'quality' => 'orta'], ['sokum' => 5000, 'tesisat' => 18700, 'yalitim' => 2200, 'seramik' => 13500, 'vitrifiye' => 14000, 'beklenmedik' => 5300], [50000, 59000, 70000], 32610],
            'parke' => ['parke-maliyet-hesaplayici', ['type' => 'laminat', 'area' => '40', 'rooms' => 'tek', 'quality' => 'orta', 'scope' => 'hazir', 'extras' => ['supurgelik']], ['parke' => 23100, 'silte' => 2800, 'iscilik' => 7200, 'supurgelik' => 5600, 'beklenmedik' => 3900], [36000, 43000, 51000], 1070],
            'zemin' => ['zemin-kaplama', ['type' => 'mermer', 'area' => '30', 'quality' => 'orta', 'scope' => 'sert'], ['sokum' => 9000, 'malzeme' => 72600, 'harc' => 3600, 'iscilik' => 22500, 'beklenmedik' => 10800], [101000, 119000, 142000], 3950],
            'mantolama' => ['mantolama-maliyeti', ['perimeter' => '60', 'floors' => '5', 'board' => 'grafit', 'thickness' => '5', 'finish' => 'boya', 'quality' => 'orta', 'scaffold' => 'var'], ['iskele' => 122400, 'levha' => 144600, 'sistem' => 176000, 'sonkat' => 68900, 'iscilik' => 290700, 'beklenmedik' => 80300], [750000, 883000, 1059000], 1150],
            'pvc' => ['pvc-dograma-hesaplayici', ['count' => '6', 'width' => '150', 'height' => '150', 'quality' => 'orta', 'color' => 'beyaz', 'glass' => 'isi', 'opening' => 'tek', 'scope' => 'degisim'], ['sokum' => 6000, 'profil' => 47700, 'cam' => 23200, 'montaj' => 5400, 'beklenmedik' => 8200], [77000, 91000, 109000], 15080],
            'sap' => ['sap-hesaplayici', ['type' => 'klasik', 'area' => '100', 'thickness' => '5', 'extras' => ['fiber']], ['malzeme' => 14500, 'iscilik' => 16000, 'katki' => 2500, 'beklenmedik' => 3300], [31000, 36000, 44000], 360],
            'kara_siva' => ['kara-siva-hesaplama', ['method' => 'geleneksel', 'side' => 'ic', 'area' => '120'], ['malzeme' => 7700, 'iscilik' => 24000, 'beklenmedik' => 3200], [30000, 35000, 42000], 290],
            'alci_siva' => ['alci-siva-hesaplama', ['type' => 'perlit_saten', 'wall' => '120', 'ceiling' => '40'], ['perlit' => 10600, 'perlit_is' => 28600, 'saten' => 2700, 'saten_is' => 15100, 'beklenmedik' => 5700], [53000, 63000, 75000], 390],
            'duvar' => ['duvar-maliyet-hesaplayici', ['material' => 'gazbeton20', 'length' => '12', 'height' => '2.8', 'openings' => '2', 'plaster' => 'tek'], ['blok' => 27100, 'harc' => 1300, 'iscilik' => 6500, 'lento' => 2100, 'siva' => 11200, 'beklenmedik' => 4800], [45000, 53000, 64000], 1790],
            'duvar_orgu' => ['duvar-orgu', ['material' => 'tugla135', 'length' => '10', 'height' => '2.8', 'plaster' => 'yok'], ['blok' => 7000, 'harc' => 2700, 'iscilik' => 7300, 'beklenmedik' => 1700], [16000, 19000, 22000], 670],
            // v120: temel kazısı eklendi — 40 × 0,7 × 0,7 = 19,6 m³ × (120 + 1,2 × (2.400/16 + 80)) = 7.762
            'bahce' => ['bahce-duvari-maliyeti', ['type' => 'betonarme', 'length' => '40', 'height' => '1.5', 'extras' => ['coping']], ['kazi' => 7800, 'temel' => 108000, 'govde' => 180000, 'harpusta' => 18000, 'beklenmedik' => 31400], [293000, 345000, 414000], 8630],
            'iskele' => ['iskele-metraji-maliyeti', ['type' => 'cephe', 'length' => '60', 'height' => '15', 'months' => '2', 'net' => 'evet'], ['kira' => 249600, 'ek_ay' => 87400, 'file' => 52800, 'nakliye' => 6000, 'beklenmedik' => 19800], [353000, 416000, 499000], 430],
            // v120: ÇŞB kalıp fiyatı KDV hariç → ×1,2 (600 m² × 1.175,39 × 1,2 = 846.300)
            'kalip' => ['kalip-plywood-maliyeti', ['element' => 'doseme', 'volume' => '100', 'material' => 'plywood'], ['kalip' => 846300, 'dikme' => 43200, 'beklenmedik' => 44500], [794000, 934000, 1121000], 1560],
            'hafriyat' => ['hafriyat-kazi-hesabi', ['width' => '15', 'length' => '20', 'depth' => '3', 'soil' => 'normal', 'truck' => '16'], ['kazi' => 108000, 'nakliye' => 163200, 'dokum' => 86400, 'beklenmedik' => 35800], [334000, 393000, 472000], 440],
            'saha' => ['saha-betonu-hesaplayici', ['area' => '200', 'thickness' => '15', 'class' => 'c25', 'mesh' => 'tek', 'finish' => 'helikopter', 'pump' => 'evet'], ['hasir' => 27600, 'beton' => 136100, 'pompa' => 9000, 'iscilik' => 14200, 'perdah' => 44000, 'beklenmedik' => 11500], [206000, 242000, 291000], 1210],
            'alcipan' => ['alcipan-hesaplayici', ['system' => 'c75', 'board' => 'water', 'area' => '30'], ['imalat' => 35400, 'kdv' => 7100], [38000, 43000, 49000], 1420],
            'asma_tavan' => ['asma-tavan-hesaplayici', ['system' => 'alu60_sarkmali', 'area' => '50'], ['imalat' => 72500, 'kdv' => 14500], [78000, 87000, 100000], 1740],
            'su_yalitimi' => ['su-yalitimi-hesaplayici', ['system' => 'cement_2k_mesh', 'area' => '60'], ['imalat' => 41400, 'kdv' => 8300], [45000, 50000, 57000], 830],
            // elle: 300×1.650 (YFK) + 300×520 UV; KDV %20 = 130.200
            'epoksi' => ['epoksi-zemin-hesaplayici', ['area' => '300', 'layers' => ['uv_topcoat']], ['epoxy' => 495000, 'uv_topcoat' => 156000, 'kdv' => 130200], [703000, 781000, 898000], 2600],
            // elle: 500×2.150 (YFK) = 1.075.000; KDV %20
            'sandvic' => ['sandvic-panel-hesaplayici', ['system' => 'roof_pu50', 'area' => '500'], ['imalat' => 1075000, 'kdv' => 215000], [1161000, 1290000, 1484000], 2580],
            'radyator' => ['radyator-petek-hesaplayici', ['insulation' => 'standart', 'area' => '100', 'type' => 'h60', 'scope' => 'petek', 'extras' => ['thermo']], ['petek' => 15900, 'vana' => 9100, 'montaj' => 8400, 'beklenmedik' => 3300], [31000, 37000, 44000], 370],
            // v120: elektrik birim fiyatı tek kaynaktan (pricing.enerji 3,24 TL/kWh, önce 3,5) → aynı faturadan daha çok kWh, sistem 5,3 → 5,7 kWp
            'ges' => ['gunes-paneli-hesaplama', ['bill' => '2000', 'sun' => '4.5', 'mount' => 'cati', 'quality' => 'orta'], ['panel' => 57000, 'inverter' => 34200, 'kons' => 20000, 'montaj' => 22800, 'proje' => 15000, 'beklenmedik' => 7500], [133000, 157000, 188000], 27460],
            'izolator' => ['deprem-izolator-maliyeti', ['columns' => '40', 'floors' => '6', 'class' => 'konut', 'scope' => 'yeni'], ['izolator' => 8401700, 'montaj' => 2100400, 'muhendislik' => 672100, 'beklenmedik' => 558700], [9973000, 11733000, 14079000], 293320],
            // elle: hazırlık 12×(450+3×90)×1,2=10.368; harç 12×3×20×1,1=792 kg ×38=30.096; işçilik 12×(650+3×160)×1,2=16.272; pas 12×280; astar 12×90
            'beton_onarim' => ['beton-onarim-maliyeti', ['mode' => 'onarim', 'area' => '12', 'depth' => '3', 'element' => 'kolon', 'rebar' => 'hafif', 'material' => 'cimento'], ['hazirlik' => 10400, 'pas' => 3400, 'astar' => 1100, 'harc' => 30100, 'iscilik' => 16300, 'beklenmedik' => 6100], [57000, 67000, 81000], 5620],
            'bina' => ['tahmini-bina-maliyeti', ['type' => 'apartman', 'area' => '1000', 'quality' => 'orta', 'extras' => ['projects']], ['kaba' => 11451200, 'ince' => 9093600, 'mekanik' => 7072800, 'elektrik' => 6062400, 'proje' => 1347200, 'beklenmedik' => 1751400], [31262000, 36779000, 44134000], 36780],
            'statik' => ['statik-proje-bedeli', ['type' => 'apartman', 'area' => '1000', 'kind' => 'yeni', 'extras' => ['soil']], ['statik' => 277900, 'zemin' => 53000], [281000, 331000, 397000], 330],
            'beton' => ['beton-hesaplayici', ['element' => 'doseme', 'length' => '10', 'width' => '10', 'thickness' => '20', 'class' => 'c25', 'pump' => 'evet'], ['beton' => 90700, 'pompa' => 9000], [85000, 100000, 120000], 4750],
            'su_tesisati' => ['su-tesisat-hesaplama', ['area' => '100', 'baths' => '2', 'kitchens' => '1', 'scope' => 'komple', 'quality' => 'orta'], ['kanal' => 7200, 'banyo' => 26000, 'mutfak' => 8000, 'ana' => 13000, 'tamir' => 5400, 'beklenmedik' => 6000], [56000, 66000, 79000], 660],
            'cati' => ['cati-maliyet-hesaplayici', ['covering' => 'clay_alaturka', 'length' => '20', 'width' => '12', 'form' => 'gable', 'underlay' => 'osb', 'membrane' => 'breathable'], ['ortu' => 283500, 'mahya' => 6500, 'underlay' => 185100, 'membrane' => 60700, 'kdv' => 107200], [579000, 643000, 739000], 2220],
            'mutfak_komple_premium' => ['mutfak-maliyet-hesaplayici', ['scope' => 'komple', 'length' => '4.2', 'floor' => '12', 'quality' => 'premium', 'counter' => 'kuvars', 'extras' => ['tezgah_arasi', 'boy_dolabi', 'led']], ['sokum' => 8200, 'dolap' => 100800, 'tezgah' => 39900, 'zemin' => 43200, 'tesisat' => 12800, 'boya' => 3600, 'tezgah_arasi' => 16800, 'ekstra' => 31600, 'beklenmedik' => 25700], [240000, 283000, 339000], 67290],
            // elle: 2,5×9.000×0,45=10.125→10.100; tezgah 2,5×2.800; birim 18.800/2,5=7.520
            'mutfak_kapak_ekonomik' => ['mutfak-maliyet-hesaplayici', ['scope' => 'kapak', 'length' => '2.5', 'quality' => 'ekonomik', 'counter' => 'laminat'], ['dolap' => 10100, 'tezgah' => 7000, 'beklenmedik' => 1700], [16000, 19000, 23000], 7520],
            'seramik_sokum_premium' => ['seramik-maliyet-hesaplayici', ['surface' => 'zemin_duvar', 'floor' => '6', 'wall' => '22', 'size' => '30x60', 'quality' => 'premium', 'scope' => 'sokum', 'extras' => ['yalitim', 'profil', 'sap', 'supurgelik']], ['sokum' => 8400, 'sap' => 2700, 'yalitim' => 15400, 'seramik' => 49300, 'yapistirici' => 3000, 'iscilik' => 12600, 'profil' => 900, 'supurgelik' => 2500, 'beklenmedik' => 9500], [89000, 104000, 125000], 3730],
            'boya_kotu_renk' => ['boya-hesaplayici', ['target' => 'daire', 'floor' => '120', 'surfaces' => 'duvar_tavan', 'color' => 'koyu_acik', 'state' => 'kotu', 'quality' => 'premium', 'extras' => ['furniture', 'door', 'radiator', 'plaster']], ['macun' => 45300, 'alci' => 12300, 'astar' => 13300, 'boya' => 49700, 'tavan' => 2900, 'iscilik' => 66100, 'sarf' => 3300, 'ekstra' => 19600, 'beklenmedik' => 21300], [199000, 234000, 281000], 1950],
            'boya_oda_ekonomik' => ['boya-hesaplayici', ['target' => 'oda', 'floor' => '15', 'surfaces' => 'duvar', 'color' => 'degisim', 'state' => 'orta', 'quality' => 'ekonomik'], ['macun' => 700, 'astar' => 1100, 'boya' => 1500, 'iscilik' => 2900, 'sarf' => 800, 'beklenmedik' => 700], [7000, 8000, 9000], 510],
            'wc_ekonomik' => ['wc-maliyet-hesabi', ['w' => '1', 'l' => '1.3', 'scope' => 'komple', 'quality' => 'ekonomik'], ['sokum' => 4100, 'tesisat' => 18000, 'yalitim' => 1600, 'seramik' => 8700, 'vitrifiye' => 8400, 'beklenmedik' => 4100], [38000, 45000, 54000], 34540],
            'parke_ahsap' => ['parke-maliyet-hesaplayici', ['type' => 'ahsap', 'area' => '85', 'rooms' => 'tek', 'quality' => 'premium', 'scope' => 'hazir', 'extras' => ['supurgelik']], ['parke' => 339200, 'silte' => 10200, 'iscilik' => 29800, 'supurgelik' => 8100, 'beklenmedik' => 38700], [362000, 426000, 511000], 5010],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        PozEstimator::flushCache();
        foreach (['15.530.1260' => 1180.50, '15.535.1013' => 1450.00, '15.270.1006' => 690.00, '15.190.1014' => 1650.00, '15.190.1017' => 520.00, '15.320.1001' => 2150.00, '15.305.1001' => 980.00, '15.305.1005' => 310.00, '15.300.1006' => 640.00, '15.330.1003' => 210.00] as $poz => $price) {
            DB::table('rayic_prices')->insert(['source' => 'YFK', 'period' => '2026-08', 'poz_no' => $poz, 'description' => 'Test '.$poz, 'unit' => 'm²', 'price' => $price, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    #[DataProvider('cases')]
    public function test_bilinen_girdi_beklenen_tutar(string $slug, array $q, array $items, array $range, int|float|null $perUnit): void
    {
        $e = KitRegistry::get($slug);
        $r = $e->estimate($e->normalize($q));

        $this->assertTrue($r['ready'], $slug.': hesap hazır olmalı');
        $this->assertSame($items, array_map(fn ($i) => (int) $i['mid'], $r['items']), $slug.': kalem tutarları');
        $this->assertSame($range, [(int) $r['low'], (int) $r['mid'], (int) $r['high']], $slug.': alt / orta / üst');
        $this->assertSame($perUnit === null ? null : (int) $perUnit, $r['per_unit'] === null ? null : (int) $r['per_unit'], $slug.': birim fiyat');

        // Kalemler aralığı tutarlı oluşturuyor
        $sum = array_sum($items);
        $this->assertSame((int) (round($sum / 1000) * 1000), (int) $r['mid']);   // altın değerlerin hepsi ≥ 2.000 TL
        foreach ($r['items'] as $it) {
            $this->assertSame(0.0, fmod((float) $it['mid'], 100), $slug.': kalemler 100 TL\'ye yuvarlı');
        }
    }

    #[DataProvider('cases')]
    public function test_canli_uc_nokta_ayni_tutari_verir(string $slug, array $q, array $items, array $range, int|float|null $perUnit): void
    {
        $r = $this->getJson('/araclar/'.$slug.'/hesapla?'.http_build_query($q))->assertOk()->json();
        $this->assertSame($range, [(int) $r['low'], (int) $r['mid'], (int) $r['high']]);
    }

    public function test_her_hesap_motoru_aracinin_altin_degeri_var(): void
    {
        $covered = array_unique(array_column(self::cases(), 0));
        $this->assertEqualsCanonicalizing(KitRegistry::slugs(), $covered, 'Yeni hesap motoru aracı eklendiyse buraya bilinen girdi → beklenen tutar satırı ekleyin');
    }

    /** brick/money'de korunması gereken yuvarlama kuralları: yarım yukarı, kalem 100 TL, toplam 1.000 TL */
    public static function roundingCases(): array
    {
        return [
            'kalem yarım yukarı' => [[2750], [2800], [2000, 3000, 3000]],   // 2.380 / 2.800 / 3.360
            'kalem yarımın altı' => [[2749.99], [2700], [2000, 3000, 3000]],
            'toplam yarım yukarı' => [[2500], [2500], [2000, 3000, 3000]],   // 2.125 / 2.500 / 3.000
            'sıfır ve negatif kalem düşer' => [[0, -500, 4049], [4000], [3000, 4000, 5000]],   // 3.400 / 4.000 / 4.800
            // v120: 2.000 TL altı toplam 100 TL'ye yuvarlanır (önceden 1.000 → küçük işte ₺0 – ₺0 çıkıyordu)
            'kuruşlu girdi' => [[1049.5, 0.49], [1000, 0], [900, 1000, 1200]],   // 1.049,5 → 1.000; 0,49 → 0 düşer
            'küçük iş ₺0 olmaz' => [[260, 30], [260, 30], [250, 290, 350]],   // v120: 500 TL altı kalem ve toplam 10 TL'ye yuvarlanır
        ];
    }

    #[DataProvider('roundingCases')]
    public function test_yuvarlama_kurallari(array $raw, array $expectedItems, array $range): void
    {
        $e = new class($raw) extends KitEstimator {
            public function __construct(private array $raw) {}

            public function key(): string { return 'test'; }

            public function slug(): string { return 'test'; }

            public function page(): array { return []; }

            public function steps(): array { return []; }

            public function contingencyRate(): float { return 0.0; }

            protected function compute(array $in): array
            {
                $items = [];
                foreach ($this->raw as $i => $v) {
                    $items['k'.$i] = ['label' => 'k'.$i, 'mid' => $v];
                }

                return ['items' => $items];
            }
        };

        $r = $e->estimate([]);
        $this->assertSame(array_values(array_filter($expectedItems)), array_values(array_map(fn ($i) => (int) $i['mid'], $r['items'])));
        $this->assertSame($range, [(int) $r['low'], (int) $r['mid'], (int) $r['high']]);
    }
}

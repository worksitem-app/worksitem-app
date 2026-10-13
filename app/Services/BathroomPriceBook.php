<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Banyo hesaplayıcısının birim fiyatları ↔ admin paneli
 * (Fiyat Merkezi → Araç Fiyatları, unit_prices tablosu).
 *
 * - Başlangıç değerleri config/worksitem_bathroom.php dosyasındadır.
 * - Admin panelinde "Yayın Onaylı" + aktif + "Piyasa Fiyatı" olan bir
 *   banyo satırı, config'teki değerin yerine geçer.
 * - Panelde kayıt değişince önbellek otomatik temizlenir (UnitPrice::booted).
 */
class BathroomPriceBook
{
    public const CACHE_KEY = 'banyo:prices:v1';

    public const CATEGORY = 'banyo_tadilati';

    private const Q = ['eko' => 0, 'orta' => 1, 'premium' => 2];

    private const QL = ['eko' => 'ekonomik', 'orta' => 'orta', 'premium' => 'premium'];

    /** key => [config yolu, ad, birim] */
    public function map(array $cfg): array
    {
        $m = [
            'banyo_kirim_m2' => ['unit.demolition_m2', 'Banyo kırım + moloz (kaplama m²)', 'TL/m2'],
            'banyo_yuzey_sokum' => ['unit.surface_removal', 'Banyo yüzey yenilemede vitrifiye sökümü', 'TL/iş'],
            'banyo_tesisat_sabit' => ['unit.plumbing_base', 'Banyo komple tesisat (su + pis su) sabit', 'TL/iş'],
            'banyo_tesisat_m2' => ['unit.plumbing_m2', 'Banyo tesisat ek bedeli (zemin m²)', 'TL/m2'],
            'banyo_tasima_sabit' => ['unit.relocation_fixed', 'Banyo yerleşim değişikliği ek sabit', 'TL/iş'],
            'banyo_yalitim_m2' => ['unit.waterproof_m2', 'Banyo zemin su yalıtımı', 'TL/m2'],
            'banyo_yalitim_dus' => ['unit.waterproof_shower_zone', 'Banyo duş bölgesi duvar yalıtımı', 'TL/iş'],
            'banyo_tavan_m2' => ['extras.ex_tavan.per_m2', 'Banyo asma tavan + spot (zemin m²)', 'TL/m2'],
            'banyo_tavan_sabit' => ['extras.ex_tavan.fixed', 'Banyo asma tavan sabit bedel', 'TL/iş'],
            'banyo_elektrik' => ['extras.ex_elektrik.fixed', 'Banyo elektrik ve aydınlatma yenileme', 'TL/iş'],
        ];

        foreach (self::Q as $q => $i) {
            $ql = self::QL[$q];
            $m["banyo_seramik_m2_$q"] = ["unit.tile_m2.$i", "Banyo seramik malzeme + işçilik ($ql)", 'TL/m2'];
            $m["banyo_vitrifiye_$q"] = ["unit.fixtures.$i", "Banyo vitrifiye ve armatür seti ($ql)", 'TL/set'];
            foreach ($cfg['showers'] as $sk => $s) {
                $m["banyo_dus_{$sk}_$q"] = ["showers.$sk.price.$i", "Banyo {$s['label']} ($ql)", 'TL/adet'];
            }
            foreach (['ex_dolap', 'ex_rezervuar', 'ex_havlupan'] as $ek) {
                $m['banyo_'.substr($ek, 3)."_$q"] = ["extras.$ek.price.$i", 'Banyo '.$cfg['extras'][$ek]['label']." ($ql)", 'TL/adet'];
            }
        }

        return $m;
    }

    /** Config + admin panelindeki onaylı fiyatlar */
    public function apply(array $cfg): array
    {
        $over = $this->overrides();
        foreach ($this->map($cfg) as $key => [$path]) {
            if (isset($over['prices'][$key])) {
                Arr::set($cfg, $path, $over['prices'][$key]);
            }
        }
        if (! empty($over['latest'])) {
            $cfg['prices_date'] = $over['latest'];
        }

        return $cfg;
    }

    /** @return array{prices:array<string,float>,latest:?string} */
    public function overrides(): array
    {
        return Cache::remember(self::CACHE_KEY, 600, function () {
            try {
                if (! Schema::hasTable('unit_prices')) {
                    return ['prices' => [], 'latest' => null];
                }
                $rows = DB::table('unit_prices')
                    ->where('category', self::CATEGORY)
                    ->where('is_active', true)
                    ->where('price_pool', 'market')
                    ->where('review_status', 'approved')
                    ->get(['key', 'price', 'source_date']);
            } catch (\Throwable $e) {
                return ['prices' => [], 'latest' => null];
            }

            return [
                'prices' => $rows->mapWithKeys(fn ($r) => [$r->key => (float) $r->price])->all(),
                'latest' => $rows->max('source_date'),
            ];
        });
    }

    /** Admin paneline eklenecek başlangıç satırları (config değerleriyle) */
    public function seedRows(array $cfg, string $date): array
    {
        $now = now();
        $rows = [];
        foreach ($this->map($cfg) as $key => [$path, $name, $unit]) {
            $rows[] = [
                'category' => self::CATEGORY,
                'key' => $key,
                'name' => $name,
                'price' => (float) Arr::get($cfg, $path),
                'unit' => $unit,
                'price_pool' => 'market',
                'source_name' => 'Worksitem başlangıç değeri (Kainta İnşaat kontrol edecek)',
                'source_date' => $date,
                'vat_included' => 1,
                'is_active' => 1,
                'review_status' => 'approved',
                'notes' => 'Banyo tadilat maliyeti hesaplayıcısı. Malzeme + işçilik, KDV dahil ortalama. Kaydettiğinizde sayfadaki hesap hemen güncellenir.',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $rows;
    }
}

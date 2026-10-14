<?php

namespace App\Estimators;

use App\Services\PriceService;

/**
 * Resmî birim fiyatlı (ÇŞB / YFK poz) imalatlar: fiyat yalnız rayiç tablosundan gelir,
 * bulunamazsa fiyat uydurulmaz (hesap "Fiyat yok" der, metraj Pro raporda yine verilir).
 * YFK fiyatları KDV hariçtir; KDV ayrı kalem olarak eklenir.
 */
abstract class PozEstimator extends KitEstimator
{
    private static array $cache = [];

    public function band(): array
    {
        return ['low' => 0.90, 'high' => 1.15];
    }

    public function contingencyRate(): float
    {
        return 0.0;
    }

    protected function period(): string
    {
        return (string) config('worksitem_estimators.yfk_period', '2026-08');
    }

    public function pricesLabel(): string
    {
        return 'YFK '.$this->period().' (ÇŞB birim fiyatları)';
    }

    /** @return array{price: float, unit: string, period: string}|null */
    protected function poz(?string $no): ?array
    {
        if (! $no) {
            return null;
        }
        if (! array_key_exists($no, self::$cache)) {
            try {
                $svc = app(PriceService::class);
                $r = $svc->byPoz($no, $this->period(), 'YFK') ?? $svc->byPoz($no, null, 'YFK');
            } catch (\Throwable) {
                $r = null;
            }
            self::$cache[$no] = is_array($r) && is_numeric($r['price'] ?? null) && (float) $r['price'] > 0
                ? ['price' => (float) $r['price'], 'unit' => (string) ($r['unit'] ?? 'm²'), 'period' => (string) ($r['period'] ?? $this->period())]
                : null;
        }

        return self::$cache[$no];
    }

    /**
     * SSS ve "kısa cevap" için örnek m² fiyatı: her seçim adımının ilk seçeneği,
     * alan 100 m². Fiyat bulunamazsa boş döner (metin uydurulmaz).
     */
    protected function typicalPerUnit(array $override = []): string
    {
        $raw = [];
        foreach ($this->steps() as $step) {
            if (($step['type'] ?? '') === 'cards' && ! empty($step['options'])) {
                $raw[$step['key']] = array_key_first($step['options']);
            }
            foreach ($step['fields'] ?? [] as $f) {
                $raw[$f['key']] = $f['default'] ?? 100;
            }
        }

        return $this->exampleRange(array_merge($raw, $override), true);
    }

    /** İlk cümlesi örnek fiyat olan SSS cevabı; fiyat yoksa yedek metin. */
    protected function priceAnswer(string $lead, string $fallback, array $override = []): string
    {
        $r = $this->typicalPerUnit($override);

        return $r !== '' ? str_replace(':fiyat', $r, $lead) : $fallback;
    }

    public static function flushCache(): void
    {
        self::$cache = [];
    }

    /** KDV kalemi ekleyerek sonucu döndürür */
    protected function withVat(array $items, array $extra = []): array
    {
        $net = array_sum(array_map(fn ($i) => (float) $i['mid'], $items));
        $items['kdv'] = ['label' => 'KDV (%20)', 'mid' => $net * 0.20, 'color' => '#D5D9DF'];

        return ['items' => $items] + $extra;
    }

    protected function blocked(string $label, ?string $poz): array
    {
        return ['blocked' => $label.' için doğrulanmış resmî birim fiyat (YFK '.$this->period().($poz ? ', poz '.$poz : '').') bulunamadı. Fiyat uydurmuyoruz; metraj ve teklif için Kainta İnşaat\'a başvurabilirsiniz.'];
    }

    public function checklist(): array
    {
        return array_merge([
            'Teklifte uygulanacak sistemin marka, ürün ve kalınlık bilgilerinin yazmasını isteyin.',
            'Resmî birim fiyat (YFK) bir referanstır; piyasa teklifleri iş büyüklüğüne ve bölgeye göre farklılaşabilir.',
        ], array_slice(parent::checklist(), 0, 5));
    }
}

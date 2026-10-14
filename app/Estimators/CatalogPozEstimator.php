<?php

namespace App\Estimators;

/** Tek sistem seçimi + alan: imalat = alan × YFK m² birim fiyatı (+ KDV) */
abstract class CatalogPozEstimator extends PozEstimator
{
    /** @return array<string, array{label:string, poz:?string, note?:string}> */
    abstract protected function catalog(): array;

    protected function systemTitle(): string { return 'Sistem'; }

    protected function areaLabel(): string { return 'Uygulama alanı'; }

    protected function speed(): array { return [60, 30]; }   // m²/gün [hızlı, yavaş]

    public function perUnitLabel(): ?string { return 'm² başına'; }

    public function steps(): array
    {
        $opts = [];
        foreach ($this->catalog() as $k => $s) {
            $opts[$k] = ['label' => $s['label'], 'hint' => $s['poz'] ? 'YFK '.$s['poz'] : 'Piyasa fiyatı'];
        }

        return [
            ['type' => 'cards', 'key' => 'system', 'title' => $this->systemTitle(), 'short' => $this->systemTitle(), 'required' => true, 'cols' => 2, 'options' => $opts],
            ['type' => 'numbers', 'title' => 'Alan', 'cols' => 1, 'fields' => [
                ['key' => 'area', 'label' => $this->areaLabel(), 'unit' => 'm²', 'min' => 1, 'max' => 200000, 'step' => 0.5, 'placeholder' => 'Örn. 100', 'required' => true],
            ]],
        ];
    }

    protected function system(array $in): ?array
    {
        $s = $this->catalog()[$in['system']] ?? null;
        if ($s && empty($s['poz'])) {
            $s['poz'] = $this->webKey($in['system']);   // resmî pozu olmayan sistem: fiyat robotunun piyasa fiyatı
        }

        return $s;
    }

    /** Resmî pozu olmayan sistemin fiyat robotu anahtarı */
    public function webKey(string $system): string
    {
        return 'WEB-'.str(class_basename(static::class))->before('Estimator')->kebab().'-'.$system;
    }

    /** Fiyat robotu için sistemler: [key (poz no ya da WEB-...), label, official] */
    public function robotSystems(): array
    {
        $out = [];
        foreach ($this->catalog() as $k => $s) {
            $out[] = ['key' => $s['poz'] ?: $this->webKey($k), 'label' => $s['label'], 'official' => (bool) $s['poz']];
        }

        return $out;
    }

    protected function compute(array $in): array
    {
        $sys = $this->system($in);
        $p = $this->poz($sys['poz'] ?? null);
        if (! $sys || ! $p) {
            return $this->blocked($sys['label'] ?? 'Bu sistem', $sys['poz'] ?? null);
        }
        $a = (float) $in['area'];
        [$fast, $slow] = $this->speed();
        $days = [max(1, (int) ceil($a / $fast)), max(1, (int) ceil($a / $slow))];

        return $this->withVat([
            'imalat' => ['label' => $sys['label'].(($p['source'] ?? 'YFK') === 'WEB' ? ' · piyasa fiyatı' : ' · YFK '.$sys['poz']), 'mid' => $a * $p['price'], 'color' => '#FFB800'],
        ], [
            'per_unit' => $a, 'days' => $days,
            'meta' => [['Birim fiyat (KDV hariç)', '₺'.number_format($p['price'], 2, ',', '.').' / '.$p['unit']], ['Tahmini süre', $days[0].'–'.$days[1].' iş günü']],
            'notes' => ($p['source'] ?? 'YFK') === 'WEB'
                ? ['Bu sistemin resmî birim fiyatı yok; güncel piyasa fiyatı (web araştırması, Kainta İnşaat Uzmanları kontrolünde) kullanıldı.']
                : array_filter([$sys['note'] ?? null]),
        ]);
    }
}

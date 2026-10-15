<?php

namespace App\Services\Market;

use App\Models\Firm;
use Illuminate\Support\Collection;

/**
 * Firma hizmet bilgileri: firmaya göre kısa soru listesi, cevap doğrulama ve saha fiyat istatistiği.
 * Tüm sorular isteğe bağlıdır (firmayı sıkmamak için); "en küçük iş" cevabı eşleştirmede kullanılır.
 */
class FirmQuestions
{
    public static function isSupplier(Firm $f): bool
    {
        return in_array($f->type, ['tedarikci', 'uretici'], true);
    }

    /** @return array<string, array> soru anahtarı => tanım */
    public static function for(Firm $f): array
    {
        $c = (array) config('worksitem_firm_questions');
        if (self::isSupplier($f)) {
            return (array) $c['supplier'];
        }
        $qs = (array) $c['common'];
        $room = max(0, (int) $c['max'] - count($qs));
        foreach ((array) $f->trades as $t) {
            foreach ((array) ($c['trades'][$t] ?? []) as $k => $q) {
                if ($room-- <= 0) {
                    break 2;
                }
                $qs[$k] = $q;
            }
        }

        return $qs;
    }

    /** Doğrulama kuralları (hepsi isteğe bağlı) */
    public static function rules(Firm $f): array
    {
        $r = [];
        foreach (self::for($f) as $k => $q) {
            $r["a.$k"] = match ($q['type']) {
                'money' => ['nullable', 'integer', 'min:1', 'max:1000000000'],
                'select' => ['nullable', 'in:'.implode(',', array_keys($q['options']))],
                'multi' => ['nullable', 'array'],
                default => ['nullable', 'string', 'max:300'],
            };
            if ($q['type'] === 'multi') {
                $r["a.$k.*"] = ['in:'.implode(',', array_keys($q['options']))];
            }
        }

        return $r;
    }

    /** Cevapları firmaya yazar */
    public static function save(Firm $f, array $answers): void
    {
        $qs = self::for($f);
        $clean = [];
        foreach ($qs as $k => $q) {
            $v = $answers[$k] ?? null;
            if ($v === null || $v === '' || $v === []) {
                continue;
            }
            $clean[$k] = match ($q['type']) {
                'money' => (int) $v,
                'multi' => array_values(array_unique((array) $v)),
                'text' => trim(strip_tags((string) $v)),
                default => (string) $v,
            };
        }
        $f->forceFill([
            'answers' => $clean ?: null,
            'answers_at' => $clean ? now() : $f->answers_at,
            'min_job' => isset($clean['min_job']) ? (int) $clean['min_job'] : null,
        ])->save();
    }

    /** Tüm fiyat soruları (anahtar => tanım), saha fiyat paneli için */
    public static function priceQuestions(): array
    {
        $c = (array) config('worksitem_firm_questions');
        $all = (array) $c['supplier'];
        foreach ((array) $c['trades'] as $t => $qs) {
            foreach ($qs as $k => $q) {
                $all[$k] = $q + ['trade' => $t];
            }
        }

        return array_filter($all, fn ($q) => ! empty($q['price']));
    }

    /**
     * Saha fiyat istatistiği: soru başına cevap sayısı, ortanca, en düşük / en yüksek ve ortancadan 2 kattan fazla sapan cevaplar.
     *
     * @return Collection<int, array>
     */
    public static function stats(): Collection
    {
        $firms = Firm::query()->whereIn('status', ['pending', 'approved'])->whereNotNull('answers')->get(['id', 'name', 'answers', 'status']);

        return collect(self::priceQuestions())->map(function ($q, $k) use ($firms) {
            $vals = $firms->map(fn ($f) => ['firm' => $f, 'v' => (int) (($f->answers ?? [])[$k] ?? 0)])->filter(fn ($x) => $x['v'] > 0)->values();
            $sorted = $vals->pluck('v')->sort()->values();
            $n = $sorted->count();
            $median = $n ? ($n % 2 ? $sorted[intdiv($n, 2)] : ($sorted[$n / 2 - 1] + $sorted[$n / 2]) / 2) : null;
            $outliers = $median && $n >= 3 ? $vals->filter(fn ($x) => $x['v'] > $median * 2 || $x['v'] < $median / 2)->map(fn ($x) => $x['firm']->name.': '.number_format($x['v'], 0, ',', '.'))->values()->all() : [];

            return ['key' => $k, 'q' => $q['q'], 'unit' => $q['unit'] ?? '', 'trade' => $q['trade'] ?? 'tedarikçi', 'n' => $n, 'median' => $median,
                'min' => $sorted->first(), 'max' => $sorted->last(), 'outliers' => $outliers];
        })->values();
    }
}

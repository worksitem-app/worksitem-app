<?php

namespace App\Services\Pricing;

use App\Models\MaterialPriceObservation;
use Illuminate\Support\Collection;

class MaterialBenchmarkAnalyzer
{
    private const MIN_CONFIDENCE = 75.0;

    private const MAX_SPREAD_RATIO = 1.35;

    public function analyze(
        string $materialKey,
        int $maxAgeDays = 90
    ): array {
        $rows = MaterialPriceObservation::query()
            ->where('material_key', $materialKey)
            ->orderByDesc('observed_at')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'status' => 'no_observations',
                'material_key' => $materialKey,
                'observation_count' => 0,
                'groups' => [],
            ];
        }

        $cutoff = now()
            ->subDays($maxAgeDays)
            ->startOfDay();

        $excluded = [
            'unknown_vat' => 0,
            'low_confidence' => 0,
            'stale' => 0,
            'invalid_price' => 0,
        ];

        $eligible = $rows->filter(
            function ($row) use (
                $cutoff,
                &$excluded
            ): bool {

                if ((float) $row->price <= 0) {
                    $excluded['invalid_price']++;
                    return false;
                }

                if (
                    $row->vat_status === 'unknown'
                ) {
                    $excluded['unknown_vat']++;
                    return false;
                }

                if (
                    (float) ($row->confidence_score ?? 0)
                    < self::MIN_CONFIDENCE
                ) {
                    $excluded['low_confidence']++;
                    return false;
                }

                $effectiveDate =
                    $row->source_date
                    ?? $row->observed_at;

                if (
                    $effectiveDate
                    && $effectiveDate->lt($cutoff)
                ) {
                    $excluded['stale']++;
                    return false;
                }

                return true;
            }
        );

        $grouped = $eligible->groupBy(
            function ($row): string {

                return implode('|', [
                    $row->market_level ?? '',
                    $row->unit ?? '',
                    $row->currency ?? '',
                    $row->vat_status ?? '',
                    $row->region ?? '',
                ]);
            }
        );

        $groups = [];

        foreach (
            $grouped as $groupKey => $groupRows
        ) {
            /*
             * Aynı kaynak birden fazla kayıt üretmişse
             * yalnız en güncel gözlem ağırlık alsın.
             */
            $latestPerSource = $groupRows
                ->sortByDesc(
                    function ($row) {
                        return optional(
                            $row->source_date
                            ?? $row->observed_at
                        )->timestamp ?? 0;
                    }
                )
                ->unique('source_name')
                ->values();

            $sourceCount =
                $latestPerSource
                    ->pluck('source_name')
                    ->filter()
                    ->unique()
                    ->count();

            $prices = $latestPerSource
                ->pluck('price')
                ->map(
                    fn ($price) =>
                        (float) $price
                )
                ->sort()
                ->values();

            $first =
                $latestPerSource->first();

            $result = [
                'group_key' => $groupKey,
                'market_level' =>
                    $first?->market_level,
                'unit' =>
                    $first?->unit,
                'currency' =>
                    $first?->currency,
                'vat_status' =>
                    $first?->vat_status,
                'region' =>
                    $first?->region,
                'source_count' =>
                    $sourceCount,
                'sources' =>
                    $latestPerSource
                        ->pluck('source_name')
                        ->values()
                        ->all(),
                'prices' =>
                    $prices->all(),
            ];

            if ($sourceCount < 2) {

                $result['status'] =
                    'insufficient_sources';

                $groups[] = $result;

                continue;
            }

            $min =
                (float) $prices->min();

            $max =
                (float) $prices->max();

            if ($min <= 0) {

                $result['status'] =
                    'invalid_price';

                $groups[] = $result;

                continue;
            }

            $ratio =
                $max / $min;

            $spreadPercent =
                (($max - $min) / $min) * 100;

            $result['min_price'] =
                round($min, 4);

            $result['max_price'] =
                round($max, 4);

            $result['spread_percent'] =
                round($spreadPercent, 2);

            if (
                $ratio
                > self::MAX_SPREAD_RATIO
            ) {

                $result['status'] =
                    'spread_too_wide';

                $groups[] = $result;

                continue;
            }

            $median =
                $this->median($prices);

            $averageConfidence =
                (float) $latestPerSource
                    ->avg('confidence_score');

            $result['status'] =
                'ready';

            $result['benchmark_price'] =
                round($median, 4);

            $result['benchmark_confidence'] =
                round(
                    min(
                        95,
                        $averageConfidence
                    ),
                    2
                );

            $groups[] = $result;
        }

        $readyGroups = collect($groups)
            ->where('status', 'ready');

        if ($readyGroups->isEmpty()) {

            return [
                'status' =>
                    'benchmark_not_ready',

                'material_key' =>
                    $materialKey,

                'observation_count' =>
                    $rows->count(),

                'eligible_count' =>
                    $eligible->count(),

                'excluded' =>
                    $excluded,

                'groups' =>
                    $groups,
            ];
        }

        /*
         * Öncelik:
         * 1 market_reference
         * 2 retail
         * 3 wholesale
         * 4 manufacturer_list
         * 5 marketplace
         */
        $priority = [
            'market_reference' => 1,
            'retail' => 2,
            'wholesale' => 3,
            'manufacturer_list' => 4,
            'marketplace' => 5,
        ];

        $selected = $readyGroups
            ->sortBy(
                fn ($group) =>
                    $priority[
                        $group['market_level']
                    ] ?? 99
            )
            ->first();

        return [
            'status' =>
                'ready',

            'material_key' =>
                $materialKey,

            'observation_count' =>
                $rows->count(),

            'eligible_count' =>
                $eligible->count(),

            'excluded' =>
                $excluded,

            'benchmark' =>
                $selected,

            'groups' =>
                $groups,
        ];
    }

    private function median(
        Collection $values
    ): float {
        $values = $values
            ->sort()
            ->values();

        $count =
            $values->count();

        if ($count === 0) {
            return 0.0;
        }

        $middle =
            intdiv($count, 2);

        if ($count % 2 === 1) {
            return (float)
                $values[$middle];
        }

        return (
            (float) $values[$middle - 1]
            +
            (float) $values[$middle]
        ) / 2;
    }
}

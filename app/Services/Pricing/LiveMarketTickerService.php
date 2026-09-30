<?php

namespace App\Services\Pricing;

use App\Models\MaterialPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LiveMarketTickerService
{
    public const LABOR_MULTIPLIER = 1.60;

    public function data(): array
    {
        $materials = $this->materials();
        $labor = $this->labor();

        return [
            'generated_at' => now()->toIso8601String(),
            'labor_multiplier' => self::LABOR_MULTIPLIER,
            'materials' => $materials,
            'labor' => $labor,
            'items' => array_merge($materials, $labor),
        ];
    }

    private function materials(): array
    {
        return MaterialPrice::query()
            ->where('is_active', true)
            ->whereNotNull('current_price')
            ->orderByDesc('source_date')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                return [
                    'type' => 'material',
                    'key' => $row->key,
                    'label' => $row->name,
                    'price' => round((float) $row->current_price, 2),
                    'base_price' => null,
                    'unit' => $row->unit,
                    'source' => $row->source_name,
                    'source_date' => optional($row->source_date)
                        ->format('Y-m-d'),
                    'poz_no' => null,
                ];
            })
            ->values()
            ->all();
    }

    private function labor(): array
    {
        if (!Schema::hasTable('tender_pozs')) {
            return [];
        }

        $columns = Schema::getColumnListing('tender_pozs');

        $descriptionColumn = in_array('description', $columns, true)
            ? 'description'
            : null;

        if (!$descriptionColumn) {
            return [];
        }

        /*
         * Sadece ham/resmi poz fiyatı.
         * offer_unit_price özellikle kullanılmaz.
         */
        $priceColumn = collect([
            'unit_price',
            'price',
            'official_unit_price',
            'rayic_price',
            'normalized_unit_cost',
        ])->first(
            fn ($column) =>
                in_array($column, $columns, true)
        );

        if (!$priceColumn) {
            return [];
        }

        $query = DB::table('tender_pozs')
            ->whereNotNull($priceColumn)
            ->where($priceColumn, '>', 0);

        if (in_array('is_active', $columns, true)) {
            $query->where('is_active', true);
        }

        if (in_array('year', $columns, true)) {
            $latestYear = DB::table('tender_pozs')
                ->max('year');

            if ($latestYear) {
                $query->where('year', $latestYear);
            }
        }

        $keywords = [
            'işçi',
            'işçilik',
            'usta',
            'elektrikçi',
            'tesisatçı',
            'boyacı',
            'kaynakçı',
            'marangoz',
            'operatör',
            'şoför',
            'teknisyen',
            'formen',
        ];

        $query->where(function ($q) use (
            $keywords,
            $descriptionColumn
        ) {
            foreach ($keywords as $keyword) {
                $q->orWhere(
                    $descriptionColumn,
                    'like',
                    '%'.$keyword.'%'
                );
            }
        });

        if (in_array('poz_no', $columns, true)) {
            $query->orderBy('poz_no');
        }

        $rows = $query
            ->limit(120)
            ->get();

        /*
         * Farklı işçiliklerden karışık seçim.
         */
        $groups = [
            'işçi' => ['işçi', 'işçilik'],
            'usta' => ['usta'],
            'elektrik' => ['elektrikçi'],
            'tesisat' => ['tesisatçı'],
            'boya' => ['boyacı'],
            'kaynak' => ['kaynakçı'],
            'operatör' => ['operatör'],
            'teknisyen' => ['teknisyen'],
        ];

        $selected = collect();

        foreach ($groups as $terms) {
            $match = $rows->first(
                function ($row) use (
                    $terms,
                    $descriptionColumn,
                    $selected
                ) {
                    $description =
                        mb_strtolower(
                            (string) $row->{$descriptionColumn},
                            'UTF-8'
                        );

                    $matched = false;

                    foreach ($terms as $term) {
                        if (
                            str_contains(
                                $description,
                                mb_strtolower($term, 'UTF-8')
                            )
                        ) {
                            $matched = true;
                            break;
                        }
                    }

                    if (!$matched) {
                        return false;
                    }

                    $pozNo = property_exists($row, 'poz_no')
                        ? (string) $row->poz_no
                        : $description;

                    return !$selected->contains(
                        fn ($item) =>
                            $item['poz_no'] === $pozNo
                    );
                }
            );

            if (!$match) {
                continue;
            }

            $basePrice =
                (float) $match->{$priceColumn};

            $pozNo =
                property_exists($match, 'poz_no')
                    ? (string) $match->poz_no
                    : '-';

            $unit =
                property_exists($match, 'unit')
                    ? (string) $match->unit
                    : '';

            $institution =
                property_exists($match, 'institution')
                    ? (string) $match->institution
                    : null;

            $selected->push([
                'type' => 'labor',
                'key' => 'labor_'.$pozNo,
                'label' => Str::limit(
                    (string) $match->{$descriptionColumn},
                    70
                ),
                'price' => round(
                    $basePrice * self::LABOR_MULTIPLIER,
                    2
                ),
                'base_price' => round($basePrice, 2),
                'unit' => $unit,
                'source' => $institution ?: 'Tender Poz',
                'source_date' => null,
                'poz_no' => $pozNo,
                'price_column' => $priceColumn,
            ]);

            if ($selected->count() >= 6) {
                break;
            }
        }

        return $selected
            ->values()
            ->all();
    }

    public function diagnostics(): array
    {
        $columns = Schema::hasTable('tender_pozs')
            ? Schema::getColumnListing('tender_pozs')
            : [];

        $priceColumn = collect([
            'unit_price',
            'price',
            'official_unit_price',
            'rayic_price',
            'normalized_unit_cost',
        ])->first(
            fn ($column) =>
                in_array($column, $columns, true)
        );

        return [
            'price_column' => $priceColumn,
            'multiplier' => self::LABOR_MULTIPLIER,
        ];
    }
}

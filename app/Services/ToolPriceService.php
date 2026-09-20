<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ToolPriceService
{
    public function forTool(string $toolSlug): Collection
    {
        if (
            !Schema::hasTable('tool_price_bindings') ||
            !Schema::hasTable('unit_prices')
        ) {
            return collect();
        }

        return DB::table('tool_price_bindings as binding')
            ->join(
                'unit_prices as price',
                'price.key',
                '=',
                'binding.price_key'
            )
            ->where('binding.tool_slug', $toolSlug)
            ->where('price.price_pool', 'market')
            ->where('price.is_active', true)
            ->select([
                'binding.role',
                'binding.price_key',
                'binding.multiplier',
                'binding.is_required',
                'binding.notes as binding_notes',
                'price.name',
                'price.price',
                'price.unit',
                'price.source_name',
                'price.source_date',
                'price.vat_included',
                'price.review_status',
                'price.notes as price_notes',
            ])
            ->orderBy('binding.role')
            ->get()
            ->map(function ($row) {
                $row->price = round(
                    (float) $row->price * (float) $row->multiplier,
                    2
                );

                return $row;
            })
            ->keyBy('role');
    }
}
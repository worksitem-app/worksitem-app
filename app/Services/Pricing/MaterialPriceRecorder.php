<?php

namespace App\Services\Pricing;

use App\Models\MaterialPrice;
use App\Models\MaterialPriceHistory;
use Illuminate\Support\Facades\DB;

class MaterialPriceRecorder
{
    public function record(array $data): array
    {
        return DB::transaction(function () use ($data): array {

            $current = MaterialPrice::query()
                ->where('key', $data['key'])
                ->lockForUpdate()
                ->first();

            $oldPrice = $current?->current_price !== null
                ? (float) $current->current_price
                : null;

            $newPrice = (float) $data['price'];

            $changed =
                $oldPrice !== null
                && abs($oldPrice - $newPrice) > 0.0001;

            $changeAmount = null;
            $changePercent = null;

            if ($changed && $oldPrice > 0) {
                $changeAmount = $newPrice - $oldPrice;

                $changePercent =
                    (($newPrice - $oldPrice) / $oldPrice) * 100;
            }

            $material = MaterialPrice::query()
                ->updateOrCreate(
                    ['key' => $data['key']],
                    [
                        'name' => $data['name'],
                        'category' => $data['category'],
                        'spec' => $data['spec'] ?? null,
                        'unit' => $data['unit'],
                        'region' => $data['region'],
                        'currency' => $data['currency'] ?? 'TRY',

                        'current_price' => $newPrice,

                        'previous_price' =>
                            $changed
                                ? $oldPrice
                                : $current?->previous_price,

                        'change_amount' =>
                            $changed
                                ? $changeAmount
                                : $current?->change_amount,

                        'change_percent' =>
                            $changed
                                ? $changePercent
                                : $current?->change_percent,

                        'source_name' => $data['source_name'],
                        'source_url' => $data['source_url'],
                        'source_date' => $data['source_date'],
                        'observed_at' => now(),

                        'vat_included' =>
                            (bool) $data['vat_included'],

                        'confidence_score' =>
                            $data['confidence_score'] ?? null,

                        'verification_status' =>
                            $data['verification_status']
                            ?? 'needs_review',

                        'is_active' => true,
                    ]
                );

            $history = MaterialPriceHistory::query()
                ->where(
                    'material_key',
                    $material->key
                )
                ->where(
                    'source_name',
                    $data['source_name']
                )
                ->whereDate(
                    'source_date',
                    $data['source_date']
                )
                ->where(
                    'price',
                    $newPrice
                )
                ->first();

            $historyCreated = false;

            if (! $history) {

                $history = MaterialPriceHistory::query()
                    ->create([
                        'material_price_id' =>
                            $material->id,

                        'material_key' =>
                            $material->key,

                        'name' =>
                            $material->name,

                        'category' =>
                            $material->category,

                        'price' =>
                            $newPrice,

                        'unit' =>
                            $material->unit,

                        'region' =>
                            $material->region,

                        'currency' =>
                            $material->currency,

                        'source_name' =>
                            $data['source_name'],

                        'source_url' =>
                            $data['source_url'],

                        'source_date' =>
                            $data['source_date'],

                        'observed_at' =>
                            now(),

                        'vat_included' =>
                            (bool) $data['vat_included'],

                        'confidence_score' =>
                            $data['confidence_score']
                            ?? null,

                        'verification_status' =>
                            $data['verification_status']
                            ?? 'needs_review',

                        'snapshot_reason' =>
                            $oldPrice === null
                                ? 'new_material'
                                : (
                                    $changed
                                        ? 'price_change'
                                        : 'daily_refresh'
                                ),
                    ]);

                $historyCreated = true;
            }

            return [
                'material' => $material,
                'history' => $history,
                'history_created' => $historyCreated,
                'changed' => $changed,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
            ];
        });
    }
}

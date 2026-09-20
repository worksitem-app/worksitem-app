<?php

namespace App\Services;

use App\Models\TenderPoz;
use Illuminate\Support\Facades\DB;

class TenderPricingService
{
    public function targetMargin(): float
    {
        $margin = (float) (
            DB::table('pricing_settings')
                ->where('key', 'tender_target_margin')
                ->value('value') ?? 0.60
        );

        return ($margin > 0 && $margin < 0.95) ? $margin : 0.60;
    }

    public function calculate(TenderPoz $poz, float $quantity): array
    {
        $quantity = max(0, $quantity);

        if ($poz->material_price !== null || $poz->installation_price !== null) {
            $officialBase =
                (float) ($poz->material_price ?? 0) +
                (float) ($poz->installation_price ?? 0);
        } else {
            $officialBase = (float) $poz->unit_price;
        }

        $margin = $this->targetMargin();

        $profitStatus = strtolower(trim(
            (string) ($poz->profit_status ?? 'unknown')
        ));

        if (!in_array($profitStatus, ['included', 'excluded', 'unknown'], true)) {
            $profitStatus = 'unknown';
        }

        if ($profitStatus === 'unknown') {
            return $this->reviewResult(
                $officialBase,
                $quantity,
                $margin,
                $poz,
                'Kaynak fiyatın yüklenici kârı/genel gider durumu doğrulanmamış.'
            );
        }

        $normalizedCost = $officialBase;

        if ($profitStatus === 'included') {
            $rate = (float) $poz->included_profit_rate;

            if ($rate <= 0) {
                return $this->reviewResult(
                    $officialBase,
                    $quantity,
                    $margin,
                    $poz,
                    'Kaynak fiyat kâr/genel gider içeriyor ancak dahil oran bilinmiyor.'
                );
            }

            $normalizedCost = $officialBase / (1 + $rate);
        }

        $offerUnitPrice = $normalizedCost / (1 - $margin);

        return [
            'status' => 'priced',
            'reason' => null,
            'official_unit_price' => round($officialBase, 2),
            'normalized_unit_cost' => round($normalizedCost, 2),
            'target_margin' => $margin,
            'offer_unit_price' => round($offerUnitPrice, 2),
            'quantity' => $quantity,
            'official_total' => round($officialBase * $quantity, 2),
            'cost_total' => round($normalizedCost * $quantity, 2),
            'offer_total' => round($offerUnitPrice * $quantity, 2),
            'vat_included' => (bool) $poz->vat_included,
        ];
    }

    private function reviewResult(
        float $officialBase,
        float $quantity,
        float $margin,
        TenderPoz $poz,
        string $reason
    ): array {
        return [
            'status' => 'requires_review',
            'reason' => $reason,
            'official_unit_price' => round($officialBase, 2),
            'normalized_unit_cost' => null,
            'target_margin' => $margin,
            'offer_unit_price' => null,
            'quantity' => $quantity,
            'official_total' => round($officialBase * $quantity, 2),
            'cost_total' => null,
            'offer_total' => null,
            'vat_included' => (bool) $poz->vat_included,
        ];
    }
}

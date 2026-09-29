<?php

namespace App\Http\Controllers\Pricing;

use App\Http\Controllers\Controller;
use App\Services\Pricing\BuildingEstimateService;
use App\Services\Pricing\CalculationSnapshotService;
use App\Services\Pricing\MarketEstimateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BuildingEstimateController extends Controller
{
    public function calculate(
        Request $request,
        BuildingEstimateService $buildingService,
        MarketEstimateService $marketService,
        CalculationSnapshotService $snapshotService
    ): JsonResponse {
        $validated = $request->validate([
            'building_type_key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
            ],
            'area' => [
                'required',
                'numeric',
                'gt:0',
                'max:10000000',
            ],
        ]);

        try {
            $official = $buildingService->estimate(
                (string) $validated['building_type_key'],
                (float) $validated['area']
            );

            $market = $marketService->estimate($official);
        } catch (\RuntimeException $exception) {
            throw ValidationException::withMessages([
                'building_type_key' => $exception->getMessage(),
            ]);
        }

        /*
         * Tek server-side snapshot:
         * - resmî referans
         * - Worksitem piyasa tahmini
         *
         * Browser fiyatın doğruluk kaynağı değildir.
         */
        $estimate = $official;

        $estimate['market_estimate'] = [
            'total' => $market['market']['total'],
            'estimate_low' => $market['market']['estimate_low'],
            'estimate_high' => $market['market']['estimate_high'],
            'market_version' => $market['market_version'],
            'budget_breakdown' => $market['budget_breakdown'],

            /*
             * Internal alanlar API response'a gönderilmez.
             * Yalnızca güvenilir server snapshot'ında tutulur.
             */
            'internal' => $market['internal'],
        ];

        $snapshot = $snapshotService->store(
            request: $request,
            calculationType: 'building_official_reference',
            estimate: $estimate
        );

        return response()->json([
            'success' => true,
            'calculation_id' => $snapshot['calculation_id'],

            /*
             * Müşteri-facing sonuç.
             */
            'total' => $market['market']['total'],
            'estimate_low' => $market['market']['estimate_low'],
            'estimate_high' => $market['market']['estimate_high'],
            'budget_breakdown' => $market['budget_breakdown'],

            'building_type_key' => $official['building_type_key'],
            'building_type_name' => $official['building_type_name'],
            'category_key' => $official['category_key'],
            'category_name' => $official['category_name'],
            'class_code' => $official['class_code'],
            'class_name' => $official['class_name'],
            'area' => $official['area'],

            /*
             * Kaynak/audit için zararsız metadata.
             * İç piyasa katsayıları burada özellikle yoktur.
             */
            'period' => $official['period'],
            'market_version' => $market['market_version'],
        ]);
    }
}

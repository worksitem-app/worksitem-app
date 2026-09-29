<?php

namespace App\Http\Controllers\Pricing;

use App\Http\Controllers\Controller;
use App\Services\Pricing\CalculationSnapshotService;
use App\Services\Pricing\RenovationEstimateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RenovationEstimateController extends Controller
{
    public function calculate(
        Request $request,
        RenovationEstimateService $renovationService,
        CalculationSnapshotService $snapshotService
    ): JsonResponse {
        $validated = $request->validate([
            'scope' => [
                'required',
                'string',
                'in:partial,medium,complete',
            ],
            'quality' => [
                'required',
                'string',
                'in:standard',
            ],
            'area' => [
                'required',
                'numeric',
                'gt:0',
                'max:1000000',
            ],
        ]);

        try {
            $estimate = $renovationService->estimate(
                (string) $validated['scope'],
                (string) $validated['quality'],
                (float) $validated['area']
            );
        } catch (\RuntimeException $exception) {
            throw ValidationException::withMessages([
                'scope' => $exception->getMessage(),
            ]);
        }

        $snapshot = $snapshotService->store(
            request: $request,
            calculationType: 'renovation_market_estimate',
            estimate: $estimate
        );

        return response()->json([
            'success' => true,
            'calculation_id' => $snapshot['calculation_id'],

            'total' => $estimate['market_estimate']['total'],
            'estimate_low' =>
                $estimate['market_estimate']['estimate_low'],
            'estimate_high' =>
                $estimate['market_estimate']['estimate_high'],

            'scope' => $estimate['scope'],
            'scope_label' => $estimate['scope_label'],
            'quality' => $estimate['quality'],
            'quality_label' => $estimate['quality_label'],
            'area' => $estimate['area'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Pricing;

use App\Http\Controllers\Controller;
use App\Services\Pricing\CalculationSnapshotService;
use App\Services\Pricing\TileEstimateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class TileEstimateController extends Controller
{
    public function calculate(
        Request $request,
        TileEstimateService $tileEstimateService,
        CalculationSnapshotService $snapshotService
    ): JsonResponse {
        $validated =
            $request->validate([
                'material' => [
                    'required',
                    'string',
                    'in:ceramic,porcelain_matt,porcelain_glazed,porcelain_polished',
                ],

                'mode' => [
                    'required',
                    'string',
                    'in:quick,detailed',
                ],

                'tile_size' => [
                    'required',
                    'string',
                    'max:30',
                ],

                'waste_rate' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:30',
                ],

                'grout_mm' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:50',
                ],

                'surfaces' => [
                    'required',
                    'array',
                    'min:1',
                    'max:50',
                ],

                'surfaces.*.type' => [
                    'required',
                    'string',
                    'in:floor,wall',
                ],

                'surfaces.*.area' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:1000000',
                ],

                'surfaces.*.width' => [
                    'nullable',
                    'numeric',
                    'gt:0',
                    'max:10000',
                ],

                'surfaces.*.height' => [
                    'nullable',
                    'numeric',
                    'gt:0',
                    'max:10000',
                ],

                'surfaces.*.deduct' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:1000000',
                ],
            ]);

        try {
            $estimate =
                $tileEstimateService
                    ->estimate(
                        material:
                            (string)
                            $validated['material'],

                        tileSize:
                            (string)
                            $validated['tile_size'],

                        wastePercent:
                            (float)
                            $validated['waste_rate'],

                        groutMm:
                            (float)
                            $validated['grout_mm'],

                        mode:
                            (string)
                            $validated['mode'],

                        surfaces:
                            $validated['surfaces']
                    );

        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages([
                'tile_price' =>
                    $exception->getMessage(),
            ]);
        }

        $snapshot =
            $snapshotService->store(
                request:
                    $request,

                calculationType:
                    'tile_market_estimate',

                estimate:
                    $estimate
            );

        return response()->json([
            'success' =>
                true,

            'calculation_id' =>
                $snapshot['calculation_id'],

            'calculation_type' =>
                'tile_market_estimate',

            ...$estimate,
        ]);
    }
}

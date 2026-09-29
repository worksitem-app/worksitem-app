<?php

namespace App\Services\Pricing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CalculationSnapshotService
{
    private const TTL_HOURS = 2;

    public function store(
        Request $request,
        string $calculationType,
        array $estimate
    ): array {
        $calculationId = (string) Str::uuid();

        $snapshot = [
            'calculation_id' => $calculationId,
            'calculation_type' => $calculationType,
            'estimate' => $estimate,
            'created_at' => now()->toIso8601String(),
        ];

        Cache::put(
            $this->cacheKey($request, $calculationId),
            $snapshot,
            now()->addHours(self::TTL_HOURS)
        );

        return $snapshot;
    }

    public function get(
        Request $request,
        string $calculationId
    ): ?array {
        $calculationId = trim($calculationId);

        if ($calculationId === '') {
            return null;
        }

        $snapshot = Cache::get(
            $this->cacheKey($request, $calculationId)
        );

        if (!is_array($snapshot)) {
            return null;
        }

        if (
            ($snapshot['calculation_id'] ?? null)
            !== $calculationId
        ) {
            return null;
        }

        if (
            !isset($snapshot['estimate'])
            || !is_array($snapshot['estimate'])
        ) {
            return null;
        }

        return $snapshot;
    }

    private function cacheKey(
        Request $request,
        string $calculationId
    ): string {
        $sessionHash = hash_hmac(
            'sha256',
            $request->session()->getId(),
            (string) config('app.key')
        );

        return 'building_calc_'
            . $calculationId
            . '_'
            . $sessionHash;
    }
}

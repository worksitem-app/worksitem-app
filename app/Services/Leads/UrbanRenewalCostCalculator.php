<?php

namespace App\Services\Leads;

use Illuminate\Validation\ValidationException;

class UrbanRenewalCostCalculator
{
    public function __construct(
        private readonly PricingEngine $pricingEngine
    ) {}

    /**
     * Kentsel dönüşüm maliyeti — 6 kalem + devlet desteği
     */
    public function calculate(
        float $area,
        string $city = 'other',
        string $classCode = '4A',
        string $quality = 'standart',
        int $relocationMonths = 18
    ): array {
        if ($area < 50) {
            throw ValidationException::withMessages([
                'area' => 'Kentsel dönüşüm hesabı için minimum 50 m² gereklidir.'
            ]);
        }

        $pricing = $this->pricingEngine->getPricingSnapshot();
        $cityKey = $this->normalizeCity($city);
        $cityM = $this->cityMultiplier($cityKey);
        $qualityM = $this->qualityMultiplier($quality);

        // 1) Yeni yapı
        $basePrice = $pricing['bina_sinif_4a_m2'];
        $newBuildTotal = $area * $basePrice * $qualityM * $cityM;
        $newBuild = [
            'total' => round($newBuildTotal),
            'unit_cost' => round($basePrice * $qualityM * $cityM),
            'note' => 'Yeni yapı maliyeti',
        ];

        // 2) Yıkım
        $demolition = [
            'total' => round($area * 350 * $cityM),
            'note' => 'Yıkım maliyeti',
        ];

        // 3) Tahliye + kira desteği
        $relocation = [
            'total' => round($area * 120 * $cityM * $relocationMonths),
            'note' => $relocationMonths . ' ay tahliye + kira',
        ];

        // 4) Ruhsat + harçlar
        $permits = [
            'total' => round(($area * 80) + 15000),
            'note' => 'Belediye harçları + ruhsat',
        ];

        // 5) Nakliye + moloz
        $transport = [
            'total' => round($area * 90),
            'note' => 'Moloz nakliyesi',
        ];

        // 6) Danışmanlık (proje, statik, denetim)
        $consultancy = [
            'total' => round($newBuild['total'] * 0.03),
            'note' => 'Proje, statik, denetim',
        ];

        // Devlet desteği (4% + alan başı 45 TL)
        $support = [
            'total' => round(($newBuild['total'] * 0.04) + ($area * 45)),
            'note' => 'Tahmini devlet desteği',
        ];

        // Toplam
        $grossTotal = $newBuild['total'] + $demolition['total'] + $relocation['total']
                    + $permits['total'] + $transport['total'] + $consultancy['total'];

        $netTotal = max($grossTotal - $support['total'], $grossTotal * 0.85);

        return [
            'gross_total' => round($grossTotal),
            'net_total' => round($netTotal),
            'min' => round($netTotal * 0.88),
            'max' => round($netTotal * 1.12),
            'breakdown' => [
                'yeni_yapi' => $newBuild,
                'yikim' => $demolition,
                'tahliye_kira' => $relocation,
                'ruhsat_harclar' => $permits,
                'nakliye_moloz' => $transport,
                'danismanlik' => $consultancy,
                'devlet_destegi' => $support,
            ],
            'price_version' => $pricing['version'],
            'price_snapshot' => [
                'mode' => 'urban_renewal',
                'city' => $cityKey,
                'quality' => $quality,
                'class' => $classCode,
                'used_prices' => $pricing,
            ],
            'calculated_at' => now()->toIso8601String(),
        ];
    }

    private function normalizeCity(string $city): string
    {
        $map = [
            'istanbul' => 'istanbul',
            'i̇stanbul' => 'istanbul',
            'ankara' => 'ankara',
            'izmir' => 'izmir',
            'antalya' => 'antalya',
        ];
        return $map[strtolower($city)] ?? 'other';
    }

    private function cityMultiplier(string $cityKey): float
    {
        return [
            'istanbul' => 1.15,
            'ankara' => 1.00,
            'izmir' => 1.08,
            'antalya' => 1.08,
            'other' => 0.95,
        ][$cityKey] ?? 0.95;
    }

    private function qualityMultiplier(string $quality): float
    {
        return [
            'ekonomik' => 0.85,
            'standart' => 1.00,
            'premium' => 1.28,
            'luks' => 1.60,
        ][$quality] ?? 1.0;
    }
}

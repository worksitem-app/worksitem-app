<?php
namespace App\Services;

use App\Models\AiGeneration;
use Illuminate\Support\Facades\DB;

class AiCostEstimationService
{
    public function estimate(int $generationId, float $roomArea): array
    {
        $generation = AiGeneration::findOrFail($generationId);

        $floorArea = $roomArea;
        $ceilingArea = $roomArea;
        $wallArea = $roomArea * 1.2;

        $items = [];

        $floorPrice = $this->getPrice('parke_laminat_ac3_m2', 850);
        $items[] = [
            'label' => 'Zemin Kaplama',
            'quantity' => $floorArea,
            'unit' => 'm²',
            'price' => $floorPrice,
            'total' => $floorArea * $floorPrice,
        ];

        $paintPrice = $this->getPrice('boya_standart_m2', 247);
        $items[] = [
            'label' => 'Duvar Boyası',
            'quantity' => $wallArea,
            'unit' => 'm²',
            'price' => $paintPrice,
            'total' => $wallArea * $paintPrice,
        ];

        $ceilingPrice = $this->getPrice('siva_alci_siva_m2', 180);
        $items[] = [
            'label' => 'Tavan Sıvası',
            'quantity' => $ceilingArea,
            'unit' => 'm²',
            'price' => $ceilingPrice,
            'total' => $ceilingArea * $ceilingPrice,
        ];

        $furnitureBudget = $this->getFurnitureBudget($generation->style, $roomArea);
        $items[] = [
            'label' => 'Mobilya & Dekorasyon',
            'quantity' => 1,
            'unit' => 'paket',
            'price' => $furnitureBudget,
            'total' => $furnitureBudget,
        ];

        $laborPrice = $this->getPrice('iscilik_usta_yevmiye', 3500);
        $laborDays = ceil($roomArea / 10);
        $items[] = [
            'label' => 'İşçilik',
            'quantity' => $laborDays,
            'unit' => 'gün',
            'price' => $laborPrice,
            'total' => $laborDays * $laborPrice,
        ];

        $total = array_sum(array_column($items, 'total'));
        $vat = $total * 0.20;
        $grandTotal = $total + $vat;

        return [
            'status' => 'success',
            'room_area' => $roomArea,
            'wall_area' => round($wallArea, 2),
            'ceiling_area' => round($ceilingArea, 2),
            'items' => $items,
            'subtotal' => round($total, 2),
            'vat' => round($vat, 2),
            'total' => round($grandTotal, 2),
            'style' => $generation->style,
        ];
    }

    private function getFurnitureBudget(?string $style, float $area): float
    {
        $perM2 = match ($style) {
            'Lüks' => 3500,
            'Klasik' => 2500,
            'Endüstriyel' => 2000,
            'Modern' => 2200,
            'Japandi' => 1800,
            'Minimalist' => 1500,
            default => 2000,
        };

        return $area * $perM2;
    }

    private function getPrice(string $key, float $fallback): float
    {
        try {
            $price = DB::table('unit_prices')
                ->where('key', $key)
                ->where('price_pool', 'market')
                ->where('is_active', true)
                ->value('price');

            return $price ? (float) $price : $fallback;
        } catch (\Exception $e) {
            return $fallback;
        }
    }
}

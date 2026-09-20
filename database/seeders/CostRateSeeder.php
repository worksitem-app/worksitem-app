<?php

namespace Database\Seeders;

use App\Models\CostRate;
use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class CostRateSeeder extends Seeder
{
    public function run(): void
    {
        $villa = ProjectType::where('slug', 'villa')->first();

        if (!$villa) {
            return;
        }

        $rates = [
            ['key' => 'base_price_m2', 'value' => 25000, 'description' => 'Standart Betonarme m2 taban fiyatı (TL)'],
            ['key' => 'quality_premium_multiplier', 'value' => 1.35, 'description' => 'Üst segment kalite çarpanı'],
            ['key' => 'quality_luxury_multiplier', 'value' => 1.75, 'description' => 'Lüks segment kalite çarpanı'],
            ['key' => 'system_steel_multiplier', 'value' => 1.25, 'description' => 'Hafif Çelik sistem çarpanı'],
            ['key' => 'system_wood_multiplier', 'value' => 1.60, 'description' => 'Ahşap sistem çarpanı'],
            ['key' => 'pool_base_cost', 'value' => 600000, 'description' => 'Standart havuz maktu maliyeti'],
            ['key' => 'garage_base_cost', 'value' => 300000, 'description' => 'Kapalı otopark maktu maliyeti'],
            ['key' => 'landscape_m2_cost', 'value' => 2000, 'description' => 'Peyzaj m2 maliyeti'],
        ];

        foreach ($rates as $rate) {
            CostRate::updateOrCreate(
                ['project_type_id' => $villa->id, 'key' => $rate['key']],
                ['value' => $rate['value'], 'description' => $rate['description']]
            );
        }
    }
}

<?php

namespace App\Services\Pricing;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class PricingSettingsService
{
    private const CACHE_KEY =
        'central_pricing_settings';

    public function all(): array
    {
        if (!Schema::hasTable('pricing_settings')) {
            return [];
        }

        return Cache::remember(
            self::CACHE_KEY,
            3600,
            function (): array {

                $rows = DB::table('pricing_settings')
                    ->orderBy('key')
                    ->get();

                $result = [];

                foreach ($rows as $row) {

                    /*
                    |--------------------------------------------------------------------------
                    | Duplicate anahtar fail-safe
                    |--------------------------------------------------------------------------
                    */

                    if (
                        array_key_exists(
                            $row->key,
                            $result
                        )
                    ) {
                        throw new RuntimeException(
                            'Tekrarlanan fiyatlandırma parametresi: '.
                            $row->key
                        );
                    }

                    $result[$row->key] = [
                        'key' =>
                            (string) $row->key,

                        'label' =>
                            (string) $row->label,

                        'value' =>
                            (string) $row->value,

                        'type' =>
                            (string) $row->type,

                        'description' =>
                            $row->description,
                    ];
                }

                return $result;
            }
        );
    }


    public function get(
        string $key
    ): ?array {

        $settings = $this->all();

        return $settings[$key] ?? null;
    }


    public function decimal(
        string $key
    ): ?float {

        $row = $this->get($key);

        if (!$row) {
            return null;
        }

        if (
            ($row['type'] ?? null)
            !== 'decimal'
        ) {
            return null;
        }

        if (
            !is_numeric(
                $row['value'] ?? null
            )
        ) {
            return null;
        }

        return (float) $row['value'];
    }


    public function requireDecimal(
        string $key
    ): float {

        $value = $this->decimal($key);

        if ($value === null) {
            throw new RuntimeException(
                'Fiyatlandırma parametresi bulunamadı: '.
                $key
            );
        }

        if ($value < 0) {
            throw new RuntimeException(
                'Fiyatlandırma parametresi negatif olamaz: '.
                $key
            );
        }

        return $value;
    }


    public function clearCache(): void
    {
        Cache::forget(
            self::CACHE_KEY
        );

        Cache::forget(
            'pricing_engine_snapshot'
        );

        Cache::forget(
            'global_system_prices'
        );
    }
}

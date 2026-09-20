<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PriceService
{
    /**
     * Worksitem unit_prices kaydını key ile getirir.
     *
     * Fiyat bulunamazsa NULL döner.
     * Kesinlikle uydurma fallback fiyat üretmez.
     */
    public function byKey(string $key): ?array
    {
        if (!Schema::hasTable('unit_prices')) {
            return null;
        }

        $row = DB::table('unit_prices')
            ->where('key', $key)
            ->where('price_pool', 'market')->where('is_active', true)
            ->first();

        if (!$row) {
            return null;
        }

        return [
            'source' => 'unit_prices',
            'id' => $row->id ?? null,
            'key' => $row->key ?? $key,
            'category' => $row->category ?? null,
            'name' => $row->name ?? null,
            'price' => isset($row->price)
                ? (float) $row->price
                : null,
            'unit' => $row->unit ?? null,
        ];
    }


    /**
     * YFK/rayiç fiyatını poz numarasıyla getirir.
     *
     * period verilmezse en yeni aktif dönem tercih edilir.
     */
    public function byPoz(
        string $pozNo,
        ?string $period = null,
        string $source = 'YFK'
    ): ?array {

        if (!Schema::hasTable('rayic_prices')) {
            return null;
        }

        $query = DB::table('rayic_prices')
            ->where('poz_no', $pozNo);

        if (
            Schema::hasColumn('rayic_prices', 'source')
        ) {
            $query->where('source', $source);
        }

        if (
            Schema::hasColumn('rayic_prices', 'is_active')
        ) {
            $query->where('is_active', true);
        }

        if ($period) {

            $query->where('period', $period);

        } else {

            $query->orderByDesc('period');
        }

        $row = $query->first();

        if (!$row) {
            return null;
        }

        return [
            'source' => 'rayic_prices',
            'source_name' => $row->source ?? $source,
            'period' => $row->period ?? null,
            'poz_no' => $row->poz_no ?? $pozNo,
            'name' => $row->description ?? null,
            'price' => isset($row->price)
                ? (float) $row->price
                : null,
            'unit' => $row->unit ?? null,
            'source_file' => $row->source_file ?? null,
            'source_page' => $row->source_page ?? null,
        ];
    }


    /**
     * Önce resmi poz, sonra Worksitem key.
     *
     * Hiçbiri bulunmazsa:
     * found=false
     *
     * Numeric fallback YOK.
     */
    public function resolve(
        ?string $pozNo = null,
        ?string $unitPriceKey = null,
        ?string $period = null
    ): array {

        if ($pozNo) {

            $rayic = $this->byPoz(
                $pozNo,
                $period
            );

            if ($rayic) {

                return [
                    'found' => true,
                    ...$rayic,
                ];
            }
        }

        if ($unitPriceKey) {

            $unitPrice = $this->byKey(
                $unitPriceKey
            );

            if ($unitPrice) {

                return [
                    'found' => true,
                    ...$unitPrice,
                ];
            }
        }

        return [
            'found' => false,
            'source' => null,
            'price' => null,
            'unit' => null,
            'poz_no' => $pozNo,
            'key' => $unitPriceKey,
            'message' =>
                'Bu iş kalemi için doğrulanmış birim fiyat bulunamadı.',
        ];
    }


    /**
     * Kategori içindeki bütün fiyatları döner.
     */
    public function category(string $category): array
    {
        if (!Schema::hasTable('unit_prices')) {
            return [];
        }

        return DB::table('unit_prices')
            ->where('category', $category)
            ->where('price_pool', 'market')->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($row) {

                return [
                    'id' => $row->id ?? null,
                    'key' => $row->key ?? null,
                    'category' => $row->category ?? null,
                    'name' => $row->name ?? null,
                    'price' => isset($row->price)
                        ? (float) $row->price
                        : null,
                    'unit' => $row->unit ?? null,
                    'source' => 'unit_prices',
                ];
            })
            ->all();
    }


    /**
     * Güvenli maliyet hesabı.
     *
     * Fiyat yoksa total üretmez.
     */
    public function calculate(
        float $quantity,
        ?string $pozNo = null,
        ?string $unitPriceKey = null,
        ?string $period = null
    ): array {

        if ($quantity < 0) {

            return [
                'found' => false,
                'quantity' => $quantity,
                'price' => null,
                'total' => null,
                'message' => 'Miktar negatif olamaz.',
            ];
        }

        $price = $this->resolve(
            $pozNo,
            $unitPriceKey,
            $period
        );

        if (!$price['found']) {

            return [
                ...$price,
                'quantity' => $quantity,
                'total' => null,
            ];
        }

        return [
            ...$price,
            'quantity' => $quantity,
            'total' => round(
                $quantity * (float) $price['price'],
                2
            ),
        ];
    }
}

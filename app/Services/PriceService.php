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
            ->where('price_pool', 'market')
            ->where('is_active', true)
            ->where('review_status', 'approved')
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
            'source_name' => $row->source_name ?? null,
            'source_date' => $row->source_date ?? null,
            'vat_included' => isset($row->vat_included)
                ? (bool) $row->vat_included
                : null,
            'review_status' => $row->review_status ?? null,
            'notes' => $row->notes ?? null,
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
     * Geniş resmî poz arşivinden birim fiyat çözer.
     *
     * institution zorunludur:
     * Aynı poz numarası farklı kurumlarda bulunabilir.
     *
     * period verilmezse en yeni aktif kayıt kullanılır.
     */
    public function byTenderPoz(
        string $pozNo,
        string $institution,
        ?string $period = null
    ): ?array {

        $pozNo =
            trim($pozNo);

        $institution =
            trim($institution);


        if (
            $pozNo === '' ||
            $institution === '' ||
            !Schema::hasTable('tender_pozs')
        ) {
            return null;
        }


        $query =
            DB::table('tender_pozs')
                ->where(
                    'poz_no',
                    $pozNo
                )
                ->where(
                    'institution',
                    $institution
                );


        if (
            Schema::hasColumn(
                'tender_pozs',
                'is_active'
            )
        ) {
            $query->where(
                'is_active',
                true
            );
        }


        if ($period) {

            $query->where(
                'period',
                $period
            );

        } else {

            $query
                ->orderByDesc('year')
                ->orderByDesc('period')
                ->orderByDesc('id');
        }


        $row =
            $query->first();


        if (!$row) {
            return null;
        }


        $price =
            isset($row->unit_price)
                ? (float) $row->unit_price
                : 0.0;


        if ($price <= 0) {
            return null;
        }


        return [
            'source' =>
                'tender_pozs',

            'source_name' =>
                $row->institution
                ?? $institution,

            'institution' =>
                $row->institution
                ?? $institution,

            'year' =>
                $row->year
                ?? null,

            'period' =>
                $row->period
                ?? null,

            'poz_no' =>
                $row->poz_no
                ?? $pozNo,

            'name' =>
                $row->description
                ?? null,

            'price' =>
                $price,

            'unit' =>
                $row->unit
                ?? null,

            'price_type' =>
                $row->price_type
                ?? null,

            'profit_status' =>
                $row->profit_status
                ?? null,

            'profit_included' =>
                isset($row->profit_included)
                    ? (bool) $row->profit_included
                    : null,

            'included_profit_rate' =>
                isset($row->included_profit_rate)
                    ? (float) $row->included_profit_rate
                    : null,

            'vat_included' =>
                isset($row->vat_included)
                    ? (bool) $row->vat_included
                    : null,

            'source_file' =>
                $row->source_file
                ?? null,

            'source_page' =>
                $row->source_page
                ?? null,

            'source_note' =>
                $row->source_note
                ?? null,
        ];
    }

    /**
     * Yapı sınıfı m² fiyatını merkezi olarak çözer.
     *
     * Öncelik:
     * 1. unit_prices / market / active / approved
     * 2. building_class_costs resmî referansı
     *
     * Numeric fallback YOK.
     */
    public function buildingClass(
        string $classCode,
        ?string $period = '2026'
    ): ?array {

        $classCode = strtoupper(
            trim($classCode)
        );

        if (
            !preg_match(
                '/^[1-5][A-E]$/',
                $classCode
            )
        ) {
            return null;
        }

        $key =
            'bina_sinif_'.
            strtolower($classCode).
            '_m2';


        /*
        |--------------------------------------------------------------------------
        | 1. Yönetilebilir piyasa fiyatı
        |--------------------------------------------------------------------------
        */

        if (Schema::hasTable('unit_prices')) {

            $market = DB::table('unit_prices')
                ->where('key', $key)
                ->where('price_pool', 'market')
                ->where('is_active', true)
                ->where('review_status', 'approved')
                ->first();

            if (
                $market &&
                isset($market->price) &&
                (float) $market->price > 0
            ) {

                return [
                    'source' => 'unit_prices',
                    'resolution' => 'market_override',

                    'id' => $market->id ?? null,
                    'key' => $key,

                    'class_code' => $classCode,

                    'price' => (float) $market->price,
                    'unit' => $market->unit ?? 'TL/m2',

                    'price_pool' =>
                        $market->price_pool ?? 'market',

                    'review_status' =>
                        $market->review_status ?? null,

                    'source_name' =>
                        $market->source_name ?? null,

                    'source_date' =>
                        $market->source_date ?? null,

                    'vat_included' =>
                        (bool) ($market->vat_included ?? false),

                    'period' => $period,
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | 2. Resmî yapı sınıfı fallback
        |--------------------------------------------------------------------------
        */

        if (
            !Schema::hasTable('building_classes') ||
            !Schema::hasTable('building_class_costs')
        ) {
            return null;
        }

        $query = DB::table('building_classes as bc')
            ->join(
                'building_class_costs as bcc',
                'bcc.building_class_id',
                '=',
                'bc.id'
            )
            ->where('bc.code', $classCode)
            ->where('bc.is_active', true)
            ->where('bcc.is_active', true);

        if ($period !== null) {
            $query->where(
                'bcc.period',
                $period
            );
        } else {
            $query->orderByDesc(
                'bcc.period'
            );
        }

        $official = $query
            ->select([
                'bc.id as building_class_id',
                'bc.code as class_code',
                'bc.name as class_name',

                'bcc.id as building_class_cost_id',
                'bcc.unit_price',
                'bcc.unit',
                'bcc.period',
                'bcc.source_name',
                'bcc.source_date',
                'bcc.vat_included',
            ])
            ->first();

        if (
            !$official ||
            !isset($official->unit_price) ||
            (float) $official->unit_price <= 0
        ) {
            return null;
        }

        return [
            'source' => 'building_class_costs',
            'resolution' => 'official_fallback',

            'id' =>
                $official->building_class_cost_id ?? null,

            'key' => $key,

            'class_code' =>
                $official->class_code ?? $classCode,

            'class_name' =>
                $official->class_name ?? null,

            'price' =>
                (float) $official->unit_price,

            'unit' =>
                $official->unit ?? 'TL/m2',

            'price_pool' => 'official',

            'review_status' =>
                'official_reference',

            'source_name' =>
                $official->source_name ?? null,

            'source_date' =>
                $official->source_date ?? null,

            'vat_included' =>
                (bool) ($official->vat_included ?? false),

            'period' =>
                $official->period ?? $period,
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
            ->where('price_pool', 'market')
            ->where('is_active', true)
            ->where('review_status', 'approved')
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

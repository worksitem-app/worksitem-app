<?php

namespace App\Services\Pricing;

use App\Models\MaterialPriceObservation;
use RuntimeException;

class TileEstimateService
{
    public function __construct(
        private readonly MaterialBenchmarkAnalyzer $benchmarkAnalyzer
    ) {
    }

    public function estimate(
        string $material,
        string $tileSize,
        float $wastePercent,
        float $groutMm,
        string $mode,
        array $surfaces
    ): array {
        $material = trim(mb_strtolower($material));
        $mode = trim(mb_strtolower($mode));

        if (!in_array($mode, ['quick', 'detailed'], true)) {
            throw new RuntimeException(
                'Hesaplama modu geçersiz.'
            );
        }

        if ($wastePercent < 0 || $wastePercent > 30) {
            throw new RuntimeException(
                'Fire oranı geçersiz.'
            );
        }

        if ($groutMm < 0 || $groutMm > 50) {
            throw new RuntimeException(
                'Derz genişliği geçersiz.'
            );
        }

        if (empty($surfaces) || count($surfaces) > 50) {
            throw new RuntimeException(
                'Uygulama yüzeyleri geçersiz.'
            );
        }

        if ($mode === 'quick' && count($surfaces) !== 1) {
            throw new RuntimeException(
                'Hızlı hesap için tek uygulama yüzeyi bekleniyor.'
            );
        }

        [$tileWidthCm, $tileHeightCm] =
            $this->tileDimensions($tileSize);

        $tileWidthM = $tileWidthCm / 100;
        $tileHeightM = $tileHeightCm / 100;
        $tileAreaM2 = $tileWidthM * $tileHeightM;
        $groutM = $groutMm / 1000;

        if ($tileAreaM2 <= 0) {
            throw new RuntimeException(
                'Karo ölçüsü geçersiz.'
            );
        }

        $netArea = 0.0;
        $orientationA = 0;
        $orientationB = 0;

        $applicationTypes = [];
        $normalizedSurfaces = [];

        foreach ($surfaces as $index => $surface) {
            if (!is_array($surface)) {
                throw new RuntimeException(
                    'Uygulama yüzeyi okunamadı.'
                );
            }

            $type = trim(
                mb_strtolower(
                    (string) ($surface['type'] ?? '')
                )
            );

            if (!in_array($type, ['floor', 'wall'], true)) {
                throw new RuntimeException(
                    'Uygulama yüzeyi türü geçersiz.'
                );
            }

            $applicationTypes[] = $type;

            if ($mode === 'quick') {
                $area = (float) ($surface['area'] ?? 0);

                if ($area <= 0 || $area > 1000000) {
                    throw new RuntimeException(
                        'Uygulama alanı geçersiz.'
                    );
                }

                $netArea += $area;

                $normalizedSurfaces[] = [
                    'type' => $type,
                    'area' => round($area, 4),
                ];

                continue;
            }

            $width = (float) ($surface['width'] ?? 0);
            $height = (float) ($surface['height'] ?? 0);
            $deduct = (float) ($surface['deduct'] ?? 0);

            if (
                $width <= 0
                || $height <= 0
                || $width > 10000
                || $height > 10000
            ) {
                throw new RuntimeException(
                    'Yüzey ölçüleri geçersiz.'
                );
            }

            $grossSurfaceArea = $width * $height;

            if (
                $deduct < 0
                || $deduct >= $grossSurfaceArea
            ) {
                throw new RuntimeException(
                    'Yüzey düşüm alanı geçersiz.'
                );
            }

            $area = $grossSurfaceArea - $deduct;

            if ($area <= 0) {
                throw new RuntimeException(
                    'Net yüzey alanı geçersiz.'
                );
            }

            $netArea += $area;

            /*
             * Browser V4 detaylı yerleşim algoritmasının
             * server-side eşdeğeri.
             */
            $theoretical =
                (int) ceil(
                    $area / $tileAreaM2
                );

            $deductedTiles =
                (int) floor(
                    $deduct / $tileAreaM2
                );

            $layoutA =
                (
                    (int) ceil(
                        ($width + $groutM)
                        /
                        ($tileWidthM + $groutM)
                    )
                )
                *
                (
                    (int) ceil(
                        ($height + $groutM)
                        /
                        ($tileHeightM + $groutM)
                    )
                )
                -
                $deductedTiles;

            $layoutB =
                (
                    (int) ceil(
                        ($width + $groutM)
                        /
                        ($tileHeightM + $groutM)
                    )
                )
                *
                (
                    (int) ceil(
                        ($height + $groutM)
                        /
                        ($tileWidthM + $groutM)
                    )
                )
                -
                $deductedTiles;

            $orientationA +=
                max(
                    $theoretical,
                    $layoutA
                );

            $orientationB +=
                max(
                    $theoretical,
                    $layoutB
                );

            $normalizedSurfaces[] = [
                'type' => $type,
                'width' => round($width, 4),
                'height' => round($height, 4),
                'deduct' => round($deduct, 4),
                'area' => round($area, 4),
            ];
        }

        if ($netArea <= 0 || $netArea > 1000000) {
            throw new RuntimeException(
                'Toplam uygulama alanı geçersiz.'
            );
        }

        $theoreticalTiles =
            (int) ceil(
                $netArea / $tileAreaM2
            );

        $layoutTiles =
            $mode === 'detailed'
                ? min(
                    $orientationA,
                    $orientationB
                )
                : $theoreticalTiles;

        $baseTiles =
            max(
                $theoreticalTiles,
                $layoutTiles
            );

        /*
         * KRİTİK:
         * recommended_tiles artık browser'dan GELMİYOR.
         * Server kendisi hesaplıyor.
         */
        $recommendedTiles =
            (int) ceil(
                $baseTiles
                *
                (1 + ($wastePercent / 100))
            );

        $grossArea =
            $recommendedTiles
            * $tileAreaM2;

        $applicationTypes =
            array_values(
                array_unique(
                    $applicationTypes
                )
            );

        /*
         * Her yüzey için piyasa anahtarı bulunmalı.
         * Eksik eşleşme varsa başka yüzeyin fiyatını
         * tüm projeye yaymıyoruz.
         */
        $marketKeys = [];

        foreach ($applicationTypes as $type) {
            $marketKey =
                $this->marketKey(
                    $material,
                    $type,
                    $tileSize
                );

            if ($marketKey === null) {
                throw new RuntimeException(
                    'Bu malzeme, yüzey ve karo ölçüsü kombinasyonu için doğrulanmış piyasa fiyatı bulunamadı.'
                );
            }

            $marketKeys[] = $marketKey;
        }

        $marketKeys =
            array_values(
                array_unique($marketKeys)
            );

        /*
         * İlk zero-trust sürümünde farklı piyasa anahtarlarını
         * tek bütçede karıştırmıyoruz.
         */
        if (count($marketKeys) !== 1) {
            throw new RuntimeException(
                'Karma yüzey fiyatlaması henüz doğrulanmış tek fiyat sözleşmesine uygun değil.'
            );
        }

        $marketKey = $marketKeys[0];

        $analysis =
            $this->benchmarkAnalyzer
                ->analyze(
                    $marketKey,
                    7
                );

        if (
            ($analysis['status'] ?? null)
            !== 'ready'
        ) {
            throw new RuntimeException(
                'Piyasa fiyat verisi mühendislik kalite kontrolünden geçmedi.'
            );
        }

        $benchmark =
            $analysis['benchmark'] ?? null;

        if (!is_array($benchmark)) {
            throw new RuntimeException(
                'Piyasa benchmark verisi doğrulanamadı.'
            );
        }

        $unit =
            (string) (
                $benchmark['unit']
                ?? ''
            );

        $currency =
            strtoupper(
                (string) (
                    $benchmark['currency']
                    ?? ''
                )
            );

        $vatStatus =
            (string) (
                $benchmark['vat_status']
                ?? ''
            );

        if (
            !in_array(
                $unit,
                ['TL/m²', 'TL/m2'],
                true
            )
            || $currency !== 'TRY'
            || $vatStatus !== 'included'
        ) {
            throw new RuntimeException(
                'Piyasa fiyat birimi veya KDV durumu doğrulanamadı.'
            );
        }

        $unitPrice =
            (float) (
                $benchmark['benchmark_price']
                ?? 0
            );

        if ($unitPrice <= 0) {
            throw new RuntimeException(
                'Piyasa birim fiyatı geçersiz.'
            );
        }

        $estimatedCost =
            round(
                $grossArea * $unitPrice,
                2
            );

        $estimatedMin =
            round(
                $estimatedCost * 0.90,
                2
            );

        $estimatedMax =
            round(
                $estimatedCost * 1.10,
                2
            );

        $sources =
            array_values(
                array_unique(
                    array_filter(
                        $benchmark['sources']
                        ?? []
                    )
                )
            );

        $latestObservedAt =
            MaterialPriceObservation::query()
                ->where(
                    'material_key',
                    $marketKey
                )
                ->when(
                    !empty($sources),
                    fn ($query) =>
                        $query->whereIn(
                            'source_name',
                            $sources
                        )
                )
                ->max('observed_at');

        $versionPayload = [
            'market_key' => $marketKey,

            'benchmark_price' =>
                round($unitPrice, 4),

            'sources' => $sources,

            'prices' =>
                $benchmark['prices']
                ?? [],

            'confidence' =>
                $benchmark[
                    'benchmark_confidence'
                ] ?? null,

            'vat_status' =>
                $vatStatus,

            'region' =>
                $benchmark['region']
                ?? null,

            'observed_at' =>
                $latestObservedAt,
        ];

        $priceVersion =
            'tile-market-v2-'
            . substr(
                hash(
                    'sha256',
                    json_encode(
                        $versionPayload,
                        JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                    )
                ),
                0,
                16
            );

        return [
            'material' =>
                $material,

            'mode' =>
                $mode,

            'tile_size' =>
                $this->normalizedTileSize(
                    $tileSize
                ),

            'tile_width_cm' =>
                round($tileWidthCm, 2),

            'tile_height_cm' =>
                round($tileHeightCm, 2),

            'tile_area_m2' =>
                round($tileAreaM2, 4),

            'surfaces' =>
                $normalizedSurfaces,

            'application_types' =>
                $applicationTypes,

            'area' =>
                round($netArea, 2),

            'grout_mm' =>
                round($groutMm, 2),

            'waste_rate' =>
                round($wastePercent, 2),

            'theoretical_tiles' =>
                $theoreticalTiles,

            'layout_tiles' =>
                $layoutTiles,

            'base_tiles' =>
                $baseTiles,

            'recommended_tiles' =>
                $recommendedTiles,

            'gross_area' =>
                round($grossArea, 2),

            'market_key' =>
                $marketKey,

            'benchmark_status' =>
                'ready',

            'benchmark_confidence' =>
                isset(
                    $benchmark[
                        'benchmark_confidence'
                    ]
                )
                    ? (float) $benchmark[
                        'benchmark_confidence'
                    ]
                    : null,

            'market_level' =>
                $benchmark['market_level']
                ?? null,

            'unit' =>
                $unit,

            'currency' =>
                $currency,

            'vat_status' =>
                $vatStatus,

            'unit_price' =>
                round($unitPrice, 2),

            'estimated_cost' =>
                $estimatedCost,

            'estimated_min' =>
                $estimatedMin,

            'estimated_max' =>
                $estimatedMax,

            'price_sources' =>
                $sources,

            'price_checked_at' =>
                $latestObservedAt,

            'price_version' =>
                $priceVersion,
        ];
    }

    private function marketKey(
        string $material,
        string $type,
        string $tileSize
    ): ?string {
        $size =
            $this->normalizedTileSize(
                $tileSize
            );

        if (
            $material === 'ceramic'
            && $type === 'wall'
            && $size === '30x60'
        ) {
            return 'tile_ceramic_wall_30x60_m2';
        }

        if (
            $material === 'ceramic'
            && $type === 'floor'
            && $size === '60x60'
        ) {
            return 'tile_ceramic_floor_60x60_m2';
        }

        if (
            $material === 'porcelain_matt'
            && $size === '60x60'
        ) {
            return 'tile_porcelain_matt_60x60_m2';
        }

        if (
            $material === 'porcelain_matt'
            && $size === '60x120'
        ) {
            return 'tile_porcelain_matt_60x120_m2';
        }

        return null;
    }

    private function tileDimensions(
        string $tileSize
    ): array {
        $size =
            $this->normalizedTileSize(
                $tileSize
            );

        if (
            !preg_match(
                '/^([0-9]+(?:\.[0-9]+)?)x([0-9]+(?:\.[0-9]+)?)$/',
                $size,
                $matches
            )
        ) {
            throw new RuntimeException(
                'Karo ebadı okunamadı.'
            );
        }

        $width =
            (float) $matches[1];

        $height =
            (float) $matches[2];

        if (
            $width <= 0
            || $height <= 0
            || $width > 500
            || $height > 500
        ) {
            throw new RuntimeException(
                'Karo ebadı izin verilen aralığın dışında.'
            );
        }

        return [
            $width,
            $height,
        ];
    }

    private function normalizedTileSize(
        string $tileSize
    ): string {
        return str_replace(
            ['×', ' ', ','],
            ['x', '', '.'],
            mb_strtolower(
                trim($tileSize)
            )
        );
    }
}

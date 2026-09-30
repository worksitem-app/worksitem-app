<?php

namespace App\Console\Commands;

use App\Models\MaterialPriceObservation;
use App\Services\Pricing\MaterialBenchmarkAnalyzer;
use Illuminate\Console\Command;

class AnalyzeMaterialBenchmarks extends Command
{
    protected $signature =
        'prices:analyze-benchmarks
        {material? : Tek material_key}
        {--max-age=90 : Maksimum kaynak yaşı}';

    protected $description =
        'Malzeme fiyat gözlemlerini benchmark uygunluğu açısından analiz eder';

    public function handle(
        MaterialBenchmarkAnalyzer $analyzer
    ): int {
        $material =
            $this->argument('material');

        $maxAge =
            max(
                1,
                (int) $this->option(
                    'max-age'
                )
            );

        $keys = $material
            ? collect([$material])
            : MaterialPriceObservation::query()
                ->distinct()
                ->orderBy('material_key')
                ->pluck('material_key');

        if ($keys->isEmpty()) {
            $this->warn(
                'OBSERVATION_YOK'
            );

            return self::SUCCESS;
        }

        foreach ($keys as $key) {

            $result =
                $analyzer->analyze(
                    $key,
                    $maxAge
                );

            $this->newLine();

            $this->info(
                'MATERIAL='.$key
            );

            $this->line(
                'STATUS='.
                strtoupper(
                    $result['status']
                )
            );

            $this->line(
                'OBSERVATIONS='.
                ($result[
                    'observation_count'
                ] ?? 0)
            );

            $this->line(
                'ELIGIBLE='.
                ($result[
                    'eligible_count'
                ] ?? 0)
            );

            if (
                isset(
                    $result['excluded']
                )
            ) {
                foreach (
                    $result['excluded']
                    as $reason => $count
                ) {
                    if ($count > 0) {
                        $this->line(
                            'EXCLUDED_'.
                            strtoupper($reason).
                            '='.
                            $count
                        );
                    }
                }
            }

            foreach (
                $result['groups'] ?? []
                as $group
            ) {

                $this->line(
                    'GROUP'.
                    ' | LEVEL='.
                    ($group[
                        'market_level'
                    ] ?? 'NULL').
                    ' | SOURCES='.
                    ($group[
                        'source_count'
                    ] ?? 0).
                    ' | STATUS='.
                    strtoupper(
                        $group['status']
                    )
                );

                if (
                    isset(
                        $group[
                            'spread_percent'
                        ]
                    )
                ) {
                    $this->line(
                        'SPREAD='.
                        $group[
                            'spread_percent'
                        ].
                        '%'
                    );
                }

                if (
                    isset(
                        $group[
                            'benchmark_price'
                        ]
                    )
                ) {
                    $this->line(
                        'BENCHMARK='.
                        $group[
                            'benchmark_price'
                        ].
                        ' '.
                        $group['unit']
                    );
                }
            }

            if (
                $result['status']
                === 'ready'
            ) {
                $benchmark =
                    $result[
                        'benchmark'
                    ];

                $this->info(
                    'BENCHMARK_READY=YES'
                );

                $this->line(
                    'PRICE='.
                    $benchmark[
                        'benchmark_price'
                    ].
                    ' '.
                    $benchmark['unit']
                );

                $this->line(
                    'CONF='.
                    $benchmark[
                        'benchmark_confidence'
                    ]
                );
            } else {
                $this->warn(
                    'BENCHMARK_READY=NO'
                );
            }
        }

        $this->newLine();

        $this->warn(
            'READ_ONLY=YES | DB_DEGISTIRILMEDI'
        );

        return self::SUCCESS;
    }
}

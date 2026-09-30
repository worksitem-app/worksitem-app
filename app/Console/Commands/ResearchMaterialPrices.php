<?php

namespace App\Console\Commands;

use App\Services\Pricing\MaterialPriceRecorder;
use App\Services\Pricing\MaterialSources\DemirFiyatlariSource;
use App\Services\Pricing\MaterialSources\ReadyConcreteSource;
use Illuminate\Console\Command;
use Throwable;

class ResearchMaterialPrices extends Command
{
    protected $signature =
        'prices:research-materials
        {--source=all : Kaynak anahtarı}
        {--dry-run : DB kaydı yapmadan araştır}';

    protected $description =
        'Malzeme piyasa kaynaklarını araştırır ve fiyat geçmişini günceller';

    public function handle(
        MaterialPriceRecorder $recorder
    ): int {
        $sources = [
            new DemirFiyatlariSource(),
            new ReadyConcreteSource(),
        ];

        $requested = (string) $this->option('source');
        $dryRun = (bool) $this->option('dry-run');

        $selected = array_filter(
            $sources,
            fn ($source) =>
                $requested === 'all'
                || $source->key() === $requested
        );

        if (empty($selected)) {
            $this->error(
                'SOURCE_NOT_FOUND='.$requested
            );

            return self::FAILURE;
        }

        $failures = 0;

        foreach ($selected as $source) {

            $this->newLine();

            $this->info(
                'SOURCE='.$source->key()
            );

            try {

                $rows = $source->fetch();

                $this->line(
                    'FOUND='.count($rows)
                );

                foreach ($rows as $data) {

                    $this->line(
                        $data['key'].
                        ' | PRICE='.$data['price'].
                        ' '.$data['unit'].
                        ' | DATE='.$data['source_date'].
                        ' | CONF='.$data['confidence_score'].
                        ' | STATUS='.$data['verification_status']
                    );

                    if ($dryRun) {
                        continue;
                    }

                    $result = $recorder->record(
                        $data
                    );

                    $this->line(
                        'SAVED='.
                        $result['material']->key.
                        ' | HISTORY='.
                        (
                            $result['history_created']
                                ? 'CREATED'
                                : 'EXISTS'
                        ).
                        ' | CHANGED='.
                        (
                            $result['changed']
                                ? 'YES'
                                : 'NO'
                        )
                    );
                }

            } catch (Throwable $exception) {

                $failures++;

                $this->error(
                    'SOURCE_FAIL='.
                    $source->key()
                );

                $this->error(
                    'ERROR='.
                    $exception->getMessage()
                );
            }
        }

        if ($dryRun) {
            $this->warn(
                'DRY_RUN=YES | DB_DEGISTIRILMEDI'
            );
        }

        return $failures === 0
            ? self::SUCCESS
            : self::FAILURE;
    }
}

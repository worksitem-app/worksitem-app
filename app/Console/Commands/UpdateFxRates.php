<?php

namespace App\Console\Commands;

use App\Services\FxRates;
use Illuminate\Console\Command;

class UpdateFxRates extends Command
{
    protected $signature = 'fx:update';

    protected $description = 'TCMB döviz satış kurlarını (USD/EUR/GBP) çekip önbelleğe yazar';

    public function handle(FxRates $fx): int
    {
        $r = $fx->refresh();
        if (! $r) {
            $this->warn('TCMB kurları alınamadı; önceki değer korunuyor.');

            return self::FAILURE;
        }

        foreach ($r['rates'] as $code => $rate) {
            $this->line("  $code = $rate TL");
        }
        $this->info('Kur tarihi: '.$r['date']);

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Services\Market\Marketplace;
use Illuminate\Console\Command;

/** Teklif pazaryeri: ödenmeyen kabulleri ve süresi dolan talepleri kapatır (saatlik) */
class MarketTick extends Command
{
    protected $signature = 'market:tick';

    protected $description = 'Ödenmeyen eşleşme bedellerini ve süresi dolan teklif taleplerini kapatır';

    public function handle(Marketplace $market): int
    {
        $n = $market->tick();
        $this->info("Süresi dolan kabul: {$n['fee_expired']} · süresi dolan talep: {$n['requests_expired']}");

        return self::SUCCESS;
    }
}

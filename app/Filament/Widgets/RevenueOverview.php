<?php

namespace App\Filament\Widgets;

use App\Models\AiCreditTransaction;
use App\Models\Lead;
use App\Models\Sponsor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Gelir: keşif ödemeleri, sponsorluk, AI kredisi, Pro rapor */
class RevenueOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '120s';

    protected ?string $heading = 'Gelir';

    protected function getStats(): array
    {
        $tl = fn ($v) => '₺'.number_format((float) $v, 0, ',', '.');
        $amount = fn (Lead $l) => (float) ($l->inspection_data['amounts']['total'] ?? ((int) $l->inspection_fee * 1.2));
        $month = now()->startOfMonth();

        $paid = Lead::query()->where('inspection_status', 'odendi')->where('inspection_paid_at', '>=', $month)->get(['inspection_data', 'inspection_fee']);
        $waiting = Lead::query()->where('inspection_status', 'kabul')->get(['inspection_data', 'inspection_fee']);

        $sponsors = Sponsor::query()->live();
        $liveCount = (clone $sponsors)->count();
        $contract = (float) (clone $sponsors)->sum('contract_amount');
        $imp = (int) Sponsor::query()->sum('impressions');
        $clk = (int) Sponsor::query()->sum('clicks');

        $credits = (int) AiCreditTransaction::query()->where('kind', 'grant')->where('delta', '>', 0)->where('created_at', '>=', $month)->sum('delta');
        $pro30 = Lead::query()->where('source', 'like', '%_pro_rapor')->where('created_at', '>=', now()->subDays(30))->count();
        $proCall = Lead::query()->where('source', 'like', '%_pro_rapor')->where('contact_consent', true)->where('created_at', '>=', now()->subDays(30))->count();

        return [
            Stat::make('Keşif geliri (bu ay)', $tl($paid->sum($amount)))
                ->description($paid->count().' ödenmiş keşif · KDV dahil')
                ->descriptionIcon('heroicon-m-banknotes')->color('success'),
            Stat::make('Ödeme bekleyen keşif', $waiting->count())
                ->description($waiting->count() ? $tl($waiting->sum($amount)).' tahsil edilecek' : 'Bekleyen yok')
                ->descriptionIcon('heroicon-m-clock')->color($waiting->count() ? 'warning' : 'gray'),
            Stat::make('Yayındaki sponsor', $liveCount)
                ->description(($contract > 0 ? $tl($contract).' anlaşma · ' : '').'TO %'.($imp > 0 ? round($clk / $imp * 100, 1) : 0).' ('.number_format($clk, 0, ',', '.').' tık)')
                ->descriptionIcon('heroicon-m-megaphone')->color($liveCount ? 'primary' : 'gray'),
            Stat::make('Yüklenen AI kredisi (bu ay)', number_format($credits, 0, ',', '.'))
                ->description('Admin "Kredi yükle" toplamı')
                ->descriptionIcon('heroicon-m-sparkles')->color('info'),
            Stat::make('Pro rapor (30 gün)', $pro30)
                ->description($proCall.' kişi aranmak istedi')
                ->descriptionIcon('heroicon-m-document-arrow-down')->color('primary'),
        ];
    }
}

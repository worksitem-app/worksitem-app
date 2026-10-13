<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Satış özeti: yeni talepler, açık potansiyel, kazanılanlar */
class LeadKpiOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected ?string $heading = 'Satış';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '60s';

    private const OPEN = ['yeni', 'aranacak', 'gorusuldu', 'nitelikli', 'kesif_planlandi', 'teklif_hazirlaniyor', 'teklif_verildi', 'takipte'];

    protected function getStats(): array
    {
        $tl = fn ($v) => '₺'.number_format((float) $v, 0, ',', '.');

        $today = Lead::whereDate('created_at', today())->count();
        $yesterday = Lead::whereDate('created_at', today()->subDay())->count();

        $daily = collect(range(13, 0))->map(fn ($d) => Lead::whereDate('created_at', today()->subDays($d))->count())->all();
        $last7 = array_sum(array_slice($daily, 7));
        $prev7 = array_sum(array_slice($daily, 0, 7));

        $untouched = Lead::where('status', 'yeni')->count();
        $open = Lead::whereIn('status', self::OPEN);
        $pipeline = (clone $open)->sum('estimated_cost');
        $openCount = (clone $open)->count();

        $since = now()->subDays(30);
        $won = Lead::where('status', 'kazanildi')->where('updated_at', '>=', $since)->count();
        $all30 = Lead::where('created_at', '>=', $since)->count();
        $rate = $all30 > 0 ? round($won / $all30 * 100, 1) : 0;

        $trend = fn (int $now, int $before) => $before === 0
            ? ($now > 0 ? 'Yeni' : 'Değişim yok')
            : (($now >= $before ? '+' : '').round(($now - $before) / $before * 100).'% önceki döneme göre');

        return [
            Stat::make('Bugün gelen teklif talebi', $today)
                ->description('Dün: '.$yesterday)
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($today > 0 ? 'success' : 'gray'),

            Stat::make('Son 7 gün', $last7)
                ->description($trend($last7, $prev7))
                ->descriptionIcon($last7 >= $prev7 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart(array_slice($daily, 7))
                ->color($last7 >= $prev7 ? 'success' : 'danger'),

            Stat::make('Bakılmamış talep', $untouched)
                ->description($untouched > 0 ? 'Aşaması "Yeni Talep" olanlar — hemen arayın' : 'Hepsine bakıldı')
                ->descriptionIcon('heroicon-m-phone-arrow-up-right')
                ->color($untouched > 0 ? 'warning' : 'success')
                ->url(\App\Filament\Resources\LeadResource::getUrl('index', ['tableFilters' => ['status' => ['values' => ['yeni']]]])),

            Stat::make('Açık iş potansiyeli', $tl($pipeline))
                ->description($openCount.' açık talebin tahmini toplamı')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Kazanılan (30 gün)', $won)
                ->description('Dönüşüm: %'.$rate.' ('.$all30.' talepten)')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
        ];
    }
}

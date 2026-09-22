<?php

namespace App\Filament\Widgets;

use App\Models\ToolEvent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ToolEventsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = ToolEvent::whereDate('created_at', today());
        $last7Days = ToolEvent::where('created_at', '>=', now()->subDays(7));
        $last30Days = ToolEvent::where('created_at', '>=', now()->subDays(30));

        // Bugünün event'leri
        $todayTotal = (clone $today)->count();
        $todayViews = (clone $today)->where('event_type', 'tool_view')->count();
        $todayCalcs = (clone $today)->where('event_type', 'calculator_start')->count();
        $todayKainta = (clone $today)->where('event_type', 'kainta_cta_click')->count();

        // Toplam
        $total = ToolEvent::count();

        // En popüler araç
        $topTool = ToolEvent::query()
            ->select('tool_slug', \DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tool_slug')
            ->orderByDesc('total')
            ->first();

        return [
            Stat::make('Bugünkü Event', $todayTotal)
                ->description("Toplam: {$total}")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Bugün Görüntüleme', $todayViews)
                ->description('Araç sayfası açılma')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),

            Stat::make('Bugün Hesaplama', $todayCalcs)
                ->description('Buton tıklama')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),

            Stat::make('Bugün Kainta CTA', $todayKainta)
                ->description('Lead adayı')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('danger'),

            Stat::make('Son 7 Gün', $last7Days->count())
                ->description('Toplam event')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('En Popüler Araç', $topTool?->tool_slug ?? '-')
                ->description('Son 30 gün: ' . ($topTool?->total ?? 0) . ' event')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
        ];
    }
}

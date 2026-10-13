<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\ToolEvent;
use Filament\Widgets\ChartWidget;

/** Son 30 gün: günlük teklif talebi ve hesaplama sayısı */
class LeadTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Son 30 gün: hesaplama ve teklif talebi';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = ['md' => 1, 'xl' => 2];

    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($d) => today()->subDays($d));

        $leads = Lead::where('created_at', '>=', today()->subDays(29))
            ->get(['created_at'])->groupBy(fn ($l) => $l->created_at->toDateString())->map->count();
        $calcs = ToolEvent::where('event_type', 'calculator_start')->where('created_at', '>=', today()->subDays(29))
            ->get(['created_at'])->groupBy(fn ($e) => \Illuminate\Support\Carbon::parse($e->created_at)->toDateString())->map->count();

        return [
            'datasets' => [
                [
                    'label' => 'Teklif talebi',
                    'data' => $days->map(fn ($d) => $leads[$d->toDateString()] ?? 0)->all(),
                    'type' => 'bar',
                    'backgroundColor' => '#101318',
                    'borderRadius' => 4,
                    'yAxisID' => 'y',
                    'order' => 2,
                ],
                [
                    'label' => 'Hesaplama',
                    'data' => $days->map(fn ($d) => $calcs[$d->toDateString()] ?? 0)->all(),
                    'borderColor' => '#FFB800',
                    'backgroundColor' => '#FFB800',
                    'tension' => 0.35,
                    'pointRadius' => 0,
                    'borderWidth' => 2.5,
                    'yAxisID' => 'y1',
                    'order' => 1,
                ],
            ],
            'labels' => $days->map(fn ($d) => $d->format('d.m'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom']],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0], 'title' => ['display' => true, 'text' => 'Teklif talebi']],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false], 'ticks' => ['precision' => 0], 'title' => ['display' => true, 'text' => 'Hesaplama']],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

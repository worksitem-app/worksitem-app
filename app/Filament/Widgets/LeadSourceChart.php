<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

/** Talep kaynakları (30 gün): hangi araç/kanal talep getiriyor */
class LeadSourceChart extends ChartWidget
{
    protected static ?string $heading = 'Talep kaynakları (30 gün)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $maxHeight = '260px';

    private const LABELS = [
        'banyo_pro_rapor' => 'Banyo Pro rapor',
        'mutfak_pro_rapor' => 'Mutfak Pro rapor',
        'seramik_pro_rapor' => 'Seramik Pro rapor',
        'boya_pro_rapor' => 'Boya Pro rapor',
        'sponsor_basvurusu' => 'Sponsor başvurusu',
        'banyo-maliyet-hesaplayici' => 'Banyo hesaplayıcı',
        'maliyet-hesapla' => 'Maliyet sihirbazı',
    ];

    protected function getData(): array
    {
        $rows = Lead::query()->where('created_at', '>=', now()->subDays(30))
            ->selectRaw("COALESCE(NULLIF(source, ''), 'diger') as src, count(*) as n")
            ->groupBy('src')->orderByDesc('n')->get();
        $top = $rows->take(6);
        $other = $rows->slice(6)->sum('n');

        $labels = $top->map(fn ($r) => self::LABELS[$r->src] ?? str($r->src)->replace(['_', '-'], ' ')->title()->toString())->all();
        $data = $top->pluck('n')->all();
        if ($other > 0) {
            $labels[] = 'Diğer';
            $data[] = $other;
        }

        return [
            'datasets' => [[
                'data' => $data,
                'backgroundColor' => ['#FFB800', '#101318', '#2F6F8F', '#8A6200', '#7FB3C8', '#C9A55A', '#D5D9DF'],
                'borderWidth' => 0,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return ['plugins' => ['legend' => ['position' => 'bottom']], 'cutout' => '62%', 'scales' => ['x' => ['display' => false], 'y' => ['display' => false]]];
    }
}

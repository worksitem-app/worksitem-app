<?php

namespace App\Filament\Widgets;

use App\Models\ToolEvent;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

/** Araç başına huni: görüntüleme → hesaplama → Kainta/teklif tıklaması (son 30 gün) */
class ToolFunnel extends Widget
{
    protected static string $view = 'filament.widgets.tool-funnel';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = ['md' => 1, 'xl' => 2];

    protected function getViewData(): array
    {
        $rows = ToolEvent::query()
            ->where('created_at', '>=', now()->subDays(30))
            ->select('tool_slug',
                DB::raw("sum(case when event_type='tool_view' then 1 else 0 end) as views"),
                DB::raw("sum(case when event_type='calculator_start' then 1 else 0 end) as calcs"),
                DB::raw("sum(case when event_type='kainta_cta_click' then 1 else 0 end) as ctas"))
            ->groupBy('tool_slug')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        return ['rows' => $rows];
    }
}

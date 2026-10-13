<?php

namespace App\Filament\Widgets;

use App\Support\SystemHealth;
use Filament\Widgets\Widget;

/** Entegrasyon ve sistem sağlığı (sırları göstermez) */
class SystemHealthWidget extends Widget
{
    protected static string $view = 'filament.widgets.system-health';

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 1;

    protected function getViewData(): array
    {
        $rows = SystemHealth::checks();

        return ['groups' => collect($rows)->groupBy('group'), 'sum' => SystemHealth::summary($rows)];
    }
}

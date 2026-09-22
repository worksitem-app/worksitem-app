<?php

namespace App\Filament\Resources\ToolEventResource\Pages;

use App\Filament\Resources\ToolEventResource;
use Filament\Resources\Pages\ListRecords;

class ListToolEvents extends ListRecords
{
    protected static string $resource = ToolEventResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ToolEventsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // Export action (opsiyonel)
        ];
    }
}

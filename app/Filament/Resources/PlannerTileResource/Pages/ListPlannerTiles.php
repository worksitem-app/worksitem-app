<?php

namespace App\Filament\Resources\PlannerTileResource\Pages;

use App\Filament\Resources\PlannerProductResource\Pages\ListPlannerProducts;
use App\Filament\Resources\PlannerTileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlannerTiles extends ListRecords
{
    protected static string $resource = PlannerTileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Yeni seramik'),
            ListPlannerProducts::importAction(),
            ListPlannerProducts::templateAction(),
        ];
    }
}

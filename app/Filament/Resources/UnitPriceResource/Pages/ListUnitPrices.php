<?php

namespace App\Filament\Resources\UnitPriceResource\Pages;

use App\Filament\Resources\UnitPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitPrices extends ListRecords
{
    protected static string $resource = UnitPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\UnitPriceResource\Pages;

use App\Filament\Resources\UnitPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitPrice extends EditRecord
{
    protected static string $resource = UnitPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

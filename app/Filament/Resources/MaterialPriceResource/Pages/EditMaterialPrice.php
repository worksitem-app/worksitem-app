<?php

namespace App\Filament\Resources\MaterialPriceResource\Pages;

use App\Filament\Resources\MaterialPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaterialPrice extends EditRecord
{
    protected static string $resource = MaterialPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

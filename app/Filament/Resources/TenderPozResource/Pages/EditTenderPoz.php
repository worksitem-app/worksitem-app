<?php

namespace App\Filament\Resources\TenderPozResource\Pages;

use App\Filament\Resources\TenderPozResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenderPoz extends EditRecord
{
    protected static string $resource = TenderPozResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

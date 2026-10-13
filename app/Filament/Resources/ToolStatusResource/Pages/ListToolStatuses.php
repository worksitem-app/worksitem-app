<?php

namespace App\Filament\Resources\ToolStatusResource\Pages;

use App\Filament\Resources\ToolStatusResource;
use App\Models\ToolStatus;
use Filament\Resources\Pages\ListRecords;

class ListToolStatuses extends ListRecords
{
    protected static string $resource = ToolStatusResource::class;

    public function mount(): void
    {
        ToolStatus::syncFromConfig();   // yeni eklenen araçlar listeye mevcut durumlarıyla gelir
        parent::mount();
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/** Kontrol Merkezi: satış, gelir, ürün kullanımı ve sistem sağlığı tek ekranda */
class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Kontrol Merkezi';
    protected static ?string $navigationLabel = 'Kontrol Merkezi';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public function getColumns(): int|string|array
    {
        return ['md' => 2, 'xl' => 3];
    }
}

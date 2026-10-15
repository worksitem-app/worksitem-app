<?php

namespace App\Filament\Pages;

use App\Services\Market\FirmQuestions;
use Filament\Pages\Page;

/** Saha fiyat beyanları: firmaların hizmet bilgilerinde yazdığı fiyatların ortancası ve sapanlar */
class FieldPrices extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Fiyat Merkezi';
    protected static ?string $navigationLabel = 'Saha fiyat beyanları';
    protected static ?string $title = 'Saha fiyat beyanları';
    protected static ?string $slug = 'saha-fiyatlari';
    protected static string $view = 'filament.pages.field-prices';

    protected function getViewData(): array
    {
        return ['rows' => FirmQuestions::stats()];
    }
}

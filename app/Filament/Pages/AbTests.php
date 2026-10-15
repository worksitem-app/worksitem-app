<?php

namespace App\Filament\Pages;

use App\Support\Ab;
use Filament\Pages\Page;

/** A/B testleri: sürüm başına görüntülenme, tıklama, dönüşüm ve anlamlılık */
class AbTests extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Raporlar';
    protected static ?string $navigationLabel = 'A/B testleri';
    protected static ?string $title = 'A/B testleri';
    protected static ?string $slug = 'ab-testleri';
    protected static string $view = 'filament.pages.ab-tests';

    protected function getViewData(): array
    {
        return ['rows' => Ab::report()];
    }
}

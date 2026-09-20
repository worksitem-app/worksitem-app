<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenderPozResource\Pages;
use App\Models\TenderPoz;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenderPozResource extends Resource
{
    protected static ?string $model = TenderPoz::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Sistem Yönetimi';
    protected static ?string $modelLabel = 'İhale Pozu';
    protected static ?string $pluralModelLabel = 'Kurum Poz Kütüphanesi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('year')->label('Yıl')->required(),
            Forms\Components\Select::make('institution')->label('Kurum')->options(['ÇŞB' => 'ÇŞB', 'Karayolları' => 'Karayolları', 'İller Bankası' => 'İller Bankası', 'Özel' => 'Özel'])->required(),
            Forms\Components\TextInput::make('poz_no')->label('Poz No')->required(),
            Forms\Components\Textarea::make('description')->label('Poz Tanımı')->required()->columnSpanFull(),
            Forms\Components\TextInput::make('unit')->label('Birim')->required(),
            Forms\Components\TextInput::make('unit_price')->label('Birim Fiyat')->numeric()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('institution')->label('Kurum')->badge()->searchable(),
            Tables\Columns\TextColumn::make('poz_no')->label('Poz No')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('description')->label('Tanım')->limit(50)->searchable(),
            Tables\Columns\TextColumn::make('unit_price')->label('Fiyat (TL)')->money('TRY')->sortable(),
            Tables\Columns\TextColumn::make('year')->label('Yıl'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('institution')->label('Kurum Filtresi')->options(['ÇŞB' => 'ÇŞB', 'Karayolları' => 'Karayolları', 'Özel' => 'Özel']),
        ])
        ->actions([Tables\Actions\EditAction::make()])
        ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListTenderPozs::route('/'),
            'create' => Pages\CreateTenderPoz::route('/create'),
            'edit' => Pages\EditTenderPoz::route('/{record}/edit'),
        ];
    }
}

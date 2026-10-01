<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialPriceResource\Pages;
use App\Filament\Resources\MaterialPriceResource\RelationManagers;
use App\Models\MaterialPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialPriceResource extends Resource
{
    protected static ?string $model = MaterialPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Fiyat Merkezi';
    protected static ?string $modelLabel = 'Piyasa Fiyatı';
    protected static ?string $pluralModelLabel = 'Günlük Piyasa Analizi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('spec')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('unit')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('region')
                    ->required()
                    ->maxLength(100)
                    ->default('Türkiye'),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(3)
                    ->default('TRY'),
                Forms\Components\TextInput::make('current_price')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('previous_price')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('change_amount')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('change_percent')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('source_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('source_url')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('source_date'),
                Forms\Components\DateTimePicker::make('observed_at'),
                Forms\Components\Toggle::make('vat_included')
                    ->required(),
                Forms\Components\TextInput::make('confidence_score')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('verification_status')
                    ->required()
                    ->maxLength(30)
                    ->default('needs_review'),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Malzeme')
                    ->description(
                        fn (MaterialPrice $record): string =>
                            $record->spec ?: $record->key
                    )
                    ->searchable(['name', 'key', 'spec'])
                    ->wrap(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_price')
                    ->label('Güncel Fiyat')
                    ->money('TRY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('previous_price')
                    ->label('Önceki Fiyat')
                    ->money('TRY')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('change_percent')
                    ->label('Değişim')
                    ->formatStateUsing(
                        fn ($state): string =>
                            $state === null
                                ? '—'
                                : number_format(
                                    (float) $state,
                                    2,
                                    ',',
                                    '.'
                                ).'%'
                    )
                    ->color(
                        fn ($state): string =>
                            $state === null
                                ? 'gray'
                                : (
                                    (float) $state > 0
                                        ? 'danger'
                                        : (
                                            (float) $state < 0
                                                ? 'success'
                                                : 'gray'
                                        )
                                )
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('source_name')
                    ->label('Kaynak')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('source_date')
                    ->label('Kaynak Tarihi')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('confidence_score')
                    ->label('Güven')
                    ->formatStateUsing(
                        fn ($state): string =>
                            $state === null
                                ? '—'
                                : number_format(
                                    (float) $state,
                                    0,
                                    ',',
                                    '.'
                                )
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('verification_status')
                    ->label('Kontrol')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'needs_review' => 'Kontrol Bekliyor',
                            'approved' => 'Onaylandı',
                            'verified' => 'Doğrulandı',
                            'rejected' => 'Reddedildi',
                            default => $state ?? '—',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'needs_review' => 'warning',
                            'approved', 'verified' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        }
                    ),
            ])
            ->defaultSort('observed_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Kontrol Durumu')
                    ->options([
                        'needs_review' => 'Kontrol Bekliyor',
                        'approved' => 'Onaylandı',
                        'verified' => 'Doğrulandı',
                        'rejected' => 'Reddedildi',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(
                        fn (): array =>
                            MaterialPrice::query()
                                ->whereNotNull('category')
                                ->distinct()
                                ->orderBy('category')
                                ->pluck('category', 'category')
                                ->all()
                    ),
            ])
            ->actions([])
            ->bulkActions([]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterialPrices::route('/'),
        ];
    }
}

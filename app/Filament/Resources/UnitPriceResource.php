<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitPriceResource\Pages;
use App\Models\UnitPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class UnitPriceResource extends Resource
{
    protected static ?string $model = UnitPrice::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Sistem Yönetimi';
    protected static ?string $modelLabel = 'Birim Fiyat';
    protected static ?string $pluralModelLabel = 'Merkezi Fiyat Motoru';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('price_pool')
                    ->label('Fiyat Havuzu')
                    ->options([
                        'market' => 'Piyasa Fiyatı',
                        'official' => 'Resmî Referans Arşivi',
                        'future' => 'Gelecekte Kullanılacak',
                    ])
                    ->default('market')
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('review_status')
                    ->label('Onay Durumu')
                    ->options([
                        'needs_engineer_review' => 'Mühendis Kontrolü Bekliyor',
                        'approved' => 'Mühendis Onaylı',
                        'official_reference' => 'Resmî Referans',
                        'needs_source' => 'Kaynak Bekliyor',
                    ])
                    ->default('needs_engineer_review')
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('category')
                    ->label('Kategori')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('key')
                    ->label('Sistem Anahtarı')
                    ->helperText('Araç bağlantıları bu anahtarı kullanır. Mevcut anahtarları zorunlu olmadıkça değiştirmeyin.')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('name')
                    ->label('Malzeme / Hizmet Adı')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->label('Güncel Fiyat')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->prefix('₺'),

                Forms\Components\TextInput::make('unit')
                    ->label('Birim')
                    ->placeholder('TL/m2, TL/Ton, TL/Adet')
                    ->required()
                    ->maxLength(50),

                Forms\Components\TextInput::make('source_name')
                    ->label('Kaynak Adı')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('source_date')
                    ->label('Kaynak Tarihi')
                    ->native(false)
                    ->displayFormat('d.m.Y'),

                Forms\Components\Toggle::make('vat_included')
                    ->label('Fiyata KDV Dahil')
                    ->default(false),

                Forms\Components\Toggle::make('is_active')
                    ->label('Araçlarda Aktif')
                    ->helperText('Sadece piyasa havuzundaki aktif kayıtlar hesaplama araçlarına gönderilir.')
                    ->default(true),

                Forms\Components\Textarea::make('notes')
                    ->label('Mühendis Notu')
                    ->rows(4)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Malzeme / Hizmet')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('key')
                    ->label('Sistem Anahtarı')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Birim'),

                Tables\Columns\TextColumn::make('price_pool')
                    ->label('Havuz')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'market' => 'Piyasa',
                        'official' => 'Resmî Arşiv',
                        'future' => 'Gelecek',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'market' => 'success',
                        'official' => 'info',
                        'future' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('review_status')
                    ->label('Onay')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'approved' => 'Mühendis Onaylı',
                        'needs_engineer_review' => 'Kontrol Bekliyor',
                        'official_reference' => 'Resmî Referans',
                        'needs_source' => 'Kaynak Bekliyor',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'official_reference' => 'info',
                        'needs_engineer_review' => 'warning',
                        'needs_source' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('source_date')
                    ->label('Kaynak Tarihi')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('price_pool')
                    ->label('Fiyat Havuzu')
                    ->options([
                        'market' => 'Piyasa Fiyatı',
                        'official' => 'Resmî Referans Arşivi',
                        'future' => 'Gelecekte Kullanılacak',
                    ]),

                Tables\Filters\SelectFilter::make('review_status')
                    ->label('Onay Durumu')
                    ->options([
                        'needs_engineer_review' => 'Mühendis Kontrolü Bekliyor',
                        'approved' => 'Mühendis Onaylı',
                        'official_reference' => 'Resmî Referans',
                        'needs_source' => 'Kaynak Bekliyor',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktiflik')
                    ->trueLabel('Sadece aktif')
                    ->falseLabel('Sadece pasif')
                    ->placeholder('Tümü'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Seçilenleri Pasife Al')
                        ->icon('heroicon-o-pause-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Seçilen fiyatlar pasife alınsın mı?')
                        ->modalDescription('Pasif kayıtlar hesaplama araçlarına gönderilmez. Kayıtlar silinmez ve daha sonra yeniden etkinleştirilebilir.')
                        ->action(function (Collection $records): void {
                            $records->each(function (UnitPrice $record): void {
                                $record->update(['is_active' => false]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitPrices::route('/'),
            'create' => Pages\CreateUnitPrice::route('/create'),
            'edit' => Pages\EditUnitPrice::route('/{record}/edit'),
        ];
    }
}
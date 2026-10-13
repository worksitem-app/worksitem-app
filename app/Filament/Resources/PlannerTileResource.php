<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlannerTileResource\Pages;
use App\Models\PlannerTile;
use App\Models\Sponsor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Banyo planlayıcıdaki sponsor seramikleri */
class PlannerTileResource extends Resource
{
    protected static ?string $model = PlannerTile::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Gelir';
    protected static ?string $modelLabel = 'Seramik';
    protected static ?string $pluralModelLabel = 'Planlayıcı seramikleri';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Seramik')->schema([
                Forms\Components\TextInput::make('name')->label('Ürün adı')->required()->maxLength(150),
                Forms\Components\TextInput::make('brand')->label('Marka')->required()->maxLength(120),
                Forms\Components\TextInput::make('series')->label('Seri')->maxLength(120),
                Forms\Components\TextInput::make('sku')->label('Stok kodu')->maxLength(80),
                Forms\Components\Select::make('sponsor_id')->label('Sponsor anlaşması')->options(fn () => Sponsor::query()->orderBy('brand')->pluck('brand', 'id'))->searchable()->native(false),
                Forms\Components\TextInput::make('product_url')->label('Ürün sayfası (https)')->url()->maxLength(500),
            ])->columns(3),
            Forms\Components\Section::make('Ebat ve doku')->schema([
                Forms\Components\TextInput::make('width_cm')->label('En (cm)')->numeric()->required()->minValue(2)->maxValue(300),
                Forms\Components\TextInput::make('height_cm')->label('Boy (cm)')->numeric()->required()->minValue(2)->maxValue(300),
                Forms\Components\Select::make('finish')->label('Yüzey')->options(['Mat' => 'Mat', 'Parlak' => 'Parlak', 'Yarı mat' => 'Yarı mat', 'Rölyefli' => 'Rölyefli'])->native(false),
                Forms\Components\Select::make('usage')->label('Kullanım')->options(['both' => 'Zemin ve duvar', 'floor' => 'Zemin', 'wall' => 'Duvar'])->default('both')->native(false),
                Forms\Components\FileUpload::make('image_path')->label('Tek karo dokusu (JPG/PNG, en çok 4 MB)')
                    ->disk('public')->directory('planner/tiles')->image()->maxSize(4096)
                    ->helperText('Karonun kendisi; düz, perspektifsiz, gerçek oranında. Planlayıcı onu gerçek ebadıyla döşer.')->columnSpan(2),
                Forms\Components\TextInput::make('image_url')->label('… ya da doku adresi (https)')->url()->maxLength(500)->columnSpan(2),
            ])->columns(4),
            Forms\Components\Section::make('Fiyat ve yayın')->schema([
                Forms\Components\TextInput::make('price_m2_min')->label('m² fiyatı en az (TL)')->numeric(),
                Forms\Components\TextInput::make('price_m2_max')->label('m² fiyatı en çok (TL)')->numeric(),
                Forms\Components\TextInput::make('priority')->label('Sıra (küçük önce)')->numeric()->default(10),
                Forms\Components\Toggle::make('active')->label('Yayında')->default(true),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('')->disk('public')->square()->size(40),
                Tables\Columns\TextColumn::make('brand')->label('Marka')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('name')->label('Seramik')->searchable(),
                Tables\Columns\TextColumn::make('size')->label('Ebat')->state(fn (PlannerTile $r) => "{$r->width_cm}×{$r->height_cm} cm"),
                Tables\Columns\TextColumn::make('usage')->label('Kullanım')->formatStateUsing(fn ($s) => ['both' => 'Zemin + duvar', 'floor' => 'Zemin', 'wall' => 'Duvar'][$s] ?? $s)->badge(),
                Tables\Columns\IconColumn::make('active')->label('Yayında')->boolean(),
                Tables\Columns\TextColumn::make('impressions')->label('Gösterim')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('clicks')->label('Tıklama')->numeric()->sortable(),
            ])
            ->defaultSort('priority')
            ->filters([Tables\Filters\TernaryFilter::make('active')->label('Yayında')])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlannerTiles::route('/'),
            'create' => Pages\CreatePlannerTile::route('/create'),
            'edit' => Pages\EditPlannerTile::route('/{record}/edit'),
        ];
    }
}

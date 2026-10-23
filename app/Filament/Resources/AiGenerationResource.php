<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiGenerationResource\Pages;
use App\Models\AiGeneration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AiGenerationResource extends Resource
{
    protected static ?string $model = AiGeneration::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Analitik';
    protected static ?string $modelLabel = 'AI Tasarım';
    protected static ?string $pluralModelLabel = 'AI Tasarımları';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Oda Bilgileri')
                ->schema([
                    Forms\Components\TextInput::make('style')
                        ->label('Stil')
                        ->disabled(),
                    Forms\Components\TextInput::make('room_area')
                        ->label('Oda Alanı (m²)')
                        ->disabled(),
                    Forms\Components\Textarea::make('user_prompt')
                        ->label('Kullanıcı İsteği')
                        ->disabled()
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('ai_prompt')
                        ->label('AI Prompt')
                        ->disabled()
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Görseller')
                ->schema([
                    Forms\Components\FileUpload::make('input_image')
                        ->label('Orijinal Oda')
                        ->image()
                        ->disabled()
                        ->disk('local')
                        ->visibility('private'),
                    Forms\Components\FileUpload::make('output_image')
                        ->label('AI Tasarım')
                        ->image()
                        ->disabled()
                        ->disk('public'),
                ])->columns(2),

            Forms\Components\Section::make('Maliyet')
                ->schema([
                    Forms\Components\TextInput::make('estimated_cost')
                        ->label('Tahmini Maliyet (TL)')
                        ->disabled()
                        ->numeric()
                        ->prefix('₺'),
                    Forms\Components\TextInput::make('status')
                        ->label('Durum')
                        ->disabled(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('input_image')
                    ->label('Orijinal')
                    ->disk('local')
                    ->visibility('private')
                    ->height(60)
                    ->width(80),

                Tables\Columns\ImageColumn::make('output_image')
                    ->label('AI Tasarım')
                    ->disk('public')
                    ->height(60)
                    ->width(80)
                    ->defaultImageUrl(url('/images/no-image.png')),

                Tables\Columns\TextColumn::make('style')
                    ->label('Stil')
                    ->badge(),

                Tables\Columns\TextColumn::make('room_area')
                    ->label('Alan (m²)')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_prompt')
                    ->label('İstek')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('estimated_cost')
                    ->label('Maliyet')
                    ->money('TRY')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'warning' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'processing' => '⏳ İşleniyor',
                        'completed' => '✅ Tamamlandı',
                        'failed' => '❌ Başarısız',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'processing' => 'İşleniyor',
                        'completed' => 'Tamamlandı',
                        'failed' => 'Başarısız',
                    ]),
                Tables\Filters\SelectFilter::make('style')
                    ->label('Stil')
                    ->options([
                        'Modern' => 'Modern',
                        'Minimalist' => 'Minimalist',
                        'Endüstriyel' => 'Endüstriyel',
                        'Lüks' => 'Lüks',
                        'Japandi' => 'Japandi',
                        'Klasik' => 'Klasik',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiGenerations::route('/'),
            'view' => Pages\ViewAiGeneration::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) AiGeneration::whereDate('created_at', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

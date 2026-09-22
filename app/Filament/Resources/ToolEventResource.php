<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ToolEventResource\Pages;
use App\Models\ToolEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ToolEventResource extends Resource
{
    protected static ?string $model = ToolEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Analitik';
    protected static ?string $modelLabel = 'Araç Event\'i';
    protected static ?string $pluralModelLabel = 'Araç Event\'leri';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tool_slug')
                    ->label('Araç')
                    ->disabled(),
                Forms\Components\Select::make('event_type')
                    ->label('Event Tipi')
                    ->options([
                        'tool_view' => 'Araç Görüntüleme',
                        'calculator_start' => 'Hesaplama Başladı',
                        'calculator_complete' => 'Hesaplama Tamamlandı',
                        'cost_cta_click' => 'Maliyet CTA Tıklaması',
                        'kainta_cta_click' => 'Kainta CTA Tıklaması',
                        'sponsor_click' => 'Sponsor Tıklaması',
                    ])
                    ->disabled(),
                Forms\Components\TextInput::make('session_key')
                    ->label('Oturum')
                    ->disabled(),
                Forms\Components\TextInput::make('target_url')
                    ->label('Hedef URL')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('metadata')
                    ->label('Meta Veri')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Tarih')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tool_slug')
                    ->label('Araç')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\BadgeColumn::make('event_type')
                    ->label('Event')
                    ->colors([
                        'primary' => 'tool_view',
                        'warning' => 'calculator_start',
                        'success' => 'calculator_complete',
                        'info' => 'cost_cta_click',
                        'danger' => 'kainta_cta_click',
                        'secondary' => 'sponsor_click',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tool_view' => '👁️ Görüntüleme',
                        'calculator_start' => '▶️ Başladı',
                        'calculator_complete' => '✅ Tamamlandı',
                        'cost_cta_click' => '💰 Maliyet CTA',
                        'kainta_cta_click' => '🏗️ Kainta CTA',
                        'sponsor_click' => '🤝 Sponsor',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('session_key')
                    ->label('Oturum')
                    ->limit(12)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('target_url')
                    ->label('Hedef')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event_type')
                    ->label('Event Tipi')
                    ->options([
                        'tool_view' => '👁️ Görüntüleme',
                        'calculator_start' => '▶️ Başladı',
                        'calculator_complete' => '✅ Tamamlandı',
                        'cost_cta_click' => '💰 Maliyet CTA',
                        'kainta_cta_click' => '🏗️ Kainta CTA',
                        'sponsor_click' => '🤝 Sponsor',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('tool_slug')
                    ->label('Araç')
                    ->options(function () {
                        return ToolEvent::query()
                            ->distinct()
                            ->pluck('tool_slug', 'tool_slug')
                            ->toArray();
                    })
                    ->searchable()
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                Tables\Filters\Filter::make('today')
                    ->label('Bugün')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today())),

                Tables\Filters\Filter::make('last_7_days')
                    ->label('Son 7 Gün')
                    ->query(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('30s'); // Her 30 saniyede yenile
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListToolEvents::route('/'),
            'view' => Pages\ViewToolEvent::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) ToolEvent::whereDate('created_at', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function canCreate(): bool
    {
        return false; // Event'ler otomatik oluşur
    }
}

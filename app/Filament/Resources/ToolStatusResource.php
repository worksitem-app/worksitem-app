<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ToolStatusResource\Pages;
use App\Models\ToolStatus;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

/** Hangi araç yayında, hangisi incelemede (hesaplama kilitli) — canlıya çıkış kontrolü */
class ToolStatusResource extends Resource
{
    protected static ?string $model = ToolStatus::class;
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationGroup = 'Sistem Yönetimi';
    protected static ?string $navigationLabel = 'Araç Yayın Durumu';
    protected static ?string $modelLabel = 'Araç';
    protected static ?string $pluralModelLabel = 'Araç Yayın Durumu';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            return ToolStatus::query()->where('active', true)->count().' / '.ToolStatus::query()->count();
        } catch (\Throwable) {
            return null;
        }
    }

    /** @return array<string, string> */
    private static function categories(): array
    {
        return ['yatirim' => 'Yatırım', 'kaba' => 'Kaba inşaat', 'ince' => 'İnce işler', 'mekanik' => 'Mekanik/Elektrik', 'mevzuat' => 'Mevzuat'];
    }

    public static function table(Table $table): Table
    {
        $setActive = function (Collection $records, bool $active): void {
            $records->each(fn (ToolStatus $r) => $r->update(['active' => $active, 'updated_by' => auth()->id()]));
            Notification::make()->title($records->count().' araç '.($active ? 'yayına alındı' : 'incelemeye alındı'))->success()->send();
        };

        return $table
            ->defaultSort('active', 'desc')
            ->description('Yayında: herkes hesaplama yapabilir. İncelemede: sayfa ve SEO içeriği açık, hesaplama kilitli. Staging her zaman açıktır; bu ayar canlı siteyi etkiler.')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Araç')->state(fn (ToolStatus $r) => $r->title())
                    ->description(fn (ToolStatus $r) => '/araclar/'.$r->slug)
                    ->searchable(query: fn ($query, string $search) => $query->where('slug', 'like', '%'.str($search)->slug().'%')),
                Tables\Columns\TextColumn::make('cat')->label('Kategori')->state(fn (ToolStatus $r) => self::categories()[$r->category()] ?? '—')->badge()->color('gray'),
                Tables\Columns\ToggleColumn::make('active')->label('Yayında')
                    ->afterStateUpdated(fn (ToolStatus $r) => $r->update(['updated_by' => auth()->id()])),
                Tables\Columns\TextColumn::make('note')->label('Not')->placeholder('—')->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Son değişiklik')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')->label('Durum')->trueLabel('Yayında')->falseLabel('İncelemede'),
            ])
            ->actions([
                Tables\Actions\Action::make('open')->label('Aç')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (ToolStatus $r) => url('/araclar/'.$r->slug))->openUrlInNewTab(),
                Tables\Actions\Action::make('note')->label('Not')->icon('heroicon-o-pencil-square')
                    ->form([\Filament\Forms\Components\TextInput::make('note')->label('Not')->maxLength(255)])
                    ->fillForm(fn (ToolStatus $r) => ['note' => $r->note])
                    ->action(fn (ToolStatus $r, array $data) => $r->update(['note' => $data['note'], 'updated_by' => auth()->id()])),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('publish')->label('Yayına al')->icon('heroicon-o-check-circle')->color('success')
                    ->requiresConfirmation()->action(fn (Collection $records) => $setActive($records, true)),
                Tables\Actions\BulkAction::make('review')->label('İncelemeye al')->icon('heroicon-o-pause-circle')->color('warning')
                    ->requiresConfirmation()->action(fn (Collection $records) => $setActive($records, false)),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListToolStatuses::route('/')];
    }
}

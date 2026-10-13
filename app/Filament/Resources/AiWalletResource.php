<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiWalletResource\Pages;
use App\Models\AiWallet;
use App\Services\AI\AiCredits;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Telefonla giriş yapan AI kullanıcıları ve kredi yükleme */
class AiWalletResource extends Resource
{
    protected static ?string $model = AiWallet::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Gelir';
    protected static ?string $modelLabel = 'AI kullanıcısı';
    protected static ?string $pluralModelLabel = 'AI kredileri';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('phone')->label('Telefon (5XX…)')->disabled(),
                Forms\Components\TextInput::make('name')->label('Ad soyad / not adı')->maxLength(120),
                Forms\Components\TextInput::make('credits')->label('Kredi')->disabled()
                    ->helperText('Kredi yüklemek için listedeki "Kredi yükle" düğmesini kullanın (kayıt tutulur).'),
                Forms\Components\Toggle::make('blocked')->label('Engelli (AI kullanamaz)'),
                Forms\Components\Textarea::make('notes')->label('Not')->maxLength(500)->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('last_used_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->formatStateUsing(fn ($state) => '0'.$state)->searchable()->copyable(),
                Tables\Columns\TextColumn::make('name')->label('Ad')->placeholder('—')->searchable(),
                Tables\Columns\TextColumn::make('credits')->label('Kredi')->numeric()->sortable()->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('free_today')->label('Bugün ücretsiz kalan')
                    ->state(fn (AiWallet $r) => $r->freeLeft()),
                Tables\Columns\TextColumn::make('total_used')->label('Toplam kullanım')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('last_used_at')->label('Son kullanım')->since()->sortable()->placeholder('—'),
                Tables\Columns\TextColumn::make('verified_at')->label('İlk giriş')->date('d.m.Y')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('blocked')->label('Engelli')->boolean()->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_credits')->label('Kredisi olanlar')->query(fn ($q) => $q->where('credits', '>', 0)),
                Tables\Filters\Filter::make('active_week')->label('Son 7 gün kullananlar')->query(fn ($q) => $q->where('last_used_at', '>=', now()->subWeek())),
            ])
            ->actions([
                Tables\Actions\Action::make('grant')->label('Kredi yükle')->icon('heroicon-o-plus-circle')->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('amount')->label('Kredi (eksi değer düşer)')->numeric()->required()->minValue(-1000)->maxValue(10000)->default(10),
                        Forms\Components\TextInput::make('note')->label('Açıklama')->placeholder('Örn: 30 kredi paketi, havale 13.10')->maxLength(255)->required(),
                    ])
                    ->action(function (AiWallet $record, array $data): void {
                        app(AiCredits::class)->grant($record, (int) $data['amount'], $data['note'], auth()->id());
                        Notification::make()->title('Kredi güncellendi: '.$record->fresh()->credits)->success()->send();
                    }),
                Tables\Actions\EditAction::make()->label('Düzenle'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiWallets::route('/'),
            'edit' => Pages\EditAiWallet::route('/{record}/edit'),
        ];
    }
}

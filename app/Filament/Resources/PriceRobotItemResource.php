<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceRobotItemResource\Pages;
use App\Models\PriceRobotItem;
use App\Services\Pricing\PriceRobot;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

/** Fiyat robotunun bulduğu fiyatlar: yayındaki değer, onay bekleyen öneri, kaynaklar */
class PriceRobotItemResource extends Resource
{
    protected static ?string $model = PriceRobotItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Fiyat Merkezi';
    protected static ?string $navigationLabel = 'Fiyat Robotu';
    protected static ?string $modelLabel = 'Fiyat kalemi';
    protected static ?string $pluralModelLabel = 'Fiyat Robotu';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $n = PriceRobotItem::query()->whereNotNull('proposed_value')->count();

            return $n ? (string) $n : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        $statusLabels = ['live' => 'Yayında', 'needs_review' => 'Onay bekliyor', 'error' => 'Bulunamadı', 'rejected' => 'Reddedildi', 'new' => 'Yeni'];

        return $table
            ->defaultSort('researched_at', 'desc')
            ->description('Her sabah 06:30: önce resmî poz, yoksa Google fiyatı (poz yerine geçen fiyat × 1,40). Yayındaki fiyat araçlarda hemen kullanılır. Onay bekleyen öneri siz onaylayana kadar yayına girmez. Elle girdiğiniz fiyat kilitlenir; robot değiştirmez.')
            ->columns([
                Tables\Columns\TextColumn::make('label')->label('Kalem')->description(fn (PriceRobotItem $r) => $r->tool_slug.' · '.($r->unit ?? ''))->searchable()->wrap(),
                Tables\Columns\TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn (?string $state, PriceRobotItem $record) => ($record->proposed_value ? 'Onay bekliyor' : ($statusLabels[$state] ?? $state)).($record->locked ? ' · kilitli' : ''))
                    ->color(fn (?string $state, PriceRobotItem $record) => $record->proposed_value ? 'warning' : match ($state) { 'live' => 'success', 'error' => 'danger', default => 'gray' }),
                Tables\Columns\TextColumn::make('value')->label('Yayındaki (TL)')->state(fn (PriceRobotItem $r) => PriceRobotItem::fmt($r->value))
                    ->description(fn (PriceRobotItem $r) => 'Kod: '.PriceRobotItem::fmt($r->default_value)),
                Tables\Columns\TextColumn::make('proposed_value')->label('Öneri (TL)')->state(fn (PriceRobotItem $r) => $r->proposed_value ? PriceRobotItem::fmt($r->proposed_value) : '—'),
                Tables\Columns\TextColumn::make('web_median')->label('Web ortanca')->state(fn (PriceRobotItem $r) => $r->web_median ? number_format($r->web_median, 0, ',', '.').' × '.rtrim(rtrim(number_format($r->multiplier, 2, ',', ''), '0'), ',') : '—')
                    ->description(fn (PriceRobotItem $r) => $r->source_count.' kaynak'),
                Tables\Columns\TextColumn::make('researched_at')->label('Araştırma')->since()->sortable(),
                Tables\Columns\TextColumn::make('note')->label('Not')->limit(60)->toggleable(isToggledHiddenByDefault: true)->wrap(),
            ])
            ->filters([
                Tables\Filters\Filter::make('review')->label('Onay bekleyenler')->query(fn ($q) => $q->whereNotNull('proposed_value')),
                Tables\Filters\SelectFilter::make('tool_slug')->label('Araç')->options(fn () => PriceRobotItem::query()->distinct()->pluck('tool_slug', 'tool_slug')->filter()->all()),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')->label('Onayla')->icon('heroicon-o-check')->color('success')
                    ->visible(fn (PriceRobotItem $r) => (bool) $r->proposed_value)->requiresConfirmation()
                    ->modalDescription(fn (PriceRobotItem $r) => 'Öneri yayına alınacak: '.PriceRobotItem::fmt($r->proposed_value).' TL')
                    ->action(function (PriceRobotItem $r) {
                        app(PriceRobot::class)->approve($r, auth()->id());
                        Notification::make()->title('Fiyat yayına alındı')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')->label('Reddet')->icon('heroicon-o-x-mark')->color('danger')
                    ->visible(fn (PriceRobotItem $r) => (bool) $r->proposed_value)->requiresConfirmation()
                    ->action(fn (PriceRobotItem $r) => $r->update(['proposed_value' => null, 'status' => $r->value ? 'live' : 'rejected', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()])),
                Tables\Actions\Action::make('sources')->label('Kaynaklar')->icon('heroicon-o-link')->color('gray')
                    ->modalSubmitAction(false)->modalCancelActionLabel('Kapat')
                    ->modalContent(fn (PriceRobotItem $r) => new HtmlString('<ul style="line-height:1.8">'.collect($r->sources ?? [])->map(fn ($s) => '<li><b>'.e(number_format((float) ($s['price_tl'] ?? 0), 0, ',', '.')).' TL</b> · <a href="'.e($s['url'] ?? '#').'" target="_blank" rel="noopener" style="text-decoration:underline">'.e($s['name'] ?: ($s['url'] ?? '')).'</a></li>')->implode('').'</ul>'.($r->note ? '<p style="margin-top:8px;color:#6b7280">'.e($r->note).'</p>' : ''))),
                Tables\Actions\Action::make('set')->label('Fiyat gir')->icon('heroicon-o-pencil-square')
                    ->form(fn (PriceRobotItem $r) => [
                        Forms\Components\TextInput::make('value')->label(is_array($r->value ?? $r->default_value) ? 'Orta kalite fiyatı (TL) — ekonomik/premium aynı oranla ölçeklenir' : 'Fiyat (TL)')->numeric()->required()->minValue(0.01),
                        Forms\Components\Toggle::make('locked')->label('Kilitle (robot bu fiyatı değiştirmesin)')->default(true),
                    ])
                    ->fillForm(fn (PriceRobotItem $r) => ['value' => PriceRobotItem::mid($r->value ?? $r->default_value), 'locked' => true])
                    ->action(function (PriceRobotItem $r, array $data) {
                        $base = $r->value ?? $r->default_value;
                        $mid = (float) $data['value'];
                        $old = PriceRobotItem::mid($base);
                        $val = is_array($base) && $old ? array_map(fn ($v) => round((float) $v * $mid / $old, $v >= 100 ? 0 : 2), $base) : $mid;
                        $r->update(['proposed_value' => $val, 'locked' => (bool) $data['locked']]);
                        app(PriceRobot::class)->approve($r->refresh(), auth()->id());
                        Notification::make()->title('Fiyat güncellendi')->success()->send();
                    }),
                Tables\Actions\Action::make('unlock')->label('Kilidi aç')->icon('heroicon-o-lock-open')->color('gray')
                    ->visible(fn (PriceRobotItem $r) => $r->locked)->action(fn (PriceRobotItem $r) => $r->update(['locked' => false])),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPriceRobotItems::route('/')];
    }
}

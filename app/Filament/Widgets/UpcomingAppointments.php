<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/** Yaklaşan yerinde keşif randevuları */
class UpcomingAppointments extends BaseWidget
{
    protected static ?string $heading = 'Yaklaşan keşif randevuları';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Lead::query()->whereNotNull('inspection_appointment_at')->where('inspection_appointment_at', '>=', now()->startOfDay())->orderBy('inspection_appointment_at')->limit(6))
            ->paginated(false)
            ->emptyStateHeading('Planlanmış randevu yok')
            ->emptyStateDescription('Talep detayından "Keşif randevusu" girildiğinde burada görünür; müşteriye SMS ve bir gün önce hatırlatma gider.')
            ->columns([
                Tables\Columns\TextColumn::make('inspection_appointment_at')->label('Tarih')->dateTime('d.m.Y H:i')
                    ->description(fn (Lead $r) => $r->inspection_appointment_at?->locale('tr')->diffForHumans()),
                Tables\Columns\TextColumn::make('name')->label('Müşteri')->description(fn (Lead $r) => $r->phone),
                Tables\Columns\TextColumn::make('inspection_address')->label('Adres')->limit(40)->placeholder('—'),
                Tables\Columns\TextColumn::make('inspection_status')->label('Ödeme')->badge()
                    ->formatStateUsing(fn ($state) => ['odendi' => 'Ödendi', 'kabul' => 'Bekliyor', 'iptal' => 'İptal'][$state] ?? '—')
                    ->color(fn ($state) => ['odendi' => 'success', 'kabul' => 'warning', 'iptal' => 'danger'][$state] ?? 'gray'),
                Tables\Columns\IconColumn::make('inspection_reminder_sent_at')->label('Hatırlatma')->boolean()
                    ->state(fn (Lead $r) => (bool) $r->inspection_reminder_sent_at),
            ])
            ->recordUrl(fn (Lead $r) => LeadResource::getUrl('edit', ['record' => $r]));
    }
}

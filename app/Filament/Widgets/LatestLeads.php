<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/** Son gelen teklif talepleri */
class LatestLeads extends BaseWidget
{
    protected static ?string $heading = 'Son teklif talepleri';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Lead::query()->latest('created_at')->limit(8))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Müşteri')
                    ->description(fn (Lead $r) => $r->phone),
                Tables\Columns\TextColumn::make('project_type')->label('İş')->placeholder('—')->limit(22),
                Tables\Columns\TextColumn::make('estimated_cost')->label('Tahmini')
                    ->formatStateUsing(fn ($state, Lead $r) => $r->estimated_min && $r->estimated_max
                        ? '₺'.number_format($r->estimated_min / 1000, 0, ',', '.').'–'.number_format($r->estimated_max / 1000, 0, ',', '.').' bin'
                        : ($state ? '₺'.number_format($state, 0, ',', '.') : '—')),
                Tables\Columns\TextColumn::make('status')->label('Aşama')->badge()
                    ->formatStateUsing(fn (?string $state) => [
                        'yeni' => 'Yeni talep', 'aranacak' => 'Aranacak', 'gorusuldu' => 'Görüşüldü', 'nitelikli' => 'Nitelikli',
                        'kesif_planlandi' => 'Keşif planlandı', 'teklif_hazirlaniyor' => 'Teklif hazırlanıyor', 'teklif_verildi' => 'Teklif verildi',
                        'takipte' => 'Takipte', 'kazanildi' => 'Kazanıldı', 'kaybedildi' => 'Kaybedildi', 'iptal' => 'İptal',
                    ][$state] ?? $state)
                    ->color(fn (?string $state) => $state === 'yeni' ? 'warning' : ($state === 'kazanildi' ? 'success' : 'gray')),
                Tables\Columns\TextColumn::make('created_at')->label('Geliş')->since(),
            ])
            ->recordUrl(fn (Lead $r) => LeadResource::getUrl('edit', ['record' => $r]))
            ->actions([
                Tables\Actions\Action::make('call')->label('Ara')->icon('heroicon-o-phone')->color('success')
                    ->url(fn (Lead $r) => 'tel:+'.($r->phone_normalized ?: preg_replace('/\D+/', '', (string) $r->phone))),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Online siparişler (iyzico): yapay zekâ kredisi, ücretli Pro rapor */
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Gelir';
    protected static ?string $navigationLabel = 'Online siparişler';
    protected static ?string $modelLabel = 'Sipariş';
    protected static ?string $pluralModelLabel = 'Online siparişler';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('public_id')->label('Sipariş')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('title')->label('Ürün')->wrap(),
                Tables\Columns\TextColumn::make('amount')->label('Tutar (TL)')->numeric(2)->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn (string $state) => ['paid' => 'Ödendi', 'pending' => 'Bekliyor', 'failed' => 'Başarısız'][$state] ?? $state)
                    ->color(fn (string $state) => ['paid' => 'success', 'failed' => 'danger'][$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('name')->label('Müşteri')->description(fn (Order $r) => $r->phone ? '0'.$r->phone : null),
                Tables\Columns\TextColumn::make('paid_at')->label('Ödeme')->dateTime('d.m.Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('error')->label('Hata')->limit(40)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Durum')->options(['paid' => 'Ödendi', 'pending' => 'Bekliyor', 'failed' => 'Başarısız']),
                Tables\Filters\SelectFilter::make('kind')->label('Tür')->options(['credits' => 'Yapay zekâ kredisi', 'pro_report' => 'Pro rapor']),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListOrders::route('/')];
    }
}

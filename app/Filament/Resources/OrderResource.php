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
                Tables\Columns\TextColumn::make('invoice')->label('Fatura')
                    ->state(fn (Order $r) => $r->isPaid() ? ($r->invoice_no ?: 'KESİLECEK') : '—')
                    ->description(fn (Order $r) => $r->isPaid() ? $r->buyer()['type'].' · '.$r->buyer()['title'] : null)
                    ->color(fn (Order $r) => $r->isPaid() && ! $r->invoice_no ? 'danger' : null),
                Tables\Columns\TextColumn::make('error')->label('Hata')->limit(40)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')->label('Kesilecek faturalar (Excel)')->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => response()->streamDownload(function () {
                        $out = fopen('php://output', 'w');
                        fwrite($out, "\xEF\xBB\xBF");
                        fputcsv($out, ['Sipariş', 'Ödeme tarihi', 'Belge türü', 'Alıcı unvan / ad', 'VKN / TCKN', 'Vergi dairesi', 'Adres', 'E-posta', 'Telefon', 'Hizmet', 'Tutar (KDV hariç)', 'KDV %20', 'Toplam', 'iyzico ödeme no'], ';');
                        foreach (Order::query()->where('status', 'paid')->whereNull('invoiced_at')->orderBy('paid_at')->get() as $o) {
                            $b = $o->buyer();
                            fputcsv($out, [$o->public_id, $o->paid_at?->timezone('Europe/Istanbul')->format('d.m.Y H:i'), $b['type'], $b['title'], $b['tax_no'], $b['tax_office'], $b['address'], $b['email'], $b['phone'],
                                $o->title, number_format($o->net(), 2, ',', ''), number_format($o->vat(), 2, ',', ''), number_format($o->amount, 2, ',', ''), $o->payment_id], ';');
                        }
                        fclose($out);
                    }, 'worksitem-kesilecek-faturalar-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8'])),
            ])
            ->actions([
                Tables\Actions\Action::make('invoiced')->label('Fatura kesildi')->icon('heroicon-o-document-check')->color('success')
                    ->visible(fn (Order $r) => $r->isPaid() && ! $r->invoice_no)
                    ->form([\Filament\Forms\Components\TextInput::make('invoice_no')->label('Fatura / e-Arşiv numarası')->required()->maxLength(40)])
                    ->action(fn (Order $r, array $data) => $r->update(['invoice_no' => $data['invoice_no'], 'invoiced_at' => now()])),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Durum')->options(['paid' => 'Ödendi', 'pending' => 'Bekliyor', 'failed' => 'Başarısız']),
                Tables\Filters\SelectFilter::make('kind')->label('Tür')->options(Order::KINDS),
                Tables\Filters\Filter::make('to_invoice')->label('Kesilecek faturalar')->query(fn ($q) => $q->where('status', 'paid')->whereNull('invoiced_at')),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListOrders::route('/')];
    }
}

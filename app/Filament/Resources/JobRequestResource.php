<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobRequestResource\Pages;
use App\Models\JobOffer;
use App\Models\JobRequest;
use App\Services\Market\Marketplace;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

/** Teklif talepleri: kullanıcı talepleri, davet edilen firmalar, teklifler; firma bulunamayan talepler saha satışına */
class JobRequestResource extends Resource
{
    protected static ?string $model = JobRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Gelir';
    protected static ?string $navigationLabel = 'Teklif talepleri';
    protected static ?string $modelLabel = 'Teklif talebi';
    protected static ?string $pluralModelLabel = 'Teklif talepleri';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('public_id')->label('Talep')->searchable()->copyable()
                    ->description(fn (JobRequest $r) => $r->created_at->timezone('Europe/Istanbul')->format('d.m.Y H:i')),
                Tables\Columns\TextColumn::make('title')->label('İş')->wrap()->limit(60)->description(fn (JobRequest $r) => $r->tradeLabel()),
                Tables\Columns\TextColumn::make('city')->label('Yer')->formatStateUsing(fn ($state, JobRequest $record) => $record->location())->sortable(),
                Tables\Columns\TextColumn::make('est_high')->label('Yaklaşık')->formatStateUsing(fn ($state, JobRequest $record) => $record->range() ?: '—'),
                Tables\Columns\TextColumn::make('name')->label('Kullanıcı')->searchable()->description(fn (JobRequest $r) => '0'.$r->phone),
                Tables\Columns\TextColumn::make('invited_count')->label('Davet')->sortable()
                    ->color(fn ($state) => (int) $state === 0 ? 'danger' : null),
                Tables\Columns\TextColumn::make('offers_count')->label('Teklif')->counts('offers'),
                Tables\Columns\TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn (string $state) => JobRequest::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => ['matched' => 'success', 'open' => 'warning'][$state] ?? 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Durum')->options(JobRequest::STATUSES),
                Tables\Filters\Filter::make('no_firm')->label('Firma bulunamayan (saha satışı)')->query(fn ($q) => $q->where('invited_count', 0)),
                Tables\Filters\SelectFilter::make('trade')->label('İş kolu')->options(collect((array) config('worksitem_trades'))->map(fn ($t) => $t['label'])->all()),
            ])
            ->actions([
                Tables\Actions\Action::make('offers')->label('Teklifler')->icon('heroicon-o-document-text')->color('gray')
                    ->modalSubmitAction(false)->modalCancelActionLabel('Kapat')
                    ->modalContent(fn (JobRequest $r) => new HtmlString(
                        '<p style="margin-bottom:8px">'.e($r->description ?: 'Açıklama yok').'</p>'
                        .'<p style="margin-bottom:8px;color:#6b7280">Davet: '.e($r->invites()->with('firm')->get()->map(fn ($i) => ($i->firm?->name ?? '?').' ('.$i->status.')')->implode(', ') ?: '—').'</p><ul style="line-height:1.8">'
                        .$r->offers()->with('firm')->get()->map(fn (JobOffer $o) => '<li><b>'.e($o->firm?->name).'</b> · ₺'.number_format($o->amount, 0, ',', '.')
                            .' · '.e(JobOffer::STATUSES[$o->status] ?? $o->status).($o->fee_net ? ' · eşleşme bedeli ₺'.number_format($o->fee_net, 2, ',', '.').' + KDV' : '').'</li>')->implode('')
                        .'</ul><p style="margin-top:8px"><a href="'.e($r->customerUrl()).'" target="_blank" style="text-decoration:underline">Kullanıcı sayfası ↗</a></p>')),
                Tables\Actions\Action::make('reinvite')->label('Yeniden firma ara')->icon('heroicon-o-arrow-path')->color('gray')
                    ->visible(fn (JobRequest $r) => $r->isOpen())
                    ->requiresConfirmation()->modalDescription('Yeni kaydolan uygun firmalar varsa davet gönderilir.')
                    ->action(function (JobRequest $r) {
                        $n = app(Marketplace::class)->invite($r);
                        Notification::make()->title($n > 0 ? "$n firmaya davet gönderildi" : 'Uygun yeni firma yok')->success()->send();
                    }),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListJobRequests::route('/')];
    }
}

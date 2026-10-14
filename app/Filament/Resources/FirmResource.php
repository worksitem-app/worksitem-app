<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FirmResource\Pages;
use App\Models\Firm;
use App\Services\Sms\NetgsmSms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/** Firma üyeliği başvuruları: onay / ret / öne çıkan üyelik ödeme bağlantısı */
class FirmResource extends Resource
{
    protected static ?string $model = Firm::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Gelir';
    protected static ?string $navigationLabel = 'Firma üyelikleri';
    protected static ?string $modelLabel = 'Firma';
    protected static ?string $pluralModelLabel = 'Firma üyelikleri';

    public static function getNavigationBadge(): ?string
    {
        $n = Firm::query()->where('status', 'pending')->count();

        return $n > 0 ? (string) $n : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        $svc = collect((array) config('worksitem_firms.services'))->map(fn ($s) => $s[0])->all();
        $cities = array_combine((array) config('worksitem_firms.cities'), (array) config('worksitem_firms.cities'));

        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Firma adı')->required()->maxLength(150),
            Forms\Components\Select::make('type')->label('Tür')->options((array) config('worksitem_firms.types'))->required(),
            Forms\Components\CheckboxList::make('services')->label('Hizmetler')->options($svc)->columns(3)->columnSpanFull(),
            Forms\Components\Select::make('cities')->label('İller')->options($cities)->multiple()->searchable(),
            Forms\Components\TextInput::make('district')->label('İlçe / bölge')->maxLength(100),
            Forms\Components\TextInput::make('contact_name')->label('Yetkili')->required(),
            Forms\Components\TextInput::make('phone')->label('Telefon (5XXXXXXXXX)')->required()->maxLength(10),
            Forms\Components\TextInput::make('email')->label('E-posta')->email(),
            Forms\Components\TextInput::make('website')->label('Web sitesi')->url(),
            Forms\Components\TextInput::make('tax_no')->label('Vergi / TC no (yayınlanmaz)'),
            Forms\Components\TextInput::make('founded_year')->label('Kuruluş yılı')->numeric(),
            Forms\Components\Textarea::make('description')->label('Tanıtım')->rows(5)->maxLength(1500)->columnSpanFull(),
            Forms\Components\Select::make('status')->label('Durum')->options(Firm::STATUSES)->required(),
            Forms\Components\Select::make('plan')->label('Plan')->options(['free' => 'Ücretsiz', 'pro' => 'Öne çıkan']),
            Forms\Components\DateTimePicker::make('plan_until')->label('Öne çıkan bitiş'),
            Forms\Components\Textarea::make('admin_note')->label('Yönetici notu (yayınlanmaz)')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Firma')->searchable()->wrap()
                    ->description(fn (Firm $r) => $r->typeLabel().' · '.implode(', ', array_slice((array) $r->cities, 0, 3))),
                Tables\Columns\TextColumn::make('contact_name')->label('Yetkili')->searchable()->description(fn (Firm $r) => '0'.$r->phone),
                Tables\Columns\TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn (string $state) => Firm::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'][$state] ?? 'gray'),
                Tables\Columns\IconColumn::make('pro')->label('Öne çıkan')->boolean()->state(fn (Firm $r) => $r->isPro()),
                Tables\Columns\TextColumn::make('views')->label('Görüntülenme')->sortable(),
                Tables\Columns\TextColumn::make('contact_clicks')->label('İletişim tık.')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Başvuru')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Durum')->options(Firm::STATUSES),
                Tables\Filters\SelectFilter::make('type')->label('Tür')->options((array) config('worksitem_firms.types')),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')->label('Onayla')->icon('heroicon-o-check')->color('success')
                    ->visible(fn (Firm $r) => $r->status !== 'approved')->requiresConfirmation()
                    ->modalDescription('Firma onaylanır ve yetkiliye SMS gider.')
                    ->action(function (Firm $r) {
                        $r->update(['status' => 'approved', 'approved_at' => now()]);
                        self::sms($r, 'Worksitem firma üyeliğiniz onaylandı ('.$r->public_id.'). Kainta İnşaat Uzmanları');
                        Notification::make()->title('Firma onaylandı')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')->label('Reddet')->icon('heroicon-o-x-mark')->color('danger')
                    ->visible(fn (Firm $r) => $r->status === 'pending')
                    ->form([Forms\Components\Textarea::make('note')->label('Gerekçe (yalnız yönetici notu)')->rows(2)])
                    ->action(fn (Firm $r, array $data) => $r->update(['status' => 'rejected', 'admin_note' => trim(($r->admin_note ? $r->admin_note."\n" : '').($data['note'] ?? ''))])),
                Tables\Actions\Action::make('proLink')->label('Öne çıkan ödeme bağlantısı')->icon('heroicon-o-link')->color('gray')
                    ->visible(fn (Firm $r) => $r->status === 'approved')
                    ->modalSubmitAction(false)->modalCancelActionLabel('Kapat')
                    ->modalContent(fn (Firm $r) => new \Illuminate\Support\HtmlString(
                        '<p style="margin-bottom:8px">Bu bağlantıyı firmaya WhatsApp veya e-postayla gönderin (14 gün geçerli). Ödeme alınınca üyelik 1 yıl öne çıkar.</p><textarea readonly rows="4" style="width:100%;font-size:12px;border:1px solid #ddd;border-radius:8px;padding:8px" onclick="this.select()">'
                        .e(URL::temporarySignedRoute('firms.pro', now()->addDays(14), ['firm' => $r->slug])).'</textarea>')),
                Tables\Actions\Action::make('view')->label('Sayfa')->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                    ->visible(fn (Firm $r) => $r->status === 'approved' && config('worksitem_firms.directory_enabled'))
                    ->url(fn (Firm $r) => route('firms.show', $r), shouldOpenInNewTab: true),
                Tables\Actions\EditAction::make(),
            ])
            ->paginated([25, 50, 100]);
    }

    private static function sms(Firm $r, string $msg): void
    {
        try {
            app(NetgsmSms::class)->send($r->phone, $msg);
        } catch (\Throwable $e) {
            Log::warning('Firma onay SMS gönderilemedi', ['firm' => $r->public_id, 'error' => $e->getMessage()]);
        }
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListFirms::route('/'), 'edit' => Pages\EditFirm::route('/{record}/edit')];
    }
}

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
        $trades = collect((array) config('worksitem_trades'));
        $cities = array_combine((array) config('worksitem_firms.cities'), (array) config('worksitem_firms.cities'));

        return $form->schema([
            Forms\Components\Section::make('Firma ve vergi')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Firma / marka adı')->required()->maxLength(150),
                Forms\Components\Select::make('type')->label('Tür')->options((array) config('worksitem_firms.types'))->required(),
                Forms\Components\Select::make('legal_type')->label('Vergi türü')->options(['sirket' => 'Şirket', 'sahis' => 'Şahıs işletmesi'])->required(),
                Forms\Components\TextInput::make('legal_name')->label('Unvan (fatura)')->required()->maxLength(200),
                Forms\Components\TextInput::make('tax_no')->label('VKN / TCKN')->required()
                    ->rule(fn () => fn (string $attribute, $value, \Closure $fail) => \App\Support\TaxId::valid((string) $value) ? null : $fail('Numara geçerli görünmüyor.')),
                Forms\Components\TextInput::make('tax_office')->label('Vergi dairesi')->required(),
                Forms\Components\Select::make('efatura')->label('e-Fatura durumu')->options(Firm::EFATURA)->required()
                    ->helperText('GİB e-Belge portalı › e-Fatura kayıtlı kullanıcılar listesinden kontrol edin.'),
                Forms\Components\TextInput::make('mersis_no')->label('MERSİS no'),
                Forms\Components\TextInput::make('kep')->label('KEP'),
                Forms\Components\Select::make('invoice_city')->label('Fatura ili')->options($cities)->searchable(),
                Forms\Components\TextInput::make('invoice_district')->label('Fatura ilçesi'),
                Forms\Components\TextInput::make('invoice_address')->label('Fatura adresi')->columnSpanFull(),
                Forms\Components\DateTimePicker::make('tax_verified_at')->label('Vergi bilgileri doğrulandı'),
            ]),
            Forms\Components\Section::make('İş kolları ve bölge')->columns(2)->schema([
                Forms\Components\CheckboxList::make('trades')->label('İş kolları')->options($trades->map(fn ($t) => $t['label'])->all())->columns(3)->columnSpanFull(),
                Forms\Components\Select::make('subservices')->label('Alt hizmetler')->multiple()->searchable()
                    ->options($trades->flatMap(fn ($t) => $t['services'])->unique()->mapWithKeys(fn ($v) => [$v => $v])->all())->columnSpanFull(),
                Forms\Components\Select::make('cities')->label('İller')->options($cities)->multiple()->searchable(),
                Forms\Components\TagsInput::make('districts')->label('Yalnız bu ilçeler (boş = ilin tamamı)'),
                Forms\Components\Toggle::make('accepts_jobs')->label('Teklif davetleri alsın'),
                Forms\Components\TextInput::make('referrer')->label('Saha temsilcisi kodu'),
            ]),
            Forms\Components\Section::make('İletişim ve tanıtım')->columns(2)->schema([
                Forms\Components\TextInput::make('contact_name')->label('Yetkili')->required(),
                Forms\Components\TextInput::make('phone')->label('Telefon (5XXXXXXXXX)')->required()->maxLength(10),
                Forms\Components\TextInput::make('email')->label('E-posta')->email(),
                Forms\Components\TextInput::make('website')->label('Web sitesi')->url(),
                Forms\Components\TextInput::make('founded_year')->label('Kuruluş yılı')->numeric(),
                Forms\Components\Textarea::make('description')->label('Tanıtım')->rows(4)->maxLength(1500)->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Durum')->columns(3)->schema([
                Forms\Components\Select::make('status')->label('Durum')->options(Firm::STATUSES)->required(),
                Forms\Components\Select::make('plan')->label('Plan')->options(['free' => 'Ücretsiz', 'pro' => 'Öne çıkan']),
                Forms\Components\DateTimePicker::make('plan_until')->label('Öne çıkan bitiş'),
                Forms\Components\Textarea::make('admin_note')->label('Yönetici notu (yayınlanmaz)')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Firma')->searchable()->wrap()
                    ->description(fn (Firm $r) => $r->typeLabel().' · '.implode(', ', array_slice((array) $r->cities, 0, 3))),
                Tables\Columns\TextColumn::make('contact_name')->label('Yetkili')->searchable()->description(fn (Firm $r) => '0'.$r->phone),
                Tables\Columns\TextColumn::make('tax_no')->label('VKN/TCKN')->searchable()
                    ->description(fn (Firm $r) => ($r->tax_office ? $r->tax_office.' V.D. · ' : '').$r->invoiceType().($r->tax_verified_at ? ' · doğrulandı' : ' · doğrulanmadı')),
                Tables\Columns\TextColumn::make('referrer')->label('Temsilci')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('matches_count')->label('Eşleşme')->sortable()->toggleable(),
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
                Tables\Filters\Filter::make('unverified')->label('Vergi doğrulanmamış')->query(fn ($q) => $q->whereNull('tax_verified_at')),
                Tables\Filters\SelectFilter::make('referrer')->label('Saha temsilcisi')
                    ->options(fn () => Firm::query()->whereNotNull('referrer')->distinct()->orderBy('referrer')->pluck('referrer', 'referrer')->all()),
            ])
            ->actions([
                Tables\Actions\Action::make('answers')->label('Hizmet bilgileri')->icon('heroicon-o-clipboard-document-list')->color('gray')
                    ->visible(fn (Firm $r) => ! empty($r->answers))
                    ->modalSubmitAction(false)->modalCancelActionLabel('Kapat')
                    ->modalContent(function (Firm $r) {
                        $qs = \App\Services\Market\FirmQuestions::for($r);
                        $rows = collect((array) $r->answers)->map(function ($v, $k) use ($qs) {
                            $q = $qs[$k] ?? ['q' => $k, 'type' => 'text'];
                            $val = match ($q['type'] ?? 'text') {
                                'money' => number_format((int) $v, 0, ',', '.').' '.($q['unit'] ?? 'TL'),
                                'select' => $q['options'][$v] ?? $v,
                                'multi' => implode(', ', array_map(fn ($x) => $q['options'][$x] ?? $x, (array) $v)),
                                default => (string) $v,
                            };

                            return '<tr><td style="padding:4px 8px;color:#6b7280">'.e($q['q']).'</td><td style="padding:4px 8px;font-weight:600">'.e($val).'</td></tr>';
                        })->implode('');

                        return new \Illuminate\Support\HtmlString('<table>'.$rows.'</table><p style="margin-top:8px;font-size:12px;color:#6b7280">Fiyat karşılaştırması: Fiyat Merkezi › Saha fiyat beyanları</p>');
                    }),
                Tables\Actions\Action::make('taxDoc')->label('Vergi levhası')->icon('heroicon-o-document-arrow-down')->color('gray')
                    ->visible(fn (Firm $r) => $r->tax_doc_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($r->tax_doc_path))
                    ->action(fn (Firm $r) => \Illuminate\Support\Facades\Storage::disk('local')->download($r->tax_doc_path, 'vergi-levhasi-'.$r->tax_no.'.'.pathinfo($r->tax_doc_path, PATHINFO_EXTENSION))),
                Tables\Actions\Action::make('approve')->label('Onayla')->icon('heroicon-o-check')->color('success')
                    ->visible(fn (Firm $r) => $r->status !== 'approved')
                    ->form([
                        Forms\Components\Placeholder::make('kontrol')->label('Kontrol')->content(fn (Firm $r) => new \Illuminate\Support\HtmlString(
                            e($r->legal_name).' · VKN/TCKN '.e($r->tax_no).' · '.e($r->tax_office).' V.D.<br>Vergi levhasını açıp unvan, numara ve vergi dairesini karşılaştırın. e-Fatura durumunu GİB e-Belge portalındaki kayıtlı kullanıcılar listesinden kontrol edin.')),
                        Forms\Components\Select::make('efatura')->label('e-Fatura durumu')->options(['yes' => 'e-Fatura mükellefi', 'no' => 'Değil (e-Arşiv)'])->required(),
                        Forms\Components\Checkbox::make('checked')->label('Vergi levhasını kontrol ettim; bilgiler doğru')->accepted(),
                    ])
                    ->fillForm(fn (Firm $r) => ['efatura' => $r->efatura === 'unknown' ? null : $r->efatura])
                    ->action(function (Firm $r, array $data) {
                        $r->update(['status' => 'approved', 'approved_at' => now(), 'tax_verified_at' => now(), 'efatura' => $data['efatura']]);
                        self::sms($r, 'Worksitem firma üyeliğiniz onaylandı ('.$r->public_id.'). Bölgenizdeki işler SMS ile gelecek. Firma paneli: '.route('market.panel.login').' Worksitem');
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

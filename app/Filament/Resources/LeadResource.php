<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?string $navigationLabel = 'Teklif Talepleri';
    protected static ?string $modelLabel = 'Teklif Talebi';
    protected static ?string $pluralModelLabel = 'Teklif Talepleri';
    protected static ?string $navigationGroup = 'Teklifler (CRM)';
    protected static ?int $navigationSort = 1;

    /** Menüde yeni (henüz dokunulmamış) talep sayısı */
    public static function getNavigationBadge(): ?string
    {
        $n = Lead::query()->where('status', 'yeni')->count();

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Lead Özeti')
                ->description('Worksitem tarafından hesaplanan satış önceliği')
                ->schema([
                    Forms\Components\TextInput::make('ref_code')
                        ->label('Talep No (müşteriye SMS ile giden)')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('public_id')
                        ->label('Lead No')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\Select::make('lead_quality')
                        ->label('Lead Kalitesi')
                        ->options(self::qualityOptions())
                        ->required(),

                    Forms\Components\TextInput::make('lead_score')
                        ->label('Lead Skoru')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),

                    Forms\Components\Toggle::make('sales_ready')
                        ->label('Satışa Hazır')
                        ->inline(false),

                    Forms\Components\Select::make('status')
                        ->label('Satış Aşaması')
                        ->options(self::statusOptions())
                        ->required()
                        ->default('yeni'),

                    Forms\Components\Select::make('assigned_user_id')
                        ->label('Sorumlu')
                        ->options(fn (): array => User::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Select::make('inspection_status')
                        ->label('Yerinde Keşif')
                        ->options(self::inspectionOptions())
                        ->placeholder('Keşif kabul edilmedi')
                        ->live()
                        ->afterStateUpdated(function (?string $state, Forms\Set $set, Forms\Get $get): void {
                            if ($state === 'odendi' && ! $get('inspection_paid_at')) {
                                $set('inspection_paid_at', now()->format('Y-m-d H:i:s'));
                            }
                        }),

                    Forms\Components\DateTimePicker::make('inspection_accepted_at')
                        ->label('Keşif Kabul Zamanı')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\DateTimePicker::make('inspection_paid_at')
                        ->label('Keşif Ödeme Zamanı'),

                    Forms\Components\DateTimePicker::make('inspection_appointment_at')
                        ->label('Keşif Randevusu')
                        ->seconds(false)
                        ->minutesStep(15)
                        ->timezone('Europe/Istanbul')
                        ->helperText('Kaydedince müşteriye randevu SMS\'i gider; bir gün önce 18:00\'de hatırlatma gönderilir.'),

                    Forms\Components\TextInput::make('inspection_address')
                        ->label('Keşif Adresi')
                        ->maxLength(255)
                        ->placeholder('Mahalle, sokak, no, daire — ilçe/il'),

                    Forms\Components\Select::make('inspection_payment_method')
                        ->label('Keşif Ödeme Yöntemi')
                        ->options(fn (): array => collect((array) config('worksitem_inspection.payment_methods'))->map(fn ($m) => $m['label'])->all())
                        ->placeholder('—'),

                    Forms\Components\Placeholder::make('inspection_summary')
                        ->label('Keşif Siparişi')
                        ->content(function (?Lead $record): string {
                            $d = (array) ($record?->inspection_data ?? []);
                            if ($d === []) {
                                return '—';
                            }
                            $inv = (array) ($d['invoice'] ?? []);
                            $early = (bool) ($d['consents']['early_performance'] ?? false);

                            return (($inv['type'] ?? '') === 'kurumsal'
                                    ? 'Kurumsal fatura: '.($inv['title'] ?? '').' · '.($inv['tax_office'] ?? '').' / '.($inv['tax_no'] ?? '')
                                    : 'Bireysel fatura')
                                .' · '.($early ? 'Cayma süresinde keşif istendi' : 'Keşif 14 gün sonra yapılmalı')
                                .' · Sözleşme '.($record?->inspection_terms_version ?? '-')
                                .(! empty($d['iyzico']['payment_id']) ? ' · iyzico ödeme no '.$d['iyzico']['payment_id'] : '');
                        })
                        ->columnSpan(2),
                ])
                ->columns(3),

            Forms\Components\Section::make('Müşteri Bilgileri')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Ad Soyad')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\TextInput::make('phone')
                        ->label('Telefon')
                        ->tel()
                        ->required()
                        ->maxLength(30),

                    Forms\Components\TextInput::make('phone_normalized')
                        ->label('Normalize Telefon')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('email')
                        ->label('E-posta')
                        ->email()
                        ->maxLength(150),

                    Forms\Components\TextInput::make('city')
                        ->label('Şehir')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('district')
                        ->label('İlçe / Konum')
                        ->maxLength(150),
                ])
                ->columns(3),

            Forms\Components\Section::make('Proje ve Bütçe')
                ->schema([
                    Forms\Components\TextInput::make('project_type')
                        ->label('Proje Türü')
                        ->maxLength(255),

                    Forms\Components\Select::make('timeline')
                        ->label('Başlangıç Zamanı')
                        ->options([
                            'now' => 'Hemen / 0–1 Ay',
                            'soon' => 'Yakında / 1–3 Ay',
                            'later' => '3–6 Ay',
                            'research' => 'Araştırma Aşaması',
                        ]),

                    Forms\Components\Toggle::make('budget_confirmed')
                        ->label('Bütçeyi Onayladı')
                        ->inline(false),

                    Forms\Components\TextInput::make('estimated_cost')
                        ->label('Tahmini Maliyet')
                        ->numeric()
                        ->prefix('₺'),

                    Forms\Components\TextInput::make('estimated_min')
                        ->label('Alt Bütçe')
                        ->numeric()
                        ->prefix('₺'),

                    Forms\Components\TextInput::make('estimated_max')
                        ->label('Üst Bütçe')
                        ->numeric()
                        ->prefix('₺'),

                    Forms\Components\Textarea::make('notes')
                        ->label('Satış Özeti / Müşteri Notu')
                        ->rows(8)
                        ->columnSpanFull(),

                    Forms\Components\KeyValue::make('project_data')
                        ->label('Proje Verileri')
                        ->keyLabel('Alan')
                        ->valueLabel('Değer')
                        ->reorderable(false)
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Takip Planı')
                ->schema([
                    Forms\Components\DateTimePicker::make('first_contact_at')
                        ->label('İlk Temas'),

                    Forms\Components\DateTimePicker::make('next_action_at')
                        ->label('Sonraki Aksiyon'),

                    Forms\Components\DateTimePicker::make('qualified_at')
                        ->label('Nitelikli Lead Tarihi'),

                    Forms\Components\DateTimePicker::make('proposal_sent_at')
                        ->label('Teklif Gönderim Tarihi'),

                    Forms\Components\DateTimePicker::make('won_at')
                        ->label('Kazanılma Tarihi'),

                    Forms\Components\DateTimePicker::make('lost_at')
                        ->label('Kaybedilme Tarihi'),

                    Forms\Components\TextInput::make('loss_reason')
                        ->label('Kaybetme Nedeni')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Kaynak ve İzin Bilgileri')
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('source')
                        ->label('Kaynak')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('utm_source')
                        ->label('UTM Source'),

                    Forms\Components\TextInput::make('utm_medium')
                        ->label('UTM Medium'),

                    Forms\Components\TextInput::make('utm_campaign')
                        ->label('UTM Campaign'),

                    Forms\Components\TextInput::make('utm_content')
                        ->label('UTM Content'),

                    Forms\Components\TextInput::make('utm_term')
                        ->label('UTM Term'),

                    Forms\Components\TextInput::make('landing_page')
                        ->label('Giriş Sayfası')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('referrer')
                        ->label('Yönlendiren Sayfa')
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('contact_consent')
                        ->label('İletişim İzni'),

                    Forms\Components\Toggle::make('marketing_consent')
                        ->label('Pazarlama İzni'),

                    Forms\Components\TextInput::make('consent_version')
                        ->label('İzin Metni Sürümü'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('sales_ready')
                    ->label('Hazır')
                    ->boolean()
                    ->trueIcon('heroicon-o-fire')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('lead_quality')
                    ->label('Kalite')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string =>
                        self::qualityOptions()[$state] ?? ($state ?: 'Belirsiz')
                    )
                    ->color(fn (?string $state): string => match ($state) {
                        'hot' => 'danger',
                        'warm' => 'warning',
                        'nurture' => 'info',
                        'research' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('lead_score')
                    ->label('Skor')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        (int) $state >= 70 => 'danger',
                        (int) $state >= 40 => 'warning',
                        (int) $state >= 20 => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Müşteri')
                    ->description(fn (Lead $record): string =>
                        trim(($record->ref_code ? $record->ref_code.' · ' : '').($record->phone ?: 'Telefon yok'))
                    )
                    ->searchable(['name', 'phone', 'phone_normalized', 'email', 'ref_code'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('project_type')
                    ->label('Proje')
                    ->limit(35)
                    ->wrap()
                    ->placeholder('Belirtilmedi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Konum')
                    ->formatStateUsing(fn (?string $state, Lead $record): string =>
                        trim(($state ?: '-') . (
                            $record->district ? ' / ' . $record->district : ''
                        ))
                    )
                    ->searchable(['city', 'district'])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('estimated_cost')
                    ->label('Tahmini Bütçe')
                    ->formatStateUsing(function ($state, Lead $record): string {
                        $f = fn ($v) => '₺'.number_format((float) $v, 0, ',', '.');
                        if ($record->estimated_min && $record->estimated_max) {
                            return $f($record->estimated_min).' – '.$f($record->estimated_max);
                        }

                        return $state !== null ? $f($state) : '—';
                    })
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('timeline')
                    ->label('Başlangıç')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'now' => '0–1 Ay',
                        'soon' => '1–3 Ay',
                        'later' => '3–6 Ay',
                        'research' => 'Araştırma',
                        default => $state ?: '—',
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'now' => 'danger',
                        'soon' => 'warning',
                        'later' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('source')
                    ->label('Kaynak')
                    ->badge()
                    ->limit(30)
                    ->placeholder('Belirsiz')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('next_action_at')
                    ->label('Sonraki Aksiyon')
                    ->dateTime('d.m.Y H:i')
                    ->color(fn (Lead $record): string =>
                        $record->next_action_at?->isPast()
                            ? 'danger'
                            : 'gray'
                    )
                    ->placeholder('Planlanmadı')
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Aşama')
                    ->options(self::statusOptions())
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspection_status')
                    ->label('Keşif')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::inspectionOptions()[$state] ?? '—')
                    ->color(fn (?string $state): string => match ($state) {
                        'kabul' => 'warning',
                        'odendi' => 'success',
                        'iptal' => 'gray',
                        default => 'gray',
                    })
                    ->description(fn (Lead $record): ?string => $record->inspection_payment_method
                        ? config('worksitem_inspection.payment_methods.'.$record->inspection_payment_method.'.label')
                        : null)
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Geliş Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            // En yeni talep en üstte (önceden skor sırası yeni talepleri aşağıda gizliyordu)
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('sales_ready')
                    ->label('Satışa Hazır')
                    ->placeholder('Tümü')
                    ->trueLabel('Yalnızca satışa hazır')
                    ->falseLabel('Henüz satışa hazır değil'),

                Tables\Filters\SelectFilter::make('lead_quality')
                    ->label('Lead Kalitesi')
                    ->options(self::qualityOptions())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Satış Aşaması')
                    ->options(self::statusOptions())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('source')
                    ->label('Kaynak')
                    ->options(fn (): array => Lead::query()
                        ->whereNotNull('source')
                        ->where('source', '!=', '')
                        ->distinct()
                        ->orderBy('source')
                        ->pluck('source', 'source')
                        ->all())
                    ->searchable(),

                Tables\Filters\SelectFilter::make('inspection_status')
                    ->label('Yerinde Keşif')
                    ->options(self::inspectionOptions()),

                Tables\Filters\Filter::make('today_new')
                    ->label('Bugün gelenler')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),

                Tables\Filters\Filter::make('today_actions')
                    ->label('Bugün Aranacaklar')
                    ->query(fn (Builder $query): Builder =>
                        $query
                            ->whereNotNull('next_action_at')
                            ->whereDate('next_action_at', '<=', today())
                            ->whereNotIn('status', ['kazanildi', 'kaybedildi', 'iptal'])
                    ),

                Tables\Filters\Filter::make('new_sales_ready')
                    ->label('Yeni Sıcak Talepler')
                    ->query(fn (Builder $query): Builder =>
                        $query
                            ->where('sales_ready', true)
                            ->where('status', 'yeni')
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('call')
                    ->label('Ara')
                    ->icon('heroicon-o-phone')
                    ->color('success')
                    ->url(fn (Lead $record): string =>
                        'tel:+' . (
                            $record->phone_normalized
                            ?: preg_replace('/\D+/', '', $record->phone)
                        )
                    ),

                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn (Lead $record): string =>
                        'https://wa.me/' . (
                            $record->phone_normalized
                            ?: preg_replace('/\D+/', '', $record->phone)
                        )
                    )
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label('Aç'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    private static function qualityOptions(): array
    {
        return [
            'hot' => '🔥 HOT',
            'warm' => '☀️ WARM',
            'nurture' => '🌱 NURTURE',
            'research' => '🔎 RESEARCH',
            'unqualified' => 'Belirsiz',
        ];
    }

    /** @return array<string, string> */
    public static function inspectionOptions(): array
    {
        return [
            'kabul' => 'Kabul — ödeme bekleniyor',
            'odendi' => 'Ödendi — randevu verilecek',
            'iptal' => 'İptal',
        ];
    }

    private static function statusOptions(): array
    {
        return [
            'yeni' => 'Yeni Talep',
            'aranacak' => 'Aranacak',
            'gorusuldu' => 'Görüşüldü',
            'nitelikli' => 'Nitelikli',
            'kesif_planlandi' => 'Keşif Planlandı',
            'teklif_hazirlaniyor' => 'Teklif Hazırlanıyor',
            'teklif_verildi' => 'Teklif Verildi',
            'takipte' => 'Takipte',
            'kazanildi' => 'Kazanıldı',
            'kaybedildi' => 'Kaybedildi',
            'iptal' => 'İptal',
        ];
    }
}

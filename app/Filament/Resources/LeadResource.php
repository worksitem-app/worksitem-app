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
    protected static ?string $navigationLabel = 'Satış Talepleri';
    protected static ?string $modelLabel = 'Satış Talebi';
    protected static ?string $pluralModelLabel = 'Satış Talepleri';
    protected static ?string $navigationGroup = 'Müşteri Yönetimi (CRM)';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Lead Özeti')
                ->description('Worksitem tarafından hesaplanan satış önceliği')
                ->schema([
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
                        $record->phone ?: 'Telefon yok'
                    )
                    ->searchable(['name', 'phone', 'phone_normalized', 'email'])
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
                    ->money('TRY', divideBy: 1)
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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Geliş Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderByDesc('sales_ready')
                    ->orderByDesc('lead_score')
                    ->orderByDesc('created_at');
            })
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()
            ->where('sales_ready', true)
            ->where('status', 'yeni')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::query()
            ->where('sales_ready', true)
            ->where('status', 'yeni')
            ->exists()
                ? 'danger'
                : 'gray';
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

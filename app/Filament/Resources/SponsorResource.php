<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorResource\Pages;
use App\Models\Sponsor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SponsorResource extends Resource
{
    protected static ?string $model = Sponsor::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Gelir';
    protected static ?string $modelLabel = 'Sponsor';
    protected static ?string $pluralModelLabel = 'Sponsorlar';
    protected static ?int $navigationSort = 1;

    /** @return array<string, string> */
    private static function tools(): array
    {
        return collect((array) config('worksitem_sponsors.tools'))->map(fn ($t) => $t['label'])->all();
    }

    /** @return array<string, string> */
    private static function categories(?string $tool = null): array
    {
        $tools = (array) config('worksitem_sponsors.tools');
        if ($tool && isset($tools[$tool])) {
            return $tools[$tool]['categories'];
        }

        return collect($tools)->flatMap(fn ($t) => $t['categories'])->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Reklam')
                ->schema([
                    Forms\Components\TextInput::make('brand')->label('Marka')->required()->maxLength(120),
                    Forms\Components\Select::make('tool_slug')->label('Araç')->options(self::tools())->required()->live()->native(false),
                    Forms\Components\Select::make('category')->label('Kategori / kalem')
                        ->options(fn (Forms\Get $get): array => self::categories($get('tool_slug')))
                        ->default('genel')->required()->native(false),
                    Forms\Components\TextInput::make('headline')->label('Başlık')->required()->maxLength(120)
                        ->helperText('Örn: Banyonuz için 60×120 porselen koleksiyonu'),
                    Forms\Components\Textarea::make('body')->label('Kısa açıklama')->maxLength(240)->rows(2),
                    Forms\Components\TextInput::make('cta_label')->label('Düğme metni')->default('İncele')->maxLength(40),
                    Forms\Components\TextInput::make('target_url')->label('Hedef adres')->url()->required()->maxLength(500)
                        ->helperText('UTM parametreleri otomatik eklenir.'),
                    Forms\Components\TextInput::make('logo_url')->label('Logo adresi (https)')->url()->maxLength(500),
                    Forms\Components\TextInput::make('tech_sheet_url')->label('Teknik föy adresi (https)')->url()->maxLength(500),
                    Forms\Components\DatePicker::make('tech_sheet_checked_on')->label('Föyle karşılaştırma tarihi')
                        ->helperText('Yalnız sarfiyat oranlarımız bu föyle tek tek karşılaştırıldıysa girin. Girilirse Pro raporda "teknik föyüne göre kontrol edilmiştir" ibaresi çıkar.'),
                ])->columns(2),
            Forms\Components\Section::make('Yayın')
                ->schema([
                    Forms\Components\Toggle::make('active')->label('Yayında')->default(true),
                    Forms\Components\TextInput::make('priority')->label('Öncelik (küçük olan önce)')->numeric()->default(10),
                    Forms\Components\DatePicker::make('starts_on')->label('Başlangıç'),
                    Forms\Components\DatePicker::make('ends_on')->label('Bitiş'),
                    Forms\Components\TextInput::make('contract_amount')->label('Anlaşma bedeli (TL, KDV hariç)')->numeric(),
                    Forms\Components\TextInput::make('contact')->label('Marka yetkilisi')->maxLength(200),
                    Forms\Components\Textarea::make('notes')->label('Notlar')->rows(2)->columnSpanFull(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand')->label('Marka')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('headline')->label('Başlık')->limit(40),
                Tables\Columns\TextColumn::make('tool_slug')->label('Araç')->formatStateUsing(fn ($state) => self::tools()[$state] ?? $state)->toggleable(),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->formatStateUsing(fn ($state) => self::categories()[$state] ?? $state)->badge(),
                Tables\Columns\IconColumn::make('active')->label('Yayında')->boolean(),
                Tables\Columns\TextColumn::make('ends_on')->label('Bitiş')->date('d.m.Y')->placeholder('Süresiz'),
                Tables\Columns\TextColumn::make('impressions')->label('Gösterim')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('clicks')->label('Tıklama')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('ctr')->label('TO %')->state(fn (Sponsor $r) => number_format($r->ctr(), 2, ',', '.')),
                Tables\Columns\TextColumn::make('contract_amount')->label('Bedel')->money('TRY')->toggleable(),
            ])
            ->defaultSort('priority')
            ->filters([
                Tables\Filters\TernaryFilter::make('active')->label('Yayında'),
                Tables\Filters\SelectFilter::make('tool_slug')->label('Araç')->options(self::tools()),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSponsors::route('/'),
            'create' => Pages\CreateSponsor::route('/create'),
            'edit' => Pages\EditSponsor::route('/{record}/edit'),
        ];
    }
}

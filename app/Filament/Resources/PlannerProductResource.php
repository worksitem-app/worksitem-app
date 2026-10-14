<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlannerProductResource\Pages;
use App\Models\PlannerProduct;
use App\Models\Sponsor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Banyo planlayıcıdaki sponsor ürünleri (lavabo, klozet, duşakabin…) */
class PlannerProductResource extends Resource
{
    protected static ?string $model = PlannerProduct::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Gelir';
    protected static ?string $modelLabel = 'Planlayıcı ürünü';
    protected static ?string $pluralModelLabel = 'Planlayıcı ürünleri';
    protected static ?int $navigationSort = 2;

    /** @return array<string, string> */
    public static function types(): array
    {
        return collect((array) config('worksitem_planner.types'))
            ->filter(fn ($t, $k) => ! in_array($k, ['icduvar', 'engel'], true))
            ->map(fn ($t) => $t['label'])->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ürün')->schema([
                Forms\Components\Select::make('type')->label('Parça türü')->options(self::types())->required()->native(false),
                Forms\Components\TextInput::make('name')->label('Ürün adı')->required()->maxLength(150)->helperText('Örn: Dolaplı lavabo 80 cm, beyaz'),
                Forms\Components\TextInput::make('brand')->label('Marka (planlayıcıda etiket)')->required()->maxLength(120),
                Forms\Components\Select::make('sponsor_id')->label('Sponsor anlaşması')->options(fn () => Sponsor::query()->orderBy('brand')->pluck('brand', 'id'))->searchable()->native(false)
                    ->helperText('Bağlı sponsorun bitiş tarihi geçince ürün yine görünür; yayından kaldırmak için "Yayında"yı kapatın.'),
                Forms\Components\TextInput::make('sku')->label('Stok kodu')->maxLength(80),
                Forms\Components\TextInput::make('product_url')->label('Ürün sayfası (https)')->url()->maxLength(500)->helperText('Tıklamalar sayılır, UTM eklenir.'),
            ])->columns(2),
            Forms\Components\Section::make('Görsel ve 3D model')->schema([
                Forms\Components\FileUpload::make('image_path')->label('Ürün görseli (JPG/PNG/WebP, en çok 4 MB)')
                    ->disk('public')->directory('planner/images')->image()->maxSize(4096),
                Forms\Components\TextInput::make('image_url')->label('… ya da görsel adresi (https)')->url()->maxLength(500),
                Forms\Components\FileUpload::make('model_path')->label('3D model (GLB, en çok 10 MB)')
                    ->disk('public')->directory('planner/models')->maxSize(10240)
                    ->acceptedFileTypes(['model/gltf-binary', 'application/octet-stream', 'application/gltf-buffer'])
                    ->helperText('Gerçek ölçekte, ön yüz +Z. Planlayıcı modeli ürünün genişlik/derinlik/yüksekliğine oturtur; model yoksa sade şekil çizilir.'),
            ])->columns(3),
            Forms\Components\Section::make('Ölçü (cm)')->schema([
                Forms\Components\TextInput::make('width')->label('Genişlik')->numeric()->required()->minValue(5)->maxValue(400),
                Forms\Components\TextInput::make('depth')->label('Derinlik')->numeric()->required()->minValue(5)->maxValue(300),
                Forms\Components\TextInput::make('height')->label('Yükseklik')->numeric()->required()->minValue(5)->maxValue(300),
                Forms\Components\TextInput::make('width_min')->label('Genişlik en az')->numeric()->helperText('Boş: ölçü sabit'),
                Forms\Components\TextInput::make('width_max')->label('Genişlik en çok')->numeric(),
                Forms\Components\TextInput::make('depth_min')->label('Derinlik en az')->numeric(),
                Forms\Components\TextInput::make('depth_max')->label('Derinlik en çok')->numeric(),
                Forms\Components\ColorPicker::make('color')->label('Renk')->default('#FFFFFF'),
                Forms\Components\ColorPicker::make('cabinet_color')->label('Dolap rengi (dolaplı lavabo)'),
                Forms\Components\Toggle::make('tank')->label('Rezervuarlı (klozet)'),
            ])->columns(4),
            Forms\Components\Section::make('Renk / model seçenekleri')->description('Kullanıcı planlayıcıda bu ürünün rengini veya modelini seçebilir. Boş bırakılan alan ürünün kendi değerini kullanır.')->schema([
                Forms\Components\Repeater::make('variants')->label('')->schema([
                    Forms\Components\TextInput::make('name')->label('Seçenek adı')->required()->maxLength(60)->placeholder('Örn: Mat siyah'),
                    Forms\Components\ColorPicker::make('color')->label('Renk'),
                    Forms\Components\ColorPicker::make('cabinet_color')->label('Dolap rengi'),
                    Forms\Components\TextInput::make('sku')->label('Stok kodu')->maxLength(80),
                    Forms\Components\TextInput::make('image_url')->label('Görsel (https)')->url()->maxLength(500),
                    Forms\Components\TextInput::make('price_min')->label('Fiyat en az')->numeric(),
                    Forms\Components\TextInput::make('price_max')->label('Fiyat en çok')->numeric(),
                ])->columns(4)->addActionLabel('Seçenek ekle')->maxItems(12)->collapsible()->defaultItems(0),
            ])->collapsed(),
            Forms\Components\Section::make('Fiyat ve yayın')->schema([
                Forms\Components\TextInput::make('price_min')->label('Fiyat en az (TL, KDV dahil)')->numeric(),
                Forms\Components\TextInput::make('price_max')->label('Fiyat en çok (TL)')->numeric(),
                Forms\Components\TextInput::make('priority')->label('Sıra (küçük önce)')->numeric()->default(10),
                Forms\Components\Toggle::make('active')->label('Yayında')->default(true),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->label('Tür')->formatStateUsing(fn ($state) => self::types()[$state] ?? $state)->badge(),
                Tables\Columns\TextColumn::make('brand')->label('Marka')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('name')->label('Ürün')->searchable()->limit(40),
                Tables\Columns\IconColumn::make('model_path')->label('3D')->boolean()->getStateUsing(fn (PlannerProduct $r) => (bool) $r->model_path),
                Tables\Columns\TextColumn::make('size')->label('Ölçü')->state(fn (PlannerProduct $r) => "{$r->width}×{$r->depth}×{$r->height}"),
                Tables\Columns\TextColumn::make('price_min')->label('Fiyat')->state(fn (PlannerProduct $r) => $r->price_min ? number_format($r->price_min, 0, ',', '.').' – '.number_format($r->price_max ?: $r->price_min, 0, ',', '.').' ₺' : '—'),
                Tables\Columns\IconColumn::make('active')->label('Yayında')->boolean(),
                Tables\Columns\TextColumn::make('impressions')->label('Gösterim')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('clicks')->label('Tıklama')->numeric()->sortable(),
            ])
            ->defaultSort('priority')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Tür')->options(self::types()),
                Tables\Filters\TernaryFilter::make('active')->label('Yayında'),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlannerProducts::route('/'),
            'create' => Pages\CreatePlannerProduct::route('/create'),
            'edit' => Pages\EditPlannerProduct::route('/{record}/edit'),
        ];
    }
}

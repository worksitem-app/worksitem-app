<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorApplicationResource\Pages;
use App\Models\Sponsor;
use App\Models\SponsorApplication;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

/** Sponsor firma kayıtları (ön başvuru): durum takibi, logo, taslak sponsor kartı */
class SponsorApplicationResource extends Resource
{
    protected static ?string $model = SponsorApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Gelir';
    protected static ?string $navigationLabel = 'Sponsor kayıtları';
    protected static ?string $modelLabel = 'Sponsor kaydı';
    protected static ?string $pluralModelLabel = 'Sponsor kayıtları';

    public static function getNavigationBadge(): ?string
    {
        $n = SponsorApplication::query()->where('status', 'new')->count();

        return $n ? (string) $n : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        $cats = SponsorApplication::categoryOptions();

        return $table->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('company')->label('Marka')->searchable()->description(fn (SponsorApplication $r) => $r->public_id.' · '.$r->created_at->timezone('Europe/Istanbul')->format('d.m.Y')),
                Tables\Columns\TextColumn::make('contact_name')->label('Yetkili')->searchable()->description(fn (SponsorApplication $r) => trim(($r->contact_title ? $r->contact_title.' · ' : '').$r->phone.' · '.$r->email)),
                Tables\Columns\TextColumn::make('categories')->label('Ürün grupları')->wrap()
                    ->formatStateUsing(fn ($state) => collect((array) $state)->map(fn ($k) => $cats[$k] ?? $k)->implode(', ')),
                Tables\Columns\TextColumn::make('budget')->label('Bütçe')->formatStateUsing(fn ($state) => SponsorApplication::BUDGETS[$state] ?? '—'),
                Tables\Columns\TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn (string $state) => SponsorApplication::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => ['new' => 'warning', 'signed' => 'success', 'rejected' => 'danger'][$state] ?? 'gray'),
            ])
            ->filters([Tables\Filters\SelectFilter::make('status')->label('Durum')->options(SponsorApplication::STATUSES)])
            ->actions([
                Tables\Actions\Action::make('status')->label('Durum')->icon('heroicon-o-pencil-square')
                    ->form([
                        Forms\Components\Select::make('status')->label('Durum')->options(SponsorApplication::STATUSES)->required(),
                        Forms\Components\Textarea::make('admin_note')->label('Not')->rows(3),
                    ])
                    ->fillForm(fn (SponsorApplication $r) => ['status' => $r->status, 'admin_note' => $r->admin_note])
                    ->action(fn (SponsorApplication $r, array $data) => $r->update($data)),
                Tables\Actions\Action::make('logo')->label('Logo')->icon('heroicon-o-photo')->color('gray')
                    ->visible(fn (SponsorApplication $r) => $r->logo_path && Storage::disk('local')->exists($r->logo_path))
                    ->action(fn (SponsorApplication $r) => Storage::disk('local')->download($r->logo_path, \Illuminate\Support\Str::slug($r->company).'-logo.'.pathinfo($r->logo_path, PATHINFO_EXTENSION))),
                Tables\Actions\Action::make('draft')->label('Taslak sponsor kartı')->icon('heroicon-o-document-plus')->color('gray')
                    ->requiresConfirmation()->modalDescription('Pasif (yayında olmayan) bir sponsor kartı oluşturulur. Sözleşme imzalanınca Sponsorlar ekranından açın.')
                    ->action(function (SponsorApplication $r) {
                        $cat = ((array) $r->categories)[0] ?? 'genel';
                        $tool = ((array) $r->tools)[0]
                            ?? collect((array) config('worksitem_sponsors.tools'))->search(fn ($t) => isset($t['categories'][$cat]))
                            ?: 'banyo-maliyet-hesaplayici';
                        Sponsor::query()->create([
                            'brand' => $r->company, 'headline' => $r->company, 'target_url' => $r->website ?: 'https://worksitem.com',
                            'tool_slug' => $tool, 'category' => $cat, 'active' => false,
                            'contact' => $r->contact_name.' · '.$r->phone.' · '.$r->email, 'notes' => 'Sponsor kaydı '.$r->public_id,
                        ]);
                        $r->update(['status' => $r->status === 'new' ? 'contacted' : $r->status]);
                        Notification::make()->title('Pasif sponsor kartı oluşturuldu')->success()->send();
                    }),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSponsorApplications::route('/')];
    }
}

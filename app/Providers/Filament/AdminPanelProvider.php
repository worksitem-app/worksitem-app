<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->brandName(app()->environment('staging') ? 'Worksitem Staging' : 'Worksitem')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('brand/favicon.ico'))
            ->font('Inter')
            ->colors([
                'primary' => Color::hex('#FFB800'),
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
                'info' => Color::Sky,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->renderHook('panels::head.end', fn () => view('filament.theme'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Teklifler (CRM)'),
                NavigationGroup::make('Gelir'),
                NavigationGroup::make('Fiyat Merkezi'),
                NavigationGroup::make('Analitik'),
                NavigationGroup::make('Sistem Yönetimi'),
            ])
            ->navigationItems([
                NavigationItem::make('Rayiç PDF içe aktar')->url(fn () => url('/admin/rayic'))->icon('heroicon-o-document-arrow-up')->group('Fiyat Merkezi')->sort(10),
                NavigationItem::make('AI fiyat güncelle')->url(fn () => url('/admin/ai-fiyat-guncelle'))->icon('heroicon-o-bolt')->group('Fiyat Merkezi')->sort(11),
                NavigationItem::make('Poz CSV içe aktar')->url(fn () => url('/admin/poz-import'))->icon('heroicon-o-arrow-up-tray')->group('Sistem Yönetimi')->sort(10),
                NavigationItem::make('Markalar için sayfası ↗')->url(fn () => url('/markalar-icin'), shouldOpenInNewTab: true)->icon('heroicon-o-building-storefront')->group('Gelir')->sort(10),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
            ->userMenuItems([                
                /*Pages\Account\AccountPage::class => [
                    'label' => 'Mi Cuenta',
                    'icon' => 'heroicon-o-user',
                ],
                Pages\Auth\Logout::class => [
                    'label' => 'Cerrar Sesión',
                    'icon' => 'heroicon-o-arrow-right-on-rectangle',
                ],*/
            ])
            ->colors([
                'primary' => Color::hex('#3b82f6'),
                'secondary' => Color::hex('#fbbf24'),
                'success' => Color::hex('#10b981'),
                'danger' => Color::hex('#ef4444'),
                'warning' => Color::hex('#f59e0b'),
                'info' => Color::hex('#3b82f6'),
                'gray' => Color::hex('#6b7280'),
                'dark' => Color::hex('#111827'),
                'light' => Color::hex('#f3f4f6'),
            ])
            ->renderHook(
                'panels::body.start',
                fn (): string => '<style>
                    aside.fi-sidebar {
                        width: 16rem !important;
                    }
                </style>'
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
                \App\Filament\Widgets\SellerStats::class,
            ])
            /*->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ])*/
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

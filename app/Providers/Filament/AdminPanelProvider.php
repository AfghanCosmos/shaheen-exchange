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
use Afsakar\FilamentOtpLogin\FilamentOtpLoginPlugin;
use Rupadana\ApiService\ApiServicePlugin;
use Rupadana\ApiService\ApiServicePlugin as NewApiServicePlugin;
use Awcodes\FilamentQuickCreate\QuickCreatePlugin;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->plugins(
                [
                    // \Hasnayeen\Themes\ThemesPlugin::make(),
                    FilamentOtpLoginPlugin::make(),
                    // ApiServicePlugin::make(),
                    // NewApiServicePlugin::make(),
                    \EightyNine\Approvals\ApprovalPlugin::make(),
                    QuickCreatePlugin::make()
                    ->sortBy('navigation')
                    ->hiddenIcons(),
                    \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),

                ]
            )
            ->navigationGroups([


                NavigationGroup::make()
                ->label(fn(): string => __('Hawla Management'))
                ->icon('heroicon-o-archive-box')
                ->collapsed(),


                NavigationGroup::make()
                ->label(fn(): string => __('Store Management'))
                ->icon('heroicon-o-building-storefront')
                ->collapsed(),



                NavigationGroup::make()
                ->label(fn(): string => __('Customer Management'))
                ->icon('heroicon-o-user-group')
                ->collapsed(),


            NavigationGroup::make()
                ->label(fn(): string => __('Transaction'))
                ->icon('heroicon-o-cube-transparent')

                ->collapsed(),


                NavigationGroup::make()
                ->label(fn(): string => __('Finance Management'))
                ->icon('heroicon-o-wallet')
                ->collapsed(),


                NavigationGroup::make()
                    ->label(fn(): string => __('Human Resources'))
                    ->icon('heroicon-o-users')
                    ->collapsed(),

                    NavigationGroup::make()
                    ->label(fn(): string => __('Finance'))
                    ->icon('heroicon-o-banknotes')
                    ->collapsed(),



                NavigationGroup::make()
                ->label(fn(): string => __('Settings'))
                ->icon('heroicon-o-cog-6-tooth')
                ->collapsed(),

                NavigationGroup::make()
                    ->label(fn(): string => __('Configuration'))
                    // ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),


            ])
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
                // \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            ->databaseNotifications()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

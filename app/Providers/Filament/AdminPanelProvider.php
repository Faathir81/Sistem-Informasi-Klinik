<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ApotekAlertWidget;
use App\Filament\Widgets\ClinicOverviewWidget;
use App\Filament\Widgets\DailyVisitsChart;
use App\Filament\Widgets\MonthlyRevenueChart;
use App\Filament\Widgets\TopMedicineChart;
use App\Http\Middleware\AuthenticateAdminPanel;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
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
        Table::configureUsing(fn (Table $table): Table => $table
            ->striped()
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->persistFiltersInSession()
            ->persistSearchInSession());

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Klinik Ar-Ridlo')
            ->font('Figtree')
            ->colors([
                'primary' => Color::hex('#14342f'),
                'secondary' => Color::hex('#7ba891'),
                'warning' => Color::hex('#ef7b2d'),
                'success' => Color::Emerald,
                'gray' => Color::Slate,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.admin-styles'),
            )
            ->navigationGroups([
                NavigationGroup::make('Operasional')
                    ->icon(Heroicon::OutlinedBolt),
                NavigationGroup::make('Pasien')
                    ->icon(Heroicon::OutlinedUsers),
                NavigationGroup::make('Jadwal & SDM')
                    ->icon(Heroicon::OutlinedCalendarDays),
                NavigationGroup::make('Pelayanan Medis')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck),
                NavigationGroup::make('Apotek')
                    ->icon(Heroicon::OutlinedBeaker),
                NavigationGroup::make('Keuangan')
                    ->icon(Heroicon::OutlinedBanknotes),
                NavigationGroup::make('Laporan')
                    ->icon(Heroicon::OutlinedDocumentChartBar),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                ClinicOverviewWidget::class,
                ApotekAlertWidget::class,
                MonthlyRevenueChart::class,
                DailyVisitsChart::class,
                TopMedicineChart::class,
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
            ])
            ->authMiddleware([
                AuthenticateAdminPanel::class,
            ]);
    }
}

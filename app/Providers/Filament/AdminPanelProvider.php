<?php

namespace App\Providers\Filament;


use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use App\Helpers\ThemeHelper;
use App\Filament\Pages\ViewEnv;
use App\Models\ProfilPerusahaan;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\EditProfile;
use \App\Http\Middleware\CheckStatusUser;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use App\Filament\Widgets\Admin\TotalUsersWidget;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Widgets\Admin\StorageUsageChart;
use App\Filament\Widgets\Admin\UsersByRoleWidget;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Widgets\Admin\FeatureTooglesWidget;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use App\Filament\Widgets\Director\ContentGrowthTrend;
use GeoSot\FilamentEnvEditor\FilamentEnvEditorPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Widgets\Admin\RemainingStorageWidget;
use App\Filament\Widgets\Director\ContentManagerStats;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\Director\CustomerServiceStats;
use App\Filament\Widgets\Director\CustomerServiceGrowth;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Filament\Widgets\Admin\StorageUsageByFeatureChart;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Filament\Widgets\ContentManager\Produk\TopProducts;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Widgets\ContentManager\Unduhan\TopDownloads;
use App\Filament\Widgets\Director\ContentGrowthPerFiturTrend;
use App\Filament\Widgets\ContentManager\Event\EventStatusChart;
use App\Filament\Widgets\ContentManager\Event\EventTrendsChart;
use App\Filament\Widgets\Director\CustomerServiceActivityTrend;
use App\Filament\Widgets\ContentManager\Galeri\GaleriStatusChart;
use App\Filament\Widgets\ContentManager\Galeri\GaleriTrendsChart;
use App\Filament\Widgets\ContentManager\Event\UpcomingEventsChart;
use App\Filament\Widgets\ContentManager\Artikel\TopArticlesByViews;
use App\Filament\Widgets\ContentManager\Artikel\TrendArticlesChart;
use App\Filament\Widgets\ContentManager\Event\TopEventsStatsWidget;
use App\Filament\Widgets\ContentManager\General\ContentCountsChart;
use App\Filament\Widgets\ContentManager\General\ContentTrendsChart;
use App\Filament\Widgets\ContentManager\Unduhan\UnduhanStatusChart;
use App\Filament\Widgets\CustomerServices\Lamaran\LamaranStatsCard;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;
use App\Filament\Widgets\ContentManager\Galeri\GaleriDownloadsChart;
use App\Filament\Widgets\CustomerServices\Lamaran\LamaranTrendChart;
use App\Filament\Widgets\ContentManager\Produk\ProductsByStatusChart;
use App\Filament\Widgets\CustomerServices\Feedback\FeedbackStatsCard;
use App\Filament\Widgets\CustomerServices\Lowongan\LowonganStatsCard;
use App\Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use App\Filament\Widgets\ContentManager\Galeri\TopGaleriesStatsWidget;
use App\Filament\Widgets\CustomerServices\Feedback\FeedbackTrendChart;
use App\Filament\Widgets\CustomerServices\Lowongan\LowonganTrendChart;
use App\Filament\Widgets\ContentManager\CaseStudy\CaseStudyStatusChart;
use App\Filament\Widgets\ContentManager\Unduhan\DocumentDownloadsChart;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->spa()
            ->topNavigation()
            ->brandName(function () {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('profil_perusahaan')) {
                        $company = ProfilPerusahaan::first();
                        return $company ? $company->nama_perusahaan : 'Admin Panel';
                    }
                } catch (\Exception $e) {
                }
                return 'Admin Panel';
            })
            ->favicon(function () {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('profil_perusahaan')) {
                        $company = ProfilPerusahaan::first();
                        if ($company && $company->logo_perusahaan) {
                            return asset('storage/' . $company->logo_perusahaan);
                        }
                    }
                } catch (\Exception $e) {
                }
                return asset('favicon.ico');
            })
            ->globalSearch(false)
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->unsavedChangesAlerts()
            ->globalSearch(false)
            ->font('Plus jakarta Sans')
            ->registration(Register::class)
            ->emailVerification(EmailVerificationPrompt::class)
            ->colors([
                'primary' => ThemeHelper::getFilamentTheme()['primary'],
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                    // Admin widgets
                TotalUsersWidget::class,
                UsersByRoleWidget::class,
                StorageUsageChart::class,
                RemainingStorageWidget::class,
                StorageUsageByFeatureChart::class,
                FeatureTooglesWidget::class,

                    // Content Manager widgets
                ContentCountsChart::class,
                ContentTrendsChart::class,

                TopArticlesByViews::class,
                TrendArticlesChart::class,

                CaseStudyStatusChart::class,

                TopEventsStatsWidget::class,
                EventTrendsChart::class,
                UpcomingEventsChart::class,
                EventStatusChart::class,


                TopGaleriesStatsWidget::class,
                GaleriTrendsChart::class,
                GaleriDownloadsChart::class,
                GaleriStatusChart::class,

                TopProducts::class,
                ProductsByStatusChart::class,


                TopDownloads::class,
                DocumentDownloadsChart::class,
                UnduhanStatusChart::class,

                    // Customer manager widgets
                FeedbackStatsCard::class,
                FeedbackTrendChart::class,
                LamaranStatsCard::class,
                LamaranTrendChart::class,
                LowonganStatsCard::class,
                LowonganTrendChart::class,


                    // Director widgets
                ContentManagerStats::class,
                ContentGrowthTrend::class,
                ContentGrowthPerFiturTrend::class,
                CustomerServiceStats::class,
                CustomerServiceActivityTrend::class,
                CustomerServiceGrowth::class,
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
                Authenticate::class,
                CheckStatusUser::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                AuthUIEnhancerPlugin::make()
                    ->formPanelPosition('right')
                    ->mobileFormPanelPosition('bottom')
                    ->emptyPanelBackgroundImageUrl('https://images.pexels.com/photos/466685/pexels-photo-466685.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2')
                    ->formPanelWidth('40%'),
                FilamentApexChartsPlugin::make(),
                FilamentEnvEditorPlugin::make()
                    ->navigationGroup('System')
                    ->navigationLabel('Environment Editor')
                    ->navigationIcon('heroicon-o-cog')
                    ->hideKeys('APP_NAME')
                    ->viewPage(ViewEnv::class)
                    ->authorize(
                        fn() => Auth::check() && Auth::user()->hasPermissionTo('page_ViewEnv')
                    ),
                EnvironmentIndicatorPlugin::make(),
            ])
            ->userMenuItems([
                'heroicon-o-home' => MenuItem::make()
                    ->icon('heroicon-s-power')
                    ->label('Keluar dashboard')
                    ->url('/'),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}

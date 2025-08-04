<?php

namespace App\Filament\Widgets\Admin;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalUsersWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 'sm';
    protected static ?string $pollingInterval = '300s'; 
    protected static string $view = 'filament.widgets.total-users-stats';


    public static function canView(): bool
    {
        return auth()->user()?->can('widget_TotalUsersWidget');
    }
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'aktif')->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        return [
            Stat::make('Total user', $totalUsers)
                ->description('Total seluruh user')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'primary-stat'
                ]),

            Stat::make('User Aktif', $activeUsers)
                ->description('User dengan status aktif')
                ->descriptionIcon(icon: 'heroicon-m-user-group')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'primary-stat'
                ]),

            Stat::make('User Baru', $newUsersThisMonth)
                ->description('Bergabung bulan ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'primary-stat'
                ]),
        ];
    }

}

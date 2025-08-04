<?php

namespace App\Filament\Widgets\Admin;

use App\Models\FeatureToggle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FeatureTooglesWidget extends BaseWidget
{
    protected ?string $heading = 'Status Fitur';
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_FeatureTooglesWidget');
    }

    protected function getStats(): array
    {
        $enabled = FeatureToggle::where('status_aktif', true)->count();
        $disabled = FeatureToggle::where('status_aktif', false)->count();
        $total = $enabled + $disabled;

        $enabledPct = $total > 0 ? round(($enabled / $total) * 100, 1) : 0;
        $disabledPct = $total > 0 ? round(($disabled / $total) * 100, 1) : 0;

        return [
            Stat::make('Total Fitur', $total)
                ->description('Total semua fitur')
                ->descriptionIcon('heroicon-o-cog-6-tooth')
                ->color('primary')
                ->chart([$total, $total, $total, $total, $total, $total, $total]),

            Stat::make('Fitur Aktif', $enabled)
                ->description($enabledPct . '% dari total fitur')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([$enabled, $enabled, $enabled, $enabled, $enabled, $enabled, $enabled]),

            Stat::make('Fitur Nonaktif', $disabled)
                ->description($disabledPct . '% dari total fitur')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->chart([$disabled, $disabled, $disabled, $disabled, $disabled, $disabled, $disabled]),
        ];
    }

}

<?php

namespace App\Filament\Widgets\CustomerServices\Feedback;

use App\Models\Feedback;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FeedbackStatsCard extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Feedback';
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_FeedbackStatsCard');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Feedback', Feedback::query()->count())
                ->description('Jumlah keseluruhan feedback')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('Belum Ditanggapi', Feedback::query()->whereNull('tanggapan_feedback')->count())
                ->description('Total feedback yang belum ditanggapi')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make('Sudah Ditanggapi', Feedback::query()->whereNotNull('tanggapan_feedback')->count())
                ->description('Total feedback yang sudah ditanggapi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}

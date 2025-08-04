<?php

namespace App\Filament\Resources\FeedbackResource\Widgets;

use App\Models\Feedback;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class FeedbackStats extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Feedback', Feedback::query()->count())
                ->description('Total semua feedback yang masuk')
                ->descriptionIcon('heroicon-s-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('Sudah Ditanggapi', Feedback::query()
                ->whereNotNull('tanggapan_feedback')
                ->count())
                ->description('Feedback yang sudah mendapat tanggapan')
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success'),

            Stat::make('Belum Ditanggapi', Feedback::query()
                ->whereNull('tanggapan_feedback')
                ->count())
                ->description('Feedback yang masih menunggu tanggapan')
                ->descriptionIcon('heroicon-s-x-circle')
                ->color('warning'),

            Stat::make('Feedback Bulan Ini', Feedback::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count())
                ->description('Feedback yang diterima bulan ini')
                ->descriptionIcon('heroicon-s-calendar-date-range')
                ->color('info'),
        ];
    }
}

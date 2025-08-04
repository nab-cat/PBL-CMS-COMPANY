<?php

namespace App\Filament\Widgets\Director;

use App\Models\Feedback;
use App\Models\TestimoniSlider;
use App\Models\Lowongan;
use App\Models\Lamaran;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerServiceStats extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Customer Service';
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_CustomerServiceStats');
    }

    protected function getStats(): array
    {
        // Tanggal untuk perhitungan
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Total data baru dari sisi admin (lowongan) bulan ini
        $newAdminDataThisMonth = Lowongan::whereMonth('created_at', $thisMonth->month)
            ->whereYear('created_at', $thisMonth->year)
            ->count();

        // Total data yang diterima dari user (lamaran, feedback, testimoni) bulan ini
        $receivedUserDataThisMonth = Lamaran::whereMonth('created_at', $thisMonth->month)
            ->whereYear('created_at', $thisMonth->year)
            ->count() +
            Feedback::whereMonth('created_at', $thisMonth->month)
                ->whereYear('created_at', $thisMonth->year)
                ->count() +
            TestimoniSlider::whereMonth('created_at', $thisMonth->month)
                ->whereYear('created_at', $thisMonth->year)
                ->count();

        // Data yang diterima bulan lalu untuk perhitungan pertumbuhan
        $receivedUserDataLastMonth = Lamaran::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count() +
            Feedback::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count() +
            TestimoniSlider::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count();

        // Hitung persentase pertumbuhan berdasarkan data yang diterima
        $growthPercentage = 0;
        if ($receivedUserDataLastMonth > 0) {
            $growthPercentage = (($receivedUserDataThisMonth - $receivedUserDataLastMonth) / $receivedUserDataLastMonth) * 100;
        }

        // Format persentase dengan tanda plus atau minus
        $growthText = ($growthPercentage >= 0 ? '+' : '') . number_format($growthPercentage, 1) . '%';
        $growthColor = $growthPercentage >= 0 ? 'success' : 'danger';

        // Ambil data tren 6 bulan terakhir untuk charts
        $adminDataTrend = $this->getMonthlyTrend(Lowongan::class, 6);

        $lamaranTrend = $this->getMonthlyTrend(Lamaran::class, 6);
        $feedbackTrend = $this->getMonthlyTrend(Feedback::class, 6);
        $testimoniTrend = $this->getMonthlyTrend(TestimoniSlider::class, 6);

        // Gabungkan data user untuk chart kedua
        $userDataTrend = [];
        for ($i = 0; $i < 6; $i++) {
            $userDataTrend[$i] = ($lamaranTrend[$i] ?? 0) + ($feedbackTrend[$i] ?? 0) + ($testimoniTrend[$i] ?? 0);
        }

        // Buat data tren untuk growth chart
        $growthTrend = [];
        for ($i = 1; $i < 6; $i++) {
            $prev = $userDataTrend[$i - 1] ?: 1; // Hindari division by zero
            $current = $userDataTrend[$i];
            $growth = (($current - $prev) / $prev) * 100;
            $growthTrend[] = max(min($growth, 100), -100);
        }
        // Tambahkan nilai awal untuk chart yang lengkap
        array_unshift($growthTrend, 0);

        return [
            Stat::make('Data Lowongan', $newAdminDataThisMonth)
                ->description('Lowongan kerja yang dibuat bulan ini')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary')
                ->chart($adminDataTrend),

            Stat::make('Data Diterima bulan ini', $receivedUserDataThisMonth)
                ->description('Feedback, lamaran, dan testimoni')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color('success')
                ->chart($userDataTrend),

            Stat::make('Arus Data Pelanggan Bulan Ini', $growthText)
                ->description('Berdasarkan data yang diterima')
                ->descriptionIcon('heroicon-m-arrow-trending-' . ($growthPercentage >= 0 ? 'up' : 'down'))
                ->color($growthColor)
                ->chart($growthTrend),
        ];
    }

    /**
     * Get monthly trend data for the last n months
     * 
     * @param string $model Fully qualified model class name
     * @param int $months Number of months to retrieve
     * @return array Array of monthly counts
     */
    protected function getMonthlyTrend(string $model, int $months = 6): array
    {
        $trend = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = $model::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $trend[] = $count;
        }

        return $trend;
    }
}

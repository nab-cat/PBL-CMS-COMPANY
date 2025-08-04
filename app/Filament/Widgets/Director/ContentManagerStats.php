<?php

namespace App\Filament\Widgets\Director;

use App\Models\Artikel;
use App\Models\CaseStudy;
use App\Models\Event;
use App\Models\Galeri;
use App\Models\Produk;
use App\Models\Unduhan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentManagerStats extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Pengelolaan Konten';
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_ContentManagerStats');
    }

    protected function getStats(): array
    {
        // Tanggal untuk perhitungan
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Total konten baru bulan ini (artikel, galeri, event, produk, case study, unduhan)
        $newContentThisMonth = Artikel::whereMonth('created_at', $thisMonth->month)
            ->whereYear('created_at', $thisMonth->year)
            ->count() +
            Galeri::whereMonth('created_at', $thisMonth->month)
                ->whereYear('created_at', $thisMonth->year)
                ->count() +
            Event::whereMonth('created_at', $thisMonth->month)
                ->whereYear('created_at', $thisMonth->year)
                ->count() +
            Produk::whereMonth('created_at', $thisMonth->month)
                ->whereYear('created_at', $thisMonth->year)
                ->count() +
            CaseStudy::whereMonth('created_at', $thisMonth->month)
                ->whereYear('created_at', $thisMonth->year)
                ->count() +
            Unduhan::whereMonth('created_at', $thisMonth->month)
                ->whereYear('created_at', $thisMonth->year)
                ->count();

        // Total konten keseluruhan
        $totalContent = Artikel::count() +
            Galeri::count() +
            Event::count() +
            Produk::count() +
            CaseStudy::count() +
            Unduhan::count();

        // Konten bulan lalu untuk perhitungan pertumbuhan
        $lastMonthContent = Artikel::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count() +
            Galeri::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count() +
            Event::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count() +
            Produk::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count() +
            CaseStudy::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count() +
            Unduhan::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count();

        // Hitung persentase pertumbuhan berdasarkan data konten
        $growthPercentage = 0;
        if ($lastMonthContent > 0) {
            $growthPercentage = (($newContentThisMonth - $lastMonthContent) / $lastMonthContent) * 100;
        }

        // Format persentase dengan tanda plus atau minus
        $growthText = ($growthPercentage >= 0 ? '+' : '') . number_format($growthPercentage, 1) . '%';
        $growthColor = $growthPercentage >= 0 ? 'success' : 'danger';

        // Ambil data tren 6 bulan terakhir untuk charts
        $artikelTrend = $this->getMonthlyTrend(Artikel::class, 6);
        $galeriTrend = $this->getMonthlyTrend(Galeri::class, 6);
        $eventTrend = $this->getMonthlyTrend(Event::class, 6);
        $produkTrend = $this->getMonthlyTrend(Produk::class, 6);
        $caseStudyTrend = $this->getMonthlyTrend(CaseStudy::class, 6);
        $unduhanTrend = $this->getMonthlyTrend(Unduhan::class, 6);

        // Gabungkan data konten untuk chart
        $contentTrend = [];
        for ($i = 0; $i < 6; $i++) {
            $contentTrend[$i] = ($artikelTrend[$i] ?? 0) + ($galeriTrend[$i] ?? 0) +
                ($eventTrend[$i] ?? 0) + ($produkTrend[$i] ?? 0) +
                ($caseStudyTrend[$i] ?? 0) + ($unduhanTrend[$i] ?? 0);
        }

        // Buat data tren untuk growth chart
        $growthTrend = [];
        for ($i = 1; $i < 6; $i++) {
            $prev = $contentTrend[$i - 1] ?: 1; // Hindari division by zero
            $current = $contentTrend[$i];
            $growth = (($current - $prev) / $prev) * 100;
            $growthTrend[] = max(min($growth, 100), -100); // Batasi range -100 sampai 100 untuk visualisasi
        }
        // Tambahkan nilai awal untuk chart yang lengkap
        array_unshift($growthTrend, 0);

        return [
            Stat::make('Konten Baru Bulan Ini', $newContentThisMonth)
                ->description('Artikel, galeri, event, dll')
                ->descriptionIcon('heroicon-m-document-plus')
                ->color('primary')
                ->chart($contentTrend),

            Stat::make('Total Konten', $totalContent)
                ->description('Keseluruhan konten tersedia')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('success')
                ->chart($contentTrend),

            Stat::make('Pertumbuhan Data Bulan Ini', $growthText)
                ->description('Dibanding bulan lalu')
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
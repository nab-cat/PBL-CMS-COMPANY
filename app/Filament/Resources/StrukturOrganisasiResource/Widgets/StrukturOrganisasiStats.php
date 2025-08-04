<?php

namespace App\Filament\Resources\StrukturOrganisasiResource\Widgets;

use App\Models\StrukturOrganisasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\StrukturOrganisasiResource\Pages\ListStrukturOrganisasis;

class StrukturOrganisasiStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListStrukturOrganisasis::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Pegawai Organisasi', StrukturOrganisasi::query()->count())
                ->description('Jumlah Pegawai dalam organisasi')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Pegawai Aktif', StrukturOrganisasi::query()
                ->whereHas('user', function ($query) {
                    $query->where('status', 'aktif')
                        ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang']);
                })
                ->where(function ($query) {
                    $query->whereNull('tanggal_selesai')
                        ->orWhere('tanggal_selesai', '>=', now());
                })
                ->count())
                ->description('Posisi dengan pengguna aktif')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Posisi Baru Bulan Ini', StrukturOrganisasi::query()
                ->whereMonth('tanggal_mulai', now()->month)
                ->whereYear('tanggal_mulai', now()->year)
                ->count())
                ->description('Posisi baru yang dibuat bulan ini')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('warning'),
        ];

    }
}

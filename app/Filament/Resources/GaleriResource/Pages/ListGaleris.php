<?php

namespace App\Filament\Resources\GaleriResource\Pages;

use Filament\Actions;
use App\Enums\ContentStatus;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\GaleriResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\GaleriResource\Widgets\GaleriStats;

class ListGaleris extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = GaleriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-photo')
                ->label('Buat Galeri Baru'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GaleriStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make()
            ->icon('heroicon-o-squares-2x2')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('judul_galeri', 'asc')),
            'Terpublikasi' => Tab::make()
                ->icon('heroicon-o-eye')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('status_galeri', ContentStatus::TERPUBLIKASI->value)
                    ->orderBy('judul_galeri', 'asc')),
            'Tidak Terpublikasi' => Tab::make()
                ->icon('heroicon-o-eye-slash')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('status_galeri', ContentStatus::TIDAK_TERPUBLIKASI->value)
                    ->orderBy('judul_galeri', 'asc')),
            'Terbaru' => Tab::make()
                ->icon('heroicon-o-clock')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')),
            'Diarsipkan' => Tab::make()
                ->icon('heroicon-o-archive-box')
                ->query(fn($query) => $query->onlyTrashed()),
        ];
    }
}

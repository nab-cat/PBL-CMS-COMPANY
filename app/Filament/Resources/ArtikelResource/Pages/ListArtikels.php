<?php

namespace App\Filament\Resources\ArtikelResource\Pages;

use Filament\Actions;
use App\Enums\ContentStatus;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ArtikelResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\ArtikelResource\Widgets\ArtikelStats;
use Filament\Support\View\Components\Modal;

class ListArtikels extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = ArtikelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Artikel')
            ->icon('heroicon-o-pencil-square'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ArtikelStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make()
            ->icon('heroicon-o-squares-2x2')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('judul_artikel', 'asc')),

            'Terpublikasi' => Tab::make()
            ->icon('heroicon-o-check-circle')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('status_artikel', ContentStatus::TERPUBLIKASI->value)
                    ->orderBy('created_at', 'desc')),

            'Tidak Terpublikasi' => Tab::make()
            ->icon('heroicon-o-x-circle')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('status_artikel', ContentStatus::TIDAK_TERPUBLIKASI->value)
                    ->orderBy('created_at', 'desc')),

            'Terbaru' => Tab::make()
            ->icon('heroicon-o-clock')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')),

            'Terlama' => Tab::make()
            ->icon('heroicon-o-clock')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('created_at', 'asc')),

            'Terpopuler' => Tab::make()
                ->icon('heroicon-o-fire')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('jumlah_view', 'desc')),

            'Trending' => Tab::make()
                ->icon('heroicon-o-arrow-trending-up')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('jumlah_view', 'desc')
                    ->where('created_at', '>=', now()->subDays(30))),

            'Diarsipkan' => Tab::make()
            ->icon('heroicon-o-archive-box')
                ->query(fn($query) => $query->onlyTrashed()),
        ];
    }
}

<?php

namespace App\Filament\Resources\UnduhanResource\Pages;

use App\Enums\ContentStatus;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\UnduhanResource;
use App\Filament\Resources\UnduhanResource\Widgets\UnduhanStats;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListUnduhans extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = UnduhanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Buat Berkas Unduhan Baru')
                ->color('primary'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UnduhanStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make()
                ->icon('heroicon-o-squares-2x2')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('nama_unduhan', 'asc')),

            'Terpublikasi' => Tab::make()
                ->icon('heroicon-o-eye')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('status_unduhan', ContentStatus::TERPUBLIKASI->value)
                    ->orderBy('created_at', 'desc')),

            'Tidak Terpublikasi' => Tab::make()
                ->icon('heroicon-o-eye-slash')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('status_unduhan', ContentStatus::TIDAK_TERPUBLIKASI->value)
                    ->orderBy('created_at', 'desc')),

            'Terbaru' => Tab::make()
                ->icon('heroicon-o-clock')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')),

            'Terpopuler' => Tab::make()
                ->icon('heroicon-o-fire')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('jumlah_unduhan', 'desc')),

            'Diarsipkan' => Tab::make()
                ->icon('heroicon-o-archive-box')
                ->query(fn($query) => $query->onlyTrashed()),
        ];
    }
}

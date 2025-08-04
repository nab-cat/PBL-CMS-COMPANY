<?php

namespace App\Filament\Resources\CaseStudyResource\Pages;

use Filament\Actions;
use App\Enums\ContentStatus;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CaseStudyResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\CaseStudyResource\Widgets\CaseStudyStats;

class ListCaseStudies extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = CaseStudyResource::class;

     protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Case Study')
            ->icon('heroicon-o-document-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CaseStudyStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('judul_case_study', 'asc')),
            'Terpublikasi' => Tab::make()
                ->icon('heroicon-o-eye')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('status_case_study', ContentStatus::TERPUBLIKASI->value)
                    ->orderBy('judul_case_study', 'asc')),
            'Tidak Terpublikasi' => Tab::make()
                ->icon('heroicon-o-eye-slash')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('status_case_study', ContentStatus::TIDAK_TERPUBLIKASI->value)
                    ->orderBy('judul_case_study', 'asc')),
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

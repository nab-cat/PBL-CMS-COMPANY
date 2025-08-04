<?php

namespace App\Filament\Resources\FeedbackResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\FeedbackResource;
use App\Filament\Resources\FeedbackResource\Widgets\FeedbackStats;

class ListFeedback extends ListRecords
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FeedbackStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->query(fn($query) => $query->orderBy('created_at', 'desc')),
            'Belum Ditanggapi' => Tab::make()
                ->icon('heroicon-o-x-circle')
                ->query(fn($query) => $query->whereNull('tanggapan_feedback')
                    ->orderBy('created_at', 'desc')),
            'Sudah Ditanggapi' => Tab::make()
                ->icon('heroicon-o-check-circle')
                ->query(fn($query) => $query->whereNotNull('tanggapan_feedback')
                    ->orderBy('created_at', 'desc')),
        ];
    }
}

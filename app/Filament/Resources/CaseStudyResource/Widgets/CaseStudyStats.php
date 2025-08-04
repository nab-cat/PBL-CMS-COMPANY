<?php

namespace App\Filament\Resources\CaseStudyResource\Widgets;

use App\Enums\ContentStatus;
use App\Models\CaseStudy;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\CaseStudyResource\Pages\ListCaseStudies;

class CaseStudyStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListCaseStudies::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Total Case Study', CaseStudy::query()->count())
                ->description('Jumlah keseluruhan case study')
                ->descriptionIcon('heroicon-o-document')
                ->color('primary'),

            Stat::make('Terpublikasi', CaseStudy::query()->where('status_case_study', ContentStatus::TERPUBLIKASI)->whereNull('deleted_at')->count())
                ->description('Case study yang sudah dipublikasikan')
                ->descriptionIcon('heroicon-o-eye') 
                ->color('success'),

            Stat::make('Tidak Terpublikasi', CaseStudy::query()->where('status_case_study', ContentStatus::TIDAK_TERPUBLIKASI)->whereNull('deleted_at')->count())
                ->description('Case study masih sebagai draft')
                ->descriptionIcon('heroicon-o-eye-slash')
                ->color('warning'),

            Stat::make('Diarsipkan', CaseStudy::query()->onlyTrashed()->count())
                ->description('Case study yang diarsipkan')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('danger'),
        ];
    }
}

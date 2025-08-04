<?php

namespace App\Filament\Widgets\ContentManager\Artikel;

use App\Models\Artikel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopArticlesByViews extends BaseWidget
{
    protected ?string $heading = 'Widget Artikel';
    protected static ?int $sort = 3;
    protected string|int|array $columnSpan = 2;
    protected static bool $isLazy = true;
    protected static ?string $pollingInterval = '120s'; // 2 minutes

    protected function getStats(): array
    {
        $topArticles = Artikel::query()
            ->orderByDesc('jumlah_view')
            ->limit(3)
            ->get();

        return $topArticles->map(function ($article) {
            return Stat::make(
                label: $article->judul_artikel,
                value: number_format($article->jumlah_view) . ' views'
            )
                ->description('Artikel paling banyak dilihat')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success');
        })->toArray();
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_TopArticlesByViews');
    }
}

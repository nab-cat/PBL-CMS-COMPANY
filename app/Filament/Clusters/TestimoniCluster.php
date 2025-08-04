<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use App\Helpers\FilamentGroupingHelper;
use App\Filament\Clusters\TestimoniCluster\Pages\ManageTestimoni;

class TestimoniCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Testimoni';

    protected static ?string $slug = 'testimoni';

    protected static ?int $navigationSort = 50;

    protected static ?string $clusterBreadcrumb = 'Testimoni';

public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Customer Service');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTestimoni::route('/'),
        ];
    }
}

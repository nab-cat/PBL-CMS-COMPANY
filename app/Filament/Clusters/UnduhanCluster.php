<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use App\Helpers\FilamentGroupingHelper;

class UnduhanCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-arrow-down-tray';

    protected static ?string $navigationLabel = 'Unduhan';

    protected static ?string $slug = 'Unduhan-Management';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Content Management');
    }
}

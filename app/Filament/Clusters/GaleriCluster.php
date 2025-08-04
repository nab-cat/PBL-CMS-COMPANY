<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use App\Helpers\FilamentGroupingHelper;

class GaleriCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-photo';

    protected static ?string $navigationLabel = 'Galeri';

    protected static ?string $slug = 'Galeri-Management';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Content Management');
    }
}

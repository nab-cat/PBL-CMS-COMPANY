<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use App\Helpers\FilamentGroupingHelper;

class ArtikelsCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-newspaper';

    protected static ?string $navigationLabel = 'Artikel';

    protected static ?string $slug = 'Content-Management';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Content Management');
    }
}

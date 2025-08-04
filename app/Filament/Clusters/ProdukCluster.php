<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use App\Helpers\FilamentGroupingHelper;

class ProdukCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-shopping-bag';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $slug = 'Produk-Management';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Content Management');
    }
}

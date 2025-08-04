<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniEventResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniEventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTestimoniEvent extends CreateRecord
{
    protected static string $resource = TestimoniEventResource::class;
}

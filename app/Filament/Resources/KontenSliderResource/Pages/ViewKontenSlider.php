<?php

namespace App\Filament\Resources\KontenSliderResource\Pages;

use App\Models\KontenSlider;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\KontenSliderResource;

class ViewKontenSlider extends ViewRecord
{
    protected static string $resource = KontenSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-presentation-chart-line')
                ->label('Pilih Konten yang Ditampilkan'),
        ];
    }

    public function mount(int | string | null $record = null): void
    {
        // Ambil record pertama jika tidak ada ID yang diberikan
        if (!$record) {
            $firstRecord = KontenSlider::first();
            if ($firstRecord) {
                $record = $firstRecord->getKey();
            }
        }

        parent::mount($record);
    }

    protected function resolveRecord(int | string $key): \Illuminate\Database\Eloquent\Model
    {
        // Jika tidak ada key, ambil record pertama
        if (!$key) {
            return KontenSlider::firstOrFail();
        }

        return parent::resolveRecord($key);
    }
}

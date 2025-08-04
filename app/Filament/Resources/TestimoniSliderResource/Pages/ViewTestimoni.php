<?php

namespace App\Filament\Resources\TestimoniSliderResource\Pages;

use App\Models\TestimoniSlider;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TestimoniSliderResource;

class ViewTestimoni extends ViewRecord
{
    protected static string $resource = TestimoniSliderResource::class;

    public function mount(int|string|null $record = null): void
    {        // Jika tidak ada record ID, ambil record pertama
        if (!$record) {
            $firstRecord = TestimoniSlider::first();
            if ($firstRecord) {
                $record = $firstRecord->getKey();
            } else {
                // Jika tidak ada testimoni sama sekali, redirect ke edit atau create
                redirect()->to($this->getResource()::getUrl('edit', ['record' => 1]));
                return;
            }
        }

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Pilih Testimoni Yang Akan Ditampilkan')
                ->icon('heroicon-o-cursor-arrow-rays'),
        ];
    }
}

<?php

namespace App\Filament\Resources\CaseStudyResource\Pages;

use App\Filament\Resources\CaseStudyResource;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaseStudy extends EditRecord
{
    protected static string $resource = CaseStudyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    MultipleFileHandler::deleteFiles($record, 'thumbnail_case_study');
                }),
        ];
    }

    /**
     * Handle file deletion during update
     */
    public function beforeSave(): void
    {
        MultipleFileHandler::handleRemovedFiles($this->record, $this->data, 'thumbnail_case_study');
    }

    /**
     * Format uploaded files during edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MultipleFileHandler::formatFileData($data, 'thumbnail_case_study');
    }

    /**
     * Define globally searchable attributes
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['judul_case_study', 'slug_case_study'];
    }
}

<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\CaseStudy;
use Filament\Tables\Table;
use App\Enums\ContentStatus;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use App\Helpers\FilamentGroupingHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\RestoreBulkAction;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\CaseStudyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CaseStudyResource\RelationManagers;
use App\Filament\Resources\CaseStudyResource\Widgets\CaseStudyStats;

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;
    protected static ?string $navigationIcon = 'heroicon-s-book-open';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Content Management');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Case Study')
                    ->icon('heroicon-s-information-circle')
                    ->description('Informasi umum mengenai case study')
                    ->schema([
                        Forms\Components\TextInput::make('judul_case_study')
                            ->label('Judul Case Study')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!empty($state)) {
                                    $baseSlug = str($state)->slug();
                                    $dateSlug = now()->format('Y-m-d');
                                    $set('slug_case_study', $baseSlug . '-' . $dateSlug);
                                } else {
                                    $set('slug_case_study', null);
                                }
                            }),

                        Forms\Components\Select::make('id_mitra')
                            ->label('Mitra')
                            ->relationship('mitra', 'nama')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('slug_case_study')
                            ->required()
                            ->maxLength(100)
                            ->unique(CaseStudy::class, 'slug_case_study', ignoreRecord: true)
                            ->dehydrated()
                            ->helperText('Akan terisi otomatis berdasarkan judul')
                            ->validationMessages([
                                'unique' => 'Slug sudah terpakai. Silakan gunakan slug lain.',
                            ]),

                        Forms\Components\ToggleButtons::make('status_case_study')
                            ->label('Status Case Study')
                            ->inline()
                            ->options(ContentStatus::class)
                            ->default(ContentStatus::TIDAK_TERPUBLIKASI)
                            ->required(),

                        Forms\Components\Textarea::make('deskripsi_case_study')
                            ->label('Deskripsi Singkat')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Media & Konten')
                    ->icon('heroicon-s-photo')
                    ->description('Tambahkan gambar dan konten untuk case study')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail_case_study')
                            ->label('Galeri Gambar Case Study')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('case-study-thumbnails')
                            ->maxFiles(5)
                            ->helperText('Upload hingga 5 gambar untuk case study (format: jpg, png, webp)')
                            ->disk('public')
                            ->columnSpanFull()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(width: 1280)
                            ->imageResizeTargetHeight(720)
                            ->optimize('webp'),

                        Forms\Components\RichEditor::make('isi_case_study')
                            ->label('Konten Case Study')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('case-study-attachments')
                            ->columnSpanFull()
                            ->hintAction(
                                fn(Get $get) => Action::make('previewContent')
                                    ->label('Preview Konten')
                                    ->slideOver()
                                    ->form([
                                        Forms\Components\ViewField::make('preview')
                                            ->view('forms.preview-konten-case-study')
                                            ->viewData([
                                                'konten' => $get('isi_case_study'),
                                            ])
                                            ->columnSpanFull(),
                                    ])
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('thumbnail_case_study')
                    ->label('Thumbnail')
                    ->formatStateUsing(function ($record) {
                        $images = [];
                        $totalImages = 0;

                        if (is_array($record->thumbnail_case_study) && !empty($record->thumbnail_case_study)) {
                            $totalImages = count($record->thumbnail_case_study);

                            // Ambil maksimal 3 gambar untuk stack effect
                            $imagesToShow = array_slice($record->thumbnail_case_study, 0, 3);

                            foreach ($imagesToShow as $imagePath) {
                                $images[] = route('thumbnail', [
                                    'path' => base64_encode($imagePath),
                                    'w' => 80,
                                    'h' => 80,
                                    'q' => 80
                                ]);
                            }
                        }

                        return view('filament.tables.columns.image-stack-advanced', [
                            'images' => $images,
                            'total_images' => $totalImages,
                            'remaining_count' => max(0, $totalImages - 1)
                        ])->render();
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('judul_case_study')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\ImageColumn::make('mitra.logo')
                    ->label('Logo Mitra')
                    ->disk('public')
                    ->size(40)
                    ->circular()
                    ->defaultImageUrl(url('/image/placeholder.webp')),

                Tables\Columns\TextColumn::make('mitra.nama')
                    ->label('Nama Mitra')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('status_case_study')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->onIcon('heroicon-m-eye')
                    ->offIcon('heroicon-m-eye-slash')
                    ->disabled(fn() => !auth()->user()->can('update_case::study', CaseStudy::class))
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status_case_study' => $state ? ContentStatus::TERPUBLIKASI : ContentStatus::TIDAK_TERPUBLIKASI
                        ]);
                        return $state;
                    })
                    ->getStateUsing(fn($record) => $record->status_case_study === ContentStatus::TERPUBLIKASI)
                    ->tooltip(fn($record) => $record->status_case_study === ContentStatus::TERPUBLIKASI ? 'Terpublikasi' : 'Tidak Terpublikasi'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_mitra')
                    ->label('Mitra')
                    ->relationship('mitra', 'nama'),

                Tables\Filters\SelectFilter::make('status_case_study')
                    ->label('Status')
                    ->options(ContentStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Arsipkan')
                    ->modalHeading('Arsipkan Case Study')
                    ->icon('heroicon-s-archive-box-arrow-down')
                    ->color('warning')
                    ->successNotificationTitle('Case study berhasil diarsipkan'),
                Tables\Actions\RestoreAction::make()
                    ->modalHeading('Pulihkan Case Study')
                    ->successNotificationTitle('Case study berhasil dipulihkan'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->modalHeading('Hapus Permanen Case Study')
                    ->successNotificationTitle('Case study berhasil dihapus permanen')
                    ->before(function ($record) {
                        MultipleFileHandler::deleteFiles($record, 'thumbnail_case_study');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Publikasikan Case Study')
                        ->modalDescription('Apakah Anda yakin ingin mempublikasikan case study yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_case_study' => ContentStatus::TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Case study berhasil dipublikasikan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_case::study', CaseStudy::class)),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Batalkan Publikasi')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Publikasi Case Study')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan publikasi case study yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_case_study' => ContentStatus::TIDAK_TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Publikasi case study berhasil dibatalkan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_case::study', CaseStudy::class)),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Arsipkan')
                        ->color('warning')
                        ->icon('heroicon-s-archive-box-arrow-down')
                        ->successNotificationTitle('Case study berhasil diarsipkan')
                        ->hidden(fn() => !auth()->user()->can('delete_case::study', CaseStudy::class)),
                    RestoreBulkAction::make()
                        ->successNotificationTitle('Case study berhasil dipulihkan')
                        ->hidden(fn() => !auth()->user()->can('restore_case::study', CaseStudy::class)),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->successNotificationTitle('Case study berhasil dihapus permanen')
                        ->before(function (Collection $records) {
                            MultipleFileHandler::deleteBulkFiles($records, 'thumbnail_case_study');
                        })
                        ->hidden(fn() => !auth()->user()->can('force_delete_case::study', CaseStudy::class)),
                ]),
            ]);
    }


    public static function getWidgets(): array
    {
        return [
            CaseStudyStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseStudies::route('/'),
            'create' => Pages\CreateCaseStudy::route('/create'),
            'edit' => Pages\EditCaseStudy::route('/{record}/edit'),
        ];
    }
}

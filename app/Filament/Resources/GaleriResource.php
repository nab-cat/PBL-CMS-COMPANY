<?php

namespace App\Filament\Resources;

use App\Enums\ContentStatus;
use App\Filament\Clusters\GaleriCluster;
use Filament\Forms;
use Filament\Tables;
use App\Models\Galeri;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\GaleriResource\Pages;
use App\Services\FileHandlers\MultipleFileHandler;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\GaleriResource\RelationManagers;
use App\Filament\Resources\GaleriResource\Widgets\GaleriStats;

class GaleriResource extends Resource
{
    protected static ?string $model = Galeri::class;
    protected static ?string $navigationIcon = 'heroicon-s-photo';
    protected static ?string $cluster = GaleriCluster::class;
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Galeri')
                    ->icon('heroicon-s-information-circle')
                    ->description('Isi informasi dasar galeri Anda. Judul dan kategori galeri wajib diisi.')
                    ->schema([
                        Forms\Components\TextInput::make('judul_galeri')
                            ->label('Judul Galeri')
                            ->required()
                            ->maxLength(200)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!empty($state)) {
                                    $baseSlug = str($state)->slug();
                                    $dateSlug = now()->format('Y-m-d');
                                    $set('slug', $baseSlug . '-' . $dateSlug);
                                } else {
                                    $set('slug', null);
                                }
                            }),

                        Forms\Components\Select::make('id_kategori_galeri')
                            ->label('Kategori Galeri')
                            ->relationship('kategoriGaleri', 'nama_kategori_galeri')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_galeri')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->maxLength(200),
                            ])
                            ->editOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_galeri')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->maxLength(200),
                            ])
                            ->manageOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_galeri')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->maxLength(200),
                            ]),


                        Forms\Components\Select::make('id_user')
                            ->label('Pengunggah')
                            ->relationship('user', 'name')
                            ->default(fn() => Auth::id())
                            ->searchable()
                            ->disabled()
                            ->dehydrated(true)
                            ->native(false)
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(Galeri::class, 'slug', ignoreRecord: true)
                            ->dehydrated()
                            ->helperText('Akan terisi otomatis berdasarkan judul')
                            ->validationMessages([
                                'unique' => 'Slug sudah terpakai. Silakan gunakan slug lain.',
                            ]),

                        Forms\Components\ToggleButtons::make('status_galeri')
                            ->label('Status Galeri')
                            ->inline()
                            ->options(ContentStatus::class)
                            ->default(ContentStatus::TIDAK_TERPUBLIKASI)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Media & Konten')
                    ->icon('heroicon-s-photo')
                    ->description('Unggah gambar galeri dan tambahkan deskripsi. Gambar akan digunakan sebagai thumbnail galeri.')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail_galeri')
                            ->label('Gambar Galeri')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('galeri-thumbnails')
                            ->maxFiles(10)
                            ->helperText('Upload hingga 10 gambar (format: jpg, png, webp)')
                            ->disk('public')
                            ->columnSpanFull()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(width: 1280)
                            ->imageResizeTargetHeight(720)
                            ->optimize('webp'),

                        Forms\Components\RichEditor::make('deskripsi_galeri')
                            ->label('Deskripsi Galeri')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'h1',
                                'h2',
                                'link',
                                'bulletList',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('thumbnail_galeri')
                    ->label('Thumbnail')
                    ->formatStateUsing(function ($record) {
                        $images = [];
                        $totalImages = 0;

                        if (is_array($record->thumbnail_galeri) && !empty($record->thumbnail_galeri)) {
                            $totalImages = count($record->thumbnail_galeri);

                            // Ambil maksimal 3 gambar untuk stack effect
                            $imagesToShow = array_slice($record->thumbnail_galeri, 0, 3);

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

                Tables\Columns\TextColumn::make('judul_galeri')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('kategoriGaleri.nama_kategori_galeri')
                    ->label('Kategori')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengunggah')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah_unduhan')
                    ->label('Jumlah Unduhan')
                    ->icon('heroicon-s-arrow-down-tray')
                    ->numeric()
                    ->alignCenter()
                    ->badge(),

                Tables\Columns\ToggleColumn::make('status_galeri')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->onIcon('heroicon-m-eye')
                    ->offIcon('heroicon-m-eye-slash')
                    ->disabled(fn() => !auth()->user()->can('update_galeri', Galeri::class))
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status_galeri' => $state ? ContentStatus::TERPUBLIKASI : ContentStatus::TIDAK_TERPUBLIKASI
                        ]);
                        return $state;
                    })
                    ->getStateUsing(fn($record) => $record->status_galeri === ContentStatus::TERPUBLIKASI)
                    ->tooltip(fn($record) => $record->status_galeri === ContentStatus::TERPUBLIKASI ? 'Terpublikasi' : 'Tidak Terpublikasi'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_kategori_galeri')
                    ->label('Kategori')
                    ->relationship('kategoriGaleri', 'nama_kategori_galeri'),

                Tables\Filters\SelectFilter::make('id_user')
                    ->label('Pengunggah')
                    ->relationship('user', 'name'),

                Tables\Filters\SelectFilter::make('status_galeri')
                    ->label('Status')
                    ->options(ContentStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Arsipkan')
                    ->modalHeading('Arsipkan Galeri')
                    ->icon('heroicon-s-archive-box-arrow-down')
                    ->color('warning')
                    ->successNotificationTitle('Galeri berhasil diarsipkan'),
                Tables\Actions\RestoreAction::make()
                    ->modalHeading('Pulihkan Galeri')
                    ->successNotificationTitle('Galeri berhasil dipulihkan'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->modalHeading('Hapus Permanen Galeri')
                    ->successNotificationTitle('Galeri berhasil dihapus permanen')
                    ->before(function ($record) {
                        MultipleFileHandler::deleteFiles($record, 'thumbnail_galeri');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Publikasikan Galeri')
                        ->modalDescription('Apakah Anda yakin ingin mempublikasikan galeri yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_galeri' => ContentStatus::TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Galeri berhasil dipublikasikan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_galeri', Galeri::class)),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Batalkan Publikasi')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Publikasi Galeri')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan publikasi galeri yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_galeri' => ContentStatus::TIDAK_TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Publikasi galeri berhasil dibatalkan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_galeri', Galeri::class)),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Arsipkan')
                        ->color('warning')
                        ->icon('heroicon-s-archive-box-arrow-down')
                        ->successNotificationTitle('Galeri berhasil diarsipkan')
                        ->hidden(fn() => !auth()->user()->can('delete_galeri', Galeri::class)),
                    RestoreBulkAction::make()
                        ->successNotificationTitle('Galeri berhasil dipulihkan')
                        ->hidden(fn() => !auth()->user()->can('restore_galeri', Galeri::class)),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->successNotificationTitle('Galeri berhasil dihapus permanen')
                        ->before(function (Collection $records) {
                            MultipleFileHandler::deleteBulkFiles($records, 'thumbnail_galeri');
                        })
                        ->hidden(fn() => !auth()->user()->can('force_delete_galeri', Galeri::class)),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            GaleriStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGaleris::route('/'),
            'create' => Pages\CreateGaleri::route('/create'),
            // ada tambahan validasi pada edit event
            'edit' => Pages\EditGaleri::route('/{record}/edit'),
        ];
    }
}

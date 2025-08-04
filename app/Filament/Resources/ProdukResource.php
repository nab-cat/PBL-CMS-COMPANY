<?php

namespace App\Filament\Resources;

use App\Enums\ContentStatus;
use App\Filament\Clusters\ProdukCluster;
use App\Filament\Resources\ProdukResource\Widgets\ProdukStats;
use App\Filament\Resources\ProdukResource\Pages;
use App\Filament\Resources\ProdukResource\RelationManagers;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Filters\TrashedFilter;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;
    protected static ?string $navigationIcon = 'heroicon-s-shopping-bag';
    protected static ?string $cluster = ProdukCluster::class;
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->icon('heroicon-s-information-circle')
                    ->description('Informasi terkait produk yang akan ditambahkan atau diedit.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_produk')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(100)
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

                        Forms\Components\Select::make('id_kategori_produk')
                            ->label('Kategori Produk')
                            ->relationship('kategoriProduk', 'nama_kategori_produk')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_produk')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->maxLength(200),
                            ])
                            ->editOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_produk')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->maxLength(200),
                            ])
                            ->manageOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_produk')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->maxLength(200),
                            ]),

                        Forms\Components\Toggle::make('tampilkan_harga')
                            ->label('Tampilkan Harga')
                            ->default(true)
                            ->live() // Tambahkan live() untuk reaktivitas
                            ->helperText('Aktifkan untuk menampilkan harga produk di halaman publik'),

                        Forms\Components\TextInput::make('harga_produk')
                            ->label('Harga Produk')
                            ->numeric()
                            ->prefix('Rp')
                            ->suffix(',00')
                            ->required(fn(callable $get) => $get('tampilkan_harga')) // Conditional required
                            ->maxLength(50)
                            ->helperText('Masukkan harga produk dalam format angka tanpa titik')
                            ->placeholder('0')
                            ->visible(fn(callable $get) => $get('tampilkan_harga')), // Sembunyikan jika tampilkan_harga false

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(Produk::class, 'slug', ignoreRecord: true)
                            ->dehydrated()
                            ->helperText('Akan terisi otomatis berdasarkan nama produk')
                            ->validationMessages([
                                'unique' => 'Slug sudah terpakai. Silakan gunakan slug lain.',
                            ]),

                        Forms\Components\ToggleButtons::make('status_produk')
                            ->label('Status Produk')
                            ->inline()
                            ->options(ContentStatus::class)
                            ->default(ContentStatus::TIDAK_TERPUBLIKASI)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Media & Konten')
                    ->icon('heroicon-s-photo')
                    ->description('Tambahkan gambar produk dan deskripsi untuk memperkaya informasi produk.')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail_produk')
                            ->label('Gambar Produk')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('produk-thumbnails')
                            ->helperText('Upload gambar produk (format: jpg, png, webp)')
                            ->disk('public')
                            ->columnSpanFull()
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth(1280)
                            ->imageResizeTargetHeight(720)
                            ->optimize('webp'),

                        Forms\Components\RichEditor::make('deskripsi_produk')
                            ->label('Deskripsi Produk')
                             ->toolbarButtons([
                                'redo',
                                'undo',
                            ])
                            ->required() 
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Tautan & Informasi Tambahan')
                    ->icon('heroicon-s-link')
                    ->description('Tambahkan tautan produk dan informasi tambahan yang relevan.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('link_produk')
                                    ->label('Tautan Produk')
                                    ->url()
                                    ->maxLength(255)
                                    ->helperText('Masukkan tautan produk')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('openLink')
                                        ->label('Buka Tautan')
                                        ->icon('heroicon-s-arrow-top-right-on-square')
                                        ->url(fn($get) => $get('link_produk'), true)
                                        ->visible(fn($get) => filled($get('link_produk')))
                                        ->button()
                                ])
                                    ->verticallyAlignCenter()
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('thumbnail_produk')
                    ->label('Thumbnail')
                    ->formatStateUsing(function ($record) {
                        $images = [];
                        $totalImages = 0;

                        if (is_array($record->thumbnail_produk) && !empty($record->thumbnail_produk)) {
                            $totalImages = count($record->thumbnail_produk);

                            // Ambil maksimal 3 gambar untuk stack effect
                            $imagesToShow = array_slice($record->thumbnail_produk, 0, 3);

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

                Tables\Columns\TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('kategoriProduk.nama_kategori_produk')
                    ->label('Kategori')
                    ->searchable(),

                Tables\Columns\TextColumn::make('harga_produk')
                    ->label('Harga')
                    ->money('IDR'),

                Tables\Columns\ToggleColumn::make('status_produk')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->onIcon('heroicon-m-eye')
                    ->offIcon('heroicon-m-eye-slash')
                    ->disabled(fn() => !auth()->user()->can('update_produk', Produk::class))
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status_produk' => $state ? ContentStatus::TERPUBLIKASI : ContentStatus::TIDAK_TERPUBLIKASI
                        ]);
                        return $state;
                    })
                    ->getStateUsing(fn($record) => $record->status_produk === ContentStatus::TERPUBLIKASI)
                    ->tooltip(fn($record) => $record->status_produk === ContentStatus::TERPUBLIKASI ? 'Terpublikasi' : 'Tidak Terpublikasi'),

                Tables\Columns\TextColumn::make('link_produk')
                    ->label('Tautan Produk')
                    ->icon('heroicon-s-link')
                    ->url(fn($record) => $record->link_produk)
                    ->openUrlInNewTab()
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\SelectFilter::make('id_kategori_produk')
                    ->label('Kategori')
                    ->relationship('kategoriProduk', 'nama_kategori_produk'),

                Tables\Filters\SelectFilter::make('status_produk')
                    ->label('Status')
                    ->options(ContentStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Arsipkan')
                    ->modalHeading('Arsipkan Produk')
                    ->icon('heroicon-s-archive-box-arrow-down')
                    ->color('warning')
                    ->successNotificationTitle('Produk berhasil diarsipkan'),
                Tables\Actions\RestoreAction::make()
                    ->modalHeading('Pulihkan Produk')
                    ->successNotificationTitle('Produk berhasil dipulihkan'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->modalHeading('Hapus Permanen Produk')
                    ->successNotificationTitle('Produk berhasil dihapus permanen')
                    ->before(function ($record) {
                        MultipleFileHandler::deleteFiles($record, 'thumbnail_produk');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Publikasikan Produk')
                        ->modalDescription('Apakah Anda yakin ingin mempublikasikan produk yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_produk' => ContentStatus::TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Produk berhasil dipublikasikan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_produk', Produk::class)),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Batalkan Publikasi')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Publikasi Produk')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan publikasi produk yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_produk' => ContentStatus::TIDAK_TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Publikasi produk berhasil dibatalkan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_produk', Produk::class)),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Arsipkan')
                        ->color('warning')
                        ->icon('heroicon-s-archive-box-arrow-down')
                        ->successNotificationTitle('Produk berhasil diarsipkan')
                        ->hidden(fn() => !auth()->user()->can('delete_produk', Produk::class)),
                    RestoreBulkAction::make()
                        ->successNotificationTitle('Produk berhasil dipulihkan')
                        ->hidden(fn() => !auth()->user()->can('restore_produk', Produk::class)),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->successNotificationTitle('Produk berhasil dihapus permanen')
                        ->before(function (Collection $records) {
                            MultipleFileHandler::deleteBulkFiles($records, 'thumbnail_produk');
                        })
                        ->hidden(fn() => !auth()->user()->can('force_delete_produk', Produk::class)),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProdukStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}

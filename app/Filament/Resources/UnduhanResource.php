<?php

namespace App\Filament\Resources;

use App\Enums\ContentStatus;
use App\Filament\Clusters\UnduhanCluster;
use Filament\Forms;
use Filament\Tables;
use App\Models\Unduhan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\RestoreBulkAction;
use App\Services\FileHandlers\SingleFileHandler;
use App\Filament\Resources\UnduhanResource\Pages;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UnduhanResource\RelationManagers;
use App\Filament\Resources\UnduhanResource\Widgets\UnduhanStats;

class UnduhanResource extends Resource
{
    protected static ?string $model = Unduhan::class;
    protected static ?string $navigationIcon = 'heroicon-s-arrow-down-tray';
    protected static ?string $cluster = UnduhanCluster::class;
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Unduhan')
                    ->icon('heroicon-s-information-circle')
                    ->description('Isi informasi dasar tentang unduhan yang akan dibuat.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_unduhan')
                            ->label('Nama Unduhan')
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

                        Forms\Components\Select::make('id_kategori_unduhan')
                            ->label('Kategori Unduhan')
                            ->relationship('kategoriUnduhan', 'nama_kategori_unduhan')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_unduhan')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                            ])
                            ->editOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_unduhan')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                            ])
                            ->manageOptionForm([
                                Forms\Components\TextInput::make('nama_kategori_unduhan')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(50),
                            ]),

                        Forms\Components\Select::make('id_user')
                            ->label('Pengunggah')
                            ->relationship('user', 'name')
                            ->default(fn() => Auth::id())
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(true)
                            ->native(false)
                            ->required(),


                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(Unduhan::class, 'slug', ignoreRecord: true)
                            ->dehydrated()
                            ->helperText('Akan terisi otomatis berdasarkan nama unduhan')
                            ->validationMessages([
                                'unique' => 'Slug sudah terpakai. Silakan gunakan slug lain.',
                            ]),

                        Forms\Components\ToggleButtons::make('status_unduhan')
                            ->label('Status Unduhan')
                            ->inline()
                            ->options(ContentStatus::class)
                            ->default(ContentStatus::TIDAK_TERPUBLIKASI)
                            ->required(),
                    ]),

                Forms\Components\Section::make('File & Konten')
                    ->icon('heroicon-s-document-text')
                    ->description('Unggah file unduhan dan tambahkan deskripsi jika diperlukan.')
                    ->schema([
                        Forms\Components\FileUpload::make('lokasi_file')
                            ->label('File Unduhan')
                            ->directory('unduhan-files')
                            ->maxSize(10240) // 10MB
                            ->required()
                            ->disk('public')
                            ->downloadable()
                            ->helperText('Upload file untuk diunduh (format: pdf, doc, docx, xls, xlsx, ppt, pptx, zip)'),

                         Forms\Components\RichEditor::make('deskripsi')
                            ->label('Deskripsi Unduhan')
                             ->toolbarButtons([
                                'redo',
                                'undo',
                            ])
                            ->required() 
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
                Tables\Columns\TextColumn::make('nama_unduhan')
                    ->label('Nama Unduhan')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('kategoriUnduhan.nama_kategori_unduhan')
                    ->label('Kategori')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengunggah')
                    ->icon('heroicon-s-user')
                    ->searchable(),

                Tables\Columns\TextColumn::make('lokasi_file')
                    ->label('File')
                    ->formatStateUsing(fn(string $state): string => basename($state))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('jumlah_unduhan')
                    ->label('Jumlah Unduhan')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-s-arrow-down-tray')
                    ->numeric(),

                Tables\Columns\ToggleColumn::make('status_unduhan')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->onIcon('heroicon-m-eye')
                    ->offIcon('heroicon-m-eye-slash')
                    ->disabled(fn() => !auth()->user()->can('update_unduhan', Unduhan::class))
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status_unduhan' => $state ? ContentStatus::TERPUBLIKASI : ContentStatus::TIDAK_TERPUBLIKASI
                        ]);
                        return $state;
                    })
                    ->getStateUsing(fn($record) => $record->status_unduhan === ContentStatus::TERPUBLIKASI)
                    ->tooltip(fn($record) => $record->status_unduhan === ContentStatus::TERPUBLIKASI ? 'Terpublikasi' : 'Tidak Terpublikasi'),

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
                Tables\Filters\SelectFilter::make('id_kategori_unduhan')
                    ->label('Kategori')
                    ->relationship('kategoriUnduhan', 'nama_kategori_unduhan'),

                Tables\Filters\SelectFilter::make('id_user')
                    ->label('Pengunggah')
                    ->relationship('user', 'name'),

                Tables\Filters\SelectFilter::make('status_unduhan')
                    ->label('Status')
                    ->options(ContentStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Arsipkan')
                    ->modalHeading('Arsipkan Unduhan')
                    ->icon('heroicon-s-archive-box-arrow-down')
                    ->color('warning')
                    ->successNotificationTitle('Unduhan berhasil diarsipkan'),
                Tables\Actions\RestoreAction::make()
                    ->modalHeading('Pulihkan Unduhan')
                    ->successNotificationTitle('Unduhan berhasil dipulihkan'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->modalHeading('Hapus Permanen Unduhan')
                    ->successNotificationTitle('Unduhan berhasil dihapus permanen')
                    ->before(function ($record) {
                        SingleFileHandler::deleteFile($record, 'lokasi_file');
                    }),
                Tables\Actions\Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(Unduhan $record) => url('storage/' . $record->lokasi_file))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Publikasikan Unduhan')
                        ->modalDescription('Apakah Anda yakin ingin mempublikasikan unduhan yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_unduhan' => ContentStatus::TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Unduhan berhasil dipublikasikan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_unduhan', Unduhan::class)),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Batalkan Publikasi')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Publikasi Unduhan')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan publikasi unduhan yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_unduhan' => ContentStatus::TIDAK_TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Publikasi unduhan berhasil dibatalkan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_unduhan', Unduhan::class)),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Arsipkan')
                        ->color('warning')
                        ->icon('heroicon-s-archive-box-arrow-down')
                        ->successNotificationTitle('Unduhan berhasil diarsipkan')
                        ->hidden(fn() => !auth()->user()->can('delete_unduhan', Unduhan::class)),
                    RestoreBulkAction::make()
                        ->successNotificationTitle('Unduhan berhasil dipulihkan')
                        ->hidden(fn() => !auth()->user()->can('restore_unduhan', Unduhan::class)),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->successNotificationTitle('Unduhan berhasil dihapus permanen')
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                SingleFileHandler::deleteFile($record, 'lokasi_file');
                            }
                        })
                        ->hidden(fn() => !auth()->user()->can('force_delete_unduhan', Unduhan::class)),
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
            UnduhanStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnduhans::route('/'),
            'create' => Pages\CreateUnduhan::route('/create'),
            'edit' => Pages\EditUnduhan::route('/{record}/edit'),
        ];
    }
}

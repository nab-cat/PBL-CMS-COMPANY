<?php

namespace App\Filament\Resources;

use App\Enums\ContentStatus;
use Filament\Forms;
use Filament\Tables;
use App\Models\Lowongan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\LowonganResource\Pages;
use App\Services\FileHandlers\MultipleFileHandler;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LowonganResource\RelationManagers;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use App\Helpers\FilamentGroupingHelper;

class LowonganResource extends Resource
{
    protected static ?string $model = Lowongan::class;
    protected static ?string $navigationIcon = 'heroicon-s-briefcase';
    protected static ?string $recordTitleAttribute = 'judul_lowongan';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Customer Service');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Lowongan')
                    ->icon('heroicon-s-information-circle')
                    ->description('Masukkan informasi dasar tentang lowongan yang akan dibuka')
                    ->schema([
                        Forms\Components\TextInput::make('judul_lowongan')
                            ->label('Judul Lowongan')
                            ->required()
                            ->maxLength(200)
                            ->placeholder('Masukkan judul lowongan pekerjaan')
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

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(Lowongan::class, 'slug', ignoreRecord: true)
                            ->dehydrated()
                            ->helperText('Akan terisi otomatis berdasarkan judul')
                            ->validationMessages([
                                'unique' => 'Slug sudah terpakai. Silakan gunakan slug lain.',
                            ]),

                        Forms\Components\Select::make('id_user')
                            ->label('Pembuat')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated()
                            ->native(false)
                            ->default(fn() => Auth::id())
                            ->required(),

                        Forms\Components\Select::make('jenis_lowongan')
                            ->label('Jenis Pekerjaan')
                            ->options([
                                'Full-time' => 'Full-time',
                                'Part-time' => 'Part-time',
                                'Freelance' => 'Freelance',
                                'Internship' => 'Internship',
                            ])
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('gaji')
                            ->label('Gaji')
                            ->prefix('Rp')
                            ->suffix(',-')
                            ->numeric()
                            ->placeholder('Gaji/tunjangan yang ditawarkan')
                            ->helperText('Masukkan nominal tanpa menggunakan titik'),

                        Forms\Components\TextInput::make('tenaga_dibutuhkan')
                            ->label('Jumlah Posisi')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required()
                            ->helperText('Jumlah orang yang dibutuhkan untuk posisi ini'),
                    ]),

                Forms\Components\Section::make('Detail Lowongan')
                    ->icon('heroicon-s-briefcase')
                    ->description('Tambahkan informasi pelengkap tentang lowongan yang akan dibuka')
                    ->schema([
                        Forms\Components\RichEditor::make('deskripsi_pekerjaan')
                            ->label('Deskripsi Pekerjaan')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('lowongan-attachments')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('thumbnail_lowongan')
                            ->label('Gambar Lowongan')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('lowongan-images')
                            ->maxFiles(5)
                            ->helperText('Upload hingga 5 gambar untuk artikel (format: jpg, png, webp)')
                            ->disk('public')
                            ->columnSpanFull()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(width: 1280)
                            ->imageResizeTargetHeight(720)
                            ->optimize('webp'),
                    ]),

                Forms\Components\Section::make('Periode Lowongan')
                    ->icon('heroicon-s-calendar')
                    ->description('Atur periode pembukaan dan penutupan lowongan')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_dibuka')
                                    ->label('Tanggal Dibuka')
                                    ->required()
                                    ->default(now())
                                    ->displayFormat('d F Y')
                                    ->native(false)
                                    ->minDate(fn($record) => $record ? null : today())
                                    ->validationMessages([
                                        'after_or_equal' => 'Tanggal dibuka tidak boleh sebelum hari ini.',
                                    ]),

                                Forms\Components\DatePicker::make('tanggal_ditutup')
                                    ->label('Tanggal Ditutup')
                                    ->required()
                                    ->displayFormat('d F Y')
                                    ->native(false)
                                    ->afterOrEqual('tanggal_dibuka')
                                    ->validationMessages([
                                        'after_or_equal' => 'Tanggal ditutup tidak boleh sebelum tanggal dibuka.',
                                        'required' => 'Tanggal ditutup harus diisi.',
                                    ]),
                            ]),

                        Forms\Components\ToggleButtons::make('status_lowongan')
                            ->label('Status Lowongan')
                            ->inline()
                            ->options(ContentStatus::class)
                            ->default(ContentStatus::TIDAK_TERPUBLIKASI)
                            ->required(),
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
                Tables\Columns\TextColumn::make('thumbnail_lowongan')
                    ->label('Thumbnail')
                    ->formatStateUsing(function ($record) {
                        $images = [];
                        $totalImages = 0;

                        if (is_array($record->thumbnail_lowongan) && !empty($record->thumbnail_lowongan)) {
                            $totalImages = count($record->thumbnail_lowongan);

                            // Ambil maksimal 3 gambar untuk stack effect
                            $imagesToShow = array_slice($record->thumbnail_lowongan, 0, 3);

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

                Tables\Columns\TextColumn::make('judul_lowongan')
                    ->label('Judul Lowongan')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('jenis_lowongan')
                    ->label('Jenis Pekerjaan')
                    ->badge()
                    ->colors([
                        'primary' => 'Full-time',
                        'secondary' => 'Part-time',
                        'warning' => 'Freelance',
                        'success' => 'Internship',
                    ]),

                Tables\Columns\TextColumn::make('gaji')
                    ->label('Gaji')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('tanggal_dibuka')
                    ->label('Tanggal Dibuka')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('tanggal_ditutup')
                    ->label('Tanggal Ditutup')
                    ->date('d M Y'),

                Tables\Columns\ToggleColumn::make('status_lowongan')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->onIcon('heroicon-m-eye')
                    ->offIcon('heroicon-m-eye-slash')
                    ->disabled(fn() => !auth()->user()->can('update_lowongan', Lowongan::class))
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status_lowongan' => $state ? ContentStatus::TERPUBLIKASI : ContentStatus::TIDAK_TERPUBLIKASI
                        ]);
                        return $state;
                    })
                    ->getStateUsing(fn($record) => $record->status_lowongan === ContentStatus::TERPUBLIKASI)
                    ->tooltip(fn($record) => $record->status_lowongan === ContentStatus::TERPUBLIKASI ? 'Terpublikasi' : 'Tidak Terpublikasi'),

                Tables\Columns\TextColumn::make('periode_status')
                    ->label('Periode')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Dibuka' => 'success',
                        'Ditutup' => 'danger',
                        default => 'secondary',
                    })
                    ->getStateUsing(function ($record): string {
                        $now = now();

                        if ($now->between($record->tanggal_dibuka, $record->tanggal_ditutup)) {
                            return 'Dibuka';
                        }

                        if ($now->isAfter($record->tanggal_ditutup)) {
                            return 'Ditutup';
                        }

                        return 'Belum Dibuka';
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('tenaga_dibutuhkan')
                    ->label('Posisi terbuka')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pembuat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_lowongan')
                    ->label('Jenis Pekerjaan')
                    ->options([
                        'Full-time' => 'Full-time',
                        'Part-time' => 'Part-time',
                        'Freelance' => 'Freelance',
                        'Internship' => 'Internship',
                    ]),

                Tables\Filters\Filter::make('active')
                    ->label('Periode Dibuka')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('tanggal_dibuka', '<=', now())
                        ->where('tanggal_ditutup', '>=', now())),

                Tables\Filters\SelectFilter::make('status_lowongan')
                    ->label('Status')
                    ->options(ContentStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Arsipkan')
                    ->modalHeading('Arsipkan Lowongan')
                    ->icon('heroicon-s-archive-box-arrow-down')
                    ->color('warning')
                    ->successNotificationTitle('Lowongan berhasil diarsipkan'),
                Tables\Actions\RestoreAction::make()
                    ->modalHeading('Pulihkan Lowongan')
                    ->successNotificationTitle('Lowongan berhasil dipulihkan'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->modalHeading('Hapus Permanen Lowongan')
                    ->successNotificationTitle('Lowongan berhasil dihapus permanen')
                    ->before(function ($record) {
                        MultipleFileHandler::deleteFiles($record, 'thumbnail_lowongan');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Publikasikan Lowongan')
                        ->modalDescription('Apakah Anda yakin ingin mempublikasikan lowongan yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_lowongan' => ContentStatus::TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Lowongan berhasil dipublikasikan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_lowongan', Lowongan::class)),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Batalkan Publikasi')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Publikasi Lowongan')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan publikasi lowongan yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_lowongan' => ContentStatus::TIDAK_TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Publikasi lowongan berhasil dibatalkan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_lowongan', Lowongan::class)),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Arsipkan')
                        ->color('warning')
                        ->icon('heroicon-s-archive-box-arrow-down')
                        ->successNotificationTitle('Lowongan berhasil diarsipkan')
                        ->hidden(fn() => !auth()->user()->can('delete_lowongan', Lowongan::class)),
                    RestoreBulkAction::make()
                        ->successNotificationTitle('Lowongan berhasil dipulihkan')
                        ->hidden(fn() => !auth()->user()->can('restore_lowongan', Lowongan::class)),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->successNotificationTitle('Lowongan berhasil dihapus permanen')
                        ->before(function (Collection $records) {
                            MultipleFileHandler::deleteBulkFiles($records, 'thumbnail_lowongan');
                        })
                        ->hidden(fn() => !auth()->user()->can('force_delete_lowongan', Lowongan::class)),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLowongans::route('/'),
            'create' => Pages\CreateLowongan::route('/create'),
            'edit' => Pages\EditLowongan::route('/{record}/edit'),
        ];
    }
}

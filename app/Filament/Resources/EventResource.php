<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Event;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\RestoreBulkAction;
use App\Filament\Resources\EventResource\Pages;
use App\Helpers\FilamentGroupingHelper;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Filament\Resources\EventResource\Widgets\EventStats;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-s-calendar';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Content Management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->icon('heroicon-s-information-circle')
                    ->description('Tambahkan informasi dasar event yang akan diselenggarakan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_event')
                            ->label('Nama Event')
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

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(Event::class, 'slug', ignoreRecord: true)
                            ->dehydrated()
                            ->helperText('Akan terisi otomatis berdasarkan nama event')
                            ->validationMessages([
                                'unique' => 'Slug sudah terpakai. Silakan gunakan slug lain.',
                            ]),

                        Forms\Components\RichEditor::make('deskripsi_event')
                            ->label('Deskripsi Event')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('event-attachments')
                            ->placeholder('Deskripsikan detail acara')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Detail Event')
                    ->icon('heroicon-s-calendar')
                    ->description('Tambahkan informasi detail event yang akan diselenggarakan')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail_event')
                            ->label('Thumbnail Event')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('event-thumbnails')
                            ->maxFiles(5)
                            ->helperText('Deskripsikan eventmu, maksimal 5 gambar(format: jpg, png, webp)')
                            ->disk('public')
                            ->columnSpanFull()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(width: 1280)
                            ->imageResizeTargetHeight(720)
                            ->optimize('webp'),


                        Forms\Components\TextInput::make('lokasi_event')
                            ->label('Nama Lokasi Event')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama atau alamat lokasi event')
                            ->helperText('Contoh: Gedung Serbaguna UGM, Yogyakarta')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('link_lokasi_event')
                            ->label('Link Lokasi Event (Google Maps)')
                            ->required()
                            ->maxLength(200)
                            ->url()
                            ->placeholder('https://maps.google.com/?q=Your+Location')
                            ->helperText('Berikan URL Google Maps untuk lokasi event')
                            ->prefixIcon('heroicon-s-map-pin')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('open')
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->tooltip('Open map in new tab')
                                    ->url(fn($get) => $get('link_lokasi_event'), true)
                                    ->visible(fn($get) => filled($get('link_lokasi_event')))
                            ),


                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DateTimePicker::make('waktu_start_event')
                                    ->label('Waktu Mulai')
                                    ->required()
                                    ->default(now())
                                    ->seconds(false)
                                    ->displayFormat('d F Y - H:i')
                                    ->native(false)
                                    ->minDate(fn($record) => $record ? null : today()),

                                Forms\Components\DateTimePicker::make('waktu_end_event')
                                    ->label('Waktu Selesai')
                                    ->required()
                                    ->seconds(false)
                                    ->displayFormat('d F Y - H:i')
                                    ->native(false)
                                    ->afterOrEqual('waktu_start_event')
                                    ->validationMessages([
                                        'after_or_equal' => 'waktu selesai event tidak boleh sebelum tanggal mulai event.',
                                        'required' => '\waktu selesai event harus diisi.',
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('thumbnail_event')
                    ->label('Thumbnail')
                    ->formatStateUsing(function ($record) {
                        $images = [];
                        $totalImages = 0;

                        if (is_array($record->thumbnail_event) && !empty($record->thumbnail_event)) {
                            $totalImages = count($record->thumbnail_event);

                            // Ambil maksimal 3 gambar untuk stack effect
                            $imagesToShow = array_slice($record->thumbnail_event, 0, 3);

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

                Tables\Columns\TextColumn::make('nama_event')
                    ->label('Nama Event')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('lokasi_event')
                    ->label('Lokasi')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->lokasi_event)
                    ->icon('heroicon-o-building-office'),

                Tables\Columns\TextColumn::make('link_lokasi_event')
                    ->label('Link Lokasi')
                    ->searchable()
                    ->limit(30)
                    ->url(fn($record) => $record->link_lokasi_event)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-map-pin')
                    ->tooltip('Klik untuk melihat di Google Maps')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('waktu_start_event')
                    ->label('Mulai')
                    ->dateTime('d M Y - H:i')
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('waktu_end_event')
                    ->label('Selesai')
                    ->dateTime('d M Y - H:i')
                    ->icon('heroicon-o-clock'),

                Tables\Columns\TextColumn::make('jumlah_pendaftar')
                    ->label('Jumlah Pendaftar')
                    ->alignCenter()
                    ->icon('heroicon-o-user-group')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->getStateUsing(function (Event $record): string {
                        $now = now();

                        if ($now->lt($record->waktu_start_event)) {
                            return 'Akan datang';
                        }

                        if ($now->gt($record->waktu_end_event)) {
                            return 'Selesai';
                        }

                        return 'Sedang berlangsung';
                    })
                    ->colors([
                        'warning' => 'Akan datang',
                        'success' => 'Sedang berlangsung',
                        'danger' => 'Selesai',
                    ]),


                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'upcoming' => $query->where('waktu_start_event', '>', now()),
                            'ongoing' => $query->where('waktu_start_event', '<=', now())
                                ->where('waktu_end_event', '>=', now()),
                            'completed' => $query->where('waktu_end_event', '<', now()),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Arsipkan')
                    ->modalHeading('Arsipkan Event')
                    ->icon('heroicon-s-archive-box-arrow-down')
                    ->color('warning')
                    ->successNotificationTitle('Event berhasil diarsipkan'),
                Tables\Actions\RestoreAction::make()
                    ->modalHeading('Pulihkan Event')
                    ->successNotificationTitle('Event berhasil dipulihkan'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->modalHeading('Hapus Permanen Event')
                    ->successNotificationTitle('Event berhasil dihapus permanen')
                    ->before(function ($record) {
                        MultipleFileHandler::deleteFiles($record, 'thumbnail_event');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Arsipkan')
                        ->color('warning')
                        ->icon('heroicon-s-archive-box-arrow-down')
                        ->successNotificationTitle('Event berhasil diarsipkan'),
                    RestoreBulkAction::make()
                        ->successNotificationTitle('Event berhasil dipulihkan'),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->successNotificationTitle('Event berhasil dihapus permanen')
                        ->before(function (Collection $records) {
                            MultipleFileHandler::deleteBulkFiles($records, 'thumbnail_event');
                        }),
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
            EventStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            // ada tambahan validasi pada edit event
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('waktu_start_event', '>=', now())->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) 'event yang akan datang';
    }
}

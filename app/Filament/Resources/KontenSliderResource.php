<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use App\Enums\ContentStatus;
use App\Models\KontenSlider;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use App\Helpers\FilamentGroupingHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KontenSliderResource\Pages;
use App\Filament\Resources\KontenSliderResource\RelationManagers;
use Filament\Infolists\Components\Component;
use Illuminate\Database\Eloquent\Model;

class KontenSliderResource extends Resource
{
    protected static ?string $model = KontenSlider::class;
    protected static ?string $navigationIcon = 'heroicon-s-presentation-chart-line';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Content Management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konten Slider')
                    ->description('Pilih salah satu konten untuk slider ini')
                    ->schema([
                        Forms\Components\TextInput::make('durasi_slider')
                            ->label('Durasi Slider (detik)')
                            ->prefixIcon('heroicon-s-clock')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(60)
                            ->default(5)
                            ->helperText('Durasi tampilan setiap konten di slider'),
                        Forms\Components\Select::make('id_artikel')
                            ->label('Artikel')
                            ->relationship(
                                name: 'artikel',
                                titleAttribute: 'judul_artikel',
                                modifyQueryUsing: fn($query) => $query->where('status_artikel', ContentStatus::TERPUBLIKASI)
                            )
                            ->getOptionLabelFromRecordUsing(function (Model $record) {
                                $thumbnail = is_array($record->thumbnail_artikel) && !empty($record->thumbnail_artikel)
                                    ? $record->thumbnail_artikel[0]
                                    : null;

                                if ($thumbnail) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-3">' .
                                            '<img src="' . asset('storage/' . $thumbnail) . '" class="w-10 h-10 rounded object-cover flex-shrink-0" />' .
                                            '<span class="truncate">' . e($record->judul_artikel) . '</span>' .
                                            '</div>'
                                    );
                                }

                                return $record->judul_artikel;
                            })
                            ->allowHtml()
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->helperText('Pilih artikel untuk ditampilkan di slider'),
                        Forms\Components\Select::make('id_galeri')
                            ->label('Galeri')
                            ->relationship(
                                name: 'galeri',
                                titleAttribute: 'judul_galeri',
                                modifyQueryUsing: fn($query) => $query->where('status_galeri', ContentStatus::TERPUBLIKASI)
                            )
                            ->getOptionLabelFromRecordUsing(function (Model $record) {
                                $thumbnail = is_array($record->thumbnail_galeri) && !empty($record->thumbnail_galeri)
                                    ? $record->thumbnail_galeri[0]
                                    : null;

                                if ($thumbnail) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-3">' .
                                            '<img src="' . asset('storage/' . $thumbnail) . '" class="w-10 h-10 rounded object-cover flex-shrink-0" />' .
                                            '<span class="truncate">' . e($record->judul_galeri) . '</span>' .
                                            '</div>'
                                    );
                                }

                                return $record->judul_galeri;
                            })
                            ->allowHtml()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Pilih galeri untuk ditampilkan di slider'),

                        Forms\Components\Select::make('id_event')
                            ->label('Event')
                            ->relationship('event', 'nama_event')
                            ->getOptionLabelFromRecordUsing(function (Model $record) {
                                $thumbnail = is_array($record->thumbnail_event) && !empty($record->thumbnail_event)
                                    ? $record->thumbnail_event[0]
                                    : null;

                                if ($thumbnail) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-3">' .
                                            '<img src="' . asset('storage/' . $thumbnail) . '" class="w-10 h-10 rounded object-cover flex-shrink-0" />' .
                                            '<span class="truncate">' . e($record->nama_event) . '</span>' .
                                            '</div>'
                                    );
                                }

                                return $record->nama_event;
                            })
                            ->allowHtml()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Pilih event untuk ditampilkan di slider'),

                        Forms\Components\Select::make('id_produk')
                            ->label('Produk')
                            ->relationship(
                                name: 'produk',
                                titleAttribute: 'nama_produk',
                                modifyQueryUsing: fn($query) => $query->where('status_produk', ContentStatus::TERPUBLIKASI)
                            )
                            ->getOptionLabelFromRecordUsing(function (Model $record) {
                                $thumbnail = is_array($record->thumbnail_produk) && !empty($record->thumbnail_produk)
                                    ? $record->thumbnail_produk[0]
                                    : null;

                                if ($thumbnail) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-3">' .
                                            '<img src="' . asset('storage/' . $thumbnail) . '" class="w-10 h-10 rounded object-cover flex-shrink-0" />' .
                                            '<span class="truncate">' . e($record->nama_produk) . '</span>' .
                                            '</div>'
                                    );
                                }

                                return $record->nama_produk;
                            })
                            ->allowHtml()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih produk untuk ditampilkan di slider'),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Konten Slider Aktif')
                    ->description('Konten yang sedang ditampilkan di slider')
                    ->icon('heroicon-s-play')
                    ->schema([
                        // Artikel Section
                        Components\Card::make()
                            ->schema([
                                Components\Grid::make([
                                    'sm' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        Components\ImageEntry::make('artikel.thumbnail_artikel')
                                            ->label('')
                                            ->square()
                                            ->size(200)
                                            ->disk('public')
                                            ->getStateUsing(function ($record) {
                                                if ($record->artikel && $record->artikel->thumbnail_artikel) {
                                                    $thumbnail = is_array($record->artikel->thumbnail_artikel)
                                                        ? $record->artikel->thumbnail_artikel[0]
                                                        : $record->artikel->thumbnail_artikel;
                                                    return $thumbnail;
                                                }
                                                return null;
                                            })
                                            ->columnSpan(1),

                                        Components\Grid::make(1)
                                            ->schema([
                                                Components\TextEntry::make('artikel.judul_artikel')
                                                    ->label('Artikel')
                                                    ->icon('heroicon-s-newspaper')
                                                    ->weight('bold')
                                                    ->size('lg')
                                                    ->color('primary'),

                                                Components\TextEntry::make('artikel.konten_artikel')
                                                    ->label('Konten')
                                                    ->html()
                                                    ->limit(150)
                                                    ->extraAttributes(['style' => 'line-height: 1.4;'])
                                            ])
                                            ->columnSpan(2),
                                    ])
                            ])
                            ->visible(fn($record) => $record->id_artikel !== null),

                        // Galeri Section
                        Components\Card::make()
                            ->schema([
                                Components\Grid::make([
                                    'sm' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        Components\ImageEntry::make('galeri.thumbnail_galeri')
                                            ->label('')
                                            ->square()
                                            ->size(200)
                                            ->disk('public')
                                            ->getStateUsing(function ($record) {
                                                if ($record->galeri && $record->galeri->thumbnail_galeri) {
                                                    $thumbnail = is_array($record->galeri->thumbnail_galeri)
                                                        ? $record->galeri->thumbnail_galeri[0]
                                                        : $record->galeri->thumbnail_galeri;
                                                    return $thumbnail;
                                                }
                                                return null;
                                            })
                                            ->columnSpan(1),

                                        Components\Grid::make(1)
                                            ->schema([
                                                Components\TextEntry::make('galeri.judul_galeri')
                                                    ->icon('heroicon-s-photo')
                                                    ->label('Galeri')
                                                    ->weight('bold')
                                                    ->size('lg')
                                                    ->color('primary'),

                                                Components\TextEntry::make('galeri.deskripsi_galeri')
                                                    ->label('Deskripsi')
                                                    ->limit(150)
                                                    ->extraAttributes(['style' => 'line-height: 1.4;']),
                                            ])
                                            ->columnSpan(2),
                                    ])
                            ])
                            ->visible(fn($record) => $record->id_galeri !== null),

                        // Event Section
                        Components\Card::make()
                            ->schema([
                                Components\Grid::make([
                                    'sm' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        Components\ImageEntry::make('event.thumbnail_event')
                                            ->label('')
                                            ->square()
                                            ->size(200)
                                            ->disk('public')
                                            ->getStateUsing(function ($record) {
                                                if ($record->event && $record->event->thumbnail_event) {
                                                    $thumbnail = is_array($record->event->thumbnail_event)
                                                        ? $record->event->thumbnail_event[0]
                                                        : $record->event->thumbnail_event;
                                                    return $thumbnail;
                                                }
                                                return null;
                                            })
                                            ->columnSpan(1),

                                        Components\Grid::make(1)
                                            ->schema([
                                                Components\TextEntry::make('event.nama_event')
                                                    ->icon('heroicon-s-calendar')
                                                    ->label('Event')
                                                    ->weight('bold')
                                                    ->size('lg')
                                                    ->color('primary'),

                                                Components\TextEntry::make('event.deskripsi_event')
                                                    ->label('Deskripsi')
                                                    ->limit(120)
                                                    ->html()
                                                    ->extraAttributes(['style' => 'line-height: 1.4;']),

                                                Components\TextEntry::make('event.waktu_start_event')
                                                    ->label('Waktu Mulai')
                                                    ->icon('heroicon-o-clock')
                                                    ->dateTime('d M Y H:i')
                                                    ->badge()
                                                    ->color('primary'),
                                            ])
                                            ->columnSpan(2),
                                    ])
                            ])
                            ->visible(fn($record) => $record->id_event !== null),

                        // Produk Section
                        Components\Card::make()
                            ->schema([
                                Components\Grid::make([
                                    'sm' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        Components\ImageEntry::make('produk.thumbnail_produk')
                                            ->label('')
                                            ->square()
                                            ->size(200)
                                            ->disk('public')
                                            ->getStateUsing(function ($record) {
                                                if ($record->produk && $record->produk->thumbnail_produk) {
                                                    $thumbnail = is_array($record->produk->thumbnail_produk)
                                                        ? $record->produk->thumbnail_produk[0]
                                                        : $record->produk->thumbnail_produk;
                                                    return $thumbnail;
                                                }
                                                return null;
                                            })
                                            ->columnSpan(1),

                                        Components\Grid::make(1)
                                            ->schema([
                                                Components\TextEntry::make('produk.nama_produk')
                                                    ->icon('heroicon-s-shopping-bag')
                                                    ->label('Produk')
                                                    ->weight('bold')
                                                    ->size('lg')
                                                    ->color('primary'),

                                                Components\TextEntry::make('produk.deskripsi_produk')
                                                    ->label('Deskripsi')
                                                    ->limit(120)
                                                    ->extraAttributes(['style' => 'line-height: 1.4;']),

                                                Components\TextEntry::make('produk.harga_produk')
                                                    ->label('Harga')
                                                    ->money('IDR')
                                                    ->icon('heroicon-o-wallet')
                                                    ->badge()
                                                    ->color('primary'),
                                            ])
                                            ->columnSpan(2),
                                    ])
                            ])
                            ->visible(fn($record) => $record->id_produk !== null),
                    ])
                    ->columns(2),

                Components\Section::make('Durasi Slider')
                    ->schema([
                        Components\TextEntry::make('durasi_slider')
                            ->label('Durasi (detik)')
                            ->numeric()
                            ->suffix(' detik')
                            ->helperText('Durasi tampilan setiap konten di slider'),
                    ])
                    ->columns(1)
                    ->icon('heroicon-s-clock')
                    ->collapsible(),

                Components\Section::make('Informasi Waktu')
                    ->schema([
                        Components\TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d M Y H:i'),

                        Components\TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->formatStateUsing(function ($record) {
                        $imagePath = null;

                        // Prioritas: artikel -> galeri -> event -> produk
                        if ($record->artikel && $record->artikel->thumbnail_artikel) {
                            $imagePath = is_array($record->artikel->thumbnail_artikel)
                                ? $record->artikel->thumbnail_artikel[0]
                                : $record->artikel->thumbnail_artikel;
                        } elseif ($record->galeri && $record->galeri->thumbnail_galeri) {
                            $imagePath = is_array($record->galeri->thumbnail_galeri)
                                ? $record->galeri->thumbnail_galeri[0]
                                : $record->galeri->thumbnail_galeri;
                        } elseif ($record->event && $record->event->thumbnail_event) {
                            $imagePath = is_array($record->event->thumbnail_event)
                                ? $record->event->thumbnail_event[0]
                                : $record->event->thumbnail_event;
                        } elseif ($record->produk && $record->produk->thumbnail_produk) {
                            $imagePath = is_array($record->produk->thumbnail_produk)
                                ? $record->produk->thumbnail_produk[0]
                                : $record->produk->thumbnail_produk;
                        }

                        if ($imagePath) {
                            $thumbnailUrl = route('thumbnail', [
                                'path' => base64_encode($imagePath),
                                'w' => 60,
                                'h' => 60,
                                'q' => 80
                            ]);
                            return '<img src="' . $thumbnailUrl . '" class="w-15 h-15 object-cover rounded-lg" loading="lazy" decoding="async" />';
                        }

                        return '<div class="w-15 h-15 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 text-xs">No Image</div>';
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('konten_info')
                    ->label('Konten')
                    ->getStateUsing(function ($record) {
                        if ($record->artikel) {
                            return 'Artikel: ' . $record->artikel->judul_artikel;
                        }
                        if ($record->galeri) {
                            return 'Galeri: ' . $record->galeri->judul_galeri;
                        }
                        if ($record->event) {
                            return 'Event: ' . $record->event->nama_event;
                        }
                        if ($record->produk) {
                            return 'Produk: ' . $record->produk->nama_produk;
                        }
                        return 'Tidak ada konten';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->orWhereHas('artikel', fn($q) => $q->where('judul_artikel', 'like', "%{$search}%"))
                            ->orWhereHas('galeri', fn($q) => $q->where('judul_galeri', 'like', "%{$search}%"))
                            ->orWhereHas('event', fn($q) => $q->where('nama_event', 'like', "%{$search}%"))
                            ->orWhereHas('produk', fn($q) => $q->where('nama_produk', 'like', "%{$search}%"));
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('tipe_konten')
                    ->label('Tipe')
                    ->getStateUsing(function ($record) {
                        if ($record->artikel)
                            return 'Artikel';
                        if ($record->galeri)
                            return 'Galeri';
                        if ($record->event)
                            return 'Event';
                        if ($record->produk)
                            return 'Produk';
                        return 'Kosong';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Artikel' => 'info',
                        'Galeri' => 'success',
                        'Event' => 'warning',
                        'Produk' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('durasi_slider')
                    ->label('Durasi')
                    ->suffix(' detik')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('konten_type')
                    ->label('Tipe Konten')
                    ->options([
                        'artikel' => 'Artikel',
                        'galeri' => 'Galeri',
                        'event' => 'Event',
                        'produk' => 'Produk',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'artikel' => $query->whereNotNull('id_artikel'),
                            'galeri' => $query->whereNotNull('id_galeri'),
                            'event' => $query->whereNotNull('id_event'),
                            'produk' => $query->whereNotNull('id_produk'),
                            'lowongan' => $query->whereNotNull('id_lowongan'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canDelete(Model $record): bool
    {
        return false;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ViewKontenSlider::route('/'),
            'edit' => Pages\EditKontenSlider::route('/{record}/edit'),
        ];
    }
}

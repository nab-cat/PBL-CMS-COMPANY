<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use App\Models\TestimoniSlider;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\ContentStatus;
use App\Models\TestimoniEvent;
use App\Models\TestimoniProduk;
use App\Models\TestimoniArtikel;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\TestimoniLowongan;
use App\Helpers\FilamentGroupingHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Services\FileHandlers\MultipleFileHandler;
use App\Filament\Resources\TestimoniSliderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TestimoniSliderResource\RelationManagers;

class TestimoniSliderResource extends Resource
{
    protected static ?string $model = TestimoniSlider::class;
    protected static ?string $navigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';
    protected static ?string $navigationLabel = 'Testimoni Slider';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Customer Service');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pilih Testimoni')
                    ->schema([
                        Forms\Components\Select::make('id_testimoni_produk')
                            ->label('Testimoni Produk')
                            ->relationship('testimoniProduk', 'isi_testimoni')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $userPhoto = $record->user && $record->user->foto_profil
                                    ? asset('storage/' . $record->user->foto_profil)
                                    : null;

                                $userName = $record->user ? $record->user->name : 'User Anonim';
                                $testimoniText = \Illuminate\Support\Str::limit($record->isi_testimoni, 50);

                                if ($userPhoto) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-3">' .
                                            '<img src="' . $userPhoto . '" class="w-10 h-10 rounded-full object-cover flex-shrink-0" />' .
                                            '<div class="flex flex-col">' .
                                            '<span class="font-medium">' . e($userName) . '</span>' .
                                            '<span class="text-sm text-gray-500 truncate">' . e($testimoniText) . '</span>' .
                                            '</div>' .
                                            '</div>'
                                    );
                                }

                                return $userName . ' - ' . $testimoniText;
                            })
                            ->allowHtml()
                            ->searchable(['isi_testimoni', 'user.name'])
                            ->preload()
                            ->native(false),

                        Forms\Components\Select::make('id_testimoni_lowongan')
                            ->label('Testimoni Lowongan')
                            ->relationship('testimoniLowongan', 'isi_testimoni')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $userPhoto = $record->user && $record->user->foto_profil
                                    ? asset('storage/' . $record->user->foto_profil)
                                    : null;

                                $userName = $record->user ? $record->user->name : 'User Anonim';
                                $testimoniText = \Illuminate\Support\Str::limit($record->isi_testimoni, 50);

                                if ($userPhoto) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-3">' .
                                            '<img src="' . $userPhoto . '" class="w-10 h-10 rounded-full object-cover flex-shrink-0" />' .
                                            '<div class="flex flex-col">' .
                                            '<span class="font-medium">' . e($userName) . '</span>' .
                                            '<span class="text-sm text-gray-500 truncate">' . e($testimoniText) . '</span>' .
                                            '</div>' .
                                            '</div>'
                                    );
                                }

                                return $userName . ' - ' . $testimoniText;
                            })
                            ->allowHtml()
                            ->searchable(['isi_testimoni', 'user.name'])
                            ->preload()
                            ->native(false),

                        Forms\Components\Select::make('id_testimoni_event')
                            ->label('Testimoni Event')
                            ->relationship('testimoniEvent', 'isi_testimoni')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $userPhoto = $record->user && $record->user->foto_profil
                                    ? asset('storage/' . $record->user->foto_profil)
                                    : null;

                                $userName = $record->user ? $record->user->name : 'User Anonim';
                                $testimoniText = \Illuminate\Support\Str::limit($record->isi_testimoni, 50);

                                if ($userPhoto) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-3">' .
                                            '<img src="' . $userPhoto . '" class="w-10 h-10 rounded-full object-cover flex-shrink-0" />' .
                                            '<div class="flex flex-col">' .
                                            '<span class="font-medium">' . e($userName) . '</span>' .
                                            '<span class="text-sm text-gray-500 truncate">' . e($testimoniText) . '</span>' .
                                            '</div>' .
                                            '</div>'
                                    );
                                }

                                return $userName . ' - ' . $testimoniText;
                            })
                            ->allowHtml()
                            ->searchable(['isi_testimoni', 'user.name'])
                            ->preload()
                            ->native(false),

                        Forms\Components\Select::make('id_testimoni_artikel')
                            ->label('Testimoni Artikel')
                            ->relationship('testimoniArtikel', 'isi_testimoni')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $userPhoto = $record->user && $record->user->foto_profil
                                    ? asset('storage/' . $record->user->foto_profil)
                                    : null;

                                $userName = $record->user ? $record->user->name : 'User Anonim';
                                $testimoniText = \Illuminate\Support\Str::limit($record->isi_testimoni, 50);

                                if ($userPhoto) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-3">' .
                                            '<img src="' . $userPhoto . '" class="w-10 h-10 rounded-full object-cover flex-shrink-0" />' .
                                            '<div class="flex flex-col">' .
                                            '<span class="font-medium">' . e($userName) . '</span>' .
                                            '<span class="text-sm text-gray-500 truncate">' . e($testimoniText) . '</span>' .
                                            '</div>' .
                                            '</div>'
                                    );
                                }

                                return $userName . ' - ' . $testimoniText;
                            })
                            ->allowHtml()
                            ->searchable(['isi_testimoni', 'user.name'])
                            ->preload()
                            ->native(false),
                    ])
                    ->description('Pilih salah satu TestimoniSlider dari kategori yang tersedia'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                Infolists\Components\Section::make('TestimoniSlider Slider')
                    ->description('TestimoniSlider yang akan ditampilkan di homepage')
                    ->icon('heroicon-s-chat-bubble-bottom-center-text')
                    ->compact()
                    ->columns(1)
                    ->schema([
                        // Detail TestimoniSlider Produk
                        Infolists\Components\Card::make()
                            ->schema([
                                Infolists\Components\Grid::make([
                                    'sm' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        Infolists\Components\ImageEntry::make('testimonial_photo')
                                            ->label('')
                                            ->getStateUsing(function (TestimoniSlider $record): ?string {
                                                return $record->testimoniProduk && $record->testimoniProduk->user && $record->testimoniProduk->user->foto_profil
                                                    ? $record->testimoniProduk->user->foto_profil
                                                    : null;
                                            })
                                            ->circular()
                                            ->size(120)
                                            ->disk('public')
                                            ->columnSpan(1),

                                        Infolists\Components\Grid::make(1)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('testimonial_title')
                                                    ->label('TestimoniSlider Produk')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        return $record->testimoniProduk && $record->testimoniProduk->user
                                                            ? $record->testimoniProduk->user->name
                                                            : 'User Anonim';
                                                    })
                                                    ->weight('bold')
                                                    ->size('lg')
                                                    ->color('primary'),

                                                Infolists\Components\TextEntry::make('testimonial_content')
                                                    ->label('Isi TestimoniSlider')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        return $record->testimoniProduk ? ($record->testimoniProduk->isi_testimoni ?? 'Tidak ada konten') : 'Tidak ada konten';
                                                    })
                                                    ->html()
                                                    ->prose(),

                                                Infolists\Components\TextEntry::make('testimonial_rating')
                                                    ->label('Rating')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        if ($record->testimoniProduk && isset($record->testimoniProduk->rating)) {
                                                            return str_repeat('⭐', $record->testimoniProduk->rating) . " ({$record->testimoniProduk->rating}/5)";
                                                        }
                                                        return 'Tidak ada rating';
                                                    })
                                                    ->visible(fn(TestimoniSlider $record): bool => $record->testimoniProduk && isset($record->testimoniProduk->rating)),

                                                Infolists\Components\Grid::make(2)
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('testimoniProduk.produk.nama_produk')
                                                            ->label('Nama Produk')
                                                            ->icon('heroicon-o-shopping-bag')
                                                            ->badge()
                                                            ->color('success'),

                                                        Infolists\Components\TextEntry::make('testimoniProduk.produk.harga_produk')
                                                            ->label('Harga Produk')
                                                            ->money('IDR')
                                                            ->icon('heroicon-o-ticket')
                                                            ->badge()
                                                            ->color('warning'),
                                                    ]),
                                            ])
                                            ->columnSpan(2),
                                    ]),
                            ])
                            ->visible(fn(TestimoniSlider $record): bool => !is_null($record->id_testimoni_produk)),

                        // Detail TestimoniSlider Lowongan
                        Infolists\Components\Card::make()
                            ->schema([
                                Infolists\Components\Grid::make([
                                    'sm' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        Infolists\Components\ImageEntry::make('testimonial_photo')
                                            ->label('')
                                            ->getStateUsing(function (TestimoniSlider $record): ?string {
                                                return $record->testimoniLowongan && $record->testimoniLowongan->user && $record->testimoniLowongan->user->foto_profil
                                                    ? $record->testimoniLowongan->user->foto_profil
                                                    : null;
                                            })
                                            ->circular()
                                            ->size(120)
                                            ->disk('public')
                                            ->columnSpan(1),

                                        Infolists\Components\Grid::make(1)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('testimonial_title')
                                                    ->label('TestimoniSlider Lowongan')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        return $record->testimoniLowongan && $record->testimoniLowongan->user
                                                            ? $record->testimoniLowongan->user->name
                                                            : 'User Anonim';
                                                    })
                                                    ->weight('bold')
                                                    ->size('lg')
                                                    ->color('info'),

                                                Infolists\Components\TextEntry::make('testimonial_content')
                                                    ->label('Isi TestimoniSlider')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        return $record->testimoniLowongan ? ($record->testimoniLowongan->isi_testimoni ?? 'Tidak ada konten') : 'Tidak ada konten';
                                                    })
                                                    ->html()
                                                    ->prose(),

                                                Infolists\Components\TextEntry::make('testimonial_rating')
                                                    ->label('Rating')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        if ($record->testimoniLowongan && isset($record->testimoniLowongan->rating)) {
                                                            return str_repeat('⭐', $record->testimoniLowongan->rating) . " ({$record->testimoniLowongan->rating}/5)";
                                                        }
                                                        return 'Tidak ada rating';
                                                    })
                                                    ->visible(fn(TestimoniSlider $record): bool => $record->testimoniLowongan && isset($record->testimoniLowongan->rating)),

                                                Infolists\Components\Grid::make(2)
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('testimoniLowongan.lowongan.judul_lowongan')
                                                            ->label('Posisi Lowongan')
                                                            ->icon('heroicon-o-briefcase')
                                                            ->badge()
                                                            ->color('info'),
                                                        Infolists\Components\TextEntry::make('testimoniLowongan.lowongan.jenis_lowongan')
                                                            ->label('Jenis Lowongan')
                                                            ->icon('heroicon-o-tag')
                                                            ->badge()
                                                            ->color('gray'),
                                                    ]),
                                            ])
                                            ->columnSpan(2),
                                    ]),
                            ])
                            ->visible(fn(TestimoniSlider $record): bool => !is_null($record->id_testimoni_lowongan)),

                        // Detail TestimoniSlider Event
                        Infolists\Components\Card::make()
                            ->schema([
                                Infolists\Components\Grid::make([
                                    'sm' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        Infolists\Components\ImageEntry::make('testimonial_photo')
                                            ->label('')
                                            ->getStateUsing(function (TestimoniSlider $record): ?string {
                                                return $record->testimoniEvent && $record->testimoniEvent->user && $record->testimoniEvent->user->foto_profil
                                                    ? $record->testimoniEvent->user->foto_profil
                                                    : null;
                                            })
                                            ->circular()
                                            ->size(120)
                                            ->disk('public')
                                            ->columnSpan(1),

                                        Infolists\Components\Grid::make(1)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('testimonial_title')
                                                    ->label('TestimoniSlider Event')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        return $record->testimoniEvent && $record->testimoniEvent->user
                                                            ? $record->testimoniEvent->user->name
                                                            : 'User Anonim';
                                                    })
                                                    ->weight('bold')
                                                    ->size('lg')
                                                    ->color('warning'),

                                                Infolists\Components\TextEntry::make('testimonial_content')
                                                    ->label('Isi TestimoniSlider')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        return $record->testimoniEvent ? ($record->testimoniEvent->isi_testimoni ?? 'Tidak ada konten') : 'Tidak ada konten';
                                                    })
                                                    ->html()
                                                    ->prose(),

                                                Infolists\Components\TextEntry::make('testimonial_rating')
                                                    ->label('Rating')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        if ($record->testimoniEvent && isset($record->testimoniEvent->rating)) {
                                                            return str_repeat('⭐', $record->testimoniEvent->rating) . " ({$record->testimoniEvent->rating}/5)";
                                                        }
                                                        return 'Tidak ada rating';
                                                    })
                                                    ->visible(fn(TestimoniSlider $record): bool => $record->testimoniEvent && isset($record->testimoniEvent->rating)),

                                                Infolists\Components\TextEntry::make('testimoniEvent.event.nama_event')
                                                    ->label('Nama Event')
                                                    ->icon('heroicon-o-calendar-days')
                                                    ->badge()
                                                    ->color('warning'),
                                            ])
                                            ->columnSpan(2),
                                    ]),
                            ])
                            ->visible(fn(TestimoniSlider $record): bool => !is_null($record->id_testimoni_event)),

                        // Detail TestimoniSlider Artikel
                        Infolists\Components\Card::make()
                            ->schema([
                                Infolists\Components\Grid::make([
                                    'sm' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        Infolists\Components\ImageEntry::make('testimonial_photo')
                                            ->label('')
                                            ->getStateUsing(function (TestimoniSlider $record): ?string {
                                                return $record->testimoniArtikel && $record->testimoniArtikel->user && $record->testimoniArtikel->user->foto_profil
                                                    ? $record->testimoniArtikel->user->foto_profil
                                                    : null;
                                            })
                                            ->circular()
                                            ->size(120)
                                            ->disk('public')
                                            ->columnSpan(1),

                                        Infolists\Components\Grid::make(1)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('testimonial_title')
                                                    ->label('TestimoniSlider Artikel')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        return $record->testimoniArtikel && $record->testimoniArtikel->user
                                                            ? $record->testimoniArtikel->user->name
                                                            : 'User Anonim';
                                                    })
                                                    ->weight('bold')
                                                    ->size('lg')
                                                    ->color('primary'),

                                                Infolists\Components\TextEntry::make('testimonial_content')
                                                    ->label('Isi TestimoniSlider')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        return $record->testimoniArtikel ? ($record->testimoniArtikel->isi_testimoni ?? 'Tidak ada konten') : 'Tidak ada konten';
                                                    })
                                                    ->html()
                                                    ->prose(),

                                                Infolists\Components\TextEntry::make('testimonial_rating')
                                                    ->label('Rating')
                                                    ->getStateUsing(function (TestimoniSlider $record): string {
                                                        if ($record->testimoniArtikel && isset($record->testimoniArtikel->rating)) {
                                                            return str_repeat('⭐', $record->testimoniArtikel->rating) . " ({$record->testimoniArtikel->rating}/5)";
                                                        }
                                                        return 'Tidak ada rating';
                                                    })
                                                    ->visible(fn(TestimoniSlider $record): bool => $record->testimoniArtikel && isset($record->testimoniArtikel->rating)),

                                                Infolists\Components\TextEntry::make('testimoniArtikel.artikel.judul_artikel')
                                                    ->label('Judul Artikel')
                                                    ->icon('heroicon-o-document-text')
                                                    ->badge()
                                                    ->color('primary'),
                                            ])
                                            ->columnSpan(2),
                                    ]),
                            ])
                            ->visible(fn(TestimoniSlider $record): bool => !is_null($record->id_testimoni_artikel)),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Informasi Waktu')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d M Y H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
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
                // Tidak ada kolom karena kita tidak menggunakan table view
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
            'index' => Pages\ViewTestimoni::route('/{record?}'),
            'edit' => Pages\EditTestimoni::route('/{record}/edit'),
        ];
    }
}

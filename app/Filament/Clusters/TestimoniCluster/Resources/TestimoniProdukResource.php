<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources;

use App\Filament\Clusters\TestimoniCluster;
use App\Models\TestimoniProduk;
use App\Models\Produk;
use App\Models\User;
use App\Enums\ContentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Components\Tab;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniProdukResource\Pages;
use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniProdukResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;

class TestimoniProdukResource extends Resource
{
    protected static ?string $model = TestimoniProduk::class;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-bag';

    protected static ?string $navigationLabel = 'Testimoni Produk';
    protected static ?string $slug = 'testimoni-produk';

    protected static ?string $cluster = TestimoniCluster::class;

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        // Hilangkan form atau return empty form
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Testimoni')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User')
                            ->icon('heroicon-o-user'),

                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email User')
                            ->icon('heroicon-o-envelope'),

                        Infolists\Components\TextEntry::make('produk.nama_produk')
                            ->label('Produk')
                            ->icon('heroicon-o-shopping-bag'),

                        Infolists\Components\TextEntry::make('produk.kategori_produk.nama_kategori_produk')
                            ->label('Kategori Produk')
                            ->icon('heroicon-o-tag'),

                        Infolists\Components\TextEntry::make('isi_testimoni')
                            ->label('Isi Testimoni')
                            ->columnSpanFull()
                            ->icon('heroicon-o-chat-bubble-left-right'),

                        Infolists\Components\TextEntry::make('rating')
                            ->label('Rating')
                            ->formatStateUsing(fn(string $state): string => str_repeat('⭐', (int) $state))
                            ->icon('heroicon-o-star'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'terpublikasi' => 'success',
                                'tidak terpublikasi' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'terpublikasi' => 'Terpublikasi',
                                'tidak terpublikasi' => 'Tidak Terpublikasi',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-calendar'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('produk.nama_produk')
                    ->label('Produk')
                    ->limit(30)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('isi_testimoni')
                    ->label('Testimoni')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn(string $state): string => str_repeat('⭐', (int) $state))
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        ContentStatus::TERPUBLIKASI->value => ContentStatus::TERPUBLIKASI->getLabel(),
                        ContentStatus::TIDAK_TERPUBLIKASI->value => ContentStatus::TIDAK_TERPUBLIKASI->getLabel(),
                    ])
                    ->disabled(fn() => !auth()->user()->can('update_testimoni::produk', TestimoniProduk::class))
                    ->rules(['required']),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'tidak terpublikasi' => 'Tidak Terpublikasi',
                        'terpublikasi' => 'Terpublikasi',
                    ]),

                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 ⭐',
                        2 => '2 ⭐⭐',
                        3 => '3 ⭐⭐⭐',
                        4 => '4 ⭐⭐⭐⭐',
                        5 => '5 ⭐⭐⭐⭐⭐',
                    ]),

                Tables\Filters\SelectFilter::make('produk')
                    ->relationship('produk', 'nama_produk')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()->can('delete_testimoni::produk', TestimoniProduk::class)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->can('delete_any_testimoni::produk')),

                    Tables\Actions\BulkAction::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'terpublikasi']);
                            });
                        })
                        ->requiresConfirmation()
                        ->visible(fn() => auth()->user()->can('update_testimoni::produk', TestimoniProduk::class)),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'tidak terpublikasi']);
                            });
                        })
                        ->requiresConfirmation()
                        ->visible(fn() => auth()->user()->can('update_testimoni::produk', TestimoniProduk::class)),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTestimoniProduk::route('/'),
            'view' => Pages\ViewTestimoniProduk::route('/{record}'),
            // Hilangkan 'edit' dan 'create' pages
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_testimoni::produk') ?? false;
    }

    public static function canCreate(): bool
    {
        return false; // Disable create
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Disable edit
    }

    public static function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'terpublikasi' => Tab::make('Terpublikasi')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', ContentStatus::TERPUBLIKASI)),
            'tidak_terpublikasi' => Tab::make('Tidak Terpublikasi')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', ContentStatus::TIDAK_TERPUBLIKASI)),
        ];
    }
}

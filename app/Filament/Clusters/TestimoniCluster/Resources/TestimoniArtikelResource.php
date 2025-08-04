<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Artikel;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ContentStatus;
use App\Models\TestimoniArtikel;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\TestimoniCluster;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniArtikelResource\Pages;
use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniArtikelResource\RelationManagers;

class TestimoniArtikelResource extends Resource
{
    protected static ?string $model = TestimoniArtikel::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationLabel = 'Testimoni Artikel';
    protected static ?string $slug = 'testimoni-artikel';

    protected static ?string $cluster = TestimoniCluster::class;

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Testimoni')
                    ->schema([
                        Forms\Components\Select::make('id_user')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('id_artikel')
                            ->label('Artikel')
                            ->relationship('artikel', 'judul_artikel')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Textarea::make('isi_testimoni')
                            ->label('Isi Testimoni')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255)
                            ->helperText('Maksimal 5 kata')
                            ->disabled()
                            ->rules([
                                'required',
                                function ($attribute, $value, $fail) {
                                    $wordCount = str_word_count($value);
                                    if ($wordCount > 5) {
                                        $fail('Isi testimoni tidak boleh lebih dari 5 kata.');
                                    }
                                },
                            ]),

                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->options([
                                1 => '1 ⭐',
                                2 => '2 ⭐⭐',
                                3 => '3 ⭐⭐⭐',
                                4 => '4 ⭐⭐⭐⭐',
                                5 => '5 ⭐⭐⭐⭐⭐',
                            ])
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'tidak terpublikasi' => 'Tidak Terpublikasi',
                                'terpublikasi' => 'Terpublikasi',
                            ])
                            ->default('tidak terpublikasi')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
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

                        Infolists\Components\TextEntry::make('artikel.judul_artikel')
                            ->label('Artikel')
                            ->icon('heroicon-o-document-text'),

                        Infolists\Components\TextEntry::make('artikel.kategoriArtikel.nama_kategori_artikel')
                            ->label('Kategori Artikel')
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
                Tables\Columns\TextColumn::make('artikel.judul_artikel')
                    ->label('Artikel')
                    ->limit(30)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('isi_testimoni')
                    ->label('Testimoni')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')->formatStateUsing(fn(string $state): string => str_repeat('⭐', (int) $state))
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        ContentStatus::TERPUBLIKASI->value => ContentStatus::TERPUBLIKASI->getLabel(),
                        ContentStatus::TIDAK_TERPUBLIKASI->value => ContentStatus::TIDAK_TERPUBLIKASI->getLabel(),
                    ])
                    ->disabled(fn() => !auth()->user()->can('update_testimoni::artikel', TestimoniArtikel::class))
                    ->rules(['required']),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'terpublikasi']);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'tidak terpublikasi']);
                            });
                        })
                        ->requiresConfirmation(),
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
            'index' => Pages\ListTestimoniArtikel::route('/'),
            'view' => Pages\ViewTestimoniArtikel::route('/{record}'),
            'edit' => Pages\EditTestimoniArtikel::route('/{record}/edit'),
        ];
    }
    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_testimoni::artikel') ?? false;
    }
    public static function canCreate(): bool
    {
        return false;
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

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MitraResource\Pages;
use App\Filament\Resources\MitraResource\RelationManagers;
use App\Models\Mitra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use App\Services\FileHandlers\SingleFileHandler;
use App\Helpers\FilamentGroupingHelper;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;


class MitraResource extends Resource
{
    protected static ?string $model = Mitra::class;
    protected static ?string $navigationIcon = 'heroicon-s-user-plus';
    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Company Owner');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Mitra')
                    ->icon('heroicon-s-information-circle')
                    ->description('Isi informasi dasar mengenai mitra atau perusahaan yang bekerja sama.')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Mitra/Perusahaan')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Masukkan nama mitra perusahaan'),

                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Perusahaan')
                            ->image()
                            ->directory('mitra-logos')
                            ->disk('public')
                            ->helperText('Upload logo perusahaan (format: jpg, png, svg)')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(100)
                            ->imageResizeTargetHeight(100)
                            ->optimize('webp'),

                        Forms\Components\Textarea::make('alamat_mitra')
                            ->label('Alamat Mitra')
                            ->rows(3)
                            ->maxLength(200)
                            ->placeholder('Masukkan alamat lengkap mitra'),

                        Forms\Components\DatePicker::make('tanggal_kemitraan')
                            ->label('Tanggal Kemitraan')
                            ->displayFormat('d F Y')
                            ->default(now())
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Status Kemitraan')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->native(false)
                            ->default('aktif')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Dokumen Legal')
                    ->icon('heroicon-s-document-text')
                    ->description('Unggah dokumen legal yang diperlukan untuk kemitraan.')
                    ->schema([
                        Forms\Components\FileUpload::make('dok_siup')
                            ->label('Dokumen SIUP')
                            ->directory('mitra-dokumen/siup')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120) // 5MB
                            ->disk('public')
                            ->downloadable(),

                        Forms\Components\FileUpload::make('dok_npwp')
                            ->label('Dokumen NPWP')
                            ->directory('mitra-dokumen/npwp')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120) // 5MB
                            ->disk('public')
                            ->downloadable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Daftar Mitra')
            ->description('Daftar mitra yang bekerja sama.')
            ->defaultPaginationPageOption('all')
            ->paginationPageOptions(['all'])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('logo')
                        ->height('100%')
                        ->width('100%')
                        ->defaultImageUrl(url('/image/placeholder.webp'))
                        ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('nama')
                            ->label('Nama Mitra')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('alamat_mitra')
                            ->label('Alamat')
                            ->color('gray')
                            ->limit(50)
                            ->tooltip(fn(Mitra $record): string => $record->alamat_mitra ?? ''),
                    ]),
                ])->space(3),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('status')
                            ->badge()
                            ->colors([
                                'success' => 'aktif',
                                'danger' => 'nonaktif',
                            ])
                            ->grow(false),
                        Tables\Columns\TextColumn::make('tanggal_kemitraan')
                            ->label('Kemitraan sejak')
                            ->date('d M Y')
                            ->color('gray')
                            ->prefix('Sejak: '),
                    ]),
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('documents_status')
                                ->label('Dokumen')
                                ->formatStateUsing(function (Mitra $record): string {
                                    $documents = [];
                                    if ($record->dok_siup) $documents[] = 'SIUP';
                                    if ($record->dok_npwp) $documents[] = 'NPWP';

                                    return !empty($documents)
                                        ? 'Dokumen: ' . implode(', ', $documents)
                                        : 'Belum ada dokumen';
                                })
                                ->color(function (Mitra $record): string {
                                    return ($record->dok_siup && $record->dok_npwp) ? 'success' : 'warning';
                                })
                                ->icon(function (Mitra $record): string {
                                    return ($record->dok_siup && $record->dok_npwp)
                                        ? 'heroicon-o-document-check'
                                        : 'heroicon-o-document-text';
                                }),
                        ])->grow(true),
                    ]),
                ])->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),

                Tables\Filters\Filter::make('has_documents')
                    ->label('Dengan Dokumen Lengkap')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('dok_siup')->whereNotNull('dok_npwp')),

                Tables\Filters\Filter::make('recent_partners')
                    ->label('Mitra Baru')
                    ->query(fn(Builder $query): Builder => $query->where('tanggal_kemitraan', '>=', now()->subMonths(3))),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([
                12,
                24,
                48,
                'all',
            ])
            ->actions([
                Tables\Actions\Action::make('toggleStatus')
                    ->label(
                        fn(Mitra $record): string =>
                        $record->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'
                    )
                    ->icon(
                        fn(Mitra $record): string =>
                        $record->status === 'aktif' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'
                    )
                    ->color(
                        fn(Mitra $record): string =>
                        $record->status === 'aktif' ? 'danger' : 'success'
                    )
                    ->action(function (Mitra $record): void {
                        $record->status = $record->status === 'aktif' ? 'nonaktif' : 'aktif';
                        $record->save();

                        Notification::make()
                            ->title('Status mitra berhasil diperbarui')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMitras::route('/'),
            'create' => Pages\CreateMitra::route('/create'),
            'edit' => Pages\EditMitra::route('/{record}/edit'),
        ];
    }
}

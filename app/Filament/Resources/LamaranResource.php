<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Lamaran;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Helpers\FilamentGroupingHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use App\Services\FileHandlers\SingleFileHandler;
use App\Filament\Resources\LamaranResource\Pages;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LamaranResource\RelationManagers;
use App\Filament\Resources\LamaranResource\Widgets\LamaranStats;

class LamaranResource extends Resource
{
    protected static ?string $model = Lamaran::class;
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document';
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Customer Service');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelamar')
                    ->schema([
                        Forms\Components\Select::make('id_user')
                            ->label('Pelamar')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->disabled(),


                        Forms\Components\Select::make('id_lowongan')
                            ->label('Lowongan')
                            ->relationship('lowongan', 'judul_lowongan')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Dokumen Pelamar')
                    ->schema([
                        Forms\Components\FileUpload::make('surat_lamaran')
                            ->label('Surat Lamaran')
                            ->directory('lamaran-surat')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->downloadable()
                            ->disabled(),

                        Forms\Components\FileUpload::make('cv')
                            ->label('Curriculum Vitae (CV)')
                            ->directory('lamaran-cv')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->downloadable()
                            ->disabled(),

                        Forms\Components\FileUpload::make('portfolio')
                            ->label('Portfolio')
                            ->directory('lamaran-portfolio')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'])
                            ->maxSize(10240)
                            ->disk('public')
                            ->downloadable()
                            ->disabled(),

                        Forms\Components\Textarea::make('pesan_pelamar')
                            ->label('Pesan Lamaran')
                            ->maxLength(500)
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Status Lamaran')
                    ->schema([
                        Forms\Components\Select::make('status_lamaran')
                            ->label('Status')
                            ->options([
                                'Diproses' => 'Diproses',
                                'Diterima' => 'Diterima',
                                'Ditolak' => 'Ditolak',
                            ])
                            ->native(false)
                            ->default('Diproses')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelamar')
                    ->searchable(),

                Tables\Columns\TextColumn::make('lowongan.judul_lowongan')
                    ->label('Lowongan')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\IconColumn::make('surat_lamaran')
                    ->label('Surat Lamaran')
                    ->boolean()
                    ->trueIcon('heroicon-s-document')
                    ->falseIcon('heroicon-s-x-circle')
                    ->state(fn(Lamaran $record): bool => !empty($record->surat_lamaran)),

                Tables\Columns\IconColumn::make('cv')
                    ->label('CV')
                    ->boolean()
                    ->trueIcon('heroicon-s-document')
                    ->falseIcon('heroicon-s-x-circle')
                    ->state(fn(Lamaran $record): bool => !empty($record->cv)),

                Tables\Columns\IconColumn::make('portfolio')
                    ->label('Portfolio')
                    ->boolean()
                    ->trueIcon('heroicon-s-document')
                    ->falseIcon('heroicon-s-x-circle')
                    ->state(fn(Lamaran $record): bool => !empty($record->portfolio)),

                Tables\Columns\TextColumn::make('status_lamaran')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'primary' => 'Diproses',
                        'success' => 'Diterima',
                        'danger' => 'Ditolak',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Lamaran')
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
                Tables\Filters\SelectFilter::make('status_lamaran')
                    ->label('Status')
                    ->options([
                        'Diproses' => 'Diproses',
                        'Diterima' => 'Diterima',
                        'Ditolak' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('id_lowongan')
                    ->label('Lowongan')
                    ->relationship('lowongan', 'judul_lowongan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Arsipkan')
                    ->modalHeading('Arsipkan Lamaran')
                    ->icon('heroicon-s-archive-box-arrow-down')
                    ->color('warning')
                    ->successNotificationTitle('Lamaran berhasil diarsipkan'),
                Tables\Actions\RestoreAction::make()
                    ->modalHeading('Pulihkan Lamaran')
                    ->successNotificationTitle('Lamaran berhasil dipulihkan'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->modalHeading('Hapus Permanen Lamaran')
                    ->successNotificationTitle('Lamaran berhasil dihapus permanen')
                    ->before(function ($record) {
                        SingleFileHandler::deleteFile($record, 'surat_lamaran');
                        SingleFileHandler::deleteFile($record, 'cv');
                        SingleFileHandler::deleteFile($record, 'portfolio');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Forms\Components\Select::make('status_lamaran')
                                ->label('Status Baru')
                                ->options([
                                    'Diproses' => 'Diproses',
                                    'Diterima' => 'Diterima',
                                    'Ditolak' => 'Ditolak',
                                ])
                                ->native(false)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'status_lamaran' => $data['status_lamaran'],
                                ]);
                            }
                        }),

                    DeleteBulkAction::make()
                        ->label('Arsipkan')
                        ->color('warning')
                        ->icon('heroicon-s-archive-box-arrow-down')
                        ->successNotificationTitle('Lamaran berhasil diarsipkan'),
                    RestoreBulkAction::make()
                        ->successNotificationTitle('Lamaran berhasil dipulihkan'),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->successNotificationTitle('Lamaran berhasil dihapus permanen')
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                SingleFileHandler::deleteFile($record, 'surat_lamaran');
                                SingleFileHandler::deleteFile($record, 'cv');
                                SingleFileHandler::deleteFile($record, 'portfolio');
                            }
                        }),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            LamaranStats::class,
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLamarans::route('/'),
            'create' => Pages\CreateLamaran::route('/create'),
            'edit' => Pages\EditLamaran::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status_lamaran', 'Diproses')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) 'Sedang Diproses';
    }
}

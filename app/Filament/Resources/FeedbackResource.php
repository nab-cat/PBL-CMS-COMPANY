<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Feedback;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ContentStatus;
use Filament\Resources\Resource;
use App\Helpers\FilamentGroupingHelper;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\FeedbackResource\Pages;
use App\Services\FileHandlers\MultipleFileHandler;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeedbackResource\RelationManagers;
use App\Filament\Resources\FeedbackResource\Widgets\FeedbackStats;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;
    protected static ?string $navigationIcon = 'heroicon-s-chat-bubble-left-right';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Customer Service');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Feedback')
                    ->icon('heroicon-s-information-circle')
                    ->description('Informasi terkait feedback yang diberikan oleh pengguna.')
                    ->schema([
                        Forms\Components\Select::make('id_user')
                            ->label('Pengguna')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->disabled(),

                        Forms\Components\TextInput::make('subjek_feedback')
                            ->label('Subjek')
                            ->required()
                            ->maxLength(200)
                            ->disabled(),

                        Forms\Components\DatePicker::make('created_at')
                            ->label('Tanggal Feedback')
                            ->required()
                            ->default(now())
                            ->displayFormat('d F Y')
                            ->disabled(),

                        Forms\Components\TextInput::make('subjek_feedback')
                            ->label('Subjek')
                            ->required()
                            ->columnSpanFull()
                            ->disabled(),

                        Forms\Components\Textarea::make('isi_feedback')
                            ->label('Isi Feedback')
                            ->required()
                            ->columnSpanFull()
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Tanggapan Admin')
                    ->icon('heroicon-s-chat-bubble-left-right')
                    ->description('Tanggapan dari admin terkait feedback yang diberikan.')
                    ->schema([
                        Forms\Components\TextInput::make('tanggapan_feedback')
                            ->label('Tanggapan')
                            ->placeholder('Masukkan tanggapan untuk feedback ini')
                            ->columnSpanFull(),
                        Forms\Components\ToggleButtons::make('status_feedback')
                            ->label('Status Feedback')
                            ->inline()
                            ->options(ContentStatus::class)
                            ->default(ContentStatus::TIDAK_TERPUBLIKASI)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subjek_feedback')
                    ->label('Subjek Feedback')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\ToggleColumn::make('status_feedback')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->onIcon('heroicon-m-eye')
                    ->offIcon('heroicon-m-eye-slash')
                    ->disabled(fn() => !auth()->user()->can('update_feedback', Feedback::class))
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status_feedback' => $state ? ContentStatus::TERPUBLIKASI : ContentStatus::TIDAK_TERPUBLIKASI
                        ]);
                        return $state;
                    })
                    ->getStateUsing(fn($record) => $record->status_feedback === ContentStatus::TERPUBLIKASI)
                    ->tooltip(fn($record) => $record->status_feedback === ContentStatus::TERPUBLIKASI ? 'Terpublikasi' : 'Tidak Terpublikasi'),


                Tables\Columns\IconColumn::make('tanggapan_feedback')
                    ->label('Ditanggapi')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->state(fn(Feedback $record): bool => !empty($record->tanggapan_feedback)),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_tanggapan')
                    ->label('Status Tanggapan')
                    ->options([
                        'responded' => 'Sudah Ditanggapi',
                        'pending' => 'Belum Ditanggapi',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'responded' => $query->whereNotNull('tanggapan_feedback'),
                            'pending' => $query->whereNull('tanggapan_feedback'),
                            default => $query,
                        };
                    }),

                Tables\Filters\SelectFilter::make('id_user')
                    ->label('Pengguna')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Tanggapi'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->successNotificationTitle('Feedback berhasil dihapus')
                    ->requiresConfirmation()
                    ->hidden(fn() => !auth()->user()->can('delete_feedback', Feedback::class)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Publikasikan Feedback')
                        ->modalDescription('Apakah Anda yakin ingin mempublikasikan feedback yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_feedback' => ContentStatus::TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Feedback berhasil dipublikasikan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_feedback', Feedback::class)),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Batalkan Publikasi')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Publikasi Feedback')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan publikasi feedback yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status_feedback' => ContentStatus::TIDAK_TERPUBLIKASI]);
                            });
                        })
                        ->successNotificationTitle('Publikasi feedback berhasil dibatalkan')
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn() => !auth()->user()->can('update_feedback', Feedback::class)),
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
            FeedbackStats::class,
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
            'index' => Pages\ListFeedback::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::whereNull('tanggapan_feedback')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) 'Belum Ditanggapi';
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use App\Helpers\FilamentGroupingHelper;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-s-user';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('User Management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->maxLength(72)
                            ->visible(fn() => auth()->user()?->hasRole('super_admin'))
                            ->helperText('Kosongkan jika tidak ingin mengubah password. Klik ikon mata untuk melihat password.'),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->native(false)
                            ->searchable(),
                    ]),

                Forms\Components\Section::make('Data Pribadi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_profil')
                            ->label('Foto Profil')
                            ->image()
                            ->directory('profile-photos')
                            ->disk('public')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(100)
                            ->imageResizeTargetHeight(100)
                            ->optimize('webp'),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->maxLength(200),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->tel()
                            ->prefix('+62')
                            ->maxLength(15),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->numeric()
                            ->length(16),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->displayFormat('d F Y')
                            ->native(false)
                            ->maxDate(now()),
                    ])
                    ->visible(fn() => !auth()->user()?->hasRole('super_admin'))
                    ->description('Data pribadi hanya dapat diubah oleh user yang bersangkutan'),

                Forms\Components\Section::make('Informasi Kepegawaian')
                    ->schema([
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Tanggal Registrasi')
                            ->required()
                            ->displayFormat('d F Y')
                            ->native(false)
                            ->default(now()),
                        Forms\Components\Select::make('status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options([
                                'Tetap' => 'Pegawai Tetap',
                                'Kontrak' => 'Pegawai Kontrak',
                                'Magang' => 'Pegawai Magang',
                                'Percobaan' => 'Masa Percobaan',
                                'Non Pegawai' => 'Non Pegawai',
                            ])
                            ->native(false)
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->label('Status Akun')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->default('aktif')
                            ->native(false)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('foto_profil')
                    ->label('Foto')
                    ->formatStateUsing(function ($record) {
                        if ($record->foto_profil) {
                            $thumbnailUrl = route('thumbnail', [
                                'path' => base64_encode($record->foto_profil),
                                'w' => 50,
                                'h' => 50,
                                'q' => 80
                            ]);
                            return '<img src="' . $thumbnailUrl . '" class="w-12 h-12 rounded-full object-cover" loading="lazy" decoding="async" />';
                        }

                        return '<div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-xs">No Photo</div>';
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('Nomor HP')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->date('d F Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Registrasi')
                    ->date('d F Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->badge()
                    ->alignment('center')
                    ->colors([
                        'primary' => 'Tetap',
                        'secondary' => 'Non Pegawai',
                        'success' => 'Kontrak',
                        'warning' => 'Magang',
                        'danger' => 'Percobaan',
                    ]),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status Akun')
                    ->badge()
                    ->alignment('center')
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Registrasi')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kepegawaian')
                    ->options([
                        'Tetap' => 'Pegawai Tetap',
                        'Kontrak' => 'Pegawai Kontrak',
                        'Magang' => 'Pegawai Magang',
                        'Percobaan' => 'Masa Percobaan',
                    ])
                    ->label('Status Kepegawaian'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn(User $record) => auth()->user()?->hasRole('super_admin') || auth()->user()?->id_user === $record->id_user),
                Tables\Actions\EditAction::make()
                    ->visible(fn(User $record) => auth()->user()?->hasRole('super_admin') || auth()->user()?->id_user === $record->id_user),
                Tables\Actions\Action::make('toggleStatus')
                    ->label(fn(User $record) => $record->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn(User $record) => $record->status === 'aktif' ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                    ->color(fn(User $record) => $record->status === 'aktif' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->visible(fn(User $record) => auth()->user()?->hasRole('super_admin'))
                    ->action(function (User $record) {
                        $record->status = $record->status === 'aktif' ? 'nonaktif' : 'aktif';
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('toggleStatusBulk')
                        ->label('Aktifkan/Nonaktifkan')
                        ->visible(fn() => auth()->user()?->hasRole('super_admin'))
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->status = $data['status'];
                                $record->save();
                            }
                        })
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'aktif' => 'Aktif',
                                    'nonaktif' => 'Nonaktif',
                                ])
                                ->native(false)
                                ->required(),
                        ]),
                ]),
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
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        // Super admin dapat edit semua user
        if ($user?->hasRole('super_admin')) {
            return true;
        }

        // User hanya dapat edit dirinya sendiri (untuk data pribadi)
        return $user?->id_user === $record->id_user;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

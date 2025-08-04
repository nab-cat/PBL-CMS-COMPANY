<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StrukturOrganisasiResource\Pages;
use App\Filament\Resources\StrukturOrganisasiResource\RelationManagers;
use App\Filament\Resources\StrukturOrganisasiResource\Widgets\StrukturOrganisasiStats;
use App\Models\StrukturOrganisasi;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\FilamentGroupingHelper;

class StrukturOrganisasiResource extends Resource
{
    protected static ?string $model = StrukturOrganisasi::class;
    protected static ?string $navigationIcon = 'heroicon-s-users';
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return FilamentGroupingHelper::getNavigationGroup('Company Owner');
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'user.roles']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Posisi')
                    ->schema([
                        Forms\Components\Select::make('id_user')
                            ->label('Pengguna')
                            ->options(function () {
                                return User::where('status', 'aktif')
                                    ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang'])
                                    ->pluck('name', 'id_user');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->helperText('Pilih pengguna aktif dengan status kepegawaian Tetap, Kontrak, atau Magang')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if (!$state) {
                                    return;
                                }

                                try {
                                    $user = User::with('roles')->find($state);
                                    if ($user && $user->roles && $user->roles->count() > 0) {
                                        // Ambil role pertama dan konversi ke nama jabatan
                                        $roleName = $user->roles->first()->name;
                                        $jabatan = self::convertRoleToJabatan($roleName);
                                        $set('jabatan', $jabatan);
                                    }
                                } catch (\Exception $e) {
                                    // Handle any errors silently or log them
                                }
                            }),

                        Forms\Components\TextInput::make('jabatan')
                            ->label('Posisi/Jabatan')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Jabatan akan diisi otomatis berdasarkan role pengguna. Anda dapat mengedit jika diperlukan'),

                        Forms\Components\TextInput::make('deskripsi')
                            ->label('Deskripsi Posisi/Jabatan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Bertanggung jawab atas pengelolaan perusahaan, dsb'),

                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai Jabatan')
                            ->default(now())
                            ->seconds(false)
                            ->displayFormat('d F Y')
                            ->native(false)
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai Jabatan')
                            ->displayFormat('d F Y')
                            ->native(false)
                            ->afterOrEqual('tanggal_mulai')
                            ->helperText('Kosongkan jika masih aktif')
                            ->validationMessages([
                                'after_or_equal' => 'Tanggal selesai jabatan harus setelah tanggal mulai jabatan',
                            ]),
                        Forms\Components\TextInput::make('urutan')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->helperText('Urutan tampil di struktur organisasi (angka kecil akan tampil di atas). Sistem otomatis mengatur berdasarkan hierarki: Super Admin → Director → Content Management → Customer Service'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('urutan')
                    ->label('Urutan')
                    ->alignCenter()
                    ->badge()
                    ->color(fn($record) => $record->urutan == 0 ? 'danger' : 'primary')
                    ->getStateUsing(
                        fn(StrukturOrganisasi $record): string =>
                        $record->urutan == 0 ? 'Nonaktif' : (string) $record->urutan
                    ),

                Tables\Columns\ImageColumn::make('user.foto_profil')
                    ->label('Foto')
                    ->circular()
                    ->size(50)
                    ->disk('public')
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?name=' . urlencode($record->user?->name ?? 'User') . '&color=7F9CF5&background=EBF4FF';
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Posisi/Jabatan')
                    ->searchable()
                    ->icon('heroicon-s-user')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('user_role')
                    ->label('Role Sistem')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function (StrukturOrganisasi $record): string {
                        $user = $record->user;
                        if (!$user || !$user->roles || $user->roles->isEmpty()) {
                            return 'Tidak ada role';
                        }
                        $roleName = $user->roles->first()->name;
                        return ucwords(str_replace('_', ' ', $roleName));
                    })
                    ->toggledHiddenByDefault()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi Posisi/Jabatan')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn(StrukturOrganisasi $record): string => $record->deskripsi),

                Tables\Columns\TextColumn::make('user_status')
                    ->label('Status Pengguna')
                    ->badge()
                    ->color(fn($record): string => match ($record->user?->status) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                        default => 'gray',
                    })
                    ->getStateUsing(fn(StrukturOrganisasi $record): string => match ($record->user?->status) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        default => 'Tidak ada status',
                    }),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->badge()
                    ->color(fn($record): string => match ($record->user?->status_kepegawaian) {
                        'Tetap' => 'success',
                        'Kontrak' => 'warning',
                        'Magang' => 'info',
                        default => 'gray',
                    })
                    ->getStateUsing(fn(StrukturOrganisasi $record): string => $record->user?->status_kepegawaian ?? 'Tidak ada status'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->defaultSort('urutan', 'asc')
            ->reorderable('urutan')
            ->reorderRecordsTriggerAction(
                fn(\Filament\Tables\Actions\Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Selesai Mengatur Urutan' : 'Atur Urutan')
                    ->color($isReordering ? 'success' : 'primary')
                    ->after(function () {
                        // Ensure inactive users always have urutan 0 after reordering
                        \DB::table('struktur_organisasi')
                            ->join('users', 'struktur_organisasi.id_user', '=', 'users.id_user')
                            ->where('users.status', 'nonaktif')
                            ->update(['struktur_organisasi.urutan' => 0]);

                        // Force clear cache after reordering
                        \App\Observers\StrukturOrganisasiObserver::clearCache();
                        // \Illuminate\Support\Facades\Log::info('Cache cleared after manual reordering and urutan fixed for inactive users');
                    })
            )
            ->filters([
                Tables\Filters\SelectFilter::make('user.status')
                    ->label('Status Pengguna')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),

                Tables\Filters\SelectFilter::make('user.status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->options([
                        'Tetap' => 'Tetap',
                        'Kontrak' => 'Kontrak',
                        'Magang' => 'Magang',
                        'Non Pegawai' => 'Non Pegawai',
                    ]),

                Tables\Filters\SelectFilter::make('role')
                    ->label('Role Pengguna')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'Director' => 'Director',
                        'Content Management' => 'Content Management',
                        'Customer Service' => 'Customer Service',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (filled($data['value'])) {
                            return $query->whereHas('user.roles', function (Builder $query) use ($data) {
                                $query->where('name', $data['value']);
                            });
                        }
                        return $query;
                    }),

                Tables\Filters\Filter::make('tanggal_mulai')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('active_positions')
                    ->label('Posisi Aktif')
                    ->query(fn(Builder $query): Builder => $query
                        ->whereHas('user', function (Builder $query) {
                            $query->where('status', 'aktif')
                                ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang']);
                        })
                        ->where(function (Builder $query) {
                            $query->whereNull('tanggal_selesai')
                                ->orWhere('tanggal_selesai', '>=', now());
                        })),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // No bulk actions - archive and delete disabled
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
            StrukturOrganisasiStats::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStrukturOrganisasis::route('/'),
            'edit' => Pages\EditStrukturOrganisasi::route('/{record}/edit'),
        ];
    }

    /**
     * Konversi nama role ke nama jabatan yang sesuai
     */
    public static function convertRoleToJabatan(string $roleName): string
    {
        return match ($roleName) {
            'super_admin' => 'Super Administrator',
            'Director' => 'Direktur',
            'Content Management' => 'Manager Konten',
            'Customer Service' => 'Customer Service',
            default => ucwords(str_replace('_', ' ', $roleName))
        };
    }
}

<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\FeatureToggle;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeatureToggleResource\Pages;
use App\Filament\Resources\FeatureToggleResource\RelationManagers;
use App\Filament\Resources\FeatureToggleResource\Pages\EditFeatureToggle;
use App\Filament\Resources\FeatureToggleResource\Pages\ListFeatureToggles;
use App\Filament\Resources\FeatureToggleResource\Pages\CreateFeatureToggle;

class FeatureToggleResource extends Resource
{
    protected static ?string $model = FeatureToggle::class;

    protected static ?string $navigationIcon = 'heroicon-s-adjustments-horizontal';
    protected static ?string $label = 'Fitur';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Daftar Fitur')
            ->description('Daftar fitur yang dapat diaktifkan atau dinonaktifkan. Perubahan hanya akan berlaku di halaman website, bukan panel admin.')
            ->defaultPaginationPageOption('all')
            ->paginationPageOptions(['all'])
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Daftar Fitur'),
                Tables\Columns\ToggleColumn::make('status_aktif')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-m-eye')
                    ->offIcon('heroicon-m-eye-slash')
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status_aktif' => $state
                        ]);
                        return $state;
                    })
                    ->getStateUsing(fn($record) => (bool) $record->status_aktif)
                    ->disabled(fn() => !auth()->user()->can('update_feature::toggle', FeatureToggle::class)),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('info')
                    ->label('Info')
                    ->icon('heroicon-m-information-circle')
                    ->color('primary')
                    ->modalHeading(fn($record) => 'Informasi Fitur: ' . $record->label)
                    ->modalDescription(function ($record) {
                        if ($record->key == 'artikel_module') {
                            return 'Module untuk mengelola artikel atau berita. Fitur ini memungkinkan admin untuk membuat, mengedit, dan menghapus artikel yang akan ditampilkan di website.';
                        } elseif ($record->key == 'case_study_module') {
                            return 'Module untuk mengelola studi kasus. Fitur ini digunakan untuk menampilkan portfolio atau project yang telah dikerjakan perusahaan.';
                        } elseif ($record->key == 'event_module') {
                            return 'Module untuk mengelola event atau acara. Fitur ini memungkinkan admin untuk membuat dan mengelola informasi tentang event yang akan diselenggarakan.';
                        } elseif ($record->key == 'feedback_module') {
                            return 'Module untuk mengelola feedback dari pengunjung. Fitur ini memungkinkan admin untuk melihat dan merespon masukan dari pengguna website.';
                        } elseif ($record->key == 'galeri_module') {
                            return 'Module untuk mengelola galeri foto. Fitur ini digunakan untuk menampilkan dokumentasi visual perusahaan.';
                        } elseif ($record->key == 'lamaran_module') {
                            return 'Module untuk mengelola lamaran pekerjaan. Fitur ini memungkinkan admin untuk melihat dan mengelola aplikasi pekerjaan dari calon karyawan.';
                        } elseif ($record->key == 'lowongan_module') {
                            return 'Module untuk mengelola lowongan pekerjaan. Fitur ini digunakan untuk membuat dan mengelola informasi tentang posisi yang tersedia di perusahaan.';
                        } elseif ($record->key == 'mitra_module') {
                            return 'Module untuk mengelola informasi mitra atau partner. Fitur ini digunakan untuk menampilkan daftar perusahaan atau organisasi yang bekerjasama.';
                        } elseif ($record->key == 'produk_module') {
                            return 'Module untuk mengelola produk atau layanan. Fitur ini memungkinkan admin untuk menampilkan dan mengelola katalog produk perusahaan.';
                        } elseif ($record->key == 'testimoni_module') {
                            return 'Module untuk mengelola testimoni pelanggan. Fitur ini digunakan untuk menampilkan review atau ulasan positif dari klien.';
                        } elseif ($record->key == 'unduhan_module') {
                            return 'Module untuk mengelola file download. Fitur ini memungkinkan admin untuk menyediakan file yang dapat diunduh oleh pengunjung website.';
                        } else {
                            return 'Tidak ada deskripsi untuk fitur ini.';
                        }
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->tooltip('Lihat informasi fitur'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('activate_all')
                    ->label('Aktifkan semua')
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update(['status_aktif' => true]);
                        });
                    })
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn() => auth()->user()->can('update_feature::toggle', FeatureToggle::class)),

                Tables\Actions\BulkAction::make('deactivate_all')
                    ->label('Nonaktifkan semua')
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update(['status_aktif' => false]);
                        });
                    })
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn() => auth()->user()->can('update_feature::toggle', FeatureToggle::class)),
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
            'index' => Pages\ListFeatureToggles::route('/'),
            // 'create' => Pages\CreateFeatureToggle::route('/create'),
            // 'edit' => Pages\EditFeatureToggle::route('/{record}/edit'),
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

    public static function canDeleteAny(): bool
    {
        return false;
    }
}

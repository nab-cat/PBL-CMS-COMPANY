<?php

namespace App\Filament\Resources\StrukturOrganisasiResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\StrukturOrganisasiResource;
use App\Filament\Resources\StrukturOrganisasiResource\Widgets\StrukturOrganisasiStats;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ListStrukturOrganisasis extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = StrukturOrganisasiResource::class;

    /**
     * Auto-sync users when page is loaded
     */
    public function mount(): void
    {
        parent::mount();
        $this->autoGenerateUrutanIfNeeded();
        $this->autoSyncUsersToStructure();
    }

    protected function getHeaderActions(): array
    {
        return [
            // Tombol regenerate urutan berdasarkan hierarki role
            Actions\Action::make('regenerate_urutan')
                ->label('Regenerate Urutan')
                ->icon('heroicon-o-arrows-up-down')
                ->action('regenerateUrutan')
                ->color('warning')
                ->tooltip('Regenerate ulang urutan berdasarkan hierarki role')
                ->requiresConfirmation()
                ->modalHeading('Regenerate Urutan Struktur Organisasi')
                ->modalDescription('Apakah Anda yakin ingin regenerate urutan berdasarkan hierarki role? Urutan saat ini akan diganti dengan: Super Admin → Director → Content Management → Customer Service')
                ->modalSubmitActionLabel('Ya, Regenerate'),

            // Tombol refresh data - memperbarui tabel dan force auto-sync
            Actions\Action::make('refresh')
                ->label('Perbarui Data')
                ->icon('heroicon-o-arrow-path')
                ->action('refreshData')
                ->color('gray')
                ->tooltip('Memperbarui data dan sinkronisasi otomatis'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StrukturOrganisasiStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'Posisi Aktif' => Tab::make()
                ->query(fn($query) => $query->whereHas('user', function ($query) {
                    $query->where('status', 'aktif')
                        ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang']);
                })
                    ->where(function ($query) {
                        $query->whereNull('tanggal_selesai')
                            ->orWhere('tanggal_selesai', '>=', now());
                    })
                    ->orderBy('urutan', 'asc'))
                ->icon('heroicon-o-check-circle'),
            'Posisi Nonaktif' => Tab::make()
                ->icon('heroicon-o-x-circle')
                ->query(fn($query) => $query->where(function ($query) {
                    $query->whereHas('user', fn($query) => $query->where('status', 'nonaktif'))
                        ->orWhere('tanggal_selesai', '<', now());
                })
                    ->orderBy('created_at', 'desc')), // Order by created_at for inactive positions
        ];
    }

    /**
     * Refresh data and force auto-sync
     */
    public function refreshData(): void
    {
        try {
            // Clear cache to force fresh sync
            $cacheKey = 'struktur_organisasi_last_sync_check';
            Cache::forget($cacheKey);

            // Force auto-sync
            $this->autoSyncUsersToStructure();

            // Refresh the table data
            $this->resetTable();

            \Filament\Notifications\Notification::make()
                ->title('Data Diperbarui')
                ->body('Data struktur organisasi telah diperbarui dan disinkronisasi.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Gagal memperbarui data: ' . $e->getMessage())
                ->danger()
                ->send();

            Log::error('Failed to refresh struktur organisasi data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Auto-sync users to structure organisasi
     * Uses cache to prevent unnecessary sync operations
     */
    protected function autoSyncUsersToStructure(): void
    {
        $cacheKey = 'struktur_organisasi_last_sync_check';

        try {
            $lastSyncCheck = Cache::get($cacheKey);

            // Only check for sync every 5 minutes to reduce database load
            if ($lastSyncCheck && now()->diffInMinutes($lastSyncCheck) < 5) {
                return;
            }

            $users = \App\Models\User::where('status', 'aktif')
                ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang'])
                ->whereDoesntHave('strukturOrganisasi')
                ->with('roles')
                ->get();

            if ($users->isNotEmpty()) {
                $syncedCount = 0;
                $nextUrutan = \App\Models\StrukturOrganisasi::max('urutan') ?? 0;
                $nextUrutan += 1;

                foreach ($users as $user) {
                    try {
                        $jabatan = $user->getSuggestedJabatan();

                        \App\Models\StrukturOrganisasi::create([
                            'id_user' => $user->id_user,
                            'jabatan' => $jabatan,
                            'deskripsi' => "Bertanggung jawab sebagai {$jabatan}",
                            'tanggal_mulai' => now(),
                            'urutan' => $nextUrutan++,
                        ]);

                        $syncedCount++;
                    } catch (\Exception $e) {
                        Log::error('Failed to sync individual user to struktur organisasi', [
                            'user_id' => $user->id_user,
                            'user_name' => $user->name,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                if ($syncedCount > 0) {
                    Log::info('Auto-sync struktur organisasi completed', [
                        'synced_count' => $syncedCount,
                        'synced_users' => $users->take($syncedCount)->pluck('name')->toArray()
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Sinkronisasi Otomatis')
                        ->body("Berhasil menambahkan {$syncedCount} karyawan baru ke struktur organisasi.")
                        ->success()
                        ->duration(5000)
                        ->send();
                }
            }

            // Update cache timestamp regardless of sync result
            Cache::put($cacheKey, now(), now()->addMinutes(10));
        } catch (\Exception $e) {
            Log::error('Auto-sync struktur organisasi failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Still update cache to prevent repeated failures
            Cache::put($cacheKey, now(), now()->addMinutes(10));
        }
    }

    /**
     * Manual sync method - kept for potential future use or testing
     * But not exposed in UI
     */
    private function syncUsersToStructure()
    {
        $users = \App\Models\User::where('status', 'aktif')
            ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang'])
            ->whereDoesntHave('strukturOrganisasi')
            ->with('roles')
            ->get();

        $syncedCount = 0;
        $nextUrutan = \App\Models\StrukturOrganisasi::max('urutan') + 1;

        foreach ($users as $user) {
            $jabatan = $user->getSuggestedJabatan();

            \App\Models\StrukturOrganisasi::create([
                'id_user' => $user->id_user,
                'jabatan' => $jabatan,
                'deskripsi' => "Bertanggung jawab sebagai {$jabatan}",
                'tanggal_mulai' => now(),
                'urutan' => $nextUrutan++,
            ]);

            $syncedCount++;
        }

        if ($syncedCount > 0) {
            \Filament\Notifications\Notification::make()
                ->title('Sinkronisasi Berhasil')
                ->body("Berhasil menambahkan {$syncedCount} user ke struktur organisasi.")
                ->success()
                ->send();
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Tidak Ada Data Baru')
                ->body('Semua user aktif sudah ada di struktur organisasi.')
                ->info()
                ->send();
        }
    }

    /**
     * Auto-generate urutan based on role hierarchy if all records have urutan 0
     */
    protected function autoGenerateUrutanIfNeeded(): void
    {
        try {
            // Check if all active structure records have urutan 0
            $activeStructures = \App\Models\StrukturOrganisasi::whereHas('user', function ($query) {
                $query->where('status', 'aktif')
                    ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang']);
            })->with(['user', 'user.roles'])->get();

            // If all active records have urutan 0, auto-generate based on hierarchy
            $needsGeneration = $activeStructures->every(fn($so) => $so->urutan == 0);

            if ($needsGeneration && $activeStructures->count() > 0) {
                $this->generateUrutanByRoleHierarchy($activeStructures);

                \Filament\Notifications\Notification::make()
                    ->title('Urutan Otomatis Dibuat')
                    ->body('Urutan struktur organisasi telah di-generate otomatis berdasarkan hierarki role.')
                    ->success()
                    ->duration(5000)
                    ->send();

                Log::info('Auto-generated urutan for struktur organisasi', [
                    'records_updated' => $activeStructures->count()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to auto-generate urutan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Generate urutan based on role hierarchy
     */
    protected function generateUrutanByRoleHierarchy($structures): void
    {
        // Define role hierarchy (lower number = higher priority)
        $roleHierarchy = [
            'super_admin' => 1,
            'Director' => 2,
            'Content Management' => 3,
            'Customer Service' => 4,
        ];

        // Group structures by role
        $groupedByRole = $structures->groupBy(function ($structure) {
            $role = $structure->user->roles->first()?->name;
            return $role ?? 'unknown';
        });

        $currentUrutan = 1;

        // Process each role in hierarchy order
        foreach ($roleHierarchy as $roleName => $priority) {
            if ($groupedByRole->has($roleName)) {
                $roleStructures = $groupedByRole[$roleName];

                // Sort by name for consistent ordering within same role
                $roleStructures = $roleStructures->sortBy('user.name');

                foreach ($roleStructures as $structure) {
                    $structure->update(['urutan' => $currentUrutan]);
                    $currentUrutan++;
                }
            }
        }

        // Handle any unknown roles at the end
        if ($groupedByRole->has('unknown')) {
            $unknownStructures = $groupedByRole['unknown']->sortBy('user.name');
            foreach ($unknownStructures as $structure) {
                $structure->update(['urutan' => $currentUrutan]);
                $currentUrutan++;
            }
        }

        // Clear cache after updating
        \App\Observers\StrukturOrganisasiObserver::clearCache();
    }

    /**
     * Regenerate urutan based on role hierarchy (manual trigger)
     */
    public function regenerateUrutan(): void
    {
        try {
            // Get all active structures
            $activeStructures = \App\Models\StrukturOrganisasi::whereHas('user', function ($query) {
                $query->where('status', 'aktif')
                    ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang']);
            })->with(['user', 'user.roles'])->get();

            if ($activeStructures->count() > 0) {
                $this->generateUrutanByRoleHierarchy($activeStructures);

                // Refresh the table data
                $this->resetTable();

                \Filament\Notifications\Notification::make()
                    ->title('Urutan Berhasil Diregenerasi')
                    ->body("Berhasil mengatur ulang urutan untuk {$activeStructures->count()} posisi berdasarkan hierarki role.")
                    ->success()
                    ->send();

                Log::info('Manual regenerate urutan completed', [
                    'records_updated' => $activeStructures->count()
                ]);
            } else {
                \Filament\Notifications\Notification::make()
                    ->title('Tidak Ada Data')
                    ->body('Tidak ada posisi aktif yang perlu diatur urutannya.')
                    ->info()
                    ->send();
            }
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Gagal meregenerasi urutan: ' . $e->getMessage())
                ->danger()
                ->send();

            Log::error('Failed to regenerate urutan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

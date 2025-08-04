<?php

namespace App\Observers;

use App\Models\User;
use App\Models\StrukturOrganisasi;
use App\Observers\StrukturOrganisasiObserver;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Auto-add eligible users to struktur organisasi.
     */
    public function created(User $user): void
    {
        // Auto-sync eligible new users to struktur organisasi
        if ($this->isEligibleForStrukturOrganisasi($user)) {
            $this->autoAddToStrukturOrganisasi($user);
        }
    }

    /**
     * Handle the User "updated" event.
     * Clear struktur organisasi cache when user data changes.
     */
    public function updated(User $user): void
    {
        $changes = $user->getChanges();

        // Check if user became eligible for struktur organisasi
        if (!$user->strukturOrganisasi()->exists() && $this->isEligibleForStrukturOrganisasi($user)) {
            // Check if status or status_kepegawaian changed to make them eligible
            if (array_key_exists('status', $changes) || array_key_exists('status_kepegawaian', $changes)) {
                $this->autoAddToStrukturOrganisasi($user);
                return;
            }
        }        // Check if this user is part of struktur organisasi
        $hasStrukturOrganisasi = $user->strukturOrganisasi()->exists();

        if ($hasStrukturOrganisasi) {
            Log::info('User with struktur organisasi updated', [
                'user_id' => $user->id_user,
                'name' => $user->name,
                'changes' => array_keys($changes)
            ]);            // Handle status changes - set urutan to 0 for inactive users
            if (array_key_exists('status', $changes)) {
                $this->handleStatusChange($user, $user->status);
            }

            // Clear struktur organisasi cache when user data changes
            StrukturOrganisasiObserver::clearCache();

            // Log specific changes that affect struktur organisasi display
            if (array_key_exists('foto_profil', $changes)) {
                Log::info('User profile photo updated - clearing struktur organisasi cache', [
                    'user_id' => $user->id_user,
                    'old_photo' => $user->getOriginal('foto_profil') ?? 'none',
                    'new_photo' => $user->foto_profil ?? 'none'
                ]);
            }

            if (array_key_exists('name', $changes)) {
                Log::info('User name updated - clearing struktur organisasi cache', [
                    'user_id' => $user->id_user,
                    'old_name' => $user->getOriginal('name'),
                    'new_name' => $user->name
                ]);
            }

            if (array_key_exists('status', $changes) || array_key_exists('status_kepegawaian', $changes)) {
                Log::info('User status updated - clearing struktur organisasi cache', [
                    'user_id' => $user->id_user,
                    'status_changes' => array_intersect_key($changes, array_flip(['status', 'status_kepegawaian']))
                ]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     * Clear struktur organisasi cache when user is deleted.
     */
    public function deleted(User $user): void
    {
        // Check if this user was part of struktur organisasi
        $hadStrukturOrganisasi = $user->strukturOrganisasi()->withTrashed()->exists();

        if ($hadStrukturOrganisasi) {
            Log::info('User with struktur organisasi deleted', [
                'user_id' => $user->id_user,
                'name' => $user->name
            ]);

            StrukturOrganisasiObserver::clearCache();
        }
    }

    /**
     * Handle the User "restored" event.
     * Clear struktur organisasi cache when user is restored.
     */
    public function restored(User $user): void
    {
        $hasStrukturOrganisasi = $user->strukturOrganisasi()->exists();

        if ($hasStrukturOrganisasi) {
            Log::info('User with struktur organisasi restored', [
                'user_id' => $user->id_user,
                'name' => $user->name
            ]);

            StrukturOrganisasiObserver::clearCache();
        }
    }

    /**
     * Check if user is eligible for struktur organisasi
     */
    protected function isEligibleForStrukturOrganisasi(User $user): bool
    {
        return $user->status === 'aktif' &&
            in_array($user->status_kepegawaian, ['Tetap', 'Kontrak', 'Magang']);
    }    /**
         * Auto-add eligible user to struktur organisasi
         */
    protected function autoAddToStrukturOrganisasi(User $user): void
    {
        try {
            if ($user->status === 'nonaktif') {
                $nextUrutan = 0;
            } else {
                $nextUrutan = $this->calculateNextUrutanByRole($user);
            }

            $jabatan = $user->getSuggestedJabatan();

            StrukturOrganisasi::create([
                'id_user' => $user->id_user,
                'jabatan' => $jabatan,
                'deskripsi' => "Bertanggung jawab sebagai {$jabatan}",
                'tanggal_mulai' => now(),
                'urutan' => $nextUrutan,
            ]);

            Log::info('User auto-added to struktur organisasi', [
                'user_id' => $user->id_user,
                'name' => $user->name,
                'jabatan' => $jabatan,
                'urutan' => $nextUrutan,
                'status' => $user->status
            ]);

            // Clear cache after adding
            StrukturOrganisasiObserver::clearCache();

        } catch (\Exception $e) {
            Log::error('Failed to auto-add user to struktur organisasi', [
                'user_id' => $user->id_user,
                'name' => $user->name,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate next urutan based on user role and existing hierarchy
     */
    protected function calculateNextUrutanByRole(User $user): int
    {
        // Define role hierarchy (lower number = higher priority)
        $roleHierarchy = [
            'super_admin' => 1,
            'Director' => 2,
            'Content Management' => 3,
            'Customer Service' => 4,
        ];

        $userRole = $user->roles->first()?->name ?? 'unknown';
        $userRolePriority = $roleHierarchy[$userRole] ?? 999;

        // Get all active structures with their roles
        $existingStructures = StrukturOrganisasi::whereHas('user', function ($query) {
            $query->where('status', 'aktif')
                ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang']);
        })->with(['user', 'user.roles'])
            ->where('urutan', '>', 0)
            ->orderBy('urutan')
            ->get();

        // If no existing structures, start with role-based position
        if ($existingStructures->isEmpty()) {
            return $userRolePriority;
        }

        // Find appropriate position based on role hierarchy
        $insertPosition = 1;

        foreach ($existingStructures as $structure) {
            $existingRole = $structure->user->roles->first()?->name ?? 'unknown';
            $existingRolePriority = $roleHierarchy[$existingRole] ?? 999;

            if ($userRolePriority <= $existingRolePriority) {
                // Insert before this position
                break;
            }
            $insertPosition++;
        }

        // Shift existing records down if needed
        StrukturOrganisasi::where('urutan', '>=', $insertPosition)
            ->where('urutan', '>', 0)
            ->increment('urutan');

        return $insertPosition;
    }

    /**
     * Handle user status change and adjust struktur organisasi urutan
     */
    protected function handleStatusChange(User $user, string $newStatus): void
    {
        try {
            $strukturOrganisasi = $user->strukturOrganisasi;

            if (!$strukturOrganisasi) {
                return;
            }

            if ($newStatus === 'nonaktif') {
                // Set urutan to 0 for inactive users
                $strukturOrganisasi->update(['urutan' => 0]);

                Log::info('User status changed to nonaktif - urutan set to 0', [
                    'user_id' => $user->id_user,
                    'name' => $user->name,
                    'struktur_id' => $strukturOrganisasi->id_struktur_organisasi
                ]);
            } elseif ($newStatus === 'aktif' && $strukturOrganisasi->urutan == 0) {
                // Restore urutan for reactivated users using role hierarchy
                $nextUrutan = $this->calculateNextUrutanByRole($user);
                $strukturOrganisasi->update(['urutan' => $nextUrutan]);

                Log::info('User status changed to aktif - urutan restored', [
                    'user_id' => $user->id_user,
                    'name' => $user->name,
                    'new_urutan' => $nextUrutan,
                    'struktur_id' => $strukturOrganisasi->id_struktur_organisasi
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle user status change for struktur organisasi', [
                'user_id' => $user->id_user,
                'name' => $user->name,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MediaSosial;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaSosialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_media::sosial');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MediaSosial $mediaSosial): bool
    {
        return $user->can('view_media::sosial');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_media::sosial');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MediaSosial $mediaSosial): bool
    {
        return $user->can('update_media::sosial');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MediaSosial $mediaSosial): bool
    {
        return $user->can('delete_media::sosial');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_media::sosial');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, MediaSosial $mediaSosial): bool
    {
        return $user->can('force_delete_media::sosial');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_media::sosial');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, MediaSosial $mediaSosial): bool
    {
        return $user->can('restore_media::sosial');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_media::sosial');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, MediaSosial $mediaSosial): bool
    {
        return $user->can('replicate_media::sosial');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_media::sosial');
    }
}

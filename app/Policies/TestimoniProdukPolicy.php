<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TestimoniProduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestimoniProdukPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_testimoni::produk');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TestimoniProduk $testimoniProduk): bool
    {
        return $user->can('view_testimoni::produk');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_testimoni::produk');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TestimoniProduk $testimoniProduk): bool
    {
        return $user->can('update_testimoni::produk');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TestimoniProduk $testimoniProduk): bool
    {
        return $user->can('delete_testimoni::produk');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_testimoni::produk');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TestimoniProduk $testimoniProduk): bool
    {
        return $user->can('force_delete_testimoni::produk');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_testimoni::produk');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TestimoniProduk $testimoniProduk): bool
    {
        return $user->can('restore_testimoni::produk');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_testimoni::produk');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, TestimoniProduk $testimoniProduk): bool
    {
        return $user->can('replicate_testimoni::produk');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_testimoni::produk');
    }
}

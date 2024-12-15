<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Effect;
use Illuminate\Auth\Access\HandlesAuthorization;

class EffectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_effect');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Effect $effect): bool
    {
        return $user->can('view_effect');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_effect');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Effect $effect): bool
    {
        return $user->can('update_effect');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Effect $effect): bool
    {
        return $user->can('delete_effect');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_effect');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Effect $effect): bool
    {
        return $user->can('force_delete_effect');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_effect');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Effect $effect): bool
    {
        return $user->can('restore_effect');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_effect');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Effect $effect): bool
    {
        return $user->can('replicate_effect');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_effect');
    }
}

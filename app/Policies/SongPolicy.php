<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Song;
use Illuminate\Auth\Access\HandlesAuthorization;

class SongPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_song');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Song $song): bool
    {
        return $user->can('view_song');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_song');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Song $song): bool
    {
        return $user->can('update_song');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Song $song): bool
    {
        return $user->can('delete_song');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_song');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Song $song): bool
    {
        return $user->can('force_delete_song');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_song');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Song $song): bool
    {
        return $user->can('restore_song');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_song');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Song $song): bool
    {
        return $user->can('replicate_song');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_song');
    }
}

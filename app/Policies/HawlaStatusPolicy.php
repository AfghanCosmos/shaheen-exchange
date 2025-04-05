<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HawlaStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class HawlaStatusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_hawla::status');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HawlaStatus $hawlaStatus): bool
    {
        return $user->can('view_hawla::status');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_hawla::status');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HawlaStatus $hawlaStatus): bool
    {
        return $user->can('update_hawla::status');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HawlaStatus $hawlaStatus): bool
    {
        return $user->can('delete_hawla::status');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_hawla::status');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, HawlaStatus $hawlaStatus): bool
    {
        return $user->can('force_delete_hawla::status');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_hawla::status');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, HawlaStatus $hawlaStatus): bool
    {
        return $user->can('restore_hawla::status');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_hawla::status');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, HawlaStatus $hawlaStatus): bool
    {
        return $user->can('replicate_hawla::status');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_hawla::status');
    }
}

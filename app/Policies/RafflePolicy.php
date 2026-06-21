<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Raffle;
use Illuminate\Auth\Access\HandlesAuthorization;

class RafflePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_raffles');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Raffle $raffle): bool
    {
        return $user->can('view_raffles');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Raffle $raffle): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Raffle $raffle): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Raffle $raffle): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Raffle $raffle): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Raffle $raffle): bool
    {
        return $user->can('manage_raffles');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('manage_raffles');
    }
}

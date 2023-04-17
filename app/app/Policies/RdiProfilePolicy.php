<?php

namespace App\Policies;

use App\Models\RdiProfile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RdiProfilePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_admin) return true;
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view an index page of all models
     */
    public function viewAll(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, RdiProfile $rdiProfile): bool
    {
        if (is_null($user)) return is_null($rdiProfile->user_id);
        else return is_null($rdiProfile->user_id) || ($rdiProfile->user_id === $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->is_admin === 1) || ($user->is_full_tier === 1);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RdiProfile $rdiProfile): bool
    {
        return $rdiProfile->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RdiProfile $rdiProfile): bool
    {
        return $rdiProfile->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RdiProfile $rdiProfile): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RdiProfile $rdiProfile): bool
    {
        //
    }
}
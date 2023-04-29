<?php

namespace App\Policies;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MealPolicy
{
    private const MAX_FREE_TIER_MEALS = 5;

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
        if ($user->is_admin || $user->is_full_tier || $user->is_free_tier) return true;
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Meal $meal): bool
    {
        return $meal->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->is_admin || $user->is_full_tier) return true;
        else if ($user->is_free_tier) {
            $count = Meal::where('user_id', $user->id)->count();
            if ($count < self::MAX_FREE_TIER_MEALS) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can clone models.
     */
    public function clone(User $user, Meal $meal): bool
    {
        if ($user->is_admin || $user->is_full_tier) {
            return $meal->user_id === $user->id;
        } else if($user->is_free_tier) {
            $count = Meal::where('user_id', $user->id)->count();
            return ($count < self::MAX_FREE_TIER_MEALS) && ($meal->user_id === $user->id);
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Meal $meal): bool
    {
        return $meal->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Meal $meal): bool
    {
        return $meal->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Meal $meal): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Meal $meal): bool
    {
        //
    }
}

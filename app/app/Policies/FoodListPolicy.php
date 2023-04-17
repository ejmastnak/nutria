<?php

namespace App\Policies;

use App\Models\FoodList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FoodListPolicy
{
    private const MAX_FREE_TIER_FOOD_LISTS = 5;

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
     * Determine whether the user can view the model.
     */
    public function view(User $user, FoodList $foodList): bool
    {
        return $foodList->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (($user->is_admin === 1) || ($user->is_full_tier === 1)) return true;
        else if ($user->is_free_tier === 1) {
            $count = FoodList::where('user_id', $user->id)->count();
            if ($count < self::MAX_FREE_TIER_FOOD_LISTS) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FoodList $foodList): bool
    {
        return $foodList->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FoodList $foodList): bool
    {
        return $foodList->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FoodList $foodList): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FoodList $foodList): bool
    {
        //
    }
}

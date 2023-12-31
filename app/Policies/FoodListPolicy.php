<?php

namespace App\Policies;

use App\Models\FoodList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FoodListPolicy
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
        if ($user->is_paying || $user->is_registered) return true;
        return false;
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
        if ($user->is_paying) return true;
        else if ($user->is_registered) {
            $count = FoodList::where('user_id', $user->id)->count();
            if ($count < config('auth.max_free_tier_food_lists')) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can clone models.
     */
    public function clone(User $user, FoodList $foodList): bool
    {
        if ($user->is_paying) {
            return $foodList->user_id === $user->id;
        } else if($user->is_registered) {
            $count = FoodList::where('user_id', $user->id)->count();
            return ($count < config('auth.max_free_tier_food_lists')) && ($foodList->user_id === $user->id);
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

<?php

namespace App\Policies;

use App\Models\Meal;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MealPolicy
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
    public function view(User $user, Meal $meal): bool
    {
        return $meal->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->is_paying) return true;
        else if ($user->is_registered) {
            $count = Meal::where('user_id', $user->id)->count();
            if ($count < config('auth.max_free_tier_meals')) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can clone models.
     */
    public function clone(User $user, Meal $meal): bool
    {
        if ($user->is_paying) {
            return $meal->user_id === $user->id;
        } else if($user->is_registered) {
            $count = Meal::where('user_id', $user->id)->count();
            return ($count < config('auth.max_free_tier_meals')) && ($meal->user_id === $user->id);
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

    public function saveAsIngredient(User $user, Meal $meal):bool {
        if ($user->is_paying) {
            return $meal->user_id === $user->id;
        } else if($user->is_registered && $meal->user_id === $user->id) {
            $count = Ingredient::where('user_id', $user->id)->count();
            return $count < config('auth.max_free_tier_ingredients');
        }
        return false;
    }
}

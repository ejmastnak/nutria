<?php

namespace App\Policies;

use App\Models\Ingredient;
use App\Models\User;

class IngredientPolicy
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
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Ingredient $ingredient): bool
    {
        if (is_null($user)) return is_null($ingredient->user_id);
        else return is_null($ingredient->user_id) || ($ingredient->user_id === $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->is_paying) return true;
        else if ($user->is_registered) {
            $count = Ingredient::where('user_id', $user->id)->count();
            return $count < config('auth.max_free_tier_ingredients');
        }
        return false;
    }

    /**
     * Determine whether the user can clone models.
     */
    public function clone(User $user, Ingredient $ingredient): bool
    {
        if ($user->is_paying) {
            return is_null($ingredient->user_id) || $ingredient->user_id === $user->id;
        } else if($user->is_registered) {
            $count = Ingredient::where('user_id', $user->id)->count();
            return ($count < config('auth.max_free_tier_ingredients')) && (is_null($ingredient->user_id) || $ingredient->user_id === $user->id);
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ingredient $ingredient): bool
    {
        return $ingredient->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ingredient $ingredient): bool
    {
        return $ingredient->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ingredient $ingredient): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ingredient $ingredient): bool
    {
        //
    }
}

<?php

namespace App\Policies;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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
    public function create(User $user): Response
    {
        if ($user->is_full_tier) return Response::allow();
        else if ($user->is_free_tier) {
            $count = Ingredient::where('user_id', $user->id)->count();
            if ($count < config('auth.max_free_tier_ingredients')) return Response::allow();
            else return Response::deny(config('auth.free_tier_resources_exceeded'));
        }
        return Response::deny(config('auth.generic_deny'));
    }

    /**
     * Determine whether the user can clone models.
     */
    public function clone(User $user, Ingredient $ingredient): bool
    {
        if ($user->is_full_tier) {
            return is_null($ingredient->user_id) || $ingredient->user_id === $user->id;
        } else if($user->is_free_tier) {
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

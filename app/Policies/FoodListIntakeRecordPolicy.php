<?php

namespace App\Policies;

use App\Models\FoodListIntakeRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FoodListIntakeRecordPolicy
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
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_paying;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FoodListIntakeRecord $foodListIntakeRecord): bool
    {
        return $foodListIntakeRecord->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FoodListIntakeRecord $foodListIntakeRecord): bool
    {
        return $foodListIntakeRecord->user_id === $user->id;
    }

}

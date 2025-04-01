<?php

namespace App\Policies;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MealPolicy
{

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Meal $meal): bool
    {
        return $user->id === $meal->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Meal $meal): bool
    {
        return $user->id === $meal->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Meal $meal): bool
    {
        return true;
    }
}

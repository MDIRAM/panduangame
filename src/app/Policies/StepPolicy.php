<?php

namespace App\Policies;

use App\Models\Step;
use App\Models\User;

class StepPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_step');
    }

    public function view(User $user, Step $step): bool
    {
        return $user->can('view_step');
    }

    public function create(User $user): bool
    {
        return $user->can('create_step');
    }

    public function update(User $user, Step $step): bool
    {
        return $user->can('update_step');
    }

    public function delete(User $user, Step $step): bool
    {
        return $user->can('delete_step');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_step');
    }
}

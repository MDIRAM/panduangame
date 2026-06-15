<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_game');
    }

    public function view(User $user, Game $game): bool
    {
        return $user->can('view_game');
    }

    public function create(User $user): bool
    {
        return $user->can('create_game');
    }

    public function update(User $user, Game $game): bool
    {
        return $user->can('update_game');
    }

    public function delete(User $user, Game $game): bool
    {
        return $user->can('delete_game');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_game');
    }
}

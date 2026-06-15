<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\User;

class ChapterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_chapter');
    }

    public function view(User $user, Chapter $chapter): bool
    {
        return $user->can('view_chapter');
    }

    public function create(User $user): bool
    {
        return $user->can('create_chapter');
    }

    public function update(User $user, Chapter $chapter): bool
    {
        return $user->can('update_chapter');
    }

    public function delete(User $user, Chapter $chapter): bool
    {
        return $user->can('delete_chapter');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_chapter');
    }
}

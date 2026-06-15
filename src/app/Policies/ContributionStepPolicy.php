<?php

namespace App\Policies;

use App\Models\ContributionStep;
use App\Models\User;

class ContributionStepPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('super_admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, ContributionStep $step): bool
    {
        return $step->contribution->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('contributor');
    }

    public function update(User $user, ContributionStep $step): bool
    {
        return $step->contribution->user_id === $user->id
            && $step->contribution->isEditableByAuthor();
    }

    public function delete(User $user, ContributionStep $step): bool
    {
        return $this->update($user, $step);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

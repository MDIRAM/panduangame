<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WalkthroughContribution;

class WalkthroughContributionPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('super_admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('contributor');
    }

    public function view(User $user, WalkthroughContribution $contribution): bool
    {
        return $user->hasRole('contributor')
            && $contribution->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('contributor');
    }

    public function update(User $user, WalkthroughContribution $contribution): bool
    {
        return $user->hasRole('contributor')
            && $contribution->user_id === $user->id
            && $contribution->isEditableByAuthor();
    }

    public function delete(User $user, WalkthroughContribution $contribution): bool
    {
        return $this->update($user, $contribution);
    }

    public function submit(User $user, WalkthroughContribution $contribution): bool
    {
        return $this->update($user, $contribution);
    }
}

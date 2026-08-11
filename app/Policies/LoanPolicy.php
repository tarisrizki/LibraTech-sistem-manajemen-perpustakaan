<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function view(User $user, Loan $loan): bool
    {
        return $user->role === UserRole::Admin || $loan->user_id === $user->id;
    }

    public function viewOwn(User $user): bool
    {
        return true;
    }

    public function request(User $user): bool
    {
        return in_array($user->role, [UserRole::Member, UserRole::Admin], true);
    }

    public function approve(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function reject(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function return(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function create(User $user): bool
    {
        return true;
    }
}

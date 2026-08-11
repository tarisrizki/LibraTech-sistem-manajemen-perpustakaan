<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Book $book): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function update(User $user, Book $book): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function restore(User $user, Book $book): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function forceDelete(User $user, Book $book): bool
    {
        return $user->role === UserRole::Admin;
    }
}

<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}


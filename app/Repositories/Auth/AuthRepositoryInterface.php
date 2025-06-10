<?php

namespace App\Repositories\Auth;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}

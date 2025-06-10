<?php

namespace App\Repositories\Auth;
use App\Models\User;

interface UserRepositoryInterface
{
    public function existsByEmail(string $email): bool;
    public function create(array $data);
    public function updatePassword(User $user, string $password): bool;
    public function findByEmail(string $email): ?User;
}

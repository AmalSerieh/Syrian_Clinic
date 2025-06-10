<?php

namespace App\Repositories\Auth;

use App\Models\User;

interface OtpRepositoryInterface
{
    public function store(string $email, int $otp);
    public function verify(string $email, string $otp): bool;
    public function delete(string $email): bool;
}

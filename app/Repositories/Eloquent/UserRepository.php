<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Auth\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }
    public function updatePassword(User $user, string $password): bool
    {
        try {
            $user->password = Hash::make($password);
            return $user->save();
        } catch (\Exception $e) {
            // يمكن تسجيل الخطأ هنا
            return false;
        }
    }
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}


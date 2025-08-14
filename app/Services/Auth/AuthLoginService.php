<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthLoginService
{
    public function __construct(
        private AuthRepositoryInterface $authRepository
    ) {
    }

    // app/Domain/Auth/Services/AuthService.php

    public function login(string $email, string $password, string $language): array
    {
        try {
            $user = $this->authRepository->findByEmail($email);

            if (!$user) {
                return [
                    'success' => false,
                    'status' => 404,
                    'message' => __('auth.user_not_found'),
                    'data' => null
                ];
            }

            if (!Hash::check($password, $user->password)) {
                return [
                    'success' => false,
                    'status' => 401,
                    'message' => __('auth.invalid_credentials'),
                    'data' => null
                ];
            }
            $isCreatedBySecretary = $user->role === 'patient' && $user->created_by === 'secretary';
            $requiresPasswordChange = $isCreatedBySecretary && !$user->has_changed_credentials;

            $this->updateUserLanguage($user, $language);
            Auth::login($user);
            $isCreatedBySecretary = $user->role === 'patient' && $user->created_by === 'secretary';


            return [
                'success' => true,
                'status' => 200,
                'message' => __('auth.login_successfully'),
                'data' => [
                    'user' => $user,
                    'token' => $user->createToken('auth_token')->plainTextToken,
                    'created_by_secretary' => $isCreatedBySecretary,
                    'requires_password_change' => $requiresPasswordChange, // 👈
                    'redirect_route' => $requiresPasswordChange ? route('patient.force-password-change') : null,


                ],
                'patient' => $user->role === 'patient' ? $user->patient : null
            ];


        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 500,
                'message' => __('auth.login_error'),
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    private function updateUserLanguage(User $user, string $language): void
    {
        $user->language = $language;
        $user->save();
        app()->setLocale($language);
    }

    private function prepareErrorResponse(string $message, int $status): array
    {
        return [
            'status' => $status,
            'data' => ['message' => $message]
        ];
    }
}

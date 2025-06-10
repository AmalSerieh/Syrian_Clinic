<?php

namespace App\Http\Controllers\Auth_Otp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\PatientResource;
use App\Models\User;
use App\Services\Auth\AuthLoginService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    /*  public function login1(LoginRequest $loginRequest)
     {
         $language = $this->parseLanguageHeader($loginRequest->header('Accept-Language'));
         app()->setLocale($language);

         $user = User::where('email', $loginRequest->email)->first();
         if (!$user) {
             return response()->json([
                 'message' => __('auth.user_not_found'),
             ], 404);
         }

         if (!$user || !Hash::check($loginRequest->password, $user->password)) {
             return response()->json([
                 'message' => __('auth.invalid_credentials'),
             ], 401);
         }
         $user->language = $language;
         $user->save();
         app()->setLocale($user->language ?? config('app.fallback_locale', 'en'));

         Auth::login($user);
         $token = $user->createToken('auth_token')->plainTextToken;
         return response()->json([
             'message' => __('auth.login_successfully'),
             'user' => $user,
             'token' => $token,
         ]);

     } */
    public function __construct(
        private AuthLoginService $authService
    ) {
    }

    public function login1(LoginRequest $request)
    {
        $language = $this->parseLanguageHeader($request->header('Accept-Language'));

        $response = $this->authService->login(
            $request->email,
            $request->password,
            $language
        );

        // تحقق من البنية الأساسية للاستجابة
        if (!isset($response['success'], $response['status'], $response['message'])) {
            return response()->json([
                'message' => 'Invalid service response structure',
                'debug' => $response
            ], 500);
        }

        // إذا كانت الاستجابة ناجحة
        if ($response['success'] === true) {
            if (!isset($response['data']['user'], $response['data']['token'])) {
                return response()->json([
                    'message' => 'Authentication succeeded but missing user data',
                    'debug' => $response
                ], 500);
            }

            return response()->json([
                'message' => $response['message'],
                'user' => new UserResource($response['data']['user']),
                'patient' => $response['patient'] ? new PatientResource($response['patient']) : null,

                'token' => $response['data']['token']
            ], $response['status']);
        }


        // إذا كانت الاستجابة فاشلة
        return response()->json([
            'message' => $response['message'],
        ], $response['status']);
    }
    protected function parseLanguageHeader(?string $header): string
    {
        if (!$header)
            return config('app.fallback_locale', 'en');

        $locales = explode(',', $header);
        $primary = trim(explode(';', $locales[0])[0]);
        $lang = strtolower(substr($primary, 0, 2));

        return in_array($lang, ['ar', 'en']) ? $lang : config('app.fallback_locale', 'en');
    }
}

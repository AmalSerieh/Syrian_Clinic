<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GoogleController extends Controller
{
    public function googleAuth(Request $request)
    {
        $language = $this->parseLanguageHeader($request->header('Accept-Language'));

        $redirectUrl = Socialite::driver('google')
            ->stateless()
            ->with([
                'hl' => $language,
                'state' => json_encode(['lang' => $language])
            ]) // إرسال اللغة إلى Google
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'url' => $redirectUrl,
            'detected_language' => $language
        ]);
    }

    public function handleGoogleLogin(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        try {
            // 1. تبادل الكود بـ access_token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'code' => $request->code,
            ]);

            if (!$response->successful()) {
                return response()->json(['error' => __('auth.exchange_failed')], 400);
            }
            $accessToken = $response->json()['access_token'];

            // 2. جلب بيانات المستخدم من Google
            $googleUserResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (!$googleUserResponse->successful()) {
                return response()->json(['error' => __('auth.fetch_failed')], 400);
            }
            // 3. استخراج اللغة من state (إن أمكن)
            $language = 'en'; // القيمة الافتراضية
            if ($request->has('state')) {
                $stateData = json_decode($request->input('state'), true);
                if (!empty($stateData['lang']) && in_array($stateData['lang'], ['ar', 'en'])) {
                    $language = $stateData['lang'];
                }
            }
            $googleUser = $googleUserResponse->json();
            // 3. تسجيل أو تحديث المستخدم
            $user = User::where('google_id', $googleUser['sub'])->first();


            $isNewUser = false;

            if (!$user) {
                // مستخدم جديد
                $user = User::create([
                    'google_id' => $googleUser['sub'],
                    'name' => $googleUser['name'],
                    'email' => $googleUser['email'],
                    'password' => bcrypt('possability'),
                    'language' => $language,
                ]);
                $isNewUser = true;
            } else {
                // تحديث بيانات المستخدم الحالي
                $user->update([
                    'name' => $googleUser['name'],
                    'email' => $googleUser['email'],
                    'language' => $language,
                ]);
            }

            // اجعل اللغة المعتمدة الحالية هي التي اختارها المستخدم
            app()->setLocale($user->language ?? config('app.fallback_locale', 'en'));

            // 4. تسجيل الدخول وإنشاء توكن
            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => $isNewUser ? __('auth.register_success') : __('auth.login_success'),
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Google login failed: ' . $e->getMessage());
            return response()->json([
                'error' => __('auth.login_failed'),
                'message' => $e->getMessage(),
            ], 500);
        }
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

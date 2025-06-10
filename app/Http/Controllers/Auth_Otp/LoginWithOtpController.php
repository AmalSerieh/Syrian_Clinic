<?php

namespace App\Http\Controllers\Auth_Otp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginWithOtpController extends Controller
{
    public function login(LoginRequest $loginRequest)
    {
        $language = $this->parseLanguageHeader($loginRequest->header('Accept-Language'));
        // dd($language);
        app()->setLocale($language);
        //  dd( app()->setLocale($language));
        $user = User::where('email', $loginRequest->email)->first();
        if (!$user || !Hash::check($loginRequest->password, $user->password)) {
            return response()->json([
                'message' => __('auth.invalid_credentials'),
            ], 401);
        }
        $otp = rand(100000, 999999);

        $emailOtp = EmailOtp::updateOrCreate(
            ['email' => $loginRequest->email],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinutes(10)
            ]
        );
        // dd($emailOtp);
       // Mail::to($loginRequest->email)->send(new EmailOtpMail($otp));
        // 3. حفظ البيانات المؤقتة بدون كلمة المرور
        $loginRequest->session()->put('login_email', $loginRequest->email);
        $loginRequest->session()->put('login_password', Hash::make($loginRequest->password));


        return response()->json([
            'message' => __('auth.otp_sent'),
            'email' => $loginRequest->email,
            'language' => $language,
        ]);
    }
    public function login1(LoginRequest $loginRequest)
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

    }
    public function verifyOtpStore(Request $request)
    {
        $language = $this->parseLanguageHeader($request->header('Accept-Language'));
        app()->setLocale($language);
        //  dd(app()->setLocale($language));
        $request->validate([
            'otp' => ['required', 'string', 'digits:6','size:6'],
        ]);

        $email = $request->session()->get('login_email');
        $password = $request->session()->get('login_password');
        if (!$email) {
            return response()->json([
                'message' => __('auth.session_expired'),
            ], 440); // 440 = login timeout
        }

        $emailotp = EmailOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', Carbon::now())
            ->first();
        if (!$emailotp) {
            return response()->json([
                'message' => __('auth.invalid_otp'),
            ], 422);
        }
        // dd('otp verify successfully');

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'message' => __('auth.user_not_found'),
            ], 404);
        }

        $user->language = $language;
        $user->save();

        $emailotp->delete();
        $request->session()->forget('login_email');
        $request->session()->forget('login_password');
        //اجعل اللغة المعتمدة الحالية هي التي اختارها المستخدم
        app()->setLocale($user->language ?? config('app.fallback_locale', 'en'));

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => __('auth.login_successfully'),
            'user' => $user,
            'token' => $token,
        ]);
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

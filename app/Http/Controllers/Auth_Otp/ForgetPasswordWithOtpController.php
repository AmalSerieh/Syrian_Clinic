<?php

namespace App\Http\Controllers\Auth_Otp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\Auth\PasswordResetService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Request;

class ForgetPasswordWithOtpController extends Controller
{
    public function sendResetOtpPassword(ForgetPasswordRequest $forgetPasswordRequest)
    {
        $language = $this->parseLanguageHeader($forgetPasswordRequest->header('Accept-Language'));
        // dd($language);
        app()->setLocale($language);
        //  dd( app()->setLocale($language));
        $user = User::where('email', $forgetPasswordRequest->email)->first();
        if (!$user) {
            return response()->json([
                'message' => __('validation.attributes.email'),
            ], 401);
        }
        $otp = rand(100000, 999999);

        $emailOtp = EmailOtp::updateOrCreate(
            ['email' => $forgetPasswordRequest->email],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinutes(10)
            ]
        );
        //Mail::to($forgetPasswordRequest->email)->send(new ResetPasswordOtpMail($otp));
        $forgetPasswordRequest->session()->put('reset_password_email', $forgetPasswordRequest->email);

        return response()->json([
            'message' => __('auth.otp_sent'),
        ]);

    }
    public function verifyResetOtp(VerifyOtpRequest $request)
    {
        /*  $request->validate([
             'otp' => 'required|digits:6',
         ]); */
        $email = $request->session()->get('reset_password_email');

        $otpRecord = EmailOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => __('auth.invalid_otp')], 422);
        }

        // حفظ البريد في الجلسة بعد التحقق
        //session(['reset_password_email' => $request->email]);

        return response()->json(['message' => __('auth.otp_verified')]);
    }
    public function setNewPasswordAfterOtp(ResetPasswordOtpRequest $request)
    {
     /*    $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]); */
        $email = $request->session()->get('reset_password_email');

        //$email = session('reset_password_email');

        if (!$email) {
            return response()->json(['message' => __('auth.session_expired')], 403);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => __('auth.user_not_found')], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // حذف سجل OTP بعد الاستخدام
        EmailOtp::where('email', $email)->delete();

        // ننسى الجلسة
        $request->session()->forget('reset_password_email');

        return response()->json(['message' => __('auth.password_reset_successfully')]);
    }


    /*  public function resetPasswordWithOtp(ResetPasswordOtpRequest $resetPasswordRequest)
     {
         $otpRecord = EmailOtp::where('email', $resetPasswordRequest->email)
             ->where('otp', $resetPasswordRequest->otp)
             ->where('expired_at', '>=', now())
             ->first();

         if (!$otpRecord) {
             return response()->json(['message' => __('auth.invalid_otp')], 422);
         }

         $user = User::where('email', $resetPasswordRequest->email)->first();
         $user->password = Hash::make($resetPasswordRequest->password);
         app()->setLocale($user->language ?? config('app.fallback_locale', 'en'));
         $user->save();

         $otpRecord->delete();

         return response()->json(['message' => 'Password has been reset successfully.']);
     } */

   public function __construct(
        protected PasswordResetService $passwordResetService
    ) {}


     public function sendOtp(ForgetPasswordRequest $request)
    {
        $response = $this->passwordResetService->sendResetOtpPassword($request);

        return response()->json(['message' => $response['message']], $response['status']);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $response = $this->passwordResetService->verifyResetOtp($request);

        return response()->json(['message' => $response['message']], $response['status']);
    }

    public function setNewPassword(ResetPasswordOtpRequest $request)
    {
        $response = $this->passwordResetService->setNewPasswordAfterOtp($request);

        return response()->json(['message' => $response['message']], $response['status']);
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

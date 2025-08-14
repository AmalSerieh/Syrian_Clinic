<?php
namespace App\Services\Auth;
use App\Mail\ResetPasswordOtpMail;
use App\Models\EmailOtp;
use App\Repositories\Auth\OtpRepositoryInterface;
use App\Repositories\Auth\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;

class PasswordResetService
{

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function sendResetOtpPassword(Request $request): array
    {
        $language = $this->parseLanguageHeader($request->header('Accept-Language'));
        app()->setLocale($language);

        $email = $request->email;

        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'status' => 404
            ];
        }

        $otp = $this->generateOtp();

        // حفظ OTP في قاعدة البيانات
        EmailOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinutes(10)
            ]
        );

        // حفظ البريد في الجلسة
        $request->session()->put('reset_password_email', $email);

        Mail::to($email)->send(new ResetPasswordOtpMail($otp));

        return [
            'success' => true,
            'message' => __('auth.otp_sent'),
            'status' => 200
        ];
    }

    public function verifyResetOtp(Request $request): array
    {
        $email = $request->session()->get('reset_password_email');
        if (!$email) {
            return [
                'success' => false,
                'message' => __('auth.session_expired'),
                'status' => 403
            ];
        }

        $otpRecord = EmailOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return [
                'success' => false,
                'message' => __('auth.invalid_otp'),
                'status' => 422
            ];
        }

        return [
            'success' => true,
            'message' => __('auth.otp_verified'),
            'status' => 200
        ];
    }

    public function setNewPasswordAfterOtp(Request $request): array
    {
        $email = $request->session()->get('reset_password_email');
        if (!$email) {
            return [
                'success' => false,
                'message' => __('auth.session_expired'),
                'status' => 403
            ];
        }

        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'status' => 404
            ];
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // حذف OTP
        EmailOtp::where('email', $email)->delete();

        // نسيان الجلسة
        $request->session()->forget('reset_password_email');

        return [
            'success' => true,
            'message' => __('auth.password_reset_successfully'),
            'status' => 200
        ];
    }

    private function generateOtp(): int
    {
        return rand(10000, 99999);
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

<?php

namespace App\Repositories\Eloquent;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session; // <-- استخدم الـ Session
use App\Repositories\Auth\OtpRepositoryInterface;

class OtpRepository implements OtpRepositoryInterface
{
    private const OTP_SESSION_KEY_PREFIX = 'otp_';
    private const OTP_EXPIRY_MINUTES = 5;

    public function store(string $email, int $otp): EmailOtp
    {
        // حذف أي OTP قديم لنفس البريد
        $this->delete($email);

        // إنشاء سجل OTP جديد في قاعدة البيانات
        $otpRecord = EmailOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES)
            ]
        );

        // حفظ OTP في الجلسة
        Session::put($this->getSessionKey($email), [
            'otp' => $otp,
            'expired_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES)
        ]);

        return $otpRecord;
    }

    public function verify(string $email, string $otp): bool
    {
        $sessionData = Session::get($this->getSessionKey($email));

        // التحقق من الجلسة أولاً
        if ($sessionData && isset($sessionData['otp'], $sessionData['expired_at'])) {
            if ($sessionData['otp'] === $otp && Carbon::parse($sessionData['expired_at'])->isFuture()) {
                return true;
            }
        }

        // إذا لم يكن صحيحًا أو غير موجود، تحقق من قاعدة البيانات
        return EmailOtp::where('email', $email)
            ->where('otp', $otp)
            ->where('expired_at', '>=', Carbon::now())
            ->exists();
    }

    public function delete(string $email): bool
    {
        // حذف من الجلسة
        Session::forget($this->getSessionKey($email));

        // حذف من قاعدة البيانات
        return EmailOtp::where('email', $email)->delete() > 0;
    }

    private function getSessionKey(string $email): string
    {
        return self::OTP_SESSION_KEY_PREFIX . md5($email);
    }
}

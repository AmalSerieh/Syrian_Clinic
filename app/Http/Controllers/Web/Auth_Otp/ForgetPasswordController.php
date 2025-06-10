<?php

namespace App\Http\Controllers\Web\Auth_Otp;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForgetPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth_otp.forgot_password');
    }
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users']);
        $user = User::where('email', $request->email)->first();

        // تحقق من الدور
        if (!in_array($user->role, ['doctor', 'secretary'])) {
            return back()->withErrors(['email' => 'This user is not authorized to reset the password.']);
        }

        $otp = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expired_at' => Carbon::now()->addMinutes(10)]
        );

        // Mail::to($request->email)->send(new ResetPasswordOtpMail($otp)); // أنشئ هذه الرسالة

        $request->session()->put('reset_password_email', $request->email);


        return redirect()->route('password.otp.verify');
    }

    public function verifyOtpForm(): View
    {
        return view('auth_otp.password_otp_verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|size:6']);

        $email = $request->session()->get('reset_password_email');


        $record = EmailOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', now())
            ->first();

        if (!$record) {
            return back()->with('message', 'Invalid or expired OTP');
        }

        session(['otp_verified_email' => $email]);


        // $user = Auth::user();

        return redirect()->route('password.otp.reset.form');


    }
    public function showResetForm(): View
    {
        return view('auth_otp.password_reset_verify');
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $email = session('otp_verified_email');
        $user = User::where('email', $email)->firstOrFail();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        EmailOtp::where('email', $email)->delete();
        $request->session()->forget(['otp_verified_email', 'reset_password_email']);

        return redirect()->route('login')->with('status', 'Password reset successfully.');
    }

    public function edit()
    {
        $role = Auth::user()->role;

        return view('auth_otp.update_password_form', compact('role'));
    }

    public function update(Request $request): RedirectResponse
    {
        // منع المرضى من تحديث كلمة المرور
        if (!in_array($request->user()->role, ['doctor', 'secretary'])) {
            abort(403, 'غير مصرح لك.');
        }

        /* if ($request->user()->role === 'patient') {
            abort(403, 'You are not authorized to change the password.');
        } */

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }


}

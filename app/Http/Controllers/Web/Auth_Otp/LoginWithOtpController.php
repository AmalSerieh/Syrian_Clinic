<?php

namespace App\Http\Controllers\Web\Auth_Otp;

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
    public function create()
    {
        return view('auth_otp.login');
    }
    public function store(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->route('login')->with('status', __('auth.user_not_found'));

        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->route('login')->with('status', 'Invalid email or password.');
        }

        $otp = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinutes(10)
            ]
        );
        //Mail::to($request->email)->send(new EmailOtpMail($otp));
        $request->session()->put('login_email', $request->email);
        $request->session()->put('login_password', Hash::make($request->password));

        return redirect()->route('verify.otp.login');
    }

    public function verifyOtp()
    {
        return view('auth_otp.email_otp_login_verify');

    }
    public function verifyOtpStore(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = $request->session()->get('login_email');
        $password = $request->session()->get('login_password');

        $emailotp = EmailOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', Carbon::now())
            ->first();
        if (!$emailotp) {
            return redirect()->back()->withInput()->with(['status' => 'Invalid OTP or OTP has expired!']);
        }
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return redirect()->back()->withInput()->with(['status' => 'Invalid email or password!']);
        }
        $emailotp->delete();
        $request->session()->forget('login_email');
        $request->session()->forget('login_password');
        $user = Auth::user();
        // منع المرضى من الدخول
        if ($user->role === 'patient') {
            //  Auth::logout(); // تسجيل الخروج
            return redirect()->route('login')->with('status', 'غير مصرح لك بالدخول.');
        }

        if ($user->role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->role === 'secretary') {
            return redirect()->route('secretary.dashboard');
        }
    }
}

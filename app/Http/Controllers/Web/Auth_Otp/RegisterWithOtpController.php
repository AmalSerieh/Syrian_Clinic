<?php

namespace App\Http\Controllers\Web\Auth_Otp;

use App\Http\Controllers\Controller;
use App\Mail\EmailOtpMail;
use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class RegisterWithOtpController extends Controller
{
    public function create()
    {
        $existingSecretary = User::where('role', 'secretary')->first();

        if ($existingSecretary && auth()->check() && auth()->user()->role === 'secretary') {
            abort(403, 'غير مصرح لك بإنشاء حساب جديد.');
        }

        return view('auth_otp.register');

    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:secretary,doctor'],
            'phone' => ['required', 'digits:10', 'numeric'],
        ]);
        if ($request->role === 'secretary') {
            $existingSecretary = User::where('role', 'secretary')->first();

            if ($existingSecretary) {
                return back()->withErrors(['role' => 'يوجد بالفعل حساب سكرتيرة في النظام.'])->withInput();
            }

            /* if (auth()->check() && auth()->user()->role === 'secretary') {
                return back()->withErrors(['role' => 'السكرتيرة لا تملك صلاحية إنشاء مستخدمين.'])->withInput();
            } */
        }

        $otp = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinutes(10)
            ]
        );
        // Mail::to($request->email)->send(new EmailOtpMail($otp));
        $request->session()->put('register_email', $request->email);
        $request->session()->put('register_name', $request->name);
        $request->session()->put('register_password', Hash::make($request->password));
        $request->session()->put('register_role', $request->role);
        $request->session()->put('register_phone', $request->phone);


        return redirect()->route('verify.otp');
    }

    public function verifyOtp()
    {
        return view('auth_otp.email_otp_verify');

    }
    public function verifyOtpStore(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = $request->session()->get('register_email');
        $name = $request->session()->get('register_name');
        $password = $request->session()->get('register_password');
        $role = $request->session()->get('register_role');
        $phone = $request->session()->get('register_phone');


        $emailotp = EmailOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', Carbon::now())
            ->first();
        if (!$emailotp) {
            return redirect()->withInput()->with(['status' => 'Invalid OTP or OTP has expired!']);
        }
        //  dd('otp verify successfully');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'phone' => $phone,
        ]);
        if ($user->role === 'doctor') {
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'photo' => 'certificates/doctor.jpg',
            ]);

    /*    $doctor_record = DoctorProfile::create([
                'doctor_id' => $doctor->id,
            ]); */
        }
        $emailotp->delete();
        $request->session()->forget('register_email');
        $request->session()->forget('register_name');
        $request->session()->forget('register_password');
        $request->session()->forget('register_role');
        $request->session()->forget('register_phone');


        Auth::login($user);

        // إعادة التوجيه حسب الدور
        return match ($user->role) {
            'doctor' => redirect()->route('doctor-profile.create'),
            //'doctor' => redirect()->route('doctor.dashboard'),
            'secretary' => redirect()->route('secretary.dashboard'),
            default => abort(403)
        };
    }
}

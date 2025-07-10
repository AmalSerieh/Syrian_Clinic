<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
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

class RegisterAdminController extends Controller
{
    public function create()
    {
        $existingAdmin = User::where('role', 'admin')->first();

        if ($existingAdmin && auth()->check() && auth()->user()->role === 'admin') {
            abort(403, 'غير مصرح لك بإنشاء حساب جديد يرجى طلب المساعدة');
        }

        return view('admin.auth.register');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone' => ['required', 'digits:10', 'numeric'],
          //  'role' => ['required', 'in:admin'],
        ]);

        // تحقق من وجود أدمن قبل الإنشاء
        if ( User::where('role', 'admin')->exists()) {
            return back()
                ->withErrors(['role' => 'يوجد بالفعل حساب آدمن في النظام'])
                ->withInput();
        }


        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => 'admin', // تأكد من استخدام القيمة المصدقة
        ]);

        return redirect()->route('admin.index');
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

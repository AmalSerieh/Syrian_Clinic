<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginAdminController extends Controller
{
    public function create()
    {
        return view('admin.auth.login');
    }
    public function store(LoginRequest $loginRequest)
    {

        $user = User::where('email', $loginRequest->email)->first();

        if (!$user || !Hash::check($loginRequest->password, $user->password)) {
            return redirect()->route('login')->with('status', 'Invalid email or password.');

        }
         if (!Auth::attempt(['email' => $loginRequest->email, 'password' => $loginRequest->password])) {
            return redirect()->back()->withInput()->with(['status' => 'Login failed, try again.']);
        }
        Auth::login($user);

        // منع المرضى من الدخول
        if ($user->role === 'patient') {
            Auth::logout(); // تسجيل الخروج
            return redirect()->route('login')->with('status', 'You are not authorized to enter.');
        }
        // توجيه الطبيب لتغيير بياناته إذا لم يقم بذلك بعد
        if ($user->role === 'doctor' && !$user->has_changed_credentials) {
            return redirect()->route('doctor.first-login');
        }


        return match ($user->role) {
            'admin' => redirect()->route('admin.index'),
            'doctor' => redirect()->route('doctor.dashboard'),
            'secretary' => redirect()->route('secretary.dashboard'),
            default => abort(403),
        };

    }
}

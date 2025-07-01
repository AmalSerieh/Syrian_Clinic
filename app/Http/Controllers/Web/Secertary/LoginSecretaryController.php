<?php

namespace App\Http\Controllers\Web\Secertary;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginSecretaryController extends Controller
{
    public function create()
    {
        return view('secretary.auth.login');
    }
    public function store(LoginRequest $loginRequest)
    {
        $user = User::where('email', $loginRequest->email)->first();

        if (!$user || !Hash::check($loginRequest->password, $user->password)) {
            return redirect()->route('secretary.login')->with('status', 'Invalid email or password.');
        }

        // فقط السكرتيرة التي أنشأها الأدمن أو الأدمن نفسه يمكنهم الدخول
        $isAllowed =
            ($user->role === 'secretary' && $user->created_by === 'admin' && $user->secretary) ||
            ($user->role === 'admin');

        if (!$isAllowed) {
            return redirect()->route('secretary.login')->with('status', 'غير مصرح لك بالدخول إلى لوحة السكرتيرة.');
        }
        if (!Auth::attempt(['email' => $loginRequest->email, 'password' => $loginRequest->password])) {
            return redirect()->back()->withInput()->with(['status' => 'Login failed, try again.']);
        }
        return redirect()->route('secretary.dashboard');
    }




}

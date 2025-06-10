<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
  public function login(Request $request)
    {
        // التحقق من صحة البيانات المدخلة (البريد الإلكتروني وكلمة السر)
        $request->validate([
            'email' => 'required|email',  // تأكد من أن البريد الإلكتروني صحيح
            'password' => 'required|string', // تأكد من أن كلمة السر صحيحة
        ]);

        // محاولة العثور على المستخدم في قاعدة البيانات
        $user = User::where('email', $request->email)->first();

        // التأكد من وجود المستخدم وإذا كانت كلمة السر صحيحة
        if ($user && Hash::check($request->password, $user->password)) {
            // تسجيل الدخول باستخدام Auth::login()
            Auth::login($user);

            // إنشاء توكن جديد للمستخدم
            $token = $user->createToken('YourAppName')->plainTextToken;

            // إرسال التوكن مع بيانات المستخدم
            return response()->json([
                'message' => 'تم تسجيل الدخول بنجاح!',
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        // إذا كانت البيانات غير صحيحة
        throw ValidationException::withMessages([
            'email' => ['البريد الإلكتروني أو كلمة السر غير صحيحة.'],
        ]);
    }
}

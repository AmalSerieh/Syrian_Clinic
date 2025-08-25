<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // للتحقق: عرض محتويات الـ Request كاملاً
        \Log::info('Request data:', $request->all());
        \Log::info('Files:', $request->file() ?: ['no files']);

        if ($request->hasFile('photo')) {
            \Log::info('Photo file details:', [
                'name' => $request->file('photo')->getClientOriginalName(),
                'size' => $request->file('photo')->getSize(),
                'mime' => $request->file('photo')->getMimeType()
            ]);
        }

        // القواعد مع تجاهل البريد الحالي للمستخدم
        $rules = [
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', 'min:8', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        $validated = $request->validate($rules);

        \Log::info('Validation passed');

        DB::beginTransaction();

        try {
            // تحديث البيانات الأساسية
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            // تحديث كلمة المرور إذا تم إدخالها
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            // تحديث الصورة إذا وجدت
            if ($request->hasFile('photo')) {
                \Log::info('Processing photo upload');

                // حذف الصورة القديمة إذا كانت موجودة
                if ($user->admin && $user->admin->photo) {
                    Storage::disk('public')->delete($user->admin->photo);
                }

                // حفظ الصورة الجديدة
                $path = $request->file('photo')->store('avatars', 'public');

                // تأكد من وجود سجل admin
                if (!$user->admin) {
                    $user->admin()->create(['photo' => $path]);
                } else {
                    $user->admin->photo = $path;
                    $user->admin->save();
                }

                \Log::info('Photo saved to: ' . $path);
            }

            $user->save();
            DB::commit();

            return back()->with('success', 'تم تحديث بيانات الحساب بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating profile: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }
}

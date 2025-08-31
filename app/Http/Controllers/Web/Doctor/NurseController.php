<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Nurse;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NurseController extends Controller
{
    public function index()
    {
        // نجيب كل الممرضين مع المستخدم (الاسم، الايميل ...) ومع الخدمات
        $doctor = auth()->user()->doctor; // الطبيب الحالي

        // نجيب فقط ممرضين هذا الطبيب مع المستخدم والخدمات
        $nurses = Nurse::with(['user', 'services'])
            ->where('doctor_id', $doctor->id)
            ->get();

        return view('doctor.home.nurses', compact('nurses'));
    }




    public function nurseStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'date_of_appointment' => 'required|date',
            'gender' => 'required|in:male,female',
            'photo' => 'nullable|image|max:4096',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id'
        ]);

        $doctor = Auth::user()->doctor;

        // إنشاء مستخدم جديد
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt('nurse'),
            'role' => 'nurse',
        ]);

        // حساب مجموع أسعار الخدمات
        $totalSalary = 0;
        if ($request->services) {
            $totalSalary = Service::whereIn('id', $request->services)->sum('serv_price');
        }

        // إنشاء الممرضة
        $nurse = Nurse::create([
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'date_of_appointment' => $request->date_of_appointment,
            'gender' => $request->gender,
            'salary' => $totalSalary,
            'photo' => $request->file('photo')
                ? $request->file('photo')->store('nurses', 'public')
                : null,
        ]);

        // ربط الخدمات
        if ($request->services) {
            $nurse->services()->sync($request->services);
        }

        return redirect()->back()->with('status', 'تم إضافة الممرضة بنجاح');
    }


}

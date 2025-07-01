<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Models\Room;
use App\Models\Secretary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.home.dashboard');
    }
    public function secretary_add()
    {
        if (User::where('role', 'secretary')->exists()) {
            return redirect()->route('admin.secretary')->with('message', 'تمت إضافة السكرتيرة بالفعل.');
        }
        return view('admin.home.secretary-add');
    }

    public function secretary_store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone' => ['required', 'digits:10', 'numeric'],
            //'role' => ['required', 'in:secretary'],
            'date_of_appointment' => ['required', 'date'],
        ]);

        if ($validated['role'] === 'secretary' && User::where('role', 'secretary')->exists()) {
            return back()
                ->withErrors(['role' => 'يوجد بالفعل حساب سكرتيرة في النظام'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'role' => 'secretary',
                'created_by' => 'admin',
                'created_by_user_id' => auth()->id(),
            ]);

            Secretary::create([
                'user_id' => $user->id,
                'photo' => 'avatars/6681221.png',
                'date_of_appointment' => $validated['date_of_appointment'],
            ]);

            DB::commit();

            return redirect()->route('admin.secretary')->with('message', 'تمت إضافة السكرتيرة بنجاح!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة السكرتيرة'])->withInput();
        }
    }

    public function secretary()
    {
        $secretary = User::where('role', 'secretary')->latest()->first(); // فقط سكرتيرة واحدة

        return view('admin.home.secretary', compact('secretary'));
    }
    public function secretary_replace($id)
    {
        $secretary = User::where('id', $id)->where('role', 'secretary')->firstOrFail();

        return view('admin.home.secretary-edit', compact('secretary'));
    }
    public function secretary_update(Request $request)
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone' => ['sometimes', 'digits:10'],
            'password' => ['sometimes', 'confirmed', 'min:8'], // الباسوورد اختياري في التعديل
            'date_of_appointment' => ['sometimes', 'date'],
        ]);

        $secretary = User::where('role', 'secretary')->first();

        if (!$secretary) {
            return redirect()->back()->withErrors(['message' => 'لا توجد سكرتيرة حالياً']);
        }
        DB::beginTransaction();
        try {
            $secretary->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->filled('password') ? Hash::make($request->password) : $secretary->password,
            ]);

            // تحديث بيانات السكرتيرة
            $secretary->secretary()->update([
                'date_of_appointment' => $request->date_of_appointment,
                // يمكنك لاحقًا السماح برفع صورة جديدة
            ]);
            DB::commit();

            return redirect()->route('admin.secretary')->with('message', 'تم استبدال السكرتيرة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء استبدال السكرتيرة'])->withInput();
        }
    }
    public function doctor_add()
    {
        $language = app()->getLocale(); // 'ar' أو 'en'

        // جلب كل الغرف مع عدد الأطباء الموجودين
        $rooms = Room::withCount('doctors')->get();
        // فلترة الغرف المتاحة فقط (التي لم تصل للحد الأقصى من الأطباء)
        $availableRooms = $rooms->filter(function ($room) {
            return $room->doctors_count <= $room->room_capacity;
        })->map(function ($room) use ($language) {
            return [
                'id' => $room->id,
                'name' => $language === 'ar' ? $room->name_ar : $room->name_en,
                'specialty' => $language === 'ar' ? $room->specialty_ar : $room->specialty_en,
            ];
        });
        // إذا لم يكن هناك أي غرف متاحة → رجوع برسالة خطأ
        if ($availableRooms->isEmpty()) {
            return redirect()->back()->withErrors(['room' => 'لا توجد غرف متاحة حالياً. تم الوصول إلى الحد الأقصى لعدد الأطباء في جميع الغرف.']);
        }

        return view('admin.doctor.doctor-add', ['rooms' => $availableRooms]);
    }
    public function doctor_store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone' => ['required', 'digits:10', 'numeric'],
           // 'role' => ['required', 'in:doctor'],
            'date_of_appointment' => ['required', 'date'],
            'room_id' => 'required|exists:rooms,id',
        ]);



        DB::beginTransaction();

        try {
            $room = Room::findOrFail($validated['room_id']);

            // تحقق من السعة
            if ($room->doctors()->count() >= $room->capacity) {
                return back()->withErrors(['room_id' => 'هذه الغرفة ممتلئة بالفعل بـ 3 أطباء.'])->withInput();
            }
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'role' => 'doctor',
                'created_by' => 'admin',
                'created_by_user_id' => auth()->id(),
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'photo' => 'doctor-profile-photos/FWwTAhMEke2R8fM2CpSZ4NBN2IXWACAD9v1eVbdc.jpg',
                'date_of_appointment' => $validated['date_of_appointment'],
            ]);
            DoctorProfile::create([
                'doctor_id' => $doctor->id,
                'specialty_ar' => $room->specialty_ar,
                'specialty_en' => $room->specialty_en,
            ]);

            DB::commit();

            return redirect()->route('admin.doctor')->with('message', 'تمت إضافة الطبيب بنجاح!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة الطبيب'])->withInput();
        }
    }
    public function doctor()
    {
        $doctors = Doctor::with(['user', 'doctorProfile', 'room'])->get();

        return view('admin.home.doctor', compact('doctor'));
    }
    public function doctor_details($id)
    {
        $doctor = Doctor::with(['user', 'doctorProfile', 'room'])->findOrFail($id);

        return view('admin.doctor.doctor-details', compact('doctor'));
    }
    public function doctor_edit($id)
    {
        $doctor = Doctor::with('user', 'doctorProfile', 'room')->findOrFail($id);
        $rooms = Room::all(); // لعرض الغرف المتاحة
        return view('admin.doctor.doctor-edit', compact('doctor', 'rooms'));
    }
    public function doctor_update(Request $request, $id)
    {
        $doctor = Doctor::with('user', 'doctorProfile')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $doctor->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $doctor->update([
            'room_id' => $validated['room_id'],
        ]);

        return redirect()->route('admin.doctor')->with('message', 'تم تحديث بيانات الطبيب بنجاح');
    }
    public function doctor_destroy($id)
    {
        $doctor = Doctor::findOrFail($id);

        // حذف المستخدم المرتبط
        $doctor->user()->delete();
        $doctor->doctorProfile()->delete();
        // سيتم حذف doctor_profile تلقائياً إذا مفعّل cascading في العلاقة

        return redirect()->route('admin.doctor')->with('message', 'تم حذف الطبيب بنجاح!');
    }
    





}

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
        $secretary = User::where('role', 'secretary')->latest()->first(); // فقط سكرتيرة واحدة
        return view('admin.home.dashboard', compact('secretary'));
    }
    public function secretary_add()
    {
        if (User::where('role', 'secretary')->exists()) {
            return redirect()->route('admin.secretary')->with('message', 'تمت إضافة السكرتيرة بالفعل.');
        }
        return view('admin.home.secretary-add');
    }

    public function secretary_store(Request $request)
    {//dd($request);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone' => ['required', 'digits:10', 'numeric'],
            'date_of_appointment' => ['required', 'date'],
            'gender' => 'required|string|in:male,female',
        ]);

        if (User::where('role', 'secretary')->exists()) {
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
                'photo' => 'avatars/defaults.jpg',
                'gender' => $validated['gender'],
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'digits:10'],
            'password' => ['required', 'confirmed', 'min:8'], // الباسوورد اختياري في التعديل
            'date_of_appointment' => ['required', 'date'],
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
                'name' => $language === 'ar' ? $room->room_name_ar : $room->room_name_en,
                'specialty' => $language === 'ar' ? $room->room_specialty_ar : $room->room_specialty_en,
            ];
        });
        //    dd($availableRooms);

        // إذا لم يكن هناك أي غرف متاحة → رجوع برسالة خطأ
        if ($availableRooms->isEmpty()) {
            return redirect()->back()->withErrors(['room' => 'لا توجد غرف متاحة حالياً. تم الوصول إلى الحد الأقصى لعدد الأطباء في جميع الغرف.']);
        }

        return view('admin.doctor.add-doctor', ['rooms' => $availableRooms]);
    }
    public function doctor_store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone' => ['required', 'digits:10', 'numeric'],
            'date_of_appointment' => ['required', 'date'],
            'room_id' => 'required|exists:rooms,id',
            'gender' => ['required', 'in:male,female'],
        ]);
        //dd($validated);


        DB::beginTransaction();

        try {
            $room = Room::findOrFail($validated['room_id']);

            // تحقق من السعة
            if ($room->doctors()->count() >= $room->room_capacity) {
               // dd('الغرفة ممتلئة',$room->room_capacity); // هنا
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
                'photo' => 'doctor-profile-photos/default.jpg',
                'room_id' => $validated['room_id'],
                'date_of_appointment' => $validated['date_of_appointment'],
            ]);
            DoctorProfile::create([
                'doctor_id' => $doctor->id,
                'specialty_ar' => $room->room_specialty_ar,
                'specialty_en' => $room->room_specialty_en,
                'gender' => $validated['gender'],
            ]);

            DB::commit();
            //return back()->with(['message' => 'yes yes ']);
            return redirect()->route('admin.doctor')->with('status', 'تمت إضافة الطبيب بنجاح!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة الطبيب'])->withInput();
        }
    }
    public function doctor()
    {
        $doctors = Doctor::with(['user', 'doctorProfile', 'room'])->get();

        return view('admin.doctor.doctor', compact('doctors'));
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

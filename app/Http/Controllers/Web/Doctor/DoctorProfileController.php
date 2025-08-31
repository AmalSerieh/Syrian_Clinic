<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorProfileRequest;
use App\Models\DoctorProfile;
use App\Models\Nurse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorProfileController extends Controller
{
    public function storeProfile1(StoreDoctorProfileRequest $request)
    {
        $doctor = auth()->user()->doctor;

        if ($doctor->doctorProfile) {
            return back()->with('error', 'تم إدخال المعلومات سابقًا.');
        }

        $data = $request->validated();

        if ($request->hasFile('cer_images')) {
            $data['cer_images'] = $request->file('cer_images')->store('certificates', 'public');
        }

        $data['doctor_id'] = $doctor->id;

        DoctorProfile::create($data);

        return back()->with('success', 'تم إنشاء الملف المهني بنجاح.');
    }
    public function update(StoreDoctorProfileRequest $request)
    {
        $doctor = auth()->user()->doctor;
        $profile = $doctor->doctorProfile;

        $data = $request->validated();

        if ($request->hasFile('cer_images')) {
            if ($profile->cer_images) {
                Storage::delete($profile->cer_images);
            }
            $data['cer_images'] = $request->file('cer_images')->store('certificates', 'public');
        }

        $profile->update($data);

        return back()->with('success', 'تم تعديل الملف المهني بنجاح.');
    }

    public function createProfile()
    {
        $doctor = auth()->user()->doctor;
        /*  if ($doctor->doctorProfile) {
             return redirect()->route('doctor.dashboard')->with('status', 'تم إدخال الملف المهني مسبقًا.');
         } */
        return view('doctor.create');

    }
    public function storeProfile(StoreDoctorProfileRequest $request)
    {
        $doctor = auth()->user()->doctor;
        $profileData = $request->validated();
        // رفع صورة الشهادة إن وجدت
        // معالجة صورة الشهادة
        if ($request->hasFile('cer_images')) {
            // حذف الصورة القديمة إن وجدت
            if ($doctor->doctorProfile && $doctor->doctorProfile->cer_images) {
                Storage::disk('public')->delete($doctor->doctorProfile->cer_images);
            }

            // رفع الصورة الجديدة
            $profileData['cer_images'] = $request->file('cer_images')->store('certificates', 'public');
        } else {
            // الحفاظ على الصورة القديمة إن وجدت
            if ($doctor->doctorProfile) {
                $profileData['cer_images'] = $doctor->doctorProfile->cer_images;
            }
        }

        // تحقق: إذا كان يوجد ملف مهني للطبيب → نحدثه، إذا لا → ننشئه
        // تحقق: إذا كان يوجد ملف مهني للطبيب → نحدثه، إذا لا → ننشئه
        if ($doctor->doctorProfile) {
            $doctor->doctorProfile->update($profileData);
        } else {
            $doctor->doctorProfile()->create($profileData);
        }
        return redirect()->route('doctor.dashboard')->with('status', 'تم حفظ الملف المهني بنجاح.');
    }
    public function showProfile()
    {
         // نجيب كل الممرضين مع المستخدم (الاسم، الايميل ...) ومع الخدمات
        $doctor = auth()->user()->doctor; // الطبيب الحالي

        // نجيب فقط ممرضين هذا الطبيب مع المستخدم والخدمات
        $nurses = Nurse::with(['user', 'services'])
            ->where('doctor_id', $doctor->id)
            ->get();
        return view('doctor.showProfile',compact('nurses'));

    }
    public function edit($id)
    {
        $doctorProfile = DoctorProfile::findOrFail($id);
        return view('doctor.editProfile', compact('doctorProfile'));
    }
    public function updateProfile(Request $request, $id)
    {

        $profile = DoctorProfile::findOrFail($id);
        //dd($request->cer_images);
        $data = $request->validate([
            'date_birth' => 'required|date',
            'biography' => 'nullable|string',
            'cer_name' => 'nullable|string|max:255',
            'cer_place' => 'nullable|string|max:255',
            'cer_date' => 'nullable|date',
            'cer_images' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'exp_place' => 'nullable|string|max:255',
            'exp_years' => 'nullable|numeric',
        ]);

        // 2. معالجة الصورة
    if ($request->hasFile('cer_images')) {
        // 2.1 حذف الصورة القديمة
        if ($profile->cer_images) {
            Storage::disk('public')->delete($profile->cer_images);
        }

        // 2.2 رفع الصورة الجديدة
        $data['cer_images'] = $request->file('cer_images')->store(
            'certifications',
            'public'
        );
    } else {
        // 2.3 الاحتفاظ بالصورة الحالية
        $data['cer_images'] = $profile->cer_images;
    }

        $profile->update($data);

        return redirect()->route('doctor-profile.show')->with('success', 'تم تحديث الملف المهني بنجاح.');
    }

}

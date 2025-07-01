<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorProfileRequest;
use App\Models\DoctorProfile;
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

        if ($doctor->doctorProfile) {
            return redirect()->route('doctor.dashboard')->with('status', 'تم إدخال الملف المهني مسبقًا.');
        }
        return view('doctor.create');

    }
    public function storeProfile(StoreDoctorProfileRequest $request)
    {
        $doctor = auth()->user()->doctor;

        if ($doctor->doctorProfile) {
            return redirect()->route('doctor.dashboard')->with('info', 'تم إدخال الملف المهني مسبقًا.');
        }

        // رفع صورة الشهادة إن وجدت
        $certImagePath = null;
        if ($request->hasFile('cer_images')) {
            $certImagePath = $request->file('cer_images')->store('certificates', 'public');
        }

        // إنشاء الملف المهني للطبيب
        DoctorProfile::create([
            'doctor_id' => $doctor->id,
            'specialist_ar' => $request->specialist_ar,
            'specialist_en' => $request->specialist_en,
            'biography' => $request->biography,
            'gender' => $request->gender,
            'date_birth' => $request->date_birth,
            'cer_place' => $request->cer_place,
            'cer_name' => $request->cer_name,
            'cer_images' => $certImagePath,
            'cer_date' => $request->cer_date,
            'exp_place' => $request->exp_place,
            'exp_yesrs' => $request->exp_yesrs,
        ]);

        return redirect()->route('doctor.dashboard')->with('success', 'تم حفظ الملف المهني بنجاح.');
    }
    public function showProfile()
    {
        return view('doctor.showProfile');

    }
    public function edit($id)
    {
        $doctorProfile = DoctorProfile::findOrFail($id);
        return view('doctor.editProfile', compact('doctorProfile'));
    }
    public function updateProfile(Request $request, $id)
    {
        $profile = DoctorProfile::findOrFail($id);

        $data = $request->validate([
            'specialist' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_birth' => 'required|date',
            'biography' => 'nullable|string',
            'cer_name' => 'nullable|string|max:255',
            'cer_place' => 'nullable|string|max:255',
            'cer_date' => 'nullable|date',
            'cer_images' => 'nullable|image|max:2048',
            'exp_place' => 'nullable|string|max:255',
            'exp_years' => 'nullable|numeric',
        ]);

        if ($request->hasFile('cer_images')) {
            $data['cer_images'] = $request->file('cer_images')->store('certifications', 'public');
        }

        $profile->update($data);

        return redirect()->route('doctor-profile.show')->with('success', 'تم تحديث الملف المهني بنجاح.');
    }

}

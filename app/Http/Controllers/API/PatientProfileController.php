<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePatientProfileRequest;
use App\Http\Resources\PatientProfileResource;
use App\Models\Patient_profile;
use App\Models\Patient_record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientProfileController extends Controller
{

    public function store1(StorePatientProfileRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مسجل الدخول.'], 401);
        }

        if (!$user->patient) {
            return response()->json(['message' => 'هذا المستخدم لا يملك ملف مريض.'], 404);
        }

        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => 'لا يوجد سجل طبي بعد.'], 404);
        }

        if ($record->patient_profile) {
            return response()->json(['message' => 'تمت إضافة البيانات مسبقًا، لا يمكنك التعديل.'], 403);
        }
        $patientRecord = auth()->user()->patient->patient_record;


        $profile = $patientRecord->patient_profile()->create($request->validated());

        return new PatientProfileResource($profile);
    }

    public function show()
    {
        $user = Auth::user();

        if (!$user->patient || !$user->patient?->patient_record) {
            return response()->json(['message' => 'هذا المستخدم لا يملك سجلًا طبيًا.'], 404);
        }

        $profile = $user->patient->patient_record->patient_profile;

        if (!$profile) {
            return response()->json(['message' => 'لم يتم إدخال بيانات الملف الشخصي بعد.'], 404);
        }
       // $this->authorize('view', $profile);

        return new PatientProfileResource($profile);
    }
    public function update(StorePatientProfileRequest $request, Patient_profile $profile)
    {
        $this->authorize('update', $profile); // تحقق من الصلاحيات باستخدام الـ Policy

        $profile->update($request->validated());

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح.',
            'patient_profile' => new PatientProfileResource($profile),
        ]);
    }


}

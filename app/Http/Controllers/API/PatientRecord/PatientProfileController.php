<?php

namespace App\Http\Controllers\API\PatientRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PatientRecord\PatientProfileRequest;
use App\Http\Resources\Api\PateintRecord\PatientProfileResource;
use App\Models\Patient;
use App\Models\Patient_profile;
use App\Services\Api\PateintRecord\PatientProfileService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PatientProfileController extends Controller
{
    protected $service;

    public function __construct(PatientProfileService $service)
    {
        $this->service = $service;
    }

    public function store(PatientProfileRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => trans('auth.auth_not')], 401);
        }
        if (!$user->patient) {
            return response()->json(['message' => trans('auth.patient_not')], 403);
        }

        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        if ($record->patient_profile) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }
        if ($record->profile_submitted) {
            return response()->json(['message' => trans('message.profile_already_submitted')], 400);
        }

        $this->authorize('create', Patient_profile::class);

        $data = $request->validated();
        $data['patient_record_id'] = Auth::user()->patient->patient_record->id;

        DB::beginTransaction();
        try {
            $profile = $this->service->create($data);
            // $record->update(['profile_submitted' => true]);
            DB::commit();

            return (new PatientProfileResource($profile))->additional([
                'message' => __('message.profile_submitted_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'حدث خطأ أثناء الحفظ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // ✅ للمريض المسجل حاليًا
    public function showMyProfile()
    {
        $user = Auth::user();

        if (!$user->isPatient()) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $profile = $user->patient?->patient_record?->patient_profile;

        if (!$profile) {
            return response()->json(['message' => trans('message.no_data')], 404);
        }

        // $this->authorize('view', $profile);
        $canView = Gate::allows('view', $profile);

        // فك تشفير الحقول المشفرة إذا لزم الأمر
        // فك تشفير الحقول إذا لزم الأمر
        if (isset($profile->encryptable)) {
            foreach ($profile->encryptable as $field) {
                if (!empty($profile->$field)) {
                    try {
                        $profile->$field = Crypt::decrypt($profile->$field);
                    } catch (DecryptException $e) {
                        // التعامل مع خطأ فك التشفير
                    }
                }
            }
        }

        return (new PatientProfileResource($profile))->additional([
            'message' => __('message.profile_submitted_success'),
        ]);
    }// ✅ للطبيب الذي يعرض ملف مريض معيّن
    // ✅ عرض سجل مريض معين بواسطة الطبيب
    public function showForDoctor($patientId)
    {
        $user = Auth::user();

        if (!$user->isDoctor()) {
            return response()->json(['message' => 'غير مصرح لك.'], 403);
        }

        // الحصول على سجل المريض
        $patient = Patient::findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record || !$record->patient_profile) {
            return response()->json(['message' => 'لا يوجد بيانات لهذا المريض.'], 404);
        }

        // صلاحيات عرض الطبيب (يمكنك ربطها بـ policy لاحقًا إن أردت)
        $this->authorize('view', $record->patient_profile);

        return new PatientProfileResource($record->patient_profile);
    }

    //✅ تأكيد الإرسال
    public function saveRecord()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        // تحقق إذا كان السجل محفوظ سابقًا (مثلاً نعتبر أنه محفوظ إذا profile_submitted = 1)
        if (
            $record->profile_submitted && $record->diseases_submitted && $record->operations_submitted &&
            $record->medicalAttachments_submitted && $record->allergies_submitted && $record->family_history_submitted &&
            $record->medications_submitted && $record->medicalfiles_submitted
        ) {

            return response()->json(['message' => trans('message.already_saved')], 200);
        }

        // إذا لم يكن محفوظًا بعد، قم بالحفظ لأول مرة
        $record->update([
            'profile_submitted' => 1,
            'diseases_submitted' => 1,
            'operations_submitted' => 1,
            'medicalAttachments_submitted' => 1,
            'allergies_submitted' => 1,
            'family_history_submitted' => 1,
            'medications_submitted' => 1,
            'medicalfiles_submitted' => 1
        ]);

        return response()->json(['message' => trans('message.patient_record_saved')], 200);
    }



    /*  public function update(PatientProfileRequest $request, $id)
     {
         $profile = $this->service->repo->getByPatientId($id);
         $this->authorize('update', $profile);

         $updated = $this->service->update($request->validated(), $id);
         return new PatientProfileResource($updated);
     } */
}

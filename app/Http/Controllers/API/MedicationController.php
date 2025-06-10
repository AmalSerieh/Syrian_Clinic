<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMedicationRequest;
use App\Http\Resources\MedicationsResource;
use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicationController extends Controller
{
    //عرض جميع الأدوية✅
    public function index()
    {
        $user = Auth::user();
        $record = $user->patient->patient_record;
        if (!$record) {
            return response()->json(['message' => trans('message.no_record')]);
        }
        $medication = $record->medications;
        if ($medication->isEmpty()) {
            return response()->json(['message' => trans('message.not_filled_yet')]);
        }
        $this->authorize('viewAny', [Medication::class, $record]);
        return MedicationsResource::collection($medication);
    }
    //عرض دواء واحد ✅
    public function show(Medication $medication)
    {
        $this->authorize('view', $medication);
        return new MedicationsResource($medication);
    }
    //إنشاء دواء ✅
    public function store(StoreMedicationRequest $request)
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        if ($record->medications_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('create', [Medication::class, $record]);

        // التحقق من عدم وجود دواء بنفس الاسم مسبقًا
        $exists = $record->medications()
            ->where('med_name', $request->med_name)
            ->exists();

        if ($exists) {
            return response()->json(['message' => trans('message.medication_already_exists')], 422);
        }

        $medication = $record->medications()->create($request->validated());
        return new MedicationsResource($medication);
    }

    //✅ تأكيد الإرسال
    public function submit()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('confirmSubmission', [Medication::class, $record]);

        // التحقق من وجود أدوية
        if ($record->medications->isEmpty()) {
            return response()->json(['message' => trans('message.no_medications_found')], 404);
        }

        if ($record->medications_submitted) {
            return response()->json(['message' => trans('message.medication_already_submitted')], 400);
        }

        $record->update(['medications_submitted' => 1]);

        if ($record->medications_submitted == 1) {
            return response()->json(['message' => trans('message.medication_submitted_success')]);
        } else {
            return response()->json(['message' => trans('message.medication_submitted_failed')], 500);
        }
    }

    // ✅ تعديل دواء للطبيب
    public function update(StoreMedicationRequest $request, Medication $medication)
    {
        $this->authorize('update', $medication);
        $medication->update($request->validated());

        return new MedicationsResource($medication);
    }

    // ✅ حذف دواء للطبيب
    public function destroy(Medication $medication)
    {
        $this->authorize('delete', $medication);
        $medication->delete();

        return response()->json(['message' => trans('message.medication_deleted')]);
    }

}

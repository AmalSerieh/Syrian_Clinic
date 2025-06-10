<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDiseasesRequest;
use App\Http\Resources\DiseasesResource;
use App\Models\Disease;
use App\Models\Patient_record;
use Illuminate\Support\Facades\Auth;

class DiseasesController extends Controller
{
    // ✅ عرض جميع الأمراض
    public function index()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record ?? null;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $diseases = $record->diseases;

        if ($diseases->isEmpty()) {
            return response()->json(['message' => trans('message.not_filled_yet')], 404);
        }

        $this->authorize('viewAny', [Disease::class, $record]);

        return DiseasesResource::collection($diseases);
    }

    // ✅ عرض مرض واحد
    public function show(Disease $disease)
    {
        $this->authorize('view', $disease);
        return new DiseasesResource($disease);
    }

    // ✅ إنشاء مرض
    public function store(StoreDiseasesRequest $request)
    {
        $user = auth()->user();
        $record = $user->patient->patient_record ?? null;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }
        if ($record->diseases_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }
        $this->authorize('create', [Disease::class, $record]);

        // التحقق من وجود مرض بنفس الاسم في السجل
        $exists = $record->diseases()
            ->where('dis_name', $request->dis_name)
            ->where('dis_type', $request->dis_type)
            ->exists();

        if ($exists) {
            return response()->json(['message' => trans('message.disease_already_exists')], 422);
        }

        $disease = $record->diseases()->create($request->validated());

        return new DiseasesResource($disease);
    }


    // ✅ تأكيد الإرسال
    public function submit()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('confirmSubmission', [Disease::class, $record]);

        // التحقق من وجود أدوية
        if ($record->diseases->isEmpty()) {
            return response()->json(['message' => trans('message.no_diseasess_found')], 404);
        }

        if ($record->diseases_submitted) {
            return response()->json(['message' => trans('message.disease_already_submitted')], 400);
        }

        $record->update(['diseases_submitted' => 1]);

        if ($record->diseases_submitted == 1) {
            return response()->json(['message' => trans('message.disease_submitted_success')]);
        } else {
            return response()->json(['message' => trans('message.disease_submitted_failed')], 500);
        }
    }

    // ✅ تعديل مرض للطبيب
    public function update(StoreDiseasesRequest $request, Disease $disease)
    {
        $this->authorize('update', $disease);
        $disease->update($request->validated());

        return new DiseasesResource($disease);
    }

    // ✅ حذف مرض للطبيب
    public function destroy(Disease $disease)
    {
        $this->authorize('delete', $disease);
        $disease->delete();

        return response()->json(['message' => trans('message.disease_deleted')]);
    }

    // ✅ استخراج سجل المريض حسب نوع المستخدم
    /*    protected function getRecord(): Patient_record
       {
           $user = Auth::user();

           if ($user->doctor) {
               $recordId = request('patient_record_id');
               if (is_array($recordId)) {
                   abort(400, trans('messages.invalid_record_id'));
               }
               return Patient_record::findOrFail($recordId);
           }

           if ($user->patient) {
               return $user->patient->patient_record;
           }

           abort(403, trans('messages.not_authorized'));
       } */
}

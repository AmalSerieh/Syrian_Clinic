<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAllergyRequest;
use App\Http\Resources\AllergiesResource;
use App\Models\Allergy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllergyController extends Controller
{
     //عرض جميع الحساسيات ✅
    public function index()
    {
        $user = Auth::user();
        $record = $user->patient->patient_record;
        if (!$record) {
            return response()->json(['message' => trans('message.no_record')]);
        }
        $allergy = $record->allergies;
        if ($allergy->isEmpty()) {
            return response()->json(['message' => trans('message.not_filled_yet')]);
        }
        $this->authorize('viewAny', [Allergy::class, $record]);
        return AllergiesResource::collection($allergy);
    }

    //عرض حساسية واحد ✅
    public function show(Allergy $allergy)
    {
        $this->authorize('view', $allergy);
        return new AllergiesResource($allergy);
    }

    //إنشاء حساسية ✅
    public function store(StoreAllergyRequest $request)
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        if ($record->allergies_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('create', [Allergy::class, $record]);

        // التحقق من عدم وجود حساسية بنفس الاسم والتاريخ مسبقًا
        $exists = $record->allergies()
            ->where('aller_name', $request->aller_name)
            ->exists();

        if ($exists) {
            return response()->json(['message' => trans('message.allergy_already_exists')], 422);
        }



        $allergy = $record->allergies()->create($request->validated());

        return new AllergiesResource($allergy);
    }


    //✅ تأكيد الإرسال
    public function submit()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('confirmSubmission', [Allergy::class, $record]);

        // التحقق من وجود حساسيات
        if ($record->allergies->isEmpty()) {
            return response()->json(['message' => trans('message.no_allergys_found')], 404);
        }

        if ($record->allergies_submitted) {
            return response()->json(['message' => trans('message.allergy_already_submitted')], 400);
        }

        $record->update(['allergies_submitted' => 1]);

        if ($record->allergies_submitted == 1) {
            return response()->json(['message' => trans('message.allergy_submitted_success')]);
        } else {
            return response()->json(['message' => trans('message.allergy_submitted_failed')], 500);
        }
    }

    // ✅ تعديل حساسية للطبيب

    public function update(StoreAllergyRequest $request, Allergy $allergy)
    {
        $this->authorize('update', $allergy);
        $allergy->update($request->validated());

        return new AllergiesResource($allergy);
    }

    // ✅ حذف حساسية للطبيب
    public function destroy(Allergy $allergy)
    {
        $this->authorize('delete', $allergy);
        $allergy->delete();

        return response()->json(['message' => trans('message.allergy_deleted')]);
    }
}

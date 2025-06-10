<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFamilyHistoryRequest;
use App\Http\Resources\FamilyHistoriesResource;
use App\Models\FamilyHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamilyHistoryController extends Controller
{
     //عرض جميع الحساسيات ✅
    public function index()
    {
        $user = Auth::user();
        $record = $user->patient->patient_record;
        if (!$record) {
            return response()->json(['message' => trans('message.no_record')]);
        }
        $familyHistory = $record->familyHistories;
        if ($familyHistory->isEmpty()) {
            return response()->json(['message' => trans('message.not_filled_yet')]);
        }
        $this->authorize('viewAny', [FamilyHistoriesResource::class, $record]);
        return familyHistoriesResource::collection($familyHistory);
    }

    //عرض حساسية واحد ✅
    public function show(FamilyHistory $familyHistory)
    {
        
        $this->authorize('view', $familyHistory);
        return new familyHistoriesResource($familyHistory);
    }

    //إنشاء حساسية ✅
    public function store(StoreFamilyHistoryRequest $request)
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        if ($record->family_history_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('create', [FamilyHistory::class, $record]);

        // التحقق من عدم وجود حساسية بنفس الاسم والتاريخ مسبقًا
        $exists = $record->familyHistories()
            ->where('family_name', $request->family_name)
            ->exists();

        if ($exists) {
            return response()->json(['message' => trans('message.FamilyHistory_already_exists')], 422);
        }



        $familyHistory = $record->familyHistories()->create($request->validated());

        return new FamilyHistoriesResource($familyHistory);
    }


    //✅ تأكيد الإرسال
    public function submit()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('confirmSubmission', [FamilyHistory::class, $record]);

        // التحقق من وجود حساسيات
        if ($record->familyHistories->isEmpty()) {
            return response()->json(['message' => trans('message.no_FamilyHistorys_found')], 404);
        }

        if ($record->family_history_submitted) {
            return response()->json(['message' => trans('message.FamilyHistory_already_submitted')], 400);
        }

        $record->update(['family_history_submitted' => 1]);

        if ($record->family_history_submitted == 1) {
            return response()->json(['message' => trans('message.FamilyHistory_submitted_success')]);
        } else {
            return response()->json(['message' => trans('message.FamilyHistory_submitted_failed')], 500);
        }
    }

    // ✅ تعديل حساسية للطبيب

    public function update(StoreFamilyHistoryRequest $request, FamilyHistory $familyHistory)
    {
        $this->authorize('update', $familyHistory);
        $familyHistory->update($request->validated());

        return new familyHistoriesResource($familyHistory);
    }

    // ✅ حذف حساسية للطبيب
    public function destroy(FamilyHistory $familyHistory)
    {
        $this->authorize('delete', $familyHistory);
        $familyHistory->delete();

        return response()->json(['message' => trans('message.FamilyHistory_deleted')]);
    }
}

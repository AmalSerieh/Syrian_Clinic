<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOperationRequest;
use App\Http\Resources\OperationsResource;
use App\Models\Operation;
use Illuminate\Support\Facades\Auth;

class OperationController extends Controller
{
    //عرض جميع العمليات ✅
    public function index()
    {
        $user = Auth::user();
        $record = $user->patient->patient_record;
        if (!$record) {
            return response()->json(['message' => trans('message.no_record')]);
        }
        $operation = $record->operations;
        if ($operation->isEmpty()) {
            return response()->json(['message' => trans('message.not_filled_yet')]);
        }
        $this->authorize('viewAny', [Operation::class, $record]);
        return OperationsResource::collection($operation);
    }

    //عرض عملية واحد ✅
    public function show(Operation $operation)
    {
        $this->authorize('view', $operation);
        return new OperationsResource($operation);
    }

    //إنشاء عملية ✅
    public function store(StoreOperationRequest $request)
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        if ($record->operations_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }
        $this->authorize('create', [Operation::class, $record]);

        // التحقق من عدم وجود عملية بنفس الاسم مسبقًا
        $exists = $record->operations()
            ->where('op_name', $request->op_name)
            ->where('op_date', $request->op_date)
            ->exists();

        if ($exists) {
            return response()->json(['message' => trans('message.operation_already_exists')], 422);
        }

        $operation = $record->operations()->create($request->validated());
        return new OperationsResource($operation);
    }

    //✅ تأكيد الإرسال
    public function submit()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('confirmSubmission', [Operation::class, $record]);

        // التحقق من وجود أدوية
        if ($record->operations->isEmpty()) {
            return response()->json(['message' => trans('message.no_operations_found')], 404);
        }

        if ($record->operations_submitted) {
            return response()->json(['message' => trans('message.operation_already_submitted')], 400);
        }

        $record->update(['operations_submitted' => 1]);

        if ($record->operations_submitted == 1) {
            return response()->json(['message' => trans('message.operation_submitted_success')]);
        } else {
            return response()->json(['message' => trans('message.operation_submitted_failed')], 500);
        }
    }

    // ✅ تعديل عملية للطبيب

    public function update(StoreOperationRequest $request, Operation $operation)
    {
        $this->authorize('update', $operation);
        $operation->update($request->validated());

        return new OperationsResource($operation);
    }

    // ✅ حذف عملية للطبيب
    public function destroy(Operation $operation)
    {
        $this->authorize('delete', $operation);
        $operation->delete();

        return response()->json(['message' => trans('message.operation_deleted')]);
    }
}

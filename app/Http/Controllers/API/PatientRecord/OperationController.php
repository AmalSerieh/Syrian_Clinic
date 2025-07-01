<?php

namespace App\Http\Controllers\API\PatientRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PatientRecord\OperationRequest;
use App\Http\Resources\Api\PateintRecord\OperationResource;
use App\Models\Operation;
use App\Services\Api\PateintRecord\OperationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationController extends Controller
{
    public function __construct(protected OperationService $service)
    {
    }
    public function store(OperationRequest $request)
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }
         if ($record->operations_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('create', Operation::class);

        $operation = $this->service->create($request->validated(),$record->id);

        return response()->json([
            'data' => new OperationResource($operation)
        ], 200);
    }
    public function index()
{
    $user = Auth::user();
    $record = $user->patient?->patient_record;

    if (!$record) {
        return response()->json(['message' => trans('message.no_record')], 404);
    }

    $this->authorize('viewAny', Operation::class);

    $operations = $record->operations;
    return OperationResource::collection($operations);
}

}

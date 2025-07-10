<?php

namespace App\Http\Controllers\API\PatientRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PatientRecord\MedicationRequest;
use App\Http\Resources\Api\PateintRecord\MedicationResource;
use App\Models\Medication;
use App\Services\Api\PateintRecord\MedicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediactionController extends Controller
{
    public function __construct(protected MedicationService $service)
    {
    }
    public function store(MedicationRequest $request)
    {
        $record = Auth::user()->patient?->patient_record;
        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }
        if ($record->medications_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('create', Medication::class);


        $medication = $this->service->create($request->validated(), $record->id);
        $this->service->updateTakenQuantity($medication);

        return new MedicationResource($medication);
    }
    // لجلب الأدوية (now/archive)
    public function index()
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;
        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $medications = $record->medications;

        return response()->json([
            'now' => MedicationResource::collection($medications->where('is_active', true)),
            'archive' => MedicationResource::collection($medications->where('is_active', false))
        ]);
    }
    public function show($id, MedicationService $service)
    {
        $medication = Medication::findOrFail($id);

        // نحسب ونحدث الكمية مباشرة قبل العرض
        $service->updateTakenQuantity($medication);

        return response()->json([
            'data' => $medication
        ]);
    }
}


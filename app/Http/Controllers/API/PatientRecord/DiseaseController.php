<?php

namespace App\Http\Controllers\API\PatientRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PatientRecord\DiseaseRequest;
use App\Http\Resources\Api\PateintRecord\DiseaseResource;
use App\Models\Disease;
use App\Services\Api\PateintRecord\DiseaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DiseaseController extends Controller
{
    public function __construct(protected DiseaseService $service)
    {
    }
    public function store(DiseaseRequest $diseaseRequest)
    { {
            $record = Auth::user()->patient->patient_record;

            if (!$record) {
                return response()->json(['message' => trans('message.no_record')], 404);
            }
             if ($record->diseases_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }
            $this->authorize('create', [Disease::class, $record]);

            $data = $diseaseRequest->validated() + ['patient_record_id' => $record->id];

            $Disease = $this->service->create($data);

            return new DiseaseResource($Disease);
        }
    }
   /*  public function update(DiseaseRequest $diseaseRequest, $id)
    {
        $record = Auth::user()->record;
        $data = $diseaseRequest->validated();
        $data['record_id'] = $record->id;
        $this->service->update($id, $data);
        return response()->json([
            'message' => 'Disease updated successfully'
        ], 200);

    } */
   /*  public function destroy($id)
    {
        $record = Auth::user()->record;
        $this->service->destroy($id);
        return response()->json([
            'message' => 'Disease deleted successfully'
        ], 200);

    } */
    public function show()
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;
       if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }
        $diseases = $user->patient->patient_record->diseases;


        if ($diseases->isEmpty()) {
            return response()->json(['message' => trans('message.no_data')], 404);
        }
        //  $this->authorize('viewAny', $diseases);

        $grouped = $this->service->getGroupedByPower($record->id);
        $groupedResources = [];

        foreach (['current', 'chronic'] as $level) {
            $items = collect($grouped[$level] ?? []);

            $filtered = $items->filter(function ($disease) use ($user) {
                return Gate::forUser($user)->allows('view', $disease);
            });

            $groupedResources[$level] = DiseaseResource::collection($filtered->values());
        }
        return response()->json([
            'message' => ' Diseases',
            'data' => $groupedResources,
        ], 200);
    }
}

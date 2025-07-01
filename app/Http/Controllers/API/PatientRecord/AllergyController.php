<?php

namespace App\Http\Controllers\API\PatientRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PatientRecord\AllergyRequest;
use App\Http\Resources\Api\PateintRecord\AllergyResource;
use App\Models\Allergy;
use App\Services\Api\PateintRecord\AllergyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AllergyController extends Controller
{
    public function __construct(protected AllergyService $service)
    {
    }

    public function store(AllergyRequest $request)
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => 'لا يوجد سجل طبي.'], 404);
        }
        if ($record->allergies_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('create', Allergy::class);
        $allergyData = $request->validated()['allergies'];

        // جلب الحساسية الموجودة لهذا المريض (name + aller_power)
        $existingAllergies = $record->allergies()
            ->get(['aller_name', 'aller_power'])
            ->map(function ($item) {
                return strtolower($item->aller_name) . '|' . strtolower($item->aller_power);
            })->toArray();

        // استخراج المدخلات التي تتكرر
        $duplicates = [];

        foreach ($allergyData as $entry) {
            $key = strtolower($entry['aller_name']) . '|' . strtolower($entry['aller_power']);
            if (in_array($key, $existingAllergies)) {
                $duplicates[] = [
                    'aller_name' => $entry['aller_name'],
                    'aller_power' => $entry['aller_power']
                ];
            }
        }

        if (!empty($duplicates)) {
            return response()->json([
                'message' => 'بعض الحساسيات مكررة بنفس الاسم والقوة.',
                'duplicates' => $duplicates
            ], 422);
        }

        // التخزين
        $inserted = $this->service->createMany($allergyData, $record->id);

        return response()->json([
            'message' => trans('message.allergy_saved'),
            'allergies' => AllergyResource::collection(collect($inserted)),
        ]);
    }
    public function storeOneByOne(AllergyRequest $request)
    {
        $record = Auth::user()->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('create', [Allergy::class, $record]);

        $data = $request->validated() + ['patient_record_id' => $record->id];

        $allergy = $this->service->create($data);

        return new AllergyResource($allergy);
    }

    public function show()
    {
        $record = Auth::user()?->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $data = Allergy::where('patient_record_id', $record->id)->get();
        return AllergyResource::collection($data);
    }
    public function showGrouped()
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }
        $allergies = $user->patient->patient_record->allergies;


        if ($allergies->isEmpty()) {
            return response()->json(['message' => trans('message.no_data')], 404);
        }
        //  $this->authorize('viewAny', $allergies);

        $grouped = $this->service->getGroupedByPower($record->id);
        $groupedResources = [];

        foreach (['strong', 'medium', 'weak'] as $level) {
            $items = collect($grouped[$level] ?? []);

            $filtered = $items->filter(function ($allergy) use ($user) {
                return Gate::forUser($user)->allows('view', $allergy);
            });

            $groupedResources[$level] = AllergyResource::collection($filtered->values());
        }


        return response()->json([
            'message' => 'تم جلب الحساسيات بنجاح.',
            'data' => $groupedResources,
        ]);
    }



}

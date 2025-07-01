<?php
namespace App\Repositories\Eloquent\Api\PateintRecord;

use App\Models\Disease;
use App\Repositories\Api\PateintRecord\DiseaseRepositoryInterface ;
use Illuminate\Support\Collection;
class DiseaseRepository implements DiseaseRepositoryInterface
{

    public function getByPatientRecord($recordId)
    {
        return Disease::where('patient_record_id', $recordId)->get();
    }
    public function create(array $data)
    {
        return Disease::create($data);
    }
    public function getByPatientRecordGroupedByPower(int $recordId): Collection
    {
        return Disease::where('patient_record_id', $recordId)
            ->get()
            ->groupBy('d_type')
            ->map(function ($group) {
                return $group->values(); // Reset keys
            })
            ;
    }
}

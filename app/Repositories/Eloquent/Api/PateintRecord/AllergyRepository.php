<?php
namespace App\Repositories\Eloquent\Api\PateintRecord;

use App\Models\Allergy;
use App\Repositories\Api\PateintRecord\AllergyRepositoryInterface;
use Illuminate\Support\Collection;
class AllergyRepository implements AllergyRepositoryInterface
{
    public function createMany(array $data)
    {
        return Allergy::insert($data);
    }

    public function getByPatientRecord($recordId)
    {
        return Allergy::where('patient_record_id', $recordId)->get();
    }
    public function create(array $data)
    {
        return Allergy::create($data);
    }
    public function getByPatientRecordGroupedByPower(int $recordId): Collection
    {
        return Allergy::where('patient_record_id', $recordId)
            ->get()
            ->groupBy('aller_power')
            ->map(function ($group) {
                return $group->values(); // Reset keys
            })
            ;
    }
}

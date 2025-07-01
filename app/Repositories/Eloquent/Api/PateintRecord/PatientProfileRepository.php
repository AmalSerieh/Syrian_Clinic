<?php
namespace App\Repositories\Eloquent\Api\PateintRecord;

use App\Models\Patient_profile;
use App\Repositories\Api\PateintRecord\PatientProfileRepositoryInterface;
class PatientProfileRepository implements PatientProfileRepositoryInterface
{
    public function store(array $data)
    {
        return Patient_profile::create($data);
    }

    public function getByPatientId($id): ?Patient_profile
    {
      //  return Patient_profile::where('patient_id', $patientId)->first();
        return Patient_profile::findOrFail($id);
    }

    public function update(array $data, $id)
    {

        $profile = Patient_profile::findOrFail($id);
        $profile->update($data);
        return $profile;
    }
}

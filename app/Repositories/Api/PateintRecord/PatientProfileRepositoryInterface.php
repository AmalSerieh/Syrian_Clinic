<?php
namespace App\Repositories\Api\PateintRecord;

use App\Models\Patient_profile;

interface PatientProfileRepositoryInterface
{
    public function store(array $data);
    public function getByPatientId($id);

    //public function update(Patient_profile $profile, array $data): Patient_profile;
    public function update(array $data, $id);
    // public function findById($id);
}

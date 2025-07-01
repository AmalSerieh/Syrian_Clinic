<?php
namespace App\Repositories\Eloquent\Api\PateintRecord;

use App\Models\MedicalFile;
use App\Repositories\Api\PateintRecord\MedicalFileRepositoryInterface;
class MedicalFileRepository implements MedicalFileRepositoryInterface{
   public function create(array $data)
    {
        return MedicalFile::create($data);
    }

    public function getByPatientRecord(int $recordId)
    {
        return MedicalFile::where('patient_record_id', $recordId)->get();
    }
}

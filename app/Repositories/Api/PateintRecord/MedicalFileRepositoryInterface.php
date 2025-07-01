<?php
namespace App\Repositories\Api\PateintRecord;
interface MedicalFileRepositoryInterface{
    public function create(array $data);
    public function getByPatientRecord(int $recordId);
}

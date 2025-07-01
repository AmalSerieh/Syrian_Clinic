<?php
namespace App\Repositories\Api\PateintRecord;
interface AllergyRepositoryInterface{
    public function createMany(array $data);
    public function getByPatientRecord($recordId);
    public function create(array $data);
     public function getByPatientRecordGroupedByPower(int $recordId);
}

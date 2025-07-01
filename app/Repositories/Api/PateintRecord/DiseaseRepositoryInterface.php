<?php

namespace App\Repositories\Api\PateintRecord;

interface DiseaseRepositoryInterface
{
     public function getByPatientRecord($recordId);
    public function create(array $data);
     public function getByPatientRecordGroupedByPower(int $recordId);
}

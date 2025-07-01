<?php

namespace App\Services\Api\PateintRecord;

use App\Models\Operation;
use App\Repositories\Api\PateintRecord\OperationRepositoryInterface;

class OperationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected OperationRepositoryInterface $repo)
    {
        //
    }
    public function create(array $data, int $recordId): Operation
    {
        return $this->repo->create([
            'patient_record_id' => $recordId,
            'op_name' => $data['op_name'],
            'op_doctor_name' => $data['op_doctor_name'],
            'op_hospital_name' => $data['op_hospital_name'],
            'op_date' => $data['op_date'],

        ]);
    }
}

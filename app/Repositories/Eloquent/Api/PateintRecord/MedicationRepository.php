<?php

namespace App\Repositories\Eloquent\Api\PateintRecord;

use App\Models\Medication;
use App\Repositories\Api\PateintRecord\MedicationRepositoryInterface;

class MedicationRepository implements MedicationRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function create(array $data) {
        return Medication::create($data);
    }
}

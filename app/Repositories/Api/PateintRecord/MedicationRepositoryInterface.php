<?php

namespace App\Repositories\Api\PateintRecord;

use App\Models\Medication;

interface MedicationRepositoryInterface
{
     public function create(array $data);
}

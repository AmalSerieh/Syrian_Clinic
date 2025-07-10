<?php

namespace App\Services\Api\PateintRecord;

use App\Models\Medication;
use App\Repositories\Api\PateintRecord\MedicationAlarmInterface;
use Illuminate\Support\Facades\Auth;

class MedicationAlarmService
{
    /**
     * Create a new class instance.
     */
   protected $repo;

    public function __construct(MedicationAlarmInterface $repo)
    {
        $this->repo = $repo;
    }
     public function createAlarms(Medication $medication, array $alarm_times)
    {
        return $this->repo->createMany($medication, $alarm_times);
    }
     public function deleteAlarm(\App\Models\MedicationAlarm $alarm)
    {
        return $this->repo->delete($alarm);
    }
      public function getUserAlarms()
    {
        return $this->repo->getUserAlarms(Auth::id()->patientRecord->id);
    }
}

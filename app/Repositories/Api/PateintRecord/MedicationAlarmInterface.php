<?php

namespace App\Repositories\Api\PateintRecord;

use App\Models\MedicationAlarm;

interface MedicationAlarmInterface
{
    public function createMany($medication, array $alarm_times);
    public function delete(MedicationAlarm $alarm);
    public function getUserAlarms($userId);
}

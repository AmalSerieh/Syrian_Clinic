<?php

namespace App\Repositories\Eloquent\Api\PateintRecord;

use App\Models\MedicationAlarm;
use App\Repositories\Api\PateintRecord\MedicationAlarmInterface;

class MedicationAlarmRepository implements MedicationAlarmInterface
{
    /**
     * Create a new class instance.
     */
    public function createMany($medication, array $alarm_times)
    {
        $alarms = [];
        foreach ($alarm_times as $time) {
            $alarms[] = MedicationAlarm::create([
                'patient_record_id'=>$medication->patient_record_id,
                'medication_id' => $medication->id,
                'alarm_time' => $time,
                'alarm_start_date' => $medication->med_start_date,
                'alarm_end_date' => $medication->med_end_date,
                'alarm_frequency' => $medication->med_frequency,
                'alarm_frequency_value' => $medication->med_frequency_value,
                'alarm_dosage_form' => $medication->med_dosage_form,
                'alarm_dose' => $medication->med_dose,
                'alarm_timing' => $medication->med_timing,
                'alarm_quantity_per_dose' => $medication->med_quantity_per_dose,
                'alarm_total_quantity' => $medication->med_total_quantity,
            ]);
        }
        return $alarms;
    }
    public function delete(MedicationAlarm $alarm)
    {
        return $alarm->delete();
    }
    public function getUserAlarms($userId)
    {
        return MedicationAlarm::whereHas('medication', fn($q) => $q->where('patient_record_id', $userId))
            ->with('medication')
            ->get();
    }
}

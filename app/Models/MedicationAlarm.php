<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationAlarm extends Model
{
    protected $fillable = [
        'patient_record_id',
        'medication_id',
        'alarm_time',
        'alarm_start_date',
        'alarm_end_date',
        'alarm_frequency',
        'alarm_frequency_value',
        'alarm_dosage_form',
        'alarm_dose',
        'alarm_timing',
        'alarm_quantity_per_dose',
        'alarm_total_quantity'
    ];
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}

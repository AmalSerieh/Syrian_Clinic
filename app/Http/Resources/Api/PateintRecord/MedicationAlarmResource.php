<?php

namespace App\Http\Resources\Api\PateintRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;
class MedicationAlarmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'patient_record_id' => $this->patient_record_id,
        'medication_id' => $this->medication_id,
        'alarm_time' => $this->alarm_time,
        'alarm_start_date' => $this->alarm_start_date,
        'alarm_end_date' => $this->alarm_end_date,
        'alarm_frequency' => $this->alarm_frequency,
        'alarm_frequency_value' => $this->alarm_frequency_value,
        'alarm_dosage_form' => $this->alarm_dosage_form,
        'alarm_dose' => $this->alarm_dose,
        'alarm_timing' => $this->alarm_timing,
        'alarm_quantity_per_dose' => $this->alarm_quantity_per_dose,
        'alarm_prescribed_by_doctor' => $this->alarm_prescribed_by_doctor, // لو مش مشفرة هنا، لا تحتاج فك تشفير

        'medication' => [
            'id' => $this->medication->id,
            'patient_record_id' => $this->medication->patient_record_id,
            // فك تشفير med_name
            'med_name' => $this->decryptValue($this->medication->med_name),
            'med_start_date' => $this->medication->med_start_date,
            'med_end_date' => $this->medication->med_end_date,
            'med_frequency' => $this->medication->med_frequency,
            'med_frequency_value' => $this->medication->med_frequency_value,
            'med_dosage_form' => $this->medication->med_dosage_form,
            'med_dose' => $this->medication->med_dose,
            'med_timing' => $this->medication->med_timing,
            // فك تشفير med_prescribed_by_doctor
            'med_prescribed_by_doctor' => $this->decryptValue($this->medication->med_prescribed_by_doctor),
            'med_total_quantity' => $this->medication->med_total_quantity,
            'med_taken_quantity' => $this->medication->med_taken_quantity,
            'is_active' => $this->medication->is_active,
        ],
    ];
}


    function decryptValue($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value; // أو إرجاع null أو قيمة افتراضية عند فشل فك التشفير
        }
    }

}

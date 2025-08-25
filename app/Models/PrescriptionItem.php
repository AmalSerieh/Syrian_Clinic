<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id',
        'medication_id',
        'pre_type',
        'pre_name',
        'pre_scientific',
        'pre_alternatives',
        'pre_trade',
        'pre_start_date',
        'pre_end_date',
        'pre_frequency',
        'pre_frequency_value',
        'pre_dosage_form',
        'pre_dose',
        'pre_timing',
        'pre_quantity_per_dose',
        'pre_prescribed_by_doctor',
        'pre_total_quantity',
        'pre_taken_quantity',
        'instructions'
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
    // دالة للحصول على البدائل كقائمة
    public function getAlternativesList()
    {
        if (empty($this->pre_alternatives)) {
            return [];
        }

        if (is_array($this->pre_alternatives)) {
            return $this->pre_alternatives;
        }

        return json_decode($this->pre_alternatives, true) ?? [];
    }
}

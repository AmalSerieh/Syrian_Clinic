<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Medication extends Model
{
    protected $fillable = [
        'patient_record_id',
        'med_type',
        'med_name',
        'med_start_date',
        'med_end_date',
        'med_frequency',
        'med_frequency_value',
        'med_dosage_form',
        'med_dose',
        'med_timing',
        'med_quantity_per_dose',
        'med_prescribed_by_doctor',
        'med_total_quantity'
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
     protected $appends = ['is_active', 'total_quantity'];

    public function getIsActiveAttribute()
    {
        if ($this->med_type === 'chronic') {
            return true;
        }
        if ($this->med_end_date && now()->lte($this->med_end_date)) {
            return true;
        }
        return false;
    }
    /*  public function getTotalQuantityAttribute()
    {
        $start = Carbon::parse($this->med_start_date);
        $end = ($this->med_type === 'current' && $this->med_end_date)
            ? Carbon::parse($this->med_end_date)
            : now();

        $days = $start->diffInDays($end) + 1;
        return ceil($days * $this->med_frequency_value);
    }
 */
}

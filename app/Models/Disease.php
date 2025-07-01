<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = [
        'patient_record_id',
        'd_type',
        'd_name',
        'd_diagnosis_date',
        'd_doctor',
        'd_advice',
        'd_prohibitions'
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }

}

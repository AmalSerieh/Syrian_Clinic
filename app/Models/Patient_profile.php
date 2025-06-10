<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient_profile extends Model
{
      protected $fillable = [
        'patient_record_id',
        'gender',
        'date_birth',
        'height',
        'weight',
        'blood_type',
        'smoker',
        'alcohol',
        'matital_status'//NT Service\MSSQL$SQLEXPRESS
    ];
    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

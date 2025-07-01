<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $fillable = [
        'patient_record_id',
        'aller_power',
        'aller_name',
        'aller_type',
        'aller_cause',//المسبب
        'aller_treatment',//العلاج
        'aller_pervention',//المنوعات
        'aller_reasons'//الأسباب
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

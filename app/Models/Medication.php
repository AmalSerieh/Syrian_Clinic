<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = ['patient_record_id', 'med_name'];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

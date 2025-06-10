<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
     protected $fillable = ['patient_record_id', 'op_name', 'op_doctor_name','op_date'];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

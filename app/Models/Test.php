<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = ['patient_record_id', 'test_name', 'test_result', 'test_date'];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

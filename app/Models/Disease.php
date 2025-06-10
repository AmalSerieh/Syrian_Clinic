<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = ['patient_record_id', 'dis_name', 'dis_type','dis_notes'];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }

}

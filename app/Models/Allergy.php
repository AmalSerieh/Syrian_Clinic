<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $fillable = ['patient_record_id', 'aller_name', 'aller_reaction'];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

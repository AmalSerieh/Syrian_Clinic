<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyHistory extends Model
{
    protected $fillable = ['patient_record_id', 'family_name', 'kinship'];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

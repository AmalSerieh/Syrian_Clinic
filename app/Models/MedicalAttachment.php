<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalAttachment extends Model
{
    protected $fillable = [
        'patient_record_id',
        'ray_name',
        'ray_laboratory',
        'ray_date',
        'ray_image'
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

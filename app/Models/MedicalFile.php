<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
    protected $fillable = ['patient_record_id', 'file_image_paths'];

    protected $casts = [
        'file_image_paths' => 'array',
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
}

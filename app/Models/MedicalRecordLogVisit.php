<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordLogVisit extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_id',
        'edited_at',
    ];

    public $timestamps = true;

    protected $casts = [
        'edited_at' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitEvaluation extends Model
{
    protected $fillable = [
        'visit_id',
        'patient_id',
        'doctor_id',
        'treatment_stage',
        'treatment_final',
        'handling',
        'services',
        'final_evaluate',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}


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
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}

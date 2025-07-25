<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{  use SoftDeletes;
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'secretary_id',
        'date',
        'day',
        'start_time',
        'end_time',
        'status',
        'location_type',
        'arrivved_time',
        'created_by',
        'type_visit'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

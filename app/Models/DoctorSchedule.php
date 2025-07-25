<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id',
        'day',
        'start_time',
        'end_time',
        'patients_per_hour',
        'appointment_duration',
        'max_patients',
    ];
     public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}

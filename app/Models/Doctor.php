<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
        'room_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class, 'doctor_id');

    }
    public function doctorSchedule()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');

    }
public function hasFinishedOrConfirmedAppointmentWith($patientId): bool
{
    return $this->appointments()
        ->where('patient_id', $patientId)
        ->whereIn('status', ['confirmed', 'completed'])
        ->exists();
}

}

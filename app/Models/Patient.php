<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function patient_record()
    {
        return $this->hasOne(Patient_record::class, 'patient_id');

    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
    public function prescription()
    {
        return $this->hasMany(Prescription::class);
    }
    public function evaluat()
    {
        return $this->hasMany(VisitEvaluation::class);

    }

    // علاقة جديدة للزيارات الخاصة بالطبيب الحالي
    public function visitsWithCurrentDoctor()
    {
        return $this->hasMany(Visit::class)->where('doctor_id', auth()->user()->doctor->id);
    }


    // app/Models/Patient.php
    public function getPhotoUrlAttribute1()
    {
        return $this->photo ? Storage::disk('public')->url($this->photo) : null;
    }
    // في app/Models/Patient.php
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return null;
        }

        return "http://localhost:8000/storage/{$this->photo}";
    }
}

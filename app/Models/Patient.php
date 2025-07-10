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

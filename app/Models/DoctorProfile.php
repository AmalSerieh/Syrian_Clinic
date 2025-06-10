<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    protected $fillable = [
        'doctor_id',
        'specialist',
        'cer_place',
        'cer_name',
        'cer_images',
        'cer_date',
        'exp_place',
        'exp_yesrs',
        'biography',
        'gender',
        'date_birth',
        'age'
    ];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    //السجل العام
    public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class);

    }
}

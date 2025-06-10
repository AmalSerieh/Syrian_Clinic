<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
      protected $fillable = [
        'user_id',
        'photo',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
      public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class, 'patient_id');

    }
}

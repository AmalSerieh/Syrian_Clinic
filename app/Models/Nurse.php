<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    protected $fillable = [
        'user_id',
        'doctor_id',
        'photo',
        'date_of_appointment',
        'gender',
        'salary'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function services()
    {
        return $this->belongsToMany(Service::class, 'nurse_services', 'nurse_id', 'service_id')->withTimestamps();
    }
}

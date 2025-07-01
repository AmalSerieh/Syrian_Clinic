<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_name_ar',
        'room_name_en',
        'room_specialty_ar',
        'room_specialty_en',
        'room_capacity'
    ];
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}

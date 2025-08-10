<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    protected $fillable = [
        'appointment_id',
        'w_status',
        'w_check_in_time',
        'w_start_time',
        'w_end_time'
    ];
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

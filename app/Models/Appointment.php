<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;
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
    public function waitingList()
    {
        return $this->hasMany(WaitingList::class);
    }
    public function visit()
    {
        return $this->hasMany(Visit::class);
    }

    // في موديل Appointment
    public function scopeActive($query)
    {
        $now = now();

        return $query->where(function ($q) use ($now) {
            $q->whereDate('date', '>', $now->format('Y-m-d'))
                ->orWhere(function ($query) use ($now) {
                    $query->whereDate('date', $now->format('Y-m-d'))
                        ->whereTime('end_time', '>=', $now->format('H:i:s'));
                });
        });
    }

    public function scopeValidForAccess($query)
    {
        return $query->whereIn('status', ['confirmed', 'completed', 'in_progress'])
            ->whereIn('location_type', ['in_Home', 'on_Street', 'in_Clinic', 'at_Doctor', 'in_Payment', 'finished']);
    }
}

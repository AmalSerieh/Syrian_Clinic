<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Doctor\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DoctorController extends Controller
{
    public function show(Doctor $doctor)
{
   // dd($doctor->doctorSchedule);
    $schedule = $doctor->doctorSchedule->map(function ($day) {
        return [
            'day' => $day->day,
            'from' => $day->start_time,
            'to' => $day->end_time,
            'max_patients' => $day->max_patients,
        ];
    });

    $timeSlots = $doctor->doctorSchedule->flatMap(function ($day) {
        $slots = [];
        $start = Carbon::createFromTimeString($day->start_time);
        $end = Carbon::createFromTimeString($day->end_time);

        while ($start < $end) {
            $next = $start->copy()->addHour();
            if ($next > $end) $next = $end;

            $slots[] = [
                'day' => $day->day,
                'from' => $start->format('H:i'),
                'to' => $next->format('H:i'),
            ];

            $start = $next;
        }

        return $slots;
    });

    return response()->json([
        'doctor' => new DoctorResource($doctor),
        'schedule' => $schedule,
        'time_slots' => $timeSlots,
    ]);
}
}

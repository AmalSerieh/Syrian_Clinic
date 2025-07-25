<?php

namespace App\Http\Resources\Api\Doctor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorDetailsResource extends JsonResource
{
    protected string $lang;

    public function __construct($resource, $lang = 'ar')
    {
        parent::__construct($resource);
        $this->lang = $lang;
    }

    public function toArray($request)
    {
        return $this->resource;
       /*  return [
            'id' => $this->id,
            'name' => $this->user->name,
            'photo' => asset('storage/' . $this->photo),
            'specialization' => $this->doctorProfile?->{"specialist_{$this->lang}"} ?? '',
            'biography' => $this->doctorProfile?->biography ?? '',
            'experience_years' => $this->doctorProfile?->exp_years ?? 0,
            'schedules' => $this->doctorSchedule->groupBy('day')->map(function ($daySchedules) {
                return $daySchedules->flatMap(function ($schedule) {
                    $start = \Carbon\Carbon::parse($schedule->start_time);
                    $end = \Carbon\Carbon::parse($schedule->end_time);
                    $slots = [];

                    while ($start->lt($end)) {
                        $slotEnd = $start->copy()->addMinutes($schedule->appointment_duration * $schedule->patients_per_hour);
                        if ($slotEnd->gt($end)) {
                            $slotEnd = $end;
                        }

                        $slots[] = [
                            'start_time'=>$schedule->start_time,
                            'end_time'=>$schedule->end_time,
                            'from' => $start->format('H:i'),
                            'to' => $slotEnd->format('H:i'),
                            'patients_per_hour' => $schedule->patients_per_hour,
                        ];

                        $start = $slotEnd;
                    }

                    return $slots;
                });
            }),

        ]; */
    }
}

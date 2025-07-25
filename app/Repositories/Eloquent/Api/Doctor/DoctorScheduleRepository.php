<?php

namespace App\Repositories\Eloquent\Api\Doctor;

use App\Repositories\Api\Doctor\DoctorScheduleRepositoryInterface;
use App\Models\Doctor;
use App\Models\Appointment;
class DoctorScheduleRepository implements DoctorScheduleRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function getDoctorWithSchedule(int $doctorId)
    {
        return Doctor::with('doctorSchedule')->findOrFail($doctorId);
    }

    public function countAppointments(int $doctorId, string $date): int
    {
        return Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereIn('status', ['pending', 'confirmed']) // فقط الحجوزات الفعالة
            ->count();
    }
}

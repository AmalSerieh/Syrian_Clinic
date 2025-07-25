<?php

namespace App\Repositories\Api\Doctor;

interface DoctorScheduleRepositoryInterface
{
    public function getDoctorWithSchedule(int $doctorId);
    public function countAppointments(int $doctorId, string $date): int;
}

<?php

namespace App\Repositories\Eloquent\Secertary;

use App\Models\Appointment;
use App\Repositories\Secertary\AppointmentRepositoryInterface;
use Illuminate\Support\Carbon;

class AppointmentRepository implements AppointmentRepositoryInterface
{

    public function getPendingByDoctor($doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->where('status', 'pending')
            ->where(function ($query) {
                $now = now();
                $query->where('date', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('date', $now->toDateString())
                            ->where('start_time', '>', $now->toTimeString());
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }
    public function deleteOldPendingAppointments()
    {
        Appointment::where('status', 'pending', )
            ->whereDate('date', '<', now()->toDateString())
            ->delete(); // يستخدم SoftDeletes
    }


    public function updateStatus($appointmentId, $status)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->status = $status;
        $appointment->save();

        return $appointment;
    }
    public function fetchConfirmedAppointmentsToday()
    {
        return Appointment::with(['doctor', 'patient'])
            ->whereDate('date', Carbon::today())
            ->where('status', 'confirmed')
            ->orderBy('date') // ليس ضروريًا بما أنه اليوم فقط، لكن لا بأس
            ->orderBy('start_time')
            ->get()
             ->groupBy('doctor_id'); // تجميع حسب الطبيب;
    }
}

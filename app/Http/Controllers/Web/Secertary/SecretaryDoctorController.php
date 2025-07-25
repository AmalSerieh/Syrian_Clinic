<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Patient_record;
use App\Models\User;
use App\Services\Secertary\Appointement\AppointementSerivce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SecretaryDoctorController extends Controller
{
    protected $service;

    //عرض كل الأطباء


    public function doctors()
    {
        $today = now()->format('l'); // مثال: monday
        $now = Carbon::now();
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $doctors = Doctor::with(['user', 'doctorProfile', 'room', 'doctorSchedule'])->get()->map(function ($doctor) use ($today, $daysOfWeek) {

            // هل يعمل اليوم؟
            $doctor->is_available_today = $doctor->doctorSchedule->contains(function ($schedule) use ($today) {
                return $schedule->day === $today;
            });

            // تجهيز جدول الأسبوع كامل
            $doctor->full_schedule = collect($daysOfWeek)->map(function ($day) use ($doctor) {
                $schedule = $doctor->doctorSchedule->firstWhere('day', $day);
                return [
                    'day' => ucfirst($day),
                    'start_time' => optional($schedule)->start_time,
                    'end_time' => optional($schedule)->end_time,
                    'has_shift' => !is_null($schedule),
                ];
            });
            return $doctor;
        });

        // جلب جدول الطبيب لليوم الحالي
        /*   $todaySchedule = $doctor->doctorSchedule->firstWhere('day', $today);

          if ($todaySchedule) {
              $start = Carbon::createFromFormat('H:i:s', $todaySchedule->start_time);
              $end = Carbon::createFromFormat('H:i:s', $todaySchedule->end_time);

              $doctor->is_available_today = $now->between($start, $end);
              $doctor->available_from = $start->format('H:i');
              $doctor->available_to = $end->format('H:i');
          } else {
              $doctor->is_available_today = false;
              $doctor->available_from = null;
              $doctor->available_to = null;
          }

          return $doctor; */


        return view('secretary.doctor.doctors', compact('doctors'));
    }




    public function doctorSchedule($id)
    {
        $doctor = Doctor::with('doctorSchedule')->findOrFail($id);
        // مصفوفة الأيام بالترتيب
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // إعادة ترتيب جدول الدوام ليشمل كل الأيام
        $schedule = collect($daysOfWeek)->map(function ($day) use ($doctor) {
            $daySchedule = $doctor->doctorSchedule->firstWhere('day', $day);
            return [
                'day' => ucfirst($day), // Sunday
                'start_time' => optional($daySchedule)->start_time,
                'end_time' => optional($daySchedule)->end_time,
                'has_shift' => !is_null($daySchedule),
            ];
        });
        return view('secretary.doctor.doctorSchedule', compact('doctor', 'schedule'));
    }


    public function doctorAppointmentsDetails1(Request $request, $doctorId)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $doctor = Doctor::with(['appointments.patient.user'])->findOrFail($doctorId);

        $appointments = $doctor->appointments;

        if ($from) {
            $appointments = $appointments->where('date', '>=', $from);
        }

        if ($to) {
            $appointments = $appointments->where('date', '<=', $to);
        }

        $confirmedAppointments = $appointments->where('status', 'confirmed')->values();

        $data = [
            'doctor_name' => $doctor->user->name ?? 'غير معروف',
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('status', 'completed')->count(),
            'cancelled_appointments' => $appointments->where('status', 'cancelled')->count(),
            'confirmed_appointments' => $confirmedAppointments->map(function ($appointment) {
                return [
                    'patient_name' => $appointment->patient->user->name ?? 'غير معروف',
                    'date' => $appointment->date,
                    'time' => $appointment->start_time ?? '-',
                ];
            }),
        ];

        return view('secretary.doctor.doctorAppointment', compact('data', 'from', 'to'));
    }

    public function doctorAppointmentsDetails(Request $request, $doctorId)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $doctor = Doctor::with(['appointments.patient.user'])->findOrFail($doctorId);

        $appointments = $doctor->appointments;

        // اجعل $startDate هو أكبر قيمة بين $from و اليوم (أو اليوم إذا $from غير موجود)
        $today = Carbon::today()->toDateString();

        if ($from && $from > $today) {
            $startDate = $from;
        } else {
            $startDate = $today;
        }

        // فلترة التواريخ بحيث تكون من $startDate وطالع
        $appointments = $appointments->where('date', '>=', $startDate);

        // فلترة حد أعلى إذا وُجد $to
        if ($to) {
            $appointments = $appointments->where('date', '<=', $to);
        }

        $confirmedAppointments = $appointments->where('status', 'confirmed')->values();

        $data = [
            'doctor_name' => $doctor->user->name ?? 'غير معروف',
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('status', 'completed')->count(),
            'cancelled_appointments' => $appointments->where('status', 'cancelled')->count(),
            'confirmed_appointments' => $confirmedAppointments->map(function ($appointment) {
                return [
                    'patient_name' => $appointment->patient->user->name ?? 'غير معروف',
                    'date' => $appointment->date,
                    'start_time' => $appointment->start_time ?? '-',
                    'end_time' => $appointment->end_time ?? '-',
                    'location' => $appointment->location_type ?? '-',
                    'arrivved_time' => $appointment->arrivved_time ?? '-',
                ];
            }),
        ];

        return view('secretary.doctor.doctorAppointment', compact('data', 'from', 'to'));
    }






}

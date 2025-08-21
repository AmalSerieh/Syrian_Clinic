<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function availableDays(Request $request, $doctorId)
    {
        $monthOffset = (int) $request->query('month_offset', 0);
        $date = Carbon::now()->startOfMonth()->addMonths($monthOffset);

        $doctor = Doctor::with('schedule')->findOrFail($doctorId);
        $now = Carbon::now();

        $days = [];
        $period = CarbonPeriod::create($date->copy()->startOfMonth(), $date->copy()->endOfMonth());

        foreach ($period as $day) {
            $dayName = $day->format('l');
            $schedule = $doctor->schedule->firstWhere('day', $dayName);

            // تحديد حالة اليوم
            if ($day->lt($now->startOfDay())) {
                $status = 'past';
            } elseif (!$schedule) {
                $status = 'not_working';
            } else {
                $appointmentsCount = Appointment::where('doctor_id', $doctorId)
                    ->whereDate('date', $day)
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->count();

                $status = $appointmentsCount >= $schedule->max_patients ? 'full' : 'available';
            }

            $days[] = [
                'date' => $day->toDateString(),
                'day_name' => $day->translatedFormat('l'), // يوم الأحد، الإثنين، إلخ
                'day_number' => $day->day,
                'status' => $status,
                'appointments_count' => $appointmentsCount ?? 0,
            ];
        }

        return response()->json([
            'month' => $date->translatedFormat('F Y'),
            'month_offset' => $monthOffset,
            'days' => $days,
        ]);
    }

    public function availableTimes(Request $request, $doctorId)
    {
        $date = $request->query('date');
        $doctor = Doctor::with('schedule')->findOrFail($doctorId);

        $dayName = Carbon::parse($date)->format('l');
        $schedule = $doctor->schedule->firstWhere('day', $dayName);

        if (!$schedule) {
            return response()->json(['error' => 'الطبيب لا يعمل بهذا اليوم'], 400);
        }

        $timeSlots = $this->generateTimeSlots($schedule, $doctorId, $date);

        return response()->json([
            'date' => $date,
            'time_slots' => $timeSlots,
        ]);
    }

    private function generateTimeSlots($schedule, $doctorId, $date)
    {
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);
        $slotDuration = $schedule->slot_duration; // دقائق

        $slots = [];
        $current = $start->copy();

        while ($current->addMinutes($slotDuration)->lte($end)) {
            $slotStart = $current->copy()->subMinutes($slotDuration);
            $slotEnd = $current->copy();

            $timeSlot = $slotStart->format('H:i') . '-' . $slotEnd->format('H:i');

            $bookedCount = Appointment::where('doctor_id', $doctorId)
                ->whereDate('date', $date)
                ->where('time_slot', $timeSlot)
                ->whereIn('status', ['confirmed', 'pending'])
                ->count();

            $slots[] = [
                'time_slot' => $timeSlot,
                'start_time' => $slotStart->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'booked_count' => $bookedCount,
                'max_patients' => $schedule->patients_per_slot,
                'is_available' => $bookedCount < $schedule->patients_per_slot,
            ];
        }

        return $slots;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'time_slot' => 'required',
        ]);

        // التحقق من توفر الموعد
        $doctor = Doctor::with('schedule')->find($request->doctor_id);
        $dayName = Carbon::parse($request->date)->format('l');
        $schedule = $doctor->schedule->firstWhere('day', $dayName);

        $bookedCount = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('date', $request->date)
            ->where('time_slot', $request->time_slot)
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();

        if ($bookedCount >= $schedule->patients_per_slot) {
            return back()->with('error', 'هذا الموعد لم يعد متاحاً');
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'status' => 'confirmed',
            'created_by' => 'secretary',
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('secretary.appointments')->with('success', 'تم حجز الموعد بنجاح');
    }
}

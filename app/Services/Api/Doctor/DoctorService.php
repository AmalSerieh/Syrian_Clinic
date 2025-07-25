<?php

namespace App\Services\Api\Doctor;

use App\Repositories\Api\Doctor\DoctorRepositoryInterface;
use Illuminate\Support\Carbon;

class DoctorService
{
    /**
     * Create a new class instance.
     */
    protected $repo;

    public function __construct(DoctorRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function listAllDoctors($lang)
    {
        return $this->repo->getAllDoctors($lang);
    }

    public function getDoctorDetails($id, $lang)
    {
        return $this->repo->findDoctorWithSchedules($id, $lang);
    }

    public function formatDoctorDetails($doctor, $lang = 'ar')
    {
        $specialty = $lang === 'ar'
            ? $doctor->doctorProfile->specialist_ar
            : $doctor->doctorProfile->specialist_en;

        return [
            'id' => $doctor->id,
            'name' => $doctor->user->name,
            'photo' => asset('storage/' . $doctor->photo),
            'rating' => $doctor->doctorProfile->rating ?? 0,
            'experience_years' => (int) $doctor->doctorProfile->exp_years ?? 0,
            'biography' => $doctor->doctorProfile->biography ?? '',
            'specialty' => $specialty ?? 'غير محدد',
            'schedules' => $doctor->doctorSchedule->map(function ($schedule) {
                $slots = $this->generateTimeSlots(
                    $schedule->start_time,
                    $schedule->end_time,
                    $schedule->appointment_duration
                );

                return [
                    'day' => $this->$schedule->day,
                    'day_key' => $schedule->day,
                    'start_time' => substr($schedule->start_time, 0, 5),
                    'end_time' => substr($schedule->end_time, 0, 5),
                    'time_slots' => $slots,
                ];
            })->values(),
        ];
    }

    private function generateTimeSlots1($startTime, $endTime, $duration)
    {
        $slots = [];
        $start = Carbon::createFromTimeString($startTime);
        $end = Carbon::createFromTimeString($endTime);

        while ($start->lt($end)) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addMinutes($duration);
            if ($slotEnd->gt($end)) {
                $slotEnd = $end;
            }

            $slots[] = $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i');
            $start->addMinutes($duration);
        }

        return $slots;
    }

    public function prepareDoctorDetails($doctor, $lang = 'ar')
    {
        $specialty = $lang === 'ar'
            ? $doctor->doctorProfile->specialist_ar
            : $doctor->doctorProfile->specialist_en;

        return [
            'id' => $doctor->id,
            'name' => $doctor->user->name,
            'photo' => asset('storage/' . $doctor->photo),
            'rating' => $doctor->doctorProfile->rating ?? 0,
            'experience_years' => (int) $doctor->doctorProfile->exp_years ?? 0,
            'biography' => $doctor->doctorProfile->biography ?? '',
            'specialty' => $specialty ?? 'غير محدد',
            'schedules' => $doctor->doctorSchedule->map(function ($schedule) {
                return [
                    'day' => $this->getArabicDayName($schedule->day),
                    'day_key' => $schedule->day,
                    'start_time' => substr($schedule->start_time, 0, 5),
                    'end_time' => substr($schedule->end_time, 0, 5),
                    'time_slots' => $this->generateTimeSlots(
                        $schedule->start_time,
                        $schedule->end_time,
                        $schedule->appointment_duration
                    )
                ];
            })->values(),
        ];
    }

    private function generateTimeSlots($startTime, $endTime, $duration)
    {
        $slots = [];
        $start = Carbon::createFromTimeString($startTime);
        $end = Carbon::createFromTimeString($endTime);

        while ($start->lt($end)) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addHour();

            if ($slotEnd->gt($end)) {
                $slotEnd = $end->copy();
            }

            $slots[] = $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i');
            $start->addHour();
        }

        return $slots;
    }
    public function getSlotsForDay($doctorId, $date)
    {
        // 1️⃣ استخرج جدول هذا الطبيب لهذا اليوم (مثلاً الاثنين)
        $dayName = strtolower(Carbon::parse($date)->format('l')); // ex: monday

        $schedule = \App\Models\DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day', $dayName)
            ->first();

        if (!$schedule) {
            return []; // لا يوجد دوام بهذا اليوم
        }

        // 2️⃣ نولّد كل الـ slots بدون booked
        $rawSlots = [];
        $start = Carbon::createFromTimeString($schedule->start_time);
        $end = Carbon::createFromTimeString($schedule->end_time);
        $duration = $schedule->appointment_duration;

        while ($start->lt($end)) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addMinutes($duration);

            if ($slotEnd->gt($end)) {
                $slotEnd = $end->copy();
            }

            $rawSlots[] = $slotStart->format('H:i') . '-' . $slotEnd->format('H:i');
            $start->addMinutes($duration);
        }

        // 3️⃣ استخرج الحجوزات الفعلية لهذا اليوم
        $appointments = \App\Models\Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->get();

        // 4️⃣ عدّ كم حجز في كل slot
        $bookedSlotsPerSlot = [];
        foreach ($rawSlots as $slot) {
            [$start, $end] = explode('-', $slot);
            $count = $appointments->filter(function ($appointment) use ($start, $end) {
                return $appointment->start_time >= trim($start) && $appointment->start_time < trim($end);
            })->count();

            $bookedSlotsPerSlot[$slot] = $count;
        }

        // 5️⃣ ارجع نتيجة منظمة بنفس الشكل
        $slotsWithStatus = [];
        foreach ($rawSlots as $slot) {
            [$start, $end] = explode('-', $slot);
            $totalSlots = intval($schedule->patients_per_hour * ($duration / 60));
            $booked = $bookedSlotsPerSlot[$slot] ?? 0;

            $slotsWithStatus[] = [
                'time' => trim($slot),
                'booked_slots' => $booked,
                'total_slots' => $totalSlots,
                'available' => $booked < $totalSlots,
                'isfull' => $booked >= $totalSlots
            ];
        }

        return $slotsWithStatus;
    }


    private function getArabicDayName($dayKey)
    {
        return match ($dayKey) {
            'sunday' => 'الأحد',
            'monday' => 'الاثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
            'saturday' => 'السبت',
            default => $dayKey,
        };
    }
/* // Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyDi-pOubN0dKiYNq7eb9VmnOgP0pYV79Bc",
  authDomain: "syrianclinic-de742.firebaseapp.com",
  projectId: "syrianclinic-de742",
  storageBucket: "syrianclinic-de742.firebasestorage.app",
  messagingSenderId: "94524402201",
  appId: "1:94524402201:web:3fb1a7fee011d299db5888",
  measurementId: "G-9XVW8XSW8K"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app); */

}

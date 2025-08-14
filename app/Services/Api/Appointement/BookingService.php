<?php

namespace App\Services\Api\Appointement;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient_record;
use App\Repositories\Api\Doctor\DoctorScheduleRepositoryInterface;
use Illuminate\Support\Carbon;
class BookingService
{
    /**
     * Create a new class instance.
     */
    protected $doctorScheduleRepo;
    public function __construct(DoctorScheduleRepositoryInterface $doctorScheduleRepo)
    {
        $this->doctorScheduleRepo = $doctorScheduleRepo;
    }
    private function generateSlots($schedule, $doctorId, $date)
    {
        $slots = [];

        $start = Carbon::createFromTimeString($schedule->start_time);
        $end = Carbon::createFromTimeString($schedule->end_time);
        $patientsPerHour = $schedule->patients_per_hour;

        // استخرج الحجوزات من جدول appointments
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereIn('status', ['pending', 'confirmed']) // فقط الحجوزات الفعالة
            ->get();

        while ($start->lt($end)) {
            $slotStart = $start->copy();

            // احسب المدة المتبقية حتى نهاية الدوام
            $remainingMinutes = $slotStart->diffInMinutes($end);

            // إذا بقيت ساعة أو أكثر → نحجز ساعة
            if ($remainingMinutes >= 60) {
                $slotEnd = $slotStart->copy()->addHour();
                $totalSlots = $patientsPerHour;
            }
            // إذا بقي أقل من ساعة ولكن على الأقل 30 دقيقة → نحجز نصف ساعة
            elseif ($remainingMinutes >= 30) {
                $slotEnd = $slotStart->copy()->addMinutes(30);
                $totalSlots = intval(ceil($patientsPerHour / 2)); // نقسم عدد المرضى للنصف (ونقرب للأعلى)
            } else {
                break; // إذا بقي أقل من 30 دقيقة لا نضيف فتحة جديدة
            }

            // format: 08:00-09:00
            $slotKey = $slotStart->format('H:i') . '-' . $slotEnd->format('H:i');

            // عد كم مريض حجز فعليًا بهذا التوقيت
            $booked = $appointments->filter(function ($appointment) use ($slotStart, $slotEnd) {
                return $appointment->start_time >= $slotStart->format('H:i')
                    && $appointment->start_time < $slotEnd->format('H:i');
            })->count();

            $isFull = $booked >= $totalSlots;

            $slots[] = [
                'time' => $slotKey,
                'booked_slots' => $booked,
                'total_slots' => $totalSlots,
                'available' => !$isFull,
                'isfull' => $isFull
            ];

            // تقدم الوقت
            $start = $slotEnd;
        }

        return $slots;
    }

    // جلب الألوان
    public function getColors()
    {
        return [
            'white' => '#FFFFFF',
            'red' => '#FF0000',
            'green' => '#00FF00',
            'blue' => '#0000FF',
            'yellow' => '#FFFF00',
            'orange' => '#FFA500',
            'purple' => '#800080',
            'pink' => '#FFC0CB',
            'brown' => '#A52A2A',
            'grey' => '#808080',
        ];
    }

    public function book($doctorId, $patientId, $date, $time, )
    {
       // dd($patientId);
        $now = now();
        $dateObj = Carbon::parse($date);
        $dayName = $dateObj->format('l');

        // تحقق من السنة والشهر واليوم
        if ($dateObj->year < $now->year || $dateObj->year > $now->year + 1) {
            throw new \Exception('السنة غير مسموح بها');
        }
        if ($dateObj->year == $now->year && $dateObj->month < $now->month) {
            throw new \Exception('الشهر غير مسموح به');
        }
        if ($dateObj->lt($now->startOfDay())) {
            throw new \Exception('لا يمكن الحجز في يوم ماضي');
        }

       // dd($this->checkPatientMedicalRecord($patientId));
        // تحقق من السجل الطبي للمريض
        $this->checkPatientMedicalRecord($patientId);

        // تحقق أن الطبيب يداوم بهذا اليوم
        $doctor = $this->doctorScheduleRepo->getDoctorWithSchedule($doctorId);
        $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
        if (!$schedule) {
            throw new \Exception('الطبيب لا يعمل في هذا اليوم');
        }

        // استخرج slots لليوم المطلوب
        $slots = $this->generateSlots($schedule, $doctorId, $date);

        // ابحث عن الـslot المطلوب (مثلاً time = "08:00-09:00")
        $targetSlot = collect($slots)->firstWhere('time', $time);

        if (!$targetSlot) {
            throw new \Exception('هذا الوقت غير متاح للحجز');
        }

        if ($targetSlot['isfull']) {
            throw new \Exception('الفتحة محجوزة بالكامل');
        }
        // قارن مع total_slots


        // تحويل time من "08:00-09:00" → start_time = "08:00:00" ، end_time = "09:00:00"
        [$start, $end] = explode('-', $time);
        $startTime = $start . ':00';
        $endTime = $end . ':00';
        // تحقق runtime مباشر
        $currentActiveCount = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereTime('start_time', $startTime)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
        if ($currentActiveCount >= $targetSlot['total_slots']) {
            throw new \Exception('عذراً، هذه الفتحة أصبحت ممتلئة الآن');
        }

        // إنشاء الموعد بحالة pending
        return Appointment::create([
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'secretary_id' => null,
            'date' => $date,
            'day' => $dayName,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending',
            'location_type' => 'in_Home',
            'arrivved_time' => null,
            'created_by' => 'patient',
        ]);
    }
    private function checkPatientMedicalRecord($patientId)
    {
        // نفترض أن جدول patient_records فيه عمود patient_id يشير للمريض
        $record = Patient_record::where('patient_id', $patientId)->first();
//dd($patientId);
        if (
            !$record ||
            !$record->profile_submitted ||
            !$record->diseases_submitted ||
            !$record->operations_submitted ||
            !$record->medicalAttachments_submitted ||
            !$record->allergies_submitted ||
            !$record->family_history_submitted ||
            !$record->medications_submitted ||
            !$record->medicalfiles_submitted
        ) {
            throw new \Exception('يجب تعبئة السجل الطبي بالكامل قبل الحجز');
        }

        return true;
    }
}

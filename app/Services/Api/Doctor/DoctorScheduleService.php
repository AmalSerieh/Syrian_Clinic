<?php

namespace App\Services\Api\Doctor;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Patient_record;
use App\Repositories\Api\Doctor\DoctorScheduleRepositoryInterface;
use Carbon\Carbon;
class DoctorScheduleService
{
    /**
     * Create a new class instance.
     */
    // app/Services/DoctorScheduleService.php
    protected $doctorScheduleRepo;

    public function __construct(DoctorScheduleRepositoryInterface $doctorScheduleRepo)
    {
        $this->doctorScheduleRepo = $doctorScheduleRepo;
    }



    // جلب الأيام مع الألوانm
    public function getMonthDaysWithStatus1($doctorId, $year, $month)
    {
        $doctor = Doctor::with('doctorSchedule')->findOrFail($doctorId);

        $now = Carbon::now();

        // التحقق من أن السنة هي الحالية أو التي تليها فقط
        if ($year < $now->year || $year > $now->year + 1) {
            throw new \Exception('السنة غير مسموح بها، يجب أن تكون هذه السنة أو السنة القادمة فقط');
        }

        // التحقق من أن الشهر ليس في الماضي إذا كانت السنة هي السنة الحالية
        /* if ($year == $now->year && $month < $now->month) {
            throw new \Exception('الشهر غير مسموح به، يجب أن يكون الشهر الحالي أو بعده فقط');
        } */
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $result = [];
        $colors = $this->getColors();
        for ($day = 1; $day <= $daysInMonth; $day++) {

            $date = Carbon::create($year, $month, $day);
            $dayName = $date->format('l');
            // تحقق إذا كان التاريخ قبل اليوم (ماضٍ)
            if ($date->lt($now->startOfDay())) {
                $result[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $dayName,
                    'color' => $colors['pink'], // اللون الزهري
                    'isfull' => true // لا يمكن الحجز في الماضي
                ];
                continue;
            }

            $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);

            if (!$schedule) {
                // لا يوجد دوام بهذا اليوم (أحمر)
                $result[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $dayName,
                    'color' => $colors['red'],
                    'isfull' => false
                ];
                continue;
            }
            $dateObj = Carbon::parse($date);

            $slots = $this->generateSlots($schedule, $doctorId, $dateObj->toDateString());
            $totalSlots = count($slots);

            $booked = Appointment::where('doctor_id', $doctorId)
                ->whereDate('date', $date->toDateString())
                ->count();



            if ($booked >= $totalSlots) {
                // دوام كامل لكنه محجوز بالكامل (أبيض)
                $result[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $dayName,
                    'color' => $colors['white'],
                    'isfull' => true
                ];
            } else {
                // دوام وفيه شاغر (أزرق)
                $result[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $dayName,
                    'color' => $colors['blue'],
                    'isfull' => false
                ];
            }
        }

        return $result;
    }
    public function getMonthDaysWithStatus($doctorId, $year, $month)
    {
        $doctor = $this->doctorScheduleRepo->getDoctorWithSchedule($doctorId);

        $now = Carbon::now();

        if ($year < $now->year || $year > $now->year + 1) {
            throw new \Exception('السنة غير مسموح بها، يجب أن تكون هذه السنة أو السنة القادمة فقط');
        }

        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $colors = $this->getColors();
        $result = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dayName = $date->format('l');
            // تحقق إذا كان التاريخ قبل اليوم (ماضٍ)

            if ($date->lt($now->startOfDay())) {
                $result[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $dayName,
                    'color' => $colors['pink'],
                    'isfull' => true
                ];
                continue;
            }

            $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
            // لا يوجد دوام بهذا اليوم (أحمر)

            if (!$schedule) {
                $result[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $dayName,
                    'color' => $colors['red'],
                    'isfull' => false
                ];
                continue;
            }

            $slots = $this->generateSlots($schedule, $doctorId, $date->toDateString());
            // $totalSlots = count($slots);
            $totalSlots = array_sum(array_column($slots, 'total_slots')); // مجموع السعات الكلي

            // عدد الشروط (الحجوزات)
            $booked = $this->doctorScheduleRepo->countAppointments($doctorId, $date->toDateString());

            // دوام كامل لكنه محجوز بالكامل (أبيض)

            if ($booked >= $totalSlots) {
                $result[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $dayName,
                    'color' => $colors['white'],
                    'isfull' => true
                ];
            } else {
                // دوام وفيه شاغر (أزرق)

                $result[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $dayName,
                    'color' => $colors['blue'],
                    'isfull' => false
                ];
            }
        }

        return $result;
    }


    // جلب الأوقات ليوم محدد
    public function getDaySlots1($doctorId, $date)
    {
        $doctor = Doctor::with('doctorSchedule')->findOrFail($doctorId);
        if (!$doctor) {
            throw new \Exception('الطبيب غير موجود');
        }

        $dateObj = Carbon::parse($date);

        $now = Carbon::today();

        // التحقق من أن التاريخ ليس في الماضي
        if ($dateObj->lt($now)) {
            throw new \Exception('لا يمكن جلب الأوقات ليوم في الماضي');
        }
        $dayName = $dateObj->format('l');

        $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
        if (!$schedule) {
            throw new \Exception('الطبيب لا يعمل بهذا اليوم');
        }

        $slots = $this->generateSlots($schedule, $doctorId, $dateObj->toDateString());
        $result = [];

        foreach ($slots as $slot) {
            [$start, $end] = explode('-', $slot['time']);
            $bookedCount = Appointment::where('doctor_id', $doctorId)
                ->whereDate('date', $date)
                ->where('start_time', $start)
                ->count();

            $available = $slot['total_slots'] - $bookedCount;

            $result[] = [
                'time' => $slot['time'],
                'booked_slots' => $bookedCount,
                'total_slots' => $slot['total_slots'],
                'available' => $available > 0,
                'isfull' => $available <= 0
            ];
        }

        return $result;
    }
    public function getDaySlots2($doctorId, $date)
    {
        $doctor = $this->doctorScheduleRepo->getDoctorWithSchedule($doctorId);
        $dateObj = Carbon::parse($date);
        $now = Carbon::today();

        if ($dateObj->lt($now)) {
            throw new \Exception('لا يمكن جلب الأوقات ليوم في الماضي');
        }

        $dayName = $dateObj->format('l');
        $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
        if (!$schedule) {
            throw new \Exception('الطبيب لا يعمل بهذا اليوم');
        }

        $slots = $this->generateSlots($schedule, $doctorId, $dateObj->toDateString());
        $result = [];

        $scheduleStart = $schedule->start_time; // مثلا: 08:00
        $scheduleEnd = $schedule->end_time;     // مثلا: 10:30

        foreach ($slots as $slot) {
            [$start, $end] = explode('-', $slot['time']);
            $bookedCount = Appointment::where('doctor_id', $doctorId)
                ->whereDate('date', $date)
                ->where('start_time', $start)
                ->count();

            $available = $slot['total_slots'] - $bookedCount;

            $result[] = [
                'time' => $slot['time'],
                // 'start_time' => $scheduleStart,   // وقت بدء الدوام الأصلي
                //'end_time' => $scheduleEnd,       // وقت نهاية الدوام الأصلي
                'booked_slots' => $bookedCount,
                'total_slots' => $slot['total_slots'],
                'available' => $available > 0,
                'isfull' => $available <= 0
            ];
        }

        return $result;
    }
    public function getDaySlots($doctorId, $date)
    {
        $doctor = $this->doctorScheduleRepo->getDoctorWithSchedule($doctorId);
        $dateObj = Carbon::parse($date);
        $now = Carbon::today();

        if ($dateObj->lt($now)) {
            throw new \Exception('لا يمكن جلب الأوقات ليوم في الماضي');
        }

        $dayName = $dateObj->format('l');
        $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
        if (!$schedule) {
            throw new \Exception('الطبيب لا يعمل بهذا اليوم');
        }

        $slots = $this->generateSlots($schedule, $doctorId, $dateObj->toDateString());
        $result = [];

        $scheduleStart = $schedule->start_time; // مثلا: 08:00
        $scheduleEnd = $schedule->end_time;     // مثلا: 10:30

        // أضف السطر الأول
        $result[] = [
            "time in this day {$date}" => "{$scheduleStart}-{$scheduleEnd}"
        ];

        foreach ($slots as $slot) {
            [$start, $end] = explode('-', $slot['time']);
            $bookedCount = Appointment::where('doctor_id', $doctorId)
                ->whereDate('date', $date)
                ->where('start_time', $start)
                ->whereIn('status', ['pending', 'confirmed']) // فقط الحجوزات الفعالة
                ->count();

            $available = $slot['total_slots'] - $bookedCount;

            $result[] = [
                'time' => $slot['time'],
                'booked_slots' => $bookedCount,
                'total_slots' => $slot['total_slots'],
                'available' => $available > 0,
                'isfull' => $available <= 0
            ];
        }

        return ['data' => $result];
    }

    // منع الحجز المزدوج + إضافة حجز
    public function book1($doctorId, $patientId, $date, $startTime)
    {
        $doctor = Doctor::with('doctorSchedule')->findOrFail($doctorId);
        $dayName = Carbon::parse($date)->format('l');
        $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
        if (!$schedule) {
            throw new \Exception('لا يوجد دوام بهذا اليوم');
        }

        // تحقق من الحجز المكرر
        $bookedCount = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->where('start_time', $startTime)
            ->count();

        if ($bookedCount >= $schedule->patients_per_hour) {
            throw new \Exception('تم امتلاء هذا التوقيت');
        }

        $endTime = Carbon::parse($startTime)->addHour()->format('H:i');

        return Appointment::create([
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending',
            'location_type' => 'online',
            // 'arrivved_time'=>$arrivved_time,
            'created_by' => 'patient',
        ]);
    }

    // توليد الأوقات
    private function generateSlots1($schedule, $doctorId, $date)
    {
        $slots = [];

        $start = Carbon::createFromTimeString($schedule->start_time);
        $end = Carbon::createFromTimeString($schedule->end_time);
        $duration = $schedule->appointment_duration; // ex: 60 أو 30
        $patientsPerHour = $schedule->patients_per_hour;

        // استخرج الحجوزات من جدول appointments
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->get();

        while ($start->lt($end)) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addHour();
            if ($slotEnd->gt($end)) {
                $slotEnd = $end->copy();
            }

            // format: 16:00-17:00
            $slotKey = $slotStart->format('H:i') . '-' . $slotEnd->format('H:i');

            // total_slots = كم مريض ممكن بهذا slot (patients_per_hour * duration/60)
            $totalSlots = intval($patientsPerHour * ($duration / 60));

            // عد كم مريض حجز فعليًا بهذا التوقيت
            $booked = $appointments->filter(function ($appointment) use ($slotStart, $slotEnd) {
                return $appointment->start_time >= $slotStart->format('H:i')
                    && $appointment->start_time < $slotEnd->format('H:i');
            })->count();

            $slots[] = [
                'time' => $slotKey,
                'booked_slots' => $booked,
                'total_slots' => $totalSlots,
                'available' => $booked < $totalSlots,
                'isfull' => $booked >= $totalSlots
            ];

            $start->addHour();
        }

        return $slots;
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
            'white' => '#FFFFFF', // دوام كامل لكنه محجوز بالكامل (أبيض)
            'red' => '#FF0000',  // لا يوجد دوام بهذا اليوم (أحمر)
            'green' => '#00FF00',
            'blue' => '#0000FF',// دوام وفيه شاغر (أزرق)
            'yellow' => '#FFFF00',
            'orange' => '#FFA500',
            'purple' => '#800080',
            'pink' => '#FFC0CB', // تحقق إذا كان التاريخ قبل اليوم (ماضٍ)
            'brown' => '#A52A2A',
            'grey' => '#808080',
        ];
    }

    public function book($doctorId, $patientId, $date, $time, )
    {
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


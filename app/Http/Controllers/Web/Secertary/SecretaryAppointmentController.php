<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use App\Models\WaitingList;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentConfirmedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Secertary\Appointement\AppointementSerivce;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
class SecretaryAppointmentController extends Controller
{
    protected $service;

    public function __construct(AppointementSerivce $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $today = Carbon::today();
        $doctors = Doctor::with('doctorSchedule')->get();
        $appointments = Appointment::with(['doctor', 'patient'])
            ->whereDate('date', '>=', $today)
            ->get();
        $bookingRequests = Appointment::where('status', 'pending')
            ->whereDate('date', '>=', $today)
            ->with(['doctor', 'patient'])->get();

        $totalDone = $appointments->where('status', 'confirmed')->count();
        $totalCanceled = $appointments
            ->whereIn('status', ['canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary'])
            ->count();
        $totalAppointments = $appointments->count();


        // جلب أقرب موعد فاضي لكل دكتور
        $nearestSlots = [];
        foreach ($doctors as $doctor) {
            $nearestSlots[$doctor->id] = $this->getNearestAvailableRangeSlotForBlade($doctor->id);
        }
        // dd($nearestSlots);

        return view('secretary.appointments.appointment-all', compact(
            'doctors',
            'appointments',
            'bookingRequests',
            'totalDone',
            'totalCanceled',
            'totalAppointments',
            'nearestSlots'
        ));
    }

    // هذا التابع بيشتغل بدون response()->json عشان نقدر نستعمله بالـ Blade
    private function getNearestAvailableRangeSlotForBlade($doctorId)
    {
        $now = Carbon::now();
        $doctor = Doctor::with('doctorSchedule')->findOrFail($doctorId);

        foreach (range(0, 30) as $dayOffset) {
            $date = $now->copy()->addDays($dayOffset);
            $dayName = $date->format('l');

            $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
            if (!$schedule)
                continue;

            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);
            $patientsPerHour = max($schedule->patients_per_hour, 1);

            while ($startTime->lessThan($endTime)) {
                $rangeStart = $startTime->copy();
                $rangeEnd = $rangeStart->copy()->addHour();
                if ($rangeEnd->greaterThan($endTime)) {
                    $rangeEnd = $endTime->copy();
                }

                $hoursInRange = $rangeStart->diffInMinutes($rangeEnd) / 60;
                $totalSlots = ceil($patientsPerHour * $hoursInRange);

                $bookedSlots = Appointment::where('doctor_id', $doctorId)
                    ->where('date', $date->toDateString())
                    ->whereBetween('start_time', [$rangeStart->format('H:i:s'), $rangeEnd->format('H:i:s')])
                    ->whereIn('status', ['confirmed', 'pending', 'processing'])
                    ->count();

                if ($bookedSlots < $totalSlots) {
                    return [
                        'status' => 'available',
                        'date' => $date->toDateString(),
                        'day' => $dayName,
                        'time' => $rangeStart->format('H:i') . '-' . $rangeEnd->format('H:i'),
                    ];
                }

                $startTime = $rangeEnd->copy();
            }
        }

        return [
            'status' => 'full',
            'date' => null,
            'day' => null,
            'time' => null,
        ];
    }



    public function pendingByDoctor($doctorId)
    {
        $appointments = $this->service->getPendingAppointmentsByDoctor($doctorId);

        return view('secretary.appointments.pending', compact('appointments', 'doctorId'));
    }


    public function sendNotification(Request $request)
    {
        $firebaseToken = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();
        $SERVER_API_KEY = env('FCM_SERVER_KEY');
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        return back()->with('success', 'Notification send successfully.');

    }

    public function confirm1(Request $request, $appointmentId)
    {
        //dd($appointmentId);
        \Log::info('CSRF token from input: ' . $request->input('_token'));
        \Log::info('From header: ' . $request->header('X-CSRF-TOKEN'));

        try {
            $result = $this->service->confirmAppointment($appointmentId);
            //  dd($result);
            $message = 'تم تأكيد الموعد بنجاح';

            if (!$result['has_token']) {
                $message .= '<br><small class="text-yellow-500">ملاحظة: لم يتم إرسال إشعار للتطبيق لأن المريض ليس لديه جهاز مسجل</small>';
            } elseif (!$result['notification_sent']) {
                $message .= '<br><small class="text-yellow-500">ملاحظة: حدث خطأ في إرسال الإشعار للتطبيق</small>';
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'notification_sent' => $result['notification_sent']
                ]);
            }

            return redirect()->back()->with([
                'status' => 'تم تأكيد الموعد بنجاح',
                'notification_warning' => !$result['has_token']
                    ? 'لم يتم إرسال إشعار للتطبيق لأن المريض ليس لديه جهاز مسجل'
                    : (!$result['notification_sent'] ? 'حدث خطأ في إرسال الإشعار للتطبيق' : null)
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'فشل تأكيد الموعد: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }
    public function cancel1(Request $request, $appointmentId)
    {

        \Log::info('CSRF token from input: ' . $request->input('_token'));
        \Log::info('From header: ' . $request->header('X-CSRF-TOKEN'));

        try {
            $result = $this->service->cancelAppointment($appointmentId);

            $message = 'تم إلغاء الموعد بنجاح';

            if (!$result['has_token']) {
                $message .= '<br><small class="text-yellow-500">ملاحظة: لم يتم إرسال إشعار للتطبيق لأن المريض ليس لديه جهاز مسجل</small>';
            } elseif (!$result['notification_sent']) {
                $message .= '<br><small class="text-yellow-500">ملاحظة: حدث خطأ في إرسال الإشعار للتطبيق</small>';
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'notification_sent' => $result['notification_sent']
                ]);
            }
            //  $appointment = $this->service->cancelAppointment($appointmentId);

            return redirect()->back()->with([
                'status' => 'تم إلغاء الموعد بنجاح',
                'notification_warning' => !$result['has_token']
                    ? 'لم يتم إرسال إشعار للتطبيق لأن المريض ليس لديه جهاز مسجل'
                    : (!$result['notification_sent'] ? 'حدث خطأ في إرسال الإشعار للتطبيق' : null)
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'فشل إلغاء الموعد: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    public function cancel2(Request $request, $appointmentId)
    {
        \Log::info('CSRF token from input: ' . $request->input('_token'));
        \Log::info('From header: ' . $request->header('X-CSRF-TOKEN'));

        try {
            $appointment = $this->service->cancelAppointment($appointmentId);

            if ($request->ajax()) {
                return redirect()->back()->with([

                    'status' => 'تم إلغاء الموعد بنجاح'
                ]);
            }
            //  $appointment = $this->service->cancelAppointment($appointmentId);

            return redirect()->back()->with('status', 'تم إلغاء الموعد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل إلغاء الموعد: ' . $e->getMessage());
        }
    }

    public function todayAppointments()
    {
        $appointments = $this->service->getTodayAppointments();
        /*  return view('secretary.appointments.today', [
             'appointments' => AppointmentResource::collection($appointments)
         ]); */
        return view('secretary.appointments.today', compact('appointments'));

    }
    public function markArrived(Appointment $appointment)
    {
        $today = Carbon::today();

        // تحقق أن الموعد اليوم
        if ($appointment->date != $today->toDateString()) {
            return back()->with('error', 'الموعد ليس اليوم.');
        }

        // تحقق أن الموعد في الوقت المناسب ضمن دوام الطبيب
        $dayName = $today->locale('en')->dayName; // مثل Monday

        $schedule = DoctorSchedule::where('doctor_id', $appointment->doctor_id)
            ->where('day', $dayName)
            ->first();

        if (!$schedule) {
            return back()->with('error', 'لا يوجد دوام للطبيب اليوم.');
        }

        // تحقق من التوقيت
        if (
            $appointment->start_time < $schedule->start_time ||
            $appointment->end_time > $schedule->end_time
        ) {
            return back()->with('error', 'الموعد خارج وقت دوام الطبيب.');
        }

        // تحقق من الحالة
        if ($appointment->location_type !== 'on_Street' || $appointment->status !== 'confirmed') {
            return back()->with('error', 'المريض ليس في الطريق أو وصل مسبقًا. لا يمكن تعديل الحالة.');
        }
        // تغيير الحالة
        $appointment->update(['location_type' => 'in_Clinic']);
        WaitingList::create([
            'appointment_id' => $appointment->id,
            'check_in_time' => now(),
        ]);
        return back()->with('success', 'تم تأكيد وصول المريض وإضافته لقائمة الانتظار.');
    }


    public function getNextAvailableSlot($doctorId)
    {
        $now = now();

        // البحث عن أول موعد متاح بغض النظر عن اليوم
        $nextAvailableSlot = Appointment::where('doctor_id', $doctorId)
            ->where(function ($query) {
                $query->whereNull('patient_id') // مواعيد غير محجوزة
                    ->orWhere('status', 'pending'); // أو مواعيد معلقة
            })
            ->where(function ($query) use ($now) {
                $query->whereDate('date', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->whereDate('date', $now->toDateString())
                            ->whereTime('end_time', '>', $now->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->first();

        if (!$nextAvailableSlot) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد مواعيد متاحة حالياً للطبيب'
            ]);
        }

        // الحصول على جميع الأوقات المتاحة في يوم الموعد
        $availableTimes = $this->getAvailableTimes($doctorId, $nextAvailableSlot->date);

        return response()->json([
            'success' => true,
            'next_available_slot' => [
                'date' => $nextAvailableSlot->date,
                'day_name' => \Carbon\Carbon::parse($nextAvailableSlot->date)->translatedFormat('l'),
                'start_time' => substr($nextAvailableSlot->start_time, 0, 5),
                'end_time' => substr($nextAvailableSlot->end_time, 0, 5),
                'full_period' => substr($nextAvailableSlot->start_time, 0, 5) . '-' . substr($nextAvailableSlot->end_time, 0, 5)
            ],
            'available_times' => $availableTimes
        ]);
    }

    private function getAvailableTimes($doctorId, $date)
    {
        $slots = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->where(function ($query) {
                $query->whereNull('patient_id')
                    ->orWhere('status', 'pending');
            })
            ->orderBy('start_time')
            ->get();

        if ($slots->isEmpty()) {
            return [];
        }

        return $slots->map(function ($slot) {
            return [
                'time' => substr($slot->start_time, 0, 5) . '-' . substr($slot->end_time, 0, 5),
                'is_available' => empty($slot->patient_id),
                'slot_id' => $slot->id
            ];
        });
    }

    public function getNearestAvailableSlot($doctorId)
    {
        $now = Carbon::now();

        // جلب الطبيب مع جدول مواعيده
        $doctor = Doctor::with('doctorSchedule')->findOrFail($doctorId);

        $nearestSlot = null;

        // نعمل لوب على أيام العمل مرتبة من اليوم الحالي فصاعدًا
        foreach (range(0, 30) as $dayOffset) { // بحث لمدة 30 يوم قدام
            $date = $now->copy()->addDays($dayOffset);
            $dayName = $date->format('l'); // Saturday, Sunday ...

            // هل الطبيب عنده دوام بهذا اليوم؟
            $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
            if (!$schedule) {
                continue;
            }

            // تقسيم فترة العمل لسلوتات حسب عدد المرضى في الساعة
            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);

            // مدة الموعد بالدقائق
            $slotDuration = $schedule->patients_per_hour > 0
                ? floor(60 / $schedule->patients_per_hour)
                : 30; // إذا ما حددنا، الافتراضي 30 دقيقة

            while ($startTime->lessThan($endTime)) {
                // لازم الموعد يكون بالمستقبل
                if ($date->isToday() && $startTime->lessThanOrEqualTo($now)) {
                    $startTime->addMinutes($slotDuration);
                    continue;
                }

                // التحقق إذا هذا الوقت محجوز
                $exists = Appointment::where('doctor_id', $doctorId)
                    ->where('date', $date->toDateString())
                    ->where('start_time', $startTime->format('H:i:s'))
                    ->whereIn('status', ['confirmed', 'pending', 'processing'])
                    ->exists();

                if (!$exists) {
                    // وجدنا أقرب موعد
                    $nearestSlot = [
                        'date' => $date->toDateString(),
                        'day' => $dayName,
                        'time' => $startTime->format('H:i'),
                        'timeend' => $endTime->format('H:i'),
                    ];
                    break 2; // نخرج من كل الحلقات
                }

                $startTime->addMinutes($slotDuration);
            }
        }

        if ($nearestSlot) {
            return response()->json([
                'status' => 'available',
                'nearest_slot' => $nearestSlot
            ]);
        } else {
            return response()->json([
                'status' => 'full',
                'message' => 'لا يوجد مواعيد متاحة خلال الشهر القادم'
            ]);
        }
    }



    public function getNearestAvailableRangeSlot($doctorId)
    {
        $now = Carbon::now();
        $doctor = Doctor::with('doctorSchedule')->findOrFail($doctorId);

        foreach (range(0, 30) as $dayOffset) {
            $date = $now->copy()->addDays($dayOffset);
            $dayName = $date->format('l');

            // جلب جدول دوام الطبيب لليوم
            $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
            if (!$schedule) {
                continue;
            }

            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);
            $patientsPerHour = max($schedule->patients_per_hour, 1);

            $ranges = [];

            // تقسيم الوقت لرينجات
            while ($startTime->lessThan($endTime)) {
                $rangeStart = $startTime->copy();

                // تحديد مدة الرينج (عادة ساعة أو نصف ساعة إذا باقي أقل)
                $rangeEnd = $rangeStart->copy()->addHour();
                if ($rangeEnd->greaterThan($endTime)) {
                    $rangeEnd = $endTime->copy();
                }

                // حساب عدد الساعات أو النصف ساعة
                $hoursInRange = $rangeStart->diffInMinutes($rangeEnd) / 60;
                $totalSlots = ceil($patientsPerHour * $hoursInRange);

                // جلب عدد الحجوزات
                $bookedSlots = Appointment::where('doctor_id', $doctorId)
                    ->where('date', $date->toDateString())
                    ->whereBetween('start_time', [$rangeStart->format('H:i:s'), $rangeEnd->format('H:i:s')])
                    ->whereIn('status', ['confirmed', 'pending', 'processing'])
                    ->count();

                $isFull = $bookedSlots >= $totalSlots;
                $available = !$isFull;

                $ranges[] = [
                    'time' => $rangeStart->format('H:i') . '-' . $rangeEnd->format('H:i'),
                    'booked_slots' => $bookedSlots,
                    'total_slots' => $totalSlots,
                    'available' => $available,
                    'isfull' => $isFull
                ];

                $startTime = $rangeEnd->copy();
            }

            // إيجاد أقرب رينج فيه حجز متاح
            $nearestRange = collect($ranges)->firstWhere('available', true);

            if ($nearestRange) {
                return response()->json([
                    'status' => 'available',
                    'date' => $date->toDateString(),
                    'day' => $dayName,
                    'ranges' => $ranges,
                    'nearest_slot' => $nearestRange
                ]);
            }
        }

        return response()->json([
            'status' => 'full',
            'message' => 'لا يوجد مواعيد متاحة خلال الشهر القادم'
        ]);
    }

    public function book($doctorId, $date, $time)
    {
        $patients = Patient::with('user')->get();
        [$startTime, $endTime] = explode('-', $time); // هنا 16:00 و 17:00

        return view('secretary.appointments.appointment-book', [
            'doctor_id' => $doctorId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'patients' => $patients
        ]);
    }

    public function bookstore(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'time' => 'required',
            'end_time' => 'required',
            'type_visit' => 'required|in:appointment,review',
            'location_type' => 'required|in:in_Home,on_Street,in_Clinic,at_Doctor,in_Payment,finished',
            'arrivved_time' => 'required|integer|min:1'
        ]);

        $date = Carbon::parse($validated['date']);
        $start_time = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        $end_time = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);

        // ✅ منع الحجز في العيادة أو الشارع إلا إذا كان الموعد اليوم
        if (in_array($validated['location_type'], ['in_Clinic', 'on_Street']) && !$date->isToday()) {
            return back()->withErrors(['location_type' => 'لا يمكن الحجز في العيادة أو في الشارع إلا في تاريخ اليوم فقط.']);
        }

        // ✅ التحقق: إذا اليوم، لازم الوقت يكون >= الآن
        if ($date->isToday() && $start_time->lessThan(Carbon::now())) {
            return back()->withErrors(['time' => 'لا يمكن حجز موعد في وقت قد مضى.']);
        }

        // ✅ التحقق: وقت النهاية لازم يكون بعد البداية
        if ($end_time->lessThanOrEqualTo($start_time)) {
            return back()->withErrors(['end_time' => 'وقت الانتهاء يجب أن يكون بعد وقت البداية.']);
        }

        // ✅ التحقق: الموعد ضمن دوام الدكتور
        $doctor = Doctor::with('doctorSchedule')->findOrFail($validated['doctor_id']);
        $dayName = $date->format('l');
        $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);

        if (!$schedule) {
            return back()->withErrors(['date' => 'الدكتور غير متاح في هذا اليوم.']);
        }

        $workStart = Carbon::parse($validated['date'] . ' ' . $schedule->start_time);
        $workEnd = Carbon::parse($validated['date'] . ' ' . $schedule->end_time);

        if ($start_time->lt($workStart) || $end_time->gt($workEnd)) {
            return back()->withErrors(['time' => 'الموعد يجب أن يكون ضمن ساعات عمل الدكتور: ' . $workStart->format('H:i') . ' - ' . $workEnd->format('H:i')]);
        }
        $appointment = Appointment::create([
            'doctor_id' => $validated['doctor_id'],
            'patient_id' => $validated['patient_id'],
            'secretary_id' => Auth::user()->secretary->id,
            'date' => $validated['date'],
            'day' => Carbon::parse($validated['date'])->format('l'),
            'start_time' => $start_time->format('H:i:s'),
            'end_time' => $end_time->format('H:i:s'),
            'status' => 'confirmed',
            'location_type' => $validated['location_type'],
            'created_by' => 'secretary',
            'type_visit' => $validated['type_visit'],
            'arrivved_time' => $validated['arrivved_time'],
        ]);

        // إذا الموعد اليوم -> إكمال السجل الطبي ومنع التعديل
        if (Carbon::parse($validated['date'])->isToday()) {
            $record = $appointment->patient->patient_record; // تأكد أن لديك علاقة في الـ Model

            if ($record) {
                $record->update([
                    'patient_id' => $validated['patient_id'],
                    'profile_submitted' => 1,
                    'diseases_submitted' => 1,
                    'operations_submitted' => 1,
                    'medicalAttachments_submitted' => 1,
                    'allergies_submitted' => 1,
                    'family_history_submitted' => 1,
                    'medications_submitted' => 1,
                    'medicalfiles_submitted' => 1
                ]);
            }
        }

        // إرسال إشعار للمريض
        $user = $appointment->patient->user;
        $user->notify(new AppointmentConfirmedNotification($appointment));

        if (!empty($user->fcm_token)) {
            $this->sendFirebaseNotification(
                $user->fcm_token,
                'تم تأكيد الموعد',
                'موعدك بتاريخ ' . $appointment->date . ' الساعة ' . $appointment->start_time
            );
        }

        return redirect()->route('secretary.appointments')->with('status', 'تم حجز الموعد بنجاح');
    }

    public function sendFirebaseNotification($token, $title, $body)
    {
        if (empty($token)) {
            \Log::warning("محاولة إرسال إشعار بدون FCM Token");
            return false;
        }

        try {
            $messaging = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials_file'))
                ->createMessaging();

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData(['type' => 'appointment_update']);

            $messaging->send($message);
            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase Notification Error: ' . $e->getMessage());

            return false;
        }
    }

    public function cancelAllUpcoming($doctorId)
    {
        $today = Carbon::today();

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', '>=', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        if ($appointments->isEmpty()) {
            return redirect()->back()->with('error', 'لا يوجد مواعيد قادمة لإلغائها.');
        }

        $results = [
            'total_canceled' => 0,
            'notifications_sent' => 0,
            'patients_without_token' => 0,
            'appointments_without_patient' => 0
        ];

        foreach ($appointments as $appointment) {
            $appointment->status = 'canceled_by_secretary';
            $appointment->save();
            $results['total_canceled']++;

            if ($appointment->patient && $appointment->patient->user) {
                $user = $appointment->patient->user;


                // Send database notification
                $user->notify(new AppointmentCancelledNotification($appointment));

                // Send Firebase notification if token exists
                if (!empty($user->fcm_token)) {
                    $success = $this->sendFirebaseNotification(
                        $user->fcm_token,
                        'تم إلغاء الموعد',
                        'تم إلغاء موعدك بتاريخ ' . $appointment->date
                    );

                    if ($success) {
                        $results['notifications_sent']++;
                        \Log::info("تم إرسال إشعار FCM للمستخدم ID={$user->id}");
                    } else {
                        \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$user->id}");
                    }
                } else {
                    $results['patients_without_token']++;
                    \Log::info("لا يوجد FCM token للمستخدم ID={$user->id}، تم حفظ الإشعار في قاعدة البيانات فقط");
                }
            } else {
                $results['appointments_without_patient']++;
                \Log::warning("لا يوجد مريض مرتبط بالموعد ID={$appointment->id}");
            }
        }

        // Prepare the response message
        $message = 'تم إلغاء جميع المواعيد القادمة لهذا الطبيب. (' . $results['total_canceled'] . ' مواعيد)';

        // إضافة التفاصيل فقط إذا كانت هناك معلومات إضافية
        if ($results['notifications_sent'] > 0 || $results['patients_without_token'] > 0 || $results['appointments_without_patient'] > 0) {
            $details = [];

            if ($results['notifications_sent'] > 0) {
                $details[] = '✅ تم إرسال إشعارات إلى ' . $results['notifications_sent'] . ' مرضى';
            }

            if ($results['patients_without_token'] > 0) {
                $details[] = '⚠️ ' . $results['patients_without_token'] . ' مرضى ليس لديهم جهاز مسجل';
            }

            if ($results['appointments_without_patient'] > 0) {
                $details[] = '❌ ' . $results['appointments_without_patient'] . ' موعد غير مرتبط بمريض';
            }

            $message .= '<div class="mt-2 text-sm text-gray-600">' . implode(' - ', $details) . '</div>';
        }

        return redirect()->back()->with('status', $message);

    }



}

